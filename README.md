# php-crawler
A crawler written in php with laravel that find email addresses on the internets.
Given an entry point url, the crawler will search for emails in all the urls available for this entry point domain name.
The emails are downloadable in a text file at any time.
Several users can start searching for emails without viewing the other users' searches (searches are related to a user).

## Installation
- Create a mysql database (default name: `php_crawler`)
- Install the repo with [composer](https://getcomposer.org/):
```bash
composer create-project hedii/php-crawler php-crawler
cd php-crawler
```
- Install [npm](https://docs.npmjs.com/getting-started/what-is-npm) dependencies (optional):
```bash
npm install
```
- Open the `.env` file, check the database credentials, and modify it if needed:
```
DB_HOST=127.0.0.1
DB_DATABASE=php_crawler
DB_USERNAME=root
DB_PASSWORD=root
```
- Build the app
```bash
php artisan crawler:build
```
- Point your webserver to the public directory: `php-crawler/public`
- Done

On Apache you need to set the
``` apache
DocumentRoot /var/www/html/php-crawler/public
``` 
If you have trouble or getting 404 error, point directly to the index document.
Ex: http://localhost/index.php

## Usage
- Navigate to your php-crawler website
- Register a new account
- Create a new search
- Create more searches
- Download the found resources

## Troubleshooting

#### Server requirements
- Curl
- PHP >= 5.5.9
- Curl PHP Extension
- OpenSSL PHP Extension
- PDO PHP Extension
- Mbstring PHP Extension
- Tokenizer PHP Extension

#### Blank space in path
On some systems, if there is any blank space in the path to the crawler public directory, the crawler app won't work.
Remove any space in folders that are part of the crawler path.

#### MAMP server
If you are running the crawler on a MAMP server, edit `config/database.php` and add a unix socket conf:
```php
'mysql' => [
    'driver'    => 'mysql',
    'host'      => env('DB_HOST', 'localhost'),
    'database'  => env('DB_DATABASE', 'forge'),
    'username'  => env('DB_USERNAME', 'forge'),
    'password'  => env('DB_PASSWORD', ''),
    'charset'   => 'utf8',
    'collation' => 'utf8_unicode_ci',
    'prefix'    => '',
    'strict'    => false,
    'engine'    => null,
    
    'unix_socket' => '/Applications/MAMP/tmp/mysql/mysql.sock', // add this line
],
```


## Todo
- write php tests
- write js tests
- Crawl for other things than emails
- ...


## Screenshots
![](https://cloud.githubusercontent.com/assets/5358048/13635825/9a75ec3c-e5fe-11e5-84b8-f4d42973bbda.png)

![](https://cloud.githubusercontent.com/assets/5358048/13635826/9a76ebfa-e5fe-11e5-9bc8-5e770cccfd7a.png)
