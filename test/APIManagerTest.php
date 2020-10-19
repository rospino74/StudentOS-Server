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
		public function testHTTPS($headers) {
			$this->assertEquals($headers[2],
				(isset($headers[0]) && $headers[0] != 'on') || 
				(isset($headers[1]) && $headers[1] == 'http')
			);
		}
		public function providerHTTPS() {
			return [
				['off',  null, 		true],
				['off',  'http', 	true],
				[ null,  'http',	true],
				[ null,  null, 		true],
				[ 'on',  null, 		false],
				[ null,  'https',	false],
				[ 'on',  'https', 	false]
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
				$origin[1],
				isset($origin[0]) && 
				in_array(preg_replace('/http[s]?:\/\//mi', '', strtolower($origin[0])), $allowed_domains)
			);
		}
		public function providerCORS() {
			return [
				['localhost', 				true],
				['https://localhost', 		true],
				['http://localhost', 		true],
				['localhost:3000', 			true],
				['https://localhost:3000', 	true],
				['http://localhost:3000', 	true],
				['localhost:80', 			true],
				['https://localhost:80', 	true],
				['http://localhost:80', 	true],
				['https://google.com', 		false],
				['http://192.168.1.0', 		false],
				['https://localhost:3000', 	false],
				['https://127.0.0.1:2000', 	false]
			];
		}

		/**
		 * @dataProvider providerParseRequestedUrl
		*/
		public function testParseRequestedUrl($urls) {
			$toRemove = dirname($urls[0], 2) . '/api/';
			$this->assertEquals(
				$urls[1],
				str_replace($toRemove, '', $urls[0])
			);
		}
		public function providerParseRequestedUrl() {
			return [
				['StudentOS/Code/api/isSessionValid',	'isSessionValid'],
				['StudentOS/Code/api/addPost',			'addPost'],
				['StudentOS/Code/api/getPost',			'getPost'],
				['StudentOS/Code/api/deletePost',		'deletePost'],
				['StudentOS/Code/api/addComment',	 	'addComment'],
				['StudentOS/Code/api/deleteComment',	'deleteComment'],
				['StudentOS/Code/api/getCommentByParentId',	'getCommentByParentId'],
				['StudentOS/Code/api/getUserInfo',		'getUserInfo'],
				['StudentOS/Code/api/updateSettings',	'updateSettings'],
				['StudentOS/Code/api/uploadNewIcon',	'uploadNewIcon']
			];
		}
	}
?>