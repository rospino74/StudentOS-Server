<?php	
	function classesOfUser($id, $link) {
		$classes = $link->query("SELECT `id`, `name`, `icon`, `members` FROM `classrooms` ORDER BY `name` ASC;")->fetchAll(PDO::FETCH_ASSOC);
		
		$out = array();
		
		foreach($classes as $c){
			$json = json_decode($c["members"], true);
			
			if(in_array($id, $json["teachers"]) || in_array($id, $json["students"]))
				$out[] = array("id" => $c["id"], "name" => $c["name"], "icon" => $c["icon"]);
		}
		
		return $out;
	}
?>