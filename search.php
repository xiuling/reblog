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
	<link rel="stylesheet" type="text/css" href="css/page.css">		
</head>
<body id="intro">
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
	<div class="main">

<?php
	require 'db.inc.php';

	if($search = (isset($_GET['search'])) ? $_GET['search'] : ''){
		
		//$keys = $redis->keys("*text*");
		header('Refresh: 0; URL= index.php');
	
	}
	if($type = (isset($_GET['type'])) ? $_GET['type'] : ''){		
		$result = $redis->smembers("type:$type:object");
    	
		if ($result == NULL) {
			echo '<div class="contents"><strong>No articles found that match the search terms.</strong></div>';
		} else {
			foreach($result as $key => $value){
				$title = $redis->get("post:$value:title"); 
				$author = $redis->get("post:$value:author"); 
				//$type = $redis->get("post:$value:type"); 
				$text = $redis->get("post:$value:text"); 
				$created = $redis->get("post:$value:created"); 

				echo ' <div class="contents"> ';
	            echo ' <h3><a href="blog.php?id='.$value.'"> ' . $title . '</a></h3>';
	            echo '<p><span class="small">author:'.$author.'type:<a href="search.php?type='.$type.'">' . $type . '</a>&nbsp;&nbsp; created:' . $created .'</span></p>';
	            echo ' <div> ' . $text . '</div>' ;
	            echo '</div> ';
			}
		}
	}
	if($label = (isset($_GET['label'])) ? $_GET['label'] : ''){		
		$result = $redis->smembers("label:$label:object");
    	
		if ($result == NULL) {
			echo '<div class="contents"><strong>No articles found that match the search terms.</strong></div>';
		} else {
			foreach($result as $key => $value){
				$title = $redis->get("post:$value:title"); 
				$type = $redis->get("post:$value:type"); 
				$text = $redis->get("post:$value:text"); 
				$created = $redis->get("post:$value:created"); 

				echo ' <div class="contents"> ';
	            echo ' <h3><a href="blog.php?id='.$value.'"> ' . $title . '</a></h3>';
	            echo '<p><span class="small">type:<a href="search.php?type='.$type.'">' . $type . '</a>&nbsp;&nbsp; created:' . $created .'</span></p>';
	            echo ' <p> ' . $text . '</p>' ;
	            echo '</div> ';
			}
		}
	}
	echo '</div>';
	include 'sidebar.php';
	include 'foot.inc.php';
?>