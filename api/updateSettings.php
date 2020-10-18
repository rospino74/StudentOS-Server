<?php
	$session = $_SERVER['HTTP_X_AUTHENTICATION'];

	require_once(BASE_UTILS . 'getUserInfo.php');
	require_once(BASE_PATH . 'db.config.php');
	
	$id = getUserInfo("id", $session, $link);
	
	//Getting the current settings
	$settings = json_decode(getUserInfo("settings", $session, $link), true);
	
	//then merging it
	foreach(json_decode($_POST["entry"], true) as $k => $v) {
		if($v == null) {
			unset($settings[$k]);
		} else {
			$settings[$k] = $v;
		}
    }
	
	//storing the result
	$query = $link->prepare("UPDATE `users` SET `settings`= :settings WHERE `session` = :session");
	if($query->execute([":settings" => json_encode($settings), ":session" => $session]) == false) {
		fail('Database error', $query->errorInfo());
	} else {
		success('Settings Updated');
	}
	
?>