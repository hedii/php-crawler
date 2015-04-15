<?php
error_reporting(E_ALL); 
ini_set('display_errors', 1);
set_time_limit(86400); // 24 hours
ignore_user_abort(true);

require_once ('MysqliDb.php');

/**
* Crawler class
*/
class Crawler {
	
	private $db;
	
	public function __construct() {
		$this->db = new MysqliDb ('localhost', 'root', 'root', 'php-crawler');
	}
	
	public function crawl_urls($url) {
		
		// insert the first url in the database
		$this->insert_url($url);
		
		while ($this->there_are_non_visited_urls_in_database() === true) {
			$url = $this->get_one_non_visited_url();
			$this->find_url($url);
		}
		
	}
	
	public function crawl_emails() {
		
		while ($this->there_are_non_crawled_for_email_urls_in_database() === true) {
			$url = $this->get_one_non_crawled_for_email_url();
			$this->find_email($url);
		}
		
	}
	
	private function find_url($url) {
		
		ini_set('output_buffering', 'off');
		while (@ob_end_flush());
		ini_set('implicit_flush', true);
		ob_implicit_flush(true);
		ob_start();
		
		// initialize curl
		$ch = curl_init($url);
		
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		
		// ignore hhtp headers
		curl_setopt($ch, CURLOPT_HEADER, 0);
		
		// execute curl
		$datas = curl_exec($ch);
		
		echo '<strong class="text-green">VISITED URL: </strong>' . $url . str_pad("",2048," ") . " <br>";
		ob_flush();
		flush();
		
		// update the first url to visited (we have data from it)
		$this->update_url($url);
		
		// close curl session
		curl_close($ch);
		
		$pattern = '/\b(?:(?:https?|ftp):\/\/|www\.)[-a-z0-9+&@#\/%?=~_|!:,.;]*[-a-z0-9+&@#\/%=~_|]/i';
		preg_match_all($pattern, $datas, $urls);
		
		foreach ($urls[0] as $url) {
			if (
				$this->ends_with($url, '.jpg')  === false &&
				$this->ends_with($url, '.png')  === false &&
				$this->ends_with($url, '.gif')  === false &&
				$this->ends_with($url, '.tiff') === false &&
				$this->ends_with($url, '.bmp')  === false &&
				$this->ends_with($url, '.pdf')  === false &&
				$this->ends_with($url, '.svg')  === false &&
				$this->ends_with($url, '.fla')  === false &&
				$this->ends_with($url, '.swf')  === false &&
				$this->ends_with($url, '.css')  === false &&
				$this->ends_with($url, '.js')   === false
			) {
				
				if ($this->url_exists_in_database($url) === false) {
					$this->insert_url($url);
				}
				
			}
		}
		
	}
	
	private function find_email($url) {
		
		ini_set('output_buffering', 'off');
		while (@ob_end_flush());
		ini_set('implicit_flush', true);
		ob_implicit_flush(true);
		ob_start();
		
		// initialize curl
		$ch = curl_init($url);
		
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		
		// ignore hhtp headers
		curl_setopt($ch, CURLOPT_HEADER, 0);
		
		// execute curl
		$datas = curl_exec($ch);
		
		echo '<strong class="text-green">VISITED URL: </strong>' . $url . str_pad("",2048," ") . " <br>";
		ob_flush();
		flush();
		
		// update the first url to visited (we have data from it)
		$this->update_crawled_for_email_url($url);
		
		// close curl session
		curl_close($ch);
		
		$pattern = '#\b[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,4}\b#i';
		preg_match_all($pattern, $datas, $emails);
		
		foreach ($emails[0] as $email) {
			if (
				$this->ends_with($email, '.jpg')  === false &&
				$this->ends_with($email, '.png')  === false &&
				$this->ends_with($email, '.gif')  === false &&
				$this->ends_with($email, '.tiff') === false &&
				$this->ends_with($email, '.bmp')  === false &&
				$this->ends_with($email, '.pdf')  === false &&
				$this->ends_with($email, '.svg')  === false &&
				$this->ends_with($email, '.fla')  === false &&
				$this->ends_with($email, '.swf')  === false &&
				$this->ends_with($email, '.css')  === false &&
				$this->ends_with($email, '.js')   === false
			) {
				
				if ($this->email_exists_in_database($email) === false) {
					$this->insert_email($email);
					echo '<strong style="color: red;">EMAIL : </strong>' . $email;
					echo str_pad("",2048," ");
					echo " <br>";
				}
				
			}
		}
		
		ob_flush();
		flush();
		
	}
	
	private function insert_url($url) {
		
		$data = array(
			'url' => $url,
			'date' => date('Y-m-j H:i:s'),
		);
		if ($this->db->insert('urls', $data)) {
			return true;
		}
		echo 'insert failed: ' . $db->getLastError();
		return false;

	}
	
	private function insert_email($email) {
		
		$data = array(
			'email' => $email,
			'date' => date('Y-m-j H:i:s'),
		);
		if ($this->db->insert('emails', $data)) {
			return true;
		}
		echo 'insert failed: ' . $db->getLastError();
		return false;

	}
	
	private function update_url($url) {
		
		$data = array(
			'visited' => '1'
		);
		$this->db->where('url', $url);
		if ($this->db->update('urls', $data)) {
			return true;
		}
		echo 'update failed: ' . $db->getLastError();
		return false;

	}
	
	private function update_crawled_for_email_url($url) {
		
		$data = array(
			'email_visited' => '1'
		);
		$this->db->where('url', $url);
		if ($this->db->update('urls', $data)) {
			return true;
		}
		echo 'update failed: ' . $db->getLastError();
		return false;

	}
	
	private function url_exists_in_database($url) {
		
		$this->db->where('url', $url);
		$count = $this->db->getValue('urls', 'count(*)');
		if ($count > 0) {
			return true;
		}
		return false;
		
	}
	
	private function email_exists_in_database($email) {
		
		$this->db->where('email', $email);
		$count = $this->db->getValue('emails', 'count(*)');
		if ($count > 0) {
			return true;
		}
		return false;
		
	}
	
	private function there_are_non_visited_urls_in_database() {
		
		$this->db->where('visited', '0');
		$count = $this->db->getValue('urls', 'count(*)');
		if ($count > 0) {
			return true;
		}
		return false;
		
	}
	
	private function there_are_non_crawled_for_email_urls_in_database() {
		
		$this->db->where('email_visited', '0');
		$count = $this->db->getValue('urls', 'count(*)');
		if ($count > 0) {
			return true;
		}
		return false;
		
	}
	
	private function get_one_non_visited_url() {
		
		$this->db->where('visited', '0');
		$result = $this->db->getOne('urls');
		return $result['url'];
		
	}
	
	private function get_one_non_crawled_for_email_url() {
		
		$this->db->where('email_visited', '0');
		$result = $this->db->getOne('urls');
		return $result['url'];
		
	}
	
	private function ends_with($haystack, $needle) {
		
		$length = strlen($needle);
		if ($length == 0) {
			return true;
		}
		return (substr($haystack, -$length) === $needle);
	
	}



}
