<?php
    // include 'adminheader.inc.php';
    session_start();
    if($_SESSION['username']){
        echo '<div class="logheader">Welcome back, '.$_SESSION['username'].'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="admin.php">Manage Blog</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="change.php">Profiles</a></div>';
?>
<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Update</title>
    <link rel="stylesheet" type="text/css" href="css/page.css" />
    <script type="text/javascript" src="js/jquery-1.9.1.min.js"></script>
</head>
<body>
    <div id="wrap">
	<div id="head">
		<div id="banner"><h1><a href="index.php">Blogs</a></h1></div>
		<div id="search">
			<form method="get" action="search.php">
				<label for="search">Search</label>
				<?php
					echo '<input type="text" name="search" ';
					if(isset($_GET['search'])){
						echo 'value="' . htmlspecialchars($_GET['search']) . '"';
					}
					echo '/>';
				?>
				<input type="submit" value="Search" />
			</form>
		</div>
        <div class="clear"></div>
	</div>
	<div class="contents">

<?php
	include 'db.inc.php';

	switch ($_GET['action']) {
		case 'changepass':
			$username = $_SESSION['username'];
			$uid = $redis->get("user:$username:uid");

		    if ($uid) {
		        $password = $redis->get("user:$uid:password");
		    }

			$error = array();
			$oldPass = isset($_POST['oldPass']) ? trim($_POST['oldPass']) : '';
			if (empty($oldPass)) {
				$error[] = urlencode('Please enter the oldPass.');
			}
			if($oldPass!==$password){
				$error[] = urlencode('Old password is wrong.');
			}
			$newPass1 = isset($_POST['newPass1']) ? trim($_POST['newPass1']) : '';
			if (empty($newPass1)) {
				$error[] = urlencode('Please enter the newPass.');
			}
			$newPass2 = isset($_POST['newPass2']) ? trim($_POST['newPass2']) : '';
			if (empty($newPass2)) {
				$error[] = urlencode('Please enter the newPass again.');
			}
			if($newPass1!==$newPass2){
				$error[] = urlencode('The two new password are not the same.');
			}

			if(empty($error)){
				$result = $redis->set("user:$uid:password", $newPass2);
				if($result){
					echo 'Password has been changed.';
					header ('Refresh: 1; URL= profile.php');
				}
			}else {
				header('Location:profile.php?action=changepass'.' 
					&error='.join($error, urlencode('<br />')));
			}
			break;
		case 'changeabout':
			$title = isset($_POST['title']) ? trim($_POST['title']) : '';
			if (empty($title)) {
				$error[] = urlencode('Please enter the title.');
			}
			$text = isset($_POST['text']) ? trim($_POST['text']) : '';
			if (empty($text)) {
				$error[] = urlencode('Please enter the text.');
			}
			if(empty($error)){
		
				if($_GET['id']){
					$redis->set("about:1:title", $title);
					$redis->set("about:1:text", $text);
					$redis->set("about:1:author", $_SESSION['username']);
					$redis->set("about:1:modified", date('Y-m-d H:i:s'));

					header ('Refresh: 0; URL= about.php');					
				}else{
					$redis->set("about:1:title", $title);
					$redis->set("about:1:text", $text);
					$redis->set("about:1:author", $_SESSION['username']);
					$redis->set("about:1:created", date('Y-m-d H:i:s'));

					header ('Refresh: 0; URL= about.php');
				}
				
			}else {
				header('Location:profile.php?action=changeabout'.' 
					&error='.join($error, urlencode('<br />')));
			}
			break;
		case 'adduser':
			$error = array();
			$username = isset($_POST['username']) ? trim($_POST['username']) : '';
			if (empty($username)) {
				$error[] = urlencode('Please enter the username.');
			}
			$password = isset($_POST['password']) ? trim($_POST['password']) : '';
			if (empty($password)) {
				$error[] = urlencode('Please enter the password.');
			}
			$authorId = isset($_POST['authorId']) ? trim($_POST['authorId']) : '';
			if(empty($error)){
				$id=$redis->incr('next.user.id');
				$redis->set("user:$id:username", $username);
				$redis->set("user:$id:password", $password);
				$redis->set("user:$id:authorId", $authorId);
				$redis->set("user:$username:uid", $id);

				echo 'The New User has been added.';
				header ('Refresh: 1; URL= profile.php');
			}
			break;
		case 'addtype':
		 	$error = array();
			$type = isset($_POST['type']) ? trim($_POST['type']) : '';
			if (empty($type)) {
				$error[] = urlencode('Please enter the type name.');
			}
			if(empty($error)){
				include 'db.inc.php';
				$result = $redis->sadd('type', $type);
				if($result){
						//echo 'The New type has been added.';
						header ('Refresh: 0; URL= category.php');
				}
			}
		 	break; 
		case 'addlabel':
		 	$error = array();
			$label = isset($_POST['label']) ? trim($_POST['label']) : '';
			if (empty($label)) {
				$error[] = urlencode('Please enter the label name.');
			}
			if(empty($error)){
				include 'db.inc.php';
				$result = $redis->sadd('label', $label);
				if($result){
						//echo 'The New label has been added.';
						header ('Refresh: 0; URL= category.php');
				}
			}
		 	break; 
	}
?>
<?php

    }else{
        header ('Refresh: 1; URL= login.php');
        echo ' <p> You have not logged in. You will be redirected to login page. </p> ';
            echo ' <p> If your browser doesn\'t redirect you properly ' . 
                'automatically, <a href="login.php" >click here </a> . </p> ';
    }
?>

    </div>
<?php 
include 'foot.inc.php';
?>