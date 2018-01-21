# php-crawler

[![Build Status](https://travis-ci.org/hedii/php-crawler.svg?branch=master)](https://travis-ci.org/hedii/php-crawler)

A crawler application with a php backend using Laravel, and a js frontend using vuejs, that finds email addresses on the internets.

Given an entry point url, the crawler will search for emails in all the urls available from this entry point domain name.
The emails are downloadable as a text file.

Multiple users can start searching for emails without being able see the other users' searches (searches are related to a single user).

## Screenshots

![Screenshot1](https://user-images.githubusercontent.com/5358048/35198632-c9afcdf2-fef1-11e7-91ec-900b1ff25b62.png)

![Screenshot2](https://user-images.githubusercontent.com/5358048/35198634-cddecb44-fef1-11e7-836b-5dbe16a1bb2a.png)

![Screenshot3](https://user-images.githubusercontent.com/5358048/35198637-d015a180-fef1-11e7-93a0-9eb72e1c1281.png)

![Screenshot4](https://user-images.githubusercontent.com/5358048/35198638-d20ea1a8-fef1-11e7-8ecf-df3cf2c4c8e1.png)

## Server requirements

- PHP >= 7.2.0
- OpenSSL PHP Extension
- PDO PHP Extension
- Mbstring PHP Extension
- Tokenizer PHP Extension
- XML PHP Extension

## Installation

- Create a mysql database (default name: `crawler`)
- Install the project with [composer](https://getcomposer.org/):
```bash
composer create-project hedii/php-crawler crawler
cd crawler
```
- Open the `.env` file, check the database credentials, and modify it if needed:
```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=crawler
DB_USERNAME=root
DB_PASSWORD=your_password_here
```
- In the `.env` file, set the application url:
```
APP_URL=http://crawler.localhost
```
- Build the crawler application
```bash
php artisan crawler:build
```
- Point your web server's document / web root to be the public directory: `/some/path/crawler/public`. The index.php in this directory serves as the front controller for all HTTP requests entering your application. [See Laravel documentation](https://laravel.com/docs/master/installation). I highly recommend using [Laravel Valet](https://laravel.com/docs/master/valet) if you are using a Mac. Otherwise, check [Laravel Homestead](https://laravel.com/docs/master/homestead).
- Done

## Usage

- Navigate to your php-crawler website
- Register a new account
- Create a new search
- Create more searches
- Download the found emails

## Testing

```
composer test
```

## Contributing

**All** contributions are welcome :)

Please write some tests if you are adding or modifying features.

## License

php-crawler is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
