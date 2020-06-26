<?php
	header("Access-Control-Allow-Origin: *");
	header("Content-Type: application/json; charset=UTF-8");
	
	require_once("../session.php");
	//importing the db link
	require_once("../../db.config.php");
	
	//if session doesn't exist exit
	if(!isset($_POST['session'])){
		header('HTTP/1.1 400 Bad Request');
		echo json_encode(array(
			"error" => true,
			"description" => "Session parameter is missing"
		));
		exit;
	}
	
	
	if(isSessionValid($link, $_POST['session'])) {
		//if the session is valid
		header('HTTP/1.1 200 OK');
		echo json_encode(array(
			"error" => false,
			"description" => "Session is valid"
		));
	} else {
		header('HTTP/1.1 401 Unauthorized');
		echo json_encode(array(
			"error" => true,
			"description" => "Session is invalid"
		));
	}
?>