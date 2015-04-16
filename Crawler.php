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
			
			// get (int)x urls that has not been crawled for emails yet
			$url = $this->get_x_non_crawled_for_email_url();
			
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
		
		// maximum time the request is allowed to take
		curl_setopt($ch, CURLOPT_TIMEOUT, 5);
		
		// execute curl
		$datas = curl_exec($ch);
		
		// show result on the browser
		echo '<strong class="text-green">VISITED URL: </strong>' . $url . str_pad("", 4096, " ") . " <br>";
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
			
			// we want only urls that are equal ore shorter than 1000 characteres
			if (strlen($url) <= 1000) {
				
				// we do not want to crawl file urls
				if (
					$this->string_ends_with($url, '.jpg')  === false &&
					$this->string_ends_with($url, '.png')  === false &&
					$this->string_ends_with($url, '.gif')  === false &&
					$this->string_ends_with($url, '.tiff') === false &&
					$this->string_ends_with($url, '.bmp')  === false &&
					$this->string_ends_with($url, '.pdf')  === false &&
					$this->string_ends_with($url, '.svg')  === false &&
					$this->string_ends_with($url, '.fla')  === false &&
					$this->string_ends_with($url, '.swf')  === false &&
					$this->string_ends_with($url, '.css')  === false &&
					$this->string_ends_with($url, '.js')   === false
				) {
					
					// we don't want to store already stored urls on the database
					if ($this->url_exists_in_database($url) === false) {
						
						// we don'nt want to store anchors like http://example.com/index.php/#comment-123456
						if ($this->string_contains($url, '/#') === false) {
						
							// send url to the database
							$this->insert_url($url);
							
						}
						
					}
					
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
		
		// if there are several urls, we want to run curl multi
		if (is_array($url)) {
			
			// the number of urls
			$count = count($url);
			
			for ($i = 0; $i < $count; $i++) {
				
				${'ch' . $i} = curl_init($url[$i]['url']);
				curl_setopt(${'ch' . $i}, CURLOPT_RETURNTRANSFER, true);
				curl_setopt(${'ch' . $i}, CURLOPT_HEADER, 0);
				curl_setopt(${'ch' . $i}, CURLOPT_TIMEOUT, 5);
				
			}
			
			$mh = curl_multi_init();
			
			for ($i = 0; $i < $count; $i++) {
				
				curl_multi_add_handle($mh, ${'ch' . $i});
				
			}
			
			$running = null;
			
			do {
				
				curl_multi_exec($mh, $running);
				
			} while ($running);
			
			for ($i = 0; $i < $count; $i++) {
				
				// get the content on the url
				${'data' . $i} = curl_multi_getcontent(${'ch' . $i});
				
				// show the url to the brawser
				echo '<strong class="text-green">CRAWLED URL: </strong>' . $url[$i]['url'] . str_pad("", 4096, " ") . " <br>";
				ob_flush();
				flush();
				
				// update url to visited on the database
				$this->update_crawled_for_email_url($url[$i]['url']);
				
				// find emails on the data from the url
				$pattern = '#\b[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,4}\b#i';
				preg_match_all($pattern, ${'data' . $i}, $emails);
				
				foreach ($emails[0] as $email) {
					
					// we want only emails that are equal ore shorter than 255 characteres
					if (strlen($email) <= 255) {
					
						// we do not want @2x retina images or weird things
						if (
							$this->string_ends_with($email, '.jpg')  === false &&
							$this->string_ends_with($email, '.png')  === false &&
							$this->string_ends_with($email, '.gif')  === false &&
							$this->string_ends_with($email, '.tiff') === false &&
							$this->string_ends_with($email, '.bmp')  === false &&
							$this->string_ends_with($email, '.pdf')  === false &&
							$this->string_ends_with($email, '.svg')  === false &&
							$this->string_ends_with($email, '.fla')  === false &&
							$this->string_ends_with($email, '.swf')  === false &&
							$this->string_ends_with($email, '.css')  === false &&
							$this->string_ends_with($email, '.js')   === false
						) {
							
							// we don't want to store already stored emails on the database
							if ($this->email_exists_in_database($email) === false) {
								
								// send email to the database
								$this->insert_email($email);
								
								// show found email on the browser
								echo '<strong class="text-red">EMAIL FOUND: </strong>' . $email;
								echo str_pad("", 4096, " ");
								echo " <br>";
								
							}
							
						}
						
					}
						
				}
				
			}
			
		} else {
			
			// url is a single url, initialize curl (not curl multi)
			$ch = curl_init($url);
			
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			
			// ignore hhtp headers
			curl_setopt($ch, CURLOPT_HEADER, 0);
			
			// execute curl
			$datas = curl_exec($ch);
			
			// show result on the browser
			echo '<strong class="text-green">CRAWLED URL: </strong>' . $url . str_pad("", 4096, " ") . " <br>";
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
	 * get_x_non_crawled_for_email_url function.
	 * 
	 * @access private
	 * @param int $number (default: 2)
	 * @return array
	 */
	private function get_x_non_crawled_for_email_url($number = 2) {
		
		$this->db->where('email_visited', '0');
		$result = $this->db->get('urls', $number);
		return $result;
		
	}
	
	/**
	 * string_ends_with function.
	 * 
	 * @access private
	 * @param mixed $haystack
	 * @param mixed $needle
	 * @return bool
	 */
	private function string_ends_with($haystack, $needle) {
		
		$length = strlen($needle);
		if ($length == 0) {
			return true;
		}
		return (substr($haystack, -$length) === $needle);
	
	}
	
	/**
	 * string_contains function.
	 * 
	 * @access private
	 * @param mixed $haystack
	 * @param mixed $needle
	 * @return bool
	 */
	private function string_contains($haystack, $needle) {
		
		if (strpos($haystack, $needle) !== false) {
			return true;
		}
		return false;
		
	}
	
	/**
	 * get_total_url function.
	 * 
	 * @access public
	 * @return int
	 */
	public function get_total_url() {
		
		return $this->db->rawQuery('SELECT COUNT(id) FROM urls')[0]['COUNT(id)'];
	
	}
	
	/**
	 * get_visited_url function.
	 * 
	 * @access public
	 * @return int
	 */
	public function get_visited_url() {
		
		return $this->db->rawQuery('SELECT COUNT(id) FROM urls WHERE visited=1')[0]['COUNT(id)'];
	
	}
	
	/**
	 * get_total_email function.
	 * 
	 * @access public
	 * @return int
	 */
	public function get_total_email() {
		
		return $this->db->rawQuery('SELECT COUNT(id) FROM emails')[0]['COUNT(id)'];
	
	}
	
	/**
	 * get_crawled_for_email_url function.
	 * 
	 * @access public
	 * @return int
	 */
	public function get_crawled_for_email_url() {
		
		return $this->db->rawQuery('SELECT COUNT(id) FROM urls WHERE email_visited=1')[0]['COUNT(id)'];
	
	}

}
