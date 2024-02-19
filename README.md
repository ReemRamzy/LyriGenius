# LyriGenius

LyriGenius is a web application that allows users to analyze and explore lyrics from their favorite artists. With LyriGenius, you can retrieve lyrics, analyze word frequencies, and discover the most repeated words in an artist's discography.

## Features

- Retrieve lyrics for an artist's songs
- Analyze word frequencies in lyrics
- Discover the most repeated words in an artist's discography

## Installation

1. Clone the repository to your local machine:
git clone https://github.com/yourusername/LyriGenius.git

2. Install dependencies using Composer:
composer install

3. Set up your environment variables by creating a .env file and configuring your Genius API credentials:
GENIUS_CLIENT_ID=your_genius_client_id
GENIUS_CLIENT_SECRET=your_genius_client_secret

4. Run the application:
php artisan serve

## Usage:
Enter the name of the artist you want to analyze in the search bar.
Click on the "Analyze" button to retrieve the artist's lyrics and analyze word frequencies.
View the artist's songs along with their lyrics and the most repeated words in their discography.

## Technologies Used
PHP
Laravel
GuzzleHTTP
HTML/CSS
JavaScript


## Contributing
Contributions are welcome! If you would like to contribute to LyriGenius, please follow these steps:

Fork the repository.
Create a new branch (git checkout -b feature/improvement).
Make your changes.
Commit your changes (git commit -am 'Add new feature').
Push to the branch (git push origin feature/improvement).
Create a new Pull Request.

## License
This project is licensed under the MIT License - see the LICENSE file for details.

## Acknowledgements
LyriGenius uses the Genius API to retrieve lyrics. We thank Genius for providing this service.

This README provides an overview of the project, installation instructions, usage guidelines, technologies used, contribution guidelines, licensing information, and acknowledgments. Feel free to modify it according to your project's specific needs and requirements.

