# php-crawler
A crawler written in php: find email addresses on the internets.
See it in action here (video): https://www.youtube.com/watch?v=rWsb6E_335U

## Installation
1. Put this all files on your server
2. Create a mysql database
3. Create database tables using the SQL code below
4. Open Crawler.php and edit the __construct function with your database connection infos
5. If your are running php-crawler on a windows system, remove everything on /data/system_load.php

#### Database tables creation
Open a SQL terminal, paste this and execute:
```sql
CREATE TABLE `emails` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(255) NOT NULL DEFAULT '',
  `date` datetime NOT NULL,
  PRIMARY KEY (`id`)
);

CREATE TABLE `urls` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `url` varchar(1000) NOT NULL DEFAULT '',
  `date` datetime NOT NULL,
  `visited` tinyint(1) NOT NULL DEFAULT '0',
  `email_visited` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
);
```
## Usage
1. Navigate to index.php
2. Enter an url on the form input and click to fire the form. The crawler will scan all url on this page and put them in the database.
The crawler will then visite all unvisited url that are in the database, and do the same search for other urls.
3. Navigate to emails.php. The crawler will now start to search for email addresses in urls that are in the database.
4. If you want a list of all the emails, just export your database table 'emails', and do whatever you want with it.

## Todo
- Add an option for domain specific crawl
- Crawl for other things than emails
- ...
