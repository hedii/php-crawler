<?php
set_time_limit(86400); // 24 hours
ignore_user_abort(true);

require_once ('MysqliDb.php');

/**
* Crawler class
*/
class Crawler {
	
	/**
	 * db
	 * 
	 * @var mixed
	 * @access private
	 */
	private $db;
	
	/**
	 * __construct function.
	 * 
	 * @access public
	 * @return void
	 */
	public function __construct() {
		
		// database connection
		$this->db = new MysqliDb ('localhost', 'root', 'root', 'php-crawler');
		
	}
	
	/**
	 * crawl_urls function.
	 * 
	 * @access public
	 * @param mixed $url
	 * @return void
	 */
	public function crawl_urls($url) {
		
		// insert the first url in the database
		$this->insert_url($url);
		
		// loop through urls while there are non visited urls in the database
		while ($this->there_are_non_visited_urls_in_database() === true) {
			
			// get the first url that has not been not visited yet
			$url = $this->get_one_non_visited_url();
			
			// find urls on this url
			$this->find_url($url);
			
		}
		
	}
	
	/**
	 * crawl_emails function.
	 * 
	 * @access public
	 * @return void
	 */
	public function crawl_emails() {
		
		// loop through urls while there are non crawled for email urls in the database
		while ($this->there_are_non_crawled_for_email_urls_in_database() === true) {
			
			// get the first url that has not been crawled for emails yet
			$url = $this->get_one_non_crawled_for_email_url();
			
			// find emails on this url
			$this->find_email($url);
			
		}
		
	}
	
	/**
	 * find_url function.
	 * 
	 * @access private
	 * @param mixed $url
	 * @return void
	 */
	private function find_url($url) {
		
		// set up ob for displaying results in real time
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
		
		// show result on the browser
		echo '<strong class="text-green">VISITED URL: </strong>' . $url . str_pad("",2048," ") . " <br>";
		ob_flush();
		flush();
		
		// update the first url to visited (we have data from it)
		$this->update_url($url);
		
		// close curl session
		curl_close($ch);
		
		// find urls on the datas variable
		$pattern = '/\b(?:(?:https?|ftp):\/\/|www\.)[-a-z0-9+&@#\/%?=~_|!:,.;]*[-a-z0-9+&@#\/%=~_|]/i';
		preg_match_all($pattern, $datas, $urls);
		
		// for every url find on the datas variable
		foreach ($urls[0] as $url) {
			
			// we do not want to crawl file urls
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
				
				// we don't want to store already stored urls on the database
				if ($this->url_exists_in_database($url) === false) {
					
					// send url to the database
					$this->insert_url($url);
					
				}
				
			}
		}
		
	}
	
	/**
	 * find_email function.
	 * 
	 * @access private
	 * @param mixed $url
	 * @return void
	 */
	private function find_email($url) {
		
		// set up ob for displaying results in real time
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
		
		// show result on the browser
		echo '<strong class="text-green">CRAWLED URL: </strong>' . $url . str_pad("",2048," ") . " <br>";
		ob_flush();
		flush();
		
		// update the first url to visited (we have data from it)
		$this->update_crawled_for_email_url($url);
		
		// close curl session
		curl_close($ch);
		
		// find emails on the datas variable
		$pattern = '#\b[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,4}\b#i';
		preg_match_all($pattern, $datas, $emails);
		
		// for every email find on the datas variable
		foreach ($emails[0] as $email) {
			
			// we do not want @2x retina images or weird things
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
				
				// we don't want to store already stored emails on the database
				if ($this->email_exists_in_database($email) === false) {
					
					// send email to the database
					$this->insert_email($email);
					
					// show found email on the browser
					echo '<strong class="text-red">EMAIL FOUND: </strong>' . $email;
					echo str_pad("",2048," ");
					echo " <br>";
					
				}
				
			}
		}
		
		ob_flush();
		flush();
		
	}
	
	/**
	 * insert_url function.
	 * 
	 * @access private
	 * @param mixed $url
	 * @return bool
	 */
	private function insert_url($url) {
		
		$data = array(
			'url' => $url,
			'date' => date('Y-m-j H:i:s'),
		);
		if ($this->db->insert('urls', $data)) {
			return true;
		}
		//echo 'insert failed: ' . $db->getLastError();
		return false;

	}
	
	/**
	 * insert_email function.
	 * 
	 * @access private
	 * @param mixed $email
	 * @return bool
	 */
	private function insert_email($email) {
		
		$data = array(
			'email' => $email,
			'date' => date('Y-m-j H:i:s'),
		);
		if ($this->db->insert('emails', $data)) {
			return true;
		}
		//echo 'insert failed: ' . $db->getLastError();
		return false;

	}
	
	/**
	 * update_url function.
	 * 
	 * @access private
	 * @param mixed $url
	 * @return bool
	 */
	private function update_url($url) {
		
		$data = array(
			'visited' => '1'
		);
		$this->db->where('url', $url);
		if ($this->db->update('urls', $data)) {
			return true;
		}
		//echo 'update failed: ' . $db->getLastError();
		return false;

	}
	
	/**
	 * update_crawled_for_email_url function.
	 * 
	 * @access private
	 * @param mixed $url
	 * @return bool
	 */
	private function update_crawled_for_email_url($url) {
		
		$data = array(
			'email_visited' => '1'
		);
		$this->db->where('url', $url);
		if ($this->db->update('urls', $data)) {
			return true;
		}
		//echo 'update failed: ' . $db->getLastError();
		return false;

	}
	
	/**
	 * url_exists_in_database function.
	 * 
	 * @access private
	 * @param mixed $url
	 * @return bool
	 */
	private function url_exists_in_database($url) {
		
		$this->db->where('url', $url);
		$count = $this->db->getValue('urls', 'count(*)');
		if ($count > 0) {
			return true;
		}
		return false;
		
	}
	
	/**
	 * email_exists_in_database function.
	 * 
	 * @access private
	 * @param mixed $email
	 * @return bool
	 */
	private function email_exists_in_database($email) {
		
		$this->db->where('email', $email);
		$count = $this->db->getValue('emails', 'count(*)');
		if ($count > 0) {
			return true;
		}
		return false;
		
	}
	
	/**
	 * there_are_non_visited_urls_in_database function.
	 * 
	 * @access private
	 * @return bool
	 */
	private function there_are_non_visited_urls_in_database() {
		
		$this->db->where('visited', '0');
		$count = $this->db->getValue('urls', 'count(*)');
		if ($count > 0) {
			return true;
		}
		return false;
		
	}
	
	/**
	 * there_are_non_crawled_for_email_urls_in_database function.
	 * 
	 * @access private
	 * @return bool
	 */
	private function there_are_non_crawled_for_email_urls_in_database() {
		
		$this->db->where('email_visited', '0');
		$count = $this->db->getValue('urls', 'count(*)');
		if ($count > 0) {
			return true;
		}
		return false;
		
	}
	
	/**
	 * get_one_non_visited_url function.
	 * 
	 * @access private
	 * @return string
	 */
	private function get_one_non_visited_url() {
		
		$this->db->where('visited', '0');
		$result = $this->db->getOne('urls');
		return $result['url'];
		
	}
	
	/**
	 * get_one_non_crawled_for_email_url function.
	 * 
	 * @access private
	 * @return string
	 */
	private function get_one_non_crawled_for_email_url() {
		
		$this->db->where('email_visited', '0');
		$result = $this->db->getOne('urls');
		return $result['url'];
		
	}
	
	/**
	 * ends_with function.
	 * 
	 * @access private
	 * @param mixed $haystack
	 * @param mixed $needle
	 * @return bool
	 */
	private function ends_with($haystack, $needle) {
		
		$length = strlen($needle);
		if ($length == 0) {
			return true;
		}
		return (substr($haystack, -$length) === $needle);
	
	}

}
