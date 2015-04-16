<?php
require '../Crawler.php';
$crawler = new Crawler();
echo $crawler->get_visited_url();