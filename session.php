<?php
	function buildSession() {
		//destroying the old session and deleting the old cookies if the session exist
		if(session_status() == PHP_SESSION_ACTIVE) {
			session_destroy();
			setcookie("logged_in", 0, time() - 36000);
			setcookie("session", 0, time() - 36000);
		}
		
		//generatng a new session id
		$newSID = md5(strtotime("now") . "-" . rand());
		session_id($newSID);
		
		//then starting it
		session_start();
	}
	
	function isSessionValid($link, $session) {
		
		//if session is not specified return false
		if(!isset($session))
			return false;
		
		$query = $link->prepare("SELECT COUNT(`id`) as 'count', `session_expiration` AS 'expiration' FROM `users` WHERE `session` = :session");
		//if an error occurs return false
		if($query->execute([":session" => $session]) == false)
			return false;
		
		//if session is not registered in the db return false
		$result = $query->fetch(PDO::FETCH_ASSOC);
		if($result['count'] != 1 || $result['expiration'] < time())
			return false;
		
		//else
		return true;
	}
?>
