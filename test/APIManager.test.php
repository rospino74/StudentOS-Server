<?php
	error_reporting(E_ALL | E_STRICT);

	//defining path
	define('BASE_PATH', str_replace('\\', '/', dirname(__DIR__)) . '/');
	define('BASE_API', BASE_PATH . 'Server/api/');
	define('BASE_UTILS', BASE_PATH . 'Server/utils/');	

	class testAPIManager extends PHPUnit_Framework_TestCase {
		public function testHTTPS($headers = array()) {
			$this->assertTrue(
				(isset($headers['HTTPS']) && $headers['HTTPS'] != 'on') || 
				(isset($headers['X-Forwarded-Proto']) && $headers['X-Forwarded-Proto'] == 'http')
			);
		}

		public function testCORS($origin) {
			//Allowed domains
			$allowed_domains = array(
				'localhost:3000',
				'localhost',
				'192.168.1.80:3000',
				'192.168.1.80'
			);

			//Check of the domain is allowed
			$this->assertTrue(
				isset($origin) && 
				in_array(preg_replace('/http[s]?:\/\//mi', '', strtolower($origin)), $allowed_domains)
			);
		}

		public function testSession($session) {
			//importing the modules
			require_once(BASE_PATH . 'db.config.php');
			require_once(BASE_UTILS . 'session.php');
			
			$this->assertTrue(isSessionValid($link, $session));
		}

		public function testParseRequestedUrl($urls) {
			$toRemove = dirname($urls[0], 2) . '/api/';
			$this->assertEquals(
				$urls[1],
				str_replace($toRemove, '', $_SERVER['REQUEST_URI'])
			);
		}
	}
?>