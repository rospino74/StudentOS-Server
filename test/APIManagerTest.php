<?php
	error_reporting(E_ALL | E_STRICT);
	use PHPUnit\Framework\TestCase;


	//defining path
	define('BASE_PATH', str_replace('\\', '/', dirname(__DIR__)) . '/');
	define('BASE_API', BASE_PATH . 'Server/api/');
	define('BASE_UTILS', BASE_PATH . 'Server/utils/');	

	class APIManagerTest extends TestCase {
		/**
		 * @dataProvider providerHTTPS
		 */
		public function testHTTPS($headers = array()) {
			$this->assertEquals($headers['result'],
				(isset($headers['HTTPS']) && $headers['HTTPS'] != 'on') || 
				(isset($headers['X-Forwarded-Proto']) && $headers['X-Forwarded-Proto'] == 'http')
			);
		}
		public function providerHTTPS() {
			return [
				['HTTPS' =>'off', 'X-Forwarded-Proto' => null, 		'result' => true],
				['HTTPS' =>'off', 'X-Forwarded-Proto' => 'http', 	'result' => true],
				['HTTPS' => null, 'X-Forwarded-Proto' => 'http',	'result' => true],
				['HTTPS' => null, 'X-Forwarded-Proto' => null, 		'result' => true],
				['HTTPS' => 'on', 'X-Forwarded-Proto' => null, 		'result' => false],
				['HTTPS' => null, 'X-Forwarded-Proto' => 'https',	'result' => false],
				['HTTPS' => 'on', 'X-Forwarded-Proto' => 'https', 	'result' => false]
			];
		}
		
		/**
		 * @dataProvider providerCORS
		 */
		public function testCORS($origin) {
			//Allowed domains
			$allowed_domains = array(
				'localhost:3000',
				'localhost:80',
				'localhost',
				'127.0.0.1:3000',
				'127.0.0.1:80',
				'127.0.0.1'
			);

			//Check of the domain is allowed
			$this->assertEquals(
				$origin['result'],
				isset($origin['address']) && 
				in_array(preg_replace('/http[s]?:\/\//mi', '', strtolower($origin['address'])), $allowed_domains)
			);
		}
		public function providerCORS() {
			return [
				['address' =>'localhost', 				'result' => true],
				['address' =>'https://localhost', 		'result' => true],
				['address' =>'http://localhost', 		'result' => true],
				['address' =>'localhost:3000', 			'result' => true],
				['address' =>'https://localhost:3000', 	'result' => true],
				['address' =>'http://localhost:3000', 	'result' => true],
				['address' =>'localhost:80', 			'result' => true],
				['address' =>'https://localhost:80', 	'result' => true],
				['address' =>'http://localhost:80', 	'result' => true],
				['address' =>'https://google.com', 		'result' => false],
				['address' =>'http://192.168.1.0', 		'result' => false],
				['address' =>'https://localhost:3000', 	'result' => false],
				['address' =>'https://127.0.0.1:2000', 	'result' => false]
			];
		}

		/**
		 * @dataProvider providerParseRequestedUrl
		*/
		public function testParseRequestedUrl($urls) {
			$toRemove = dirname($urls['address'], 2) . '/api/';
			$this->assertEquals(
				$urls['result'],
				str_replace($toRemove, '', $_SERVER['REQUEST_URI'])
			);
		}
		public function providerParseRequestedUrl() {
			return [
				['address' =>'StudentOS/Code/api/isSessionValid',	'result' => 'isSessionValid'],
				['address' =>'StudentOS/Code/api/addPost',			'result' => 'addPost'],
				['address' =>'StudentOS/Code/api/getPost',			'result' => 'getPost'],
				['address' =>'StudentOS/Code/api/deletePost',		'result' => 'deletePost'],
				['address' =>'StudentOS/Code/api/addComment',	 	'result' => 'addComment'],
				['address' =>'StudentOS/Code/api/deleteComment',	'result' => 'deleteComment'],
				['address' =>'StudentOS/Code/api/getCommentByParentId',	'result' => 'getCommentByParentId'],
				['address' =>'StudentOS/Code/api/getUserInfo',		'result' => 'getUserInfo'],
				['address' =>'StudentOS/Code/api/updateSettings',	'result' => 'updateSettings'],
				['address' =>'StudentOS/Code/api/uploadNewIcon',	'result' => 'uploadNewIcon']
			];
		}
	}
?>