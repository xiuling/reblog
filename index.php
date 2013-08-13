<?php
session_start();
if($_SESSION['username']){
	echo '<div class="logheader"><p class="welcome">Welcome back, '.$_SESSION['username'].'&nbsp;&nbsp;<a href="admin.php">Manage Page</a>&nbsp;&nbsp;<a href="profile.php">Prifiles</a></p></div>';
} 
?>
<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>Blogs</title>
	<link rel="stylesheet" type="text/css" href="css/page.css" />
	
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
	<div id="nav">
		<ul>
			<li id="home"><a href="index.php">Home</a></li>
			<li id="about"><a href="about.php">About</a></li>
		</ul>
	</div>


<?php
include 'db.inc.php';

echo '<div class="main">';

$result = $redis->lrange("submit.post", 0, -1);

foreach ($result as $key => $value) {
	if($value !== ""){
		$title = $redis->get("post:$value:title");
		$text = $redis->get("post:$value:text");
		$author = $redis->get("post:$value:author");
		$created = $redis->get("post:$value:created");
		$type = implode(',', $redis->smembers("post:$value:type"));

		echo '<div class="contents">';
		echo '<h3><a href="blog.php?id='.$value.'">'.$title.'</a></h3>';
		echo '<p><span class="small">author:'.$author.'&nbsp;type:<a href="search.php?type='.$type.'">'.$type.'</a>&nbsp;created:'.$created.'</span></p>';
		echo '<div id="main">'.$text.'</div>';
		echo '</div>';
	}
}
echo '</div>';

include 'sidebar.php';
include 'foot.inc.php';
?>
