<?php   
	session_start();
   if($_SESSION['username']){
        echo '<div class="logheader"><p class="welcome">Welcome back, '.$_SESSION['username'].'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="admin.php">Manage Blog</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="change.php">Profiles</a></p></div>';
?>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>Blogs</title>
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
				if (isset($_GET['search'])) {
					echo ' value="' . htmlspecialchars($_GET['search']) . '" ';
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
	$id = $_GET['id'];
	$type=implode(' ', $redis->smembers("post:$id:type"));
	$label=$redis->smembers("post:$id:label");
    $redis->lrem("submit.post", $id, 1); //delete one value which submit.post is $id

	$redis->del(array("post:$id:title", "post:$id:text", "post:$id:author", "post:$id:created", "post:$id:status", "post:$id:modified", "post:$id:type", "post:$id:label"));
	$comments = $redis->lrange("post:$id:comment", 0, -1);
	foreach ($comments as $k => $v) {
		$redis->del(array("comment:$v:name", "comment:$v:email", "comment:$v:content", "comment:$v:created"));
	}
	$redis->del("post:$id:comment");

	$redis->srem("type:$type:object", $id);
	foreach ($label as $key => $value) {
		$redis->srem("label:$value:object", $id);
	}

	
?>
	<p> Your blog has been deleted.</p>
	<?php
	header ('Refresh: 1; URL= admin.php');
	echo ' <p> You will be redirected to your original page request. </p> ';
            echo ' <p> If your browser doesn\'t redirect you properly ' . 
                'automatically, <a href="admin.php" >click here </a> . </p> ';
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