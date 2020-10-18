<?php
	session_start();

	//I love Easter Eggs
	//Getting the current day and month
	$date = (new DateTime('now'))->format('d-m');

	//Switching X-Powered-By header on special days
	switch($date) {
		case '31-10':
			header('X-Powered-By: Trick or treat');
		break;

		case '24-12':
		case '25-12':
		case '26-12':
			header('X-Powered-By: Santa\'s cookies');
		break;

		case '31-10':
		case '01-01':
			header('X-Powered-By: Fireworks!');
		break;

		case '01-04':
			header('X-Powered-By: Fishes and teapots!');
			header('HTTP/1.1 418 I\'m a teapot');
		break;

		default:
			header('X-Powered-By: Tea, because I don\'t like coffe');
		break;
	}
	

	//Forcing HTTPS
	if(
		(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'on') || 
		(isset($_SERVER['X-Forwarded-Proto']) && $_SERVER['X-Forwarded-Proto'] == 'http')
	) {
		$newUrl = 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
		header('Location: ' . $newUrl);
		fail(
			'Request must be performed over HTTPS',
			'Your browser SHOULD redirect the request automatically. If not please use ' . $newUrl,
			400
		);
	}

	//BEGIN CORS Section
	//Allowed domains
	$allowed_domains = array(
		'localhost:3000',
		'localhost',
		'192.168.1.80:3000',
		'192.168.1.80'
	);

	//Check of the domain is allowed
	if (isset($_SERVER['HTTP_ORIGIN']) && in_array(preg_replace('/http[s]?:\/\//mi', '', strtolower($_SERVER['HTTP_ORIGIN'])), $allowed_domains)){
		header("Access-Control-Allow-Origin: $_SERVER[HTTP_ORIGIN]");
		header('Access-Control-Allow-Credentials: true');
		header('Access-Control-Max-Age: 43200');
	} else {
		header('Connection: Close');
		fail(
			'CORS: Domain not allowed',
			'Your Origin is not whitelisted!',
			401
		);
	}

	//Handling a preflight request
	if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
		header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
		header('Access-Control-Allow-Headers: X-Authentication, Content-Type, Accept, Origin');
		exit(0);
	}
	//END CORS Section

	//is the authentication header provided?
	if(!isset($_SERVER['HTTP_X_AUTHENTICATION'])){
		fail('Authentication header not sent', null, 400);
	}

	//defining path
	define('BASE_PATH', str_replace('\\', '/', dirname(__DIR__)) . '/');
	define('BASE_API', BASE_PATH . 'Server/api/');
	define('BASE_UTILS', BASE_PATH . 'Server/utils/');

	//importing the modules
	require_once(BASE_PATH . 'db.config.php');
	require_once(BASE_UTILS . 'session.php');

	//failing if the session isn't valid
	if(!isSessionValid($link, $_SERVER['HTTP_X_AUTHENTICATION'])) {
		fail('Unauthorized', 'Your session is no longer valid. This could be due to access from another computer or browser. To continue, please log in again', 401);
	}

	//Obtaining the file to include...
	$fileToInclude = BASE_API . parseRequestedUrl() . '.php';

	//...and then including it
	require_once ($fileToInclude);

	function fail(string $description = 'Internal Server Error', $additional_description = null, int $code = 500) {
		//Sending the headers
		header("HTTP/1.1 $code $description", true);
		header('Content-Type: application/json; charset=UTF-8', true);
		
		//Writing the error
		echo json_encode(
			array(
				'error' => true,
				'code' => $code,
				'description' => $description,
				'additional_description' => $additional_description
			)
		);
		
		//Exiting
		exit(1);
	}
	function success(string $description = 'OK', int $code = 200) {
		//Sending the headers
		header("HTTP/1.1 $code $description");
		header('Content-Type: application/json; charset=UTF-8', true);
		
		//Writing the error
		echo json_encode(
			array(
				'error' => false,
				'code' => $code,
				'description' => $description
			)
		);
		
		//Exiting
		exit(0);
	}

	function parseRequestedUrl() {
		$toRemove = dirname($_SERVER['PHP_SELF'], 2) . '/api/';
		return str_replace($toRemove, '', $_SERVER['REQUEST_URI']);
	}
?>