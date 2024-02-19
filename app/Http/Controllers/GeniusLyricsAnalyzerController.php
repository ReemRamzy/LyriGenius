<?php

namespace App\Http\Controllers;

use GuzzleHttp\Client;
use Illuminate\Http\Request;

class GeniusLyricsAnalyzerController extends Controller
{
    protected $client;
    protected $accessToken;

    public function __construct()
    {
        $this->client = new Client([
            'base_uri' => 'https://api.genius.com/',
        ]);

        $this->accessToken = $this->getAccessToken();
    }

    public function analyzeArtistLyrics(Request $request)
    {
        $artistName = $request->input('artist_name');
        $artistId = $this->getArtistId($artistName);

        if (!$artistId) {
            return response()->json(['error' => 'Artist not found'], 404);
        }

        $songs = $this->fetchArtistLyrics($artistId);
        $allLyrics = '';

        $songData = [];
        foreach ($songs as $song) {
            $lyrics = $song['lyrics'];
            $wordCounts = $this->getWordCounts($lyrics);

            $allLyrics .= $lyrics . "\n"; // Concatenate lyrics of all songs
            $songData[] = [
                'title' => $song['title'],
                'lyrics' => $lyrics,
            ];
        }

        $mostRepeatedWords = $this->getMostRepeatedWords($allLyrics);

        return response()->json([
            'artist_name' => $artistName,
            'songs' => $songData,
            'most_repeated_words' => $mostRepeatedWords
        ]);
    }

    protected function getAccessToken()
    {
        $response = $this->client->post('oauth/token', [
            'form_params' => [
                'client_id' => env('GENIUS_CLIENT_ID'),
                'client_secret' => env('GENIUS_CLIENT_SECRET'),
                'grant_type' => 'client_credentials',
            ],
        ]);

        $data = json_decode($response->getBody(), true);
        return $data['access_token'];
    }

    protected function getArtistId($artistName)
    {
        $response = $this->client->get('search', [
            'query' => [
                'q' => $artistName,
            ],
            'headers' => [
                'Authorization' => 'Bearer ' . $this->accessToken,
            ],
        ]);

        $data = json_decode($response->getBody(), true);

        if (!isset($data['response']['hits'][0]['result']['primary_artist']['id'])) {
            return null;
        }

        return $data['response']['hits'][0]['result']['primary_artist']['id'];
    }

    protected function fetchArtistLyrics($artistId)
    {
        $songs = [];

        $response = $this->client->get("artists/{$artistId}/songs", [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->accessToken,
            ],
        ]);

        $data = json_decode($response->getBody(), true);

        if (!isset($data['response']['songs'])) {
            return [];
        }

        foreach ($data['response']['songs'] as $song) {
            if (!isset($song['url'])) {
                continue;
            }

            $songLyrics = $this->fetchLyricsFromUrl($song['url']);

            if ($songLyrics !== null) {
                // Exclude specified words from individual song lyrics
                $excludedWords = ['i', 'that', 'you','by','an', 'the', 'on', 'to','and','my','your','it','we','he','she','us','a','in','of','me', "i'm"]; // Add more words as needed
                $filteredLyrics = $this->excludeWords($songLyrics, $excludedWords);

                $songs[] = [
                    'title' => $song['title'],
                    'lyrics' => $filteredLyrics
                ];
            }
        }

        return $songs;
    }

    protected function fetchLyricsFromUrl($url)
    {
        $html = file_get_contents($url);
        $dom = new \DOMDocument();
        @$dom->loadHTML($html); // Suppress warnings

        $xpath = new \DOMXPath($dom);
        $lyricsNodes = $xpath->query('//div[contains(@class, "Lyrics__Container-sc-1ynbvzw-1")]');

        $lyrics = '';
        foreach ($lyricsNodes as $node) {
            $title = $xpath->query('.//h3[contains(text(), "Discography")]', $node);
            if ($title->length > 0) {
                continue;
            }
            $lyrics .= $node->nodeValue . "\n"; // Get the text content of the node
        }

        return $lyrics;
    }

    protected function excludeWords($text, $excludeWords)
    {
        // Convert the text to lowercase and split it into words
        $words = str_word_count(strtolower($text), 1);

        // Remove the excluded words
        $filteredWords = array_diff($words, $excludeWords);

        // Reconstruct the text with filtered words
        $filteredText = implode(' ', $filteredWords);

        return $filteredText;
    }

    protected function getWordCounts($text)
    {
        $words = str_word_count(strtolower($text), 1);
        return count($words);
    }

    protected function getMostRepeatedWords($text, $limit = 50)
    {
        $words = str_word_count(strtolower($text), 1);
        $wordCounts = array_count_values($words);
        arsort($wordCounts);
        $mostRepeatedWords = array_slice(array_keys($wordCounts), 0, $limit);
        return $mostRepeatedWords;
    }
}

