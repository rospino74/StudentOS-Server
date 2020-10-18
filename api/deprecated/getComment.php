<?php
if(!isset($_POST['class'])){
	fail('Classroom name not sent', null, 400);
}

$session = $_SERVER['HTTP_X_AUTHENTICATION'];
$classroom = $_POST['class'];

//BEGIN Authentication Section
require_once(BASE_UTILS . 'getUserInfo.php');
require_once(BASE_UTILS . 'getClassInfo.php');

$classroomMembersRaw = getClassInfo('members', $classroom, $link);
if($classroomMembersRaw == false) {
	fail('Database error');
}

//Parsing class members
$classroomMembersDecoded = json_decode($classroomMembersRaw, true);
$classroomMembersParsed = array_merge($classroomMembersDecoded['teachers'], $classroomMembersDecoded['students']);

//checking if the user is an administrator or is a member of the classroom
$user_id = getUserInfo('id', $session, $link);
$user_role = getUserInfo('role', $session, $link);
if($user_role != 0 && in_array($user_id, $classroomMembersParsed)) {
	fail('You are not a member of the classroom', null, 403);
}
//END Authentication Section

$comments = $link->prepare('SELECT * FROM `comments-' . $classroom . '` ORDER BY `date` DESC;');
if($comments->execute() == false) {
	fail('Database error', $comments->errorInfo());
}

$return = array();
while($data = $comments->fetch(PDO::FETCH_ASSOC)) {
 
	$id = $data['id'];
	
	$tmp_date1 = explode(" ", $data['date']);
	$tmp_date = explode("-", $tmp_date1[0]);
	$date = $tmp_date[2] . '/' . $tmp_date[1] . '/' . $tmp_date[0] . " - " . $tmp_date1[1];
	
	$text = $data['content'];
	
	//author details
	$author['name'] = getUserInfo('name', $data['author_id'], $link, 'id');
	
	//if the author doesn't exist, skip the comment
	if($author['name'] == null)
		continue;
	
	$author['id'] = $data['author_id'];
	
	$isOwner = (
		getUserInfo('id', $session, $link) == $data['author_id'] || 
		getUserInfo('role', $session, $link) == 0 || 
		(getUserInfo('role', $session, $link) == 1 && getUserInfo('role', $data['author_id'], $link, "id") == 2)
	) ? true : false;

	$return[] = ["id" => $id, "parent_id" => $data['parent_id'], "date" => $date, "text" => $text, "author" => $author, "isOwner" => $isOwner];
 
};

//OUTPUT
header('Content-Type: application/json');
echo json_encode($return);

?>