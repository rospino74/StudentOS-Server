<?php
    if(!isset($_FILES['newIcon'])){
		fail('New icon not sent', null, 400);
	}	

    $session = $_SERVER['HTTP_X_AUTHENTICATION'];

    require_once(BASE_UTILS . 'getUserInfo.php');
    require_once(BASE_PATH . 'db.config.php');

    //file path
    $id = getUserInfo('id', $session, $link);
    $f = BASE_PATH . 'Server/uploads/' . $id . '_' . basename($_FILES['newIcon']['name']);
    
    //saving the file
    if (!move_uploaded_file($_FILES['newIcon']['tmp_name'], $f)) {
        fail('Upload error');
    }

    //getting the content and the extension
    $fc = file_get_contents($f);
    $mime = $_FILES['newIcon']['type'];

    //exiting if the mime is not supported
    $whitelist = array('image/png', 'image/jpeg', 'image/gif', 'image/webp', 'image/svg+xml');
    if(array_search($mime, $whitelist) === false){
		fail('Wrong mime type', 'Only the following mime types are allowed: image/png, image/jpeg, image/gif, image/webp, image/svg+xml', 400);
    }

    //building the image string
    $b64 = "data:" . $mime  . ";base64," . base64_encode($fc);

    //updating the icon in the db
    $query = $link->prepare("UPDATE `users` SET `icon`= :icon WHERE `session` = :session");
	if(!$query->execute([":icon" => $b64, ":session" => $session])) {
        fail('Database error', $query->errorInfo());
    }
    
    //outputting the result
    header('HTTP/1.1 201 Created');
    echo json_encode(array("newIcon" => $b64));

    //deleting the file
    unlink($f);
?>