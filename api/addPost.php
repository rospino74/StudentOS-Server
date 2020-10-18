<?php
	if(!isset($_POST['class'])){
		fail('Classroom name not sent', null, 400);
	}
	if(!isset($_POST['title'])){
		fail('Post title not sent', null, 400);
	}
	if(!isset($_POST['text'])){
		fail('Post text not sent', null, 400);
	}

	require_once(BASE_PATH . 'db.config.php');
	require_once(BASE_UTILS . 'getClassInfo.php');
	require_once(BASE_UTILS . 'getUserInfo.php');

	$session = $_SERVER['HTTP_X_AUTHENTICATION'];
	$classroom = $_POST['class'];
	$title = str_ireplace(["<", "script", ">"], ["&lt;", "&#115;&#99;&#114;&#105;&#112;&#116;", "&gt;"], $_POST['title']);	
	$text = str_ireplace(["<", "script", ">"], ["&lt;", "&#115;&#99;&#114;&#105;&#112;&#116;", "&gt;"], $_POST['text']);
	
	//BEGIN Authentication Section
	$classroomMembersRaw = getClassInfo('members', $classroom, $link);
	if($classroomMembersRaw == false) {
		fail('Database error', null, 500);
	}

	//Parsing class members
	$classroomMembersDecoded = json_decode($classroomMembersRaw, true);
	$classroomMembersParsed = array_merge($classroomMembersDecoded['teachers'], $classroomMembersDecoded['students']);

	//checking if the user is an administrator or is a member of the classroom
	$user_id = getUserInfo('id', $session, $link);
	$user_role = getUserInfo('role', $session, $link);
	if($user_role != 0 && in_array($user_id, $classroomMembersParsed)) {
		fail('You are not a member of the classroom', 'You must be a member of this classroom or an administrator in order to see the content', 403);
	}
	if(getClassInfo('is_readonly', $classroom, $link) == 1) {
		fail('The classroom is in readonly mode', null, 409);
	}
	//END Authentication Section
	
	require_once(BASE_UTILS . 'managePost.php');

	$author_id = getUserInfo('id', $session, $link);
	$result = addPost($classroom, ['author_id' => $author_id, 'title' => $title, 'text' => $text], $link);
	
	if($result) {
		success('Posted', 201);
	} else {
		fail('Database Error', 'An error occurred while posting the comment');
	}

?>