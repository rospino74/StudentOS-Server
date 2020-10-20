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
		public function testHTTPS($https, $XForwardedProtocol, $expected) {
			$this->assertEquals(
				$expected,
				(isset($https) && $https != 'on') || 
				(isset($XForwardedProtocol) && $XForwardedProtocol == 'http')
			);
		}
		public function providerHTTPS() {
			return [
				['off',  null, 		true],
				['off',  'http', 	true],
				[ null,  'http',	true],
				[ null,  null, 		false],
				[ 'on',  null, 		false],
				[ null,  'https',	false],
				[ 'on',  'https', 	false]
			];
		}
		
		/**
		 * @dataProvider providerCORS
		 * @depend testHTTPS
		 */
		public function testCORS($origin, $expected) {
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
				$expected,
				isset($origin) && 
				in_array(preg_replace('/http[s]?:\/\//mi', '', strtolower($origin)), $allowed_domains)
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
				['ssh://localhost:1234', 	false],
				['https://127.0.0.1:2000', 	false],
				['ftp://127.0.0.1',			false],
				[null, 						false]
			];
		}

		/**
		 * @dataProvider providerParseRequestedUrl
		*/
		public function testParseRequestedUrl($url, $expected) {
			$toRemove = dirname($url, 2) . '/api/';
			$this->assertEquals(
				$expected,
				str_replace($toRemove, '', $url)
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