<?php	
	require_once(BASE_PATH . 'db.config.php');
	require_once(BASE_UTILS . 'classesOfUser.php');
	require_once(BASE_UTILS . 'getUserInfo.php');
	require_once(BASE_UTILS . 'getClassInfo.php');
	
	if(isset($_POST['id'])) {
		$id = $_POST['id'];
	} else {
		$session = $_SERVER['HTTP_X_AUTHENTICATION'];
		$id = getUserInfo('id', $session, $link);
	}
	
	$what = explode(',', $_POST['what']);
	$out = array();
	
	foreach($what as $e) {
		switch($e) {
			case 'classrooms':
				$out['classrooms'] = classesOfUser($id, $link);
			break;
			case 'settings':
				$out['settings'] = json_decode(getUserInfo('settings', $id, $link, 'id'), true);
			break;
			default:
				$out[$e] = getUserInfo($e, $id, $link, 'id');
			break;
		}
	}
	
	//OUTPUT
	header('Content-Type: application/json; charset=UTF-8');
	echo json_encode($out);
?>