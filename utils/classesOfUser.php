<?php	
	function classesOfUser($id, $link) {
		$classes = $link->query('SELECT `id`, `name`, `icon`, `members` FROM `classrooms` ORDER BY `name` ASC;')->fetchAll(PDO::FETCH_ASSOC);
		$out = array();

		require_once(BASE_UTILS . 'getUserInfo.php');
		
		foreach($classes as $c){
			$json = json_decode($c['members'], true);
			
			if(getUserInfo('role', $id, $link, 'id') == 0 || in_array($id, $json['teachers']) || in_array($id, $json['students']))
				$out[] = [
					'id' => $c['id'],
					'name' => $c['name'],
					'icon' => $c['icon']
				];
		}
		
		return $out;
	}
?>