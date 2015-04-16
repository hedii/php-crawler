<?php
require '../Crawler.php';
$crawler = new Crawler();
echo $crawler->get_crawled_for_email_url();