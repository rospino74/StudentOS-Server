<?php
	function getUserInfo( $what, $session , $link, $byIdOrUsername = false) {
		
		$whitelist = array('name', 'username', 'icon', 'description', 'role', 'email', 'id', 'settings');
		$idUserWhitelist = array('username', 'id');

		if(array_search($what, $whitelist) === false && array_search($byIdOrUsername, $idUserWhitelist) === false)
			return false;
		
		if($byIdOrUsername != false) {
			$query = $link->prepare("SELECT `$what` AS 'return' FROM `users` WHERE `$byIdOrUsername` = ? LIMIT 1;");
		} else {	
			$query = $link->prepare("SELECT `$what` AS 'return' FROM `users` WHERE `session` = ? LIMIT 1;");
		}
		
		if($query->execute([$session]) == false)
			return false;
		
		$result = $query->fetch(PDO::FETCH_ASSOC);
		
		return $result['return'];

	}
?>