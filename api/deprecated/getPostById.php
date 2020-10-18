<?php
if(!isset($_POST['id'])){
	fail('Post id not sent', null, 400);
}
if(!isset($_POST['class'])){
	fail('Classroom name not sent', null, 400);
}

$session = $_SERVER['HTTP_X_AUTHENTICATION'];
$post_id = $_POST['id'];
$classroom = $_POST['class'];

//BEGIN Authentication Section
require_once(BASE_UTILS . 'getUserInfo.php');
require_once(BASE_UTILS . 'getClassInfo.php');

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
//END Authentication Section

$posts = $link->prepare('SELECT * FROM `' . $classroom . '` WHERE `id` = ?');
if($posts->execute([$post_id]) == false) {
	fail('Database error',  $posts->errorInfo(), 500);
}

//Fetching the data
$data = $posts->fetch(PDO::FETCH_ASSOC);
$return['id'] = $post_id;

//parsing the date
$tmp_date1 = explode(" ", $data['date']);
$tmp_date = explode("-", $tmp_date1[0]);

//Writing the result into the $return array
$return['date'] = $tmp_date[2] . '/' . $tmp_date[1] . '/' . $tmp_date[0] . " - " . $tmp_date1[1];
$return['text']['title'] = $data['title'];
$return['text']['content'] = $data['content'];
$return['author']['name'] = getUserInfo('name', $data['author_id'], $link, 'id');
$return['author']['id'] = $data['author_id'];
$return['isOwner'] = (
	getUserInfo('id', $session, $link) == $data['author_id'] || 
	getUserInfo('role', $session, $link) == 0 || 
	(getUserInfo('role', $session, $link) == 1 && getUserInfo('role', $data['author_id'], $link, "id") == 2)
) ? true : false;

//OUTPUT
header('Content-Type: application/json; charset=UTF-8');
echo json_encode($return);
?>