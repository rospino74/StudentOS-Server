<?php
	header("Access-Control-Allow-Origin: *");
	header("Content-Type: application/json; charset=UTF-8");
	
	require_once("../../db.config.php");
	require_once("../classesOfUser.php");
	require_once("../getUserInfo.php");
	require_once("../getClassInfo.php");
	
	if(!isset($_POST['session'])){
		header('HTTP/1.1 400 Bad Request');
		exit;
	}		
	require_once("../session.php");

	//BEGIN AUTHENTICATION BLOCK
	if(!isSessionValid($link, $_POST['session'])) {
		header('HTTP/1.1 401 Unauthorized');
	}
	//END AUTHENTICATION BLOCK
	
	if(isset($_POST["username"])) {
		$id = getUserInfo("id", $_POST["username"], $link, "username");
	} else {
		$id = getUserInfo("id", $_POST['session'], $link);
	}
	
	$what = explode(",", $_POST['what']);
	$out = array();
	
	foreach($what as $e) {
		if($e == "classrooms" && !isset($_POST["username"])) {
			$out["classrooms"] = classesOfUser($id, $link);
			continue;
		} else if($e == "settings" && !isset($_POST["username"])) {
			$out["settings"] = json_decode(getUserInfo("settings", $id, $link, "id"), true);
			continue;
		}
		
		$out[$e] = getUserInfo($e, $id, $link, "id");
	}
	
	echo json_encode($out);
?>