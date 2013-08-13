<?php
	session_start();
	include 'db.inc.php';

	switch ($_GET['action']) {
		case 'add':
			$error = array();
			$title = isset($_POST['title']) ? trim($_POST['title']) : '';
			if (empty($title)) {
				$error[] = urlencode('Please enter the title.');
			}
			$type = isset($_POST['type']) ? $_POST['type'] : '';
			if (empty($type)) {
				$error[] = urlencode('Please enter a type.');
			}
			$text = isset($_POST['text']) ? trim($_POST['text']) : '';
			if (empty($text)) {
				$error[] = urlencode('Please enter the content.');
			}
			$label = isset($_POST['label']) ? $_POST['label'] : '';
			$labels = implode(' ', $label);
			
			if (empty($label)) {
				$error[] = urlencode('Please enter the label.');
			}
			if (empty($error)) {
				$id=$redis->incr('next.post.id');
				$redis->set("post:$id:title", $title);
				$redis->set("post:$id:text", $text);
				$redis->set("post:$id:author", $_SESSION['username']);
				$redis->set("post:$id:created", date('Y-m-d H:i:s'));
				$redis->set("post:$id:status", "1");

				$redis->sadd("post:$id:type", $type);
				$redis->sadd("type:$type:object", $id);
				foreach ($label as $key => $value) {
					$redis->sadd("post:$id:label", $value);
					$redis->sadd("label:$value:object", $id);
				}
				
				$redis->lpush("submit.post", $id);
			} else {
				header('Location:contents.php?action=add&title='.$title.'&type='.$type.'&text='.$text.'&label='.$labels.' &error='.join($error, urlencode('<br />')));
			}
		break;
		case 'edit':
			$error = array();
			$id = $_POST['id'];
			$title = isset($_POST['title']) ? trim($_POST['title']) : '';
			if (empty($title)) {
				$error[] = urlencode('Please enter the title.');
			}
			$type = isset($_POST['type']) ? $_POST['type'] : '';
			if (empty($type)) {
				$error[] = urlencode('Please enter a type.');
			}
			$text = isset($_POST['text']) ? trim($_POST['text']) : '';
			if (empty($text)) {
				$error[] = urlencode('Please enter the text.');
			}
			$label = isset($_POST['label']) ? $_POST['label'] : '';
			if (empty($label)) {
				$error[] = urlencode('Please enter the label.');
			}

			if (empty($error)) {				
				$redis->set("post:$id:title", $title);
				$redis->set("post:$id:text", $text);
				$redis->set("post:$id:modified", date('Y-m-d H:i:s'));
				$redis->set("post:$id:status", "1");

				$redis->del(array("post:$id:type", "post:$id:label"));
				$redis->srem("type:$type0:object",$id);
				$redis->sadd("post:$id:type", $type);
				$redis->sadd("type:$type:object", $id);

				$label0=$redis->smembers("post:$id:label");
				foreach ($label0 as $key => $value) {
					$redis->srem("post:$value:object", $id);
				}

				foreach ($label as $key => $value) {
					$redis->sadd("post:$id:label", $value);
					$redis->sadd("label:$value:object", $id);					
				}
				
				//$redis->lpush("submit.post", $id);
				
			} else {
				header('Location:contents.php?action=edit&id=' . $id .
					'&error=' . join($error, urlencode('<br />')));
			}
		break;
		case 'saveDraft':
			$title = isset($_POST['title']) ? trim($_POST['title']) : '';
			if (empty($title)) {
				$error[] = urlencode('Please enter the title.');
			}
			$type = isset($_POST['type']) ? trim($_POST['type']) : '';
			$text = isset($_POST['text']) ? trim($_POST['text']) : '';
			$label = isset($_POST['label']) ? $_POST['label'] : array();
			/*strine to array
			$label = array();
			$labels = isset($_POST['label']) ? trim($_POST['label']) : '';
			$label = explode(' ', $labels);*/

			if($_POST['id']){
				$id = $_POST['id'];
				$redis->set("post:$id:title", $title);
				$redis->set("post:$id:text", $text);
				$redis->set("post:$id:modified", date('Y-m-d H:i:s'));
				$redis->set("post:$id:status", "0");

				$redis->del(array("post:$id:type", "post:$id:label"));
				$redis->srem("type:$type0:object",$id);
				$redis->sadd("post:$id:type", $type);
				$redis->sadd("type:$type:object", $id);

				$label0=$redis->smembers("post:$id:label");
				foreach ($label0 as $key => $value) {
					$redis->srem("post:$value:object", $id);
				}

				foreach ($label as $key => $value) {
					$redis->sadd("post:$id:label", $value);
					$redis->sadd("label:$value:object", $id);					
				}
				
			}else{	
				$id=$redis->incr('next.post.id');
				$redis->set("post:$id:title", $title);
				$redis->set("post:$id:text", $text);
				$redis->set("post:$id:author", $_SESSION['username']);
				$redis->set("post:$id:created", date('Y-m-d H:i:s'));
				$redis->set("post:$id:status", "0");

				$redis->sadd("post:$id:type", $type);
				$redis->sadd("type:$type:object", $id);
				foreach ($label as $key => $value) {
					$redis->sadd("post:$id:label", $value);
					$redis->sadd("label:$value:object", $id);
				}
				
				$redis->lpush("submit.post", $id);

			}
		break;
		
	}
	
?>

<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>Blogs</title>
	<link rel="stylesheet" type="text/css" href="css/page.css" />
	<script type="text/javascript" src="../js/jquery-1.9.1.min.js"></script>
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
	if (isset($_GET['search'])) {
		echo ' value="' . htmlspecialchars($_GET['search']) . '" ';
	}
	echo '/>';
?>
			<input type="submit" value="Search" />
			<div class="clear"></div>
		</form>
	</div>
</div>	
<div class="main">
	<div class="contents">
<p> Done!</p>
<?php
	header ('Refresh: 1; URL= admin.php');
	echo ' <p> You will be redirected to your original page request. </p> ';
    echo ' <p> If your browser doesn\'t redirect you properly ' . 
                'automatically, <a href="admin.php" >click here </a> . </p> ';
?>
	</div>
</div>
<div class="clear"></div>
</div>
</body>
</html>