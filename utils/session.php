<?php
	function buildSession($link, $user_id) {
		destroySession();
		
		//generatng a new session id
		$newSID = sha1('StudentOS - ' . $user_id . "-" . strtotime("now") . "-" . random_bytes(128));
		session_id($newSID);
		
		//updating database entry
		$query = $link->prepare("UPDATE `users` SET `session` = :session, `session_expiration`=(CURRENT_TIMESTAMP() + interval 12 hour) WHERE `id` = :id")->execute([":session" => $newSID, ":id" => $user_id]);

		//returning false if the query failed
		if ($query == false) {
			return false;
		}
		
		//then starting the session and updating the cookies
		session_start();
		setcookie("session", $newSID, 0, "/");
        setcookie("logged_in", 1, 0, "/");
		
		return true;
	}
	
	function isSessionValid($link, $session) {
		
		//if session is not specified return false
		if(!isset($session))
			return false;
		
		$query = $link->prepare("SELECT COUNT(`id`) as 'count' FROM `users` WHERE `session` = :session AND `session_expiration` > CURRENT_TIMESTAMP() LIMIT 1;");
		
		//if an error occurred return false
		if($query->execute([":session" => $session]) == false) {
			return false;
		}
		
		//if session is not registered in the db return false
		$result = $query->fetch(PDO::FETCH_ASSOC);
		if($result['count'] != 1) {
			return false;
		}
		
		//else
		return true;
	}
	
	function destroySession() {
		//destroying the old session and deleting the old cookies if the session exist
		if(session_status() == PHP_SESSION_ACTIVE) {
			session_destroy();
			setcookie("logged_in", 0, time() - 36000);
			setcookie("session", 0, time() - 36000);
			return true;
		} else {
			return false;
		}
	}
?>
