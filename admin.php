<?php
session_start();
if($_SESSION['username']){
	echo '<div class="logheader"><p class="welcome">Welcome back.'.$_SESSION['username'].'&nbsp;&nbsp;<a href="admin.php">Manage Page</a>&nbsp;&nbsp;<a href="profile.php">Profiles</a></p></div>';

?>
<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>Manage Blogs</title>
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
	<div class="contents">
		<h3><a href="contents.php?action=add">Add</a></h3>
		<table class="mytable">
			<tr>
				<th>Title</th>
				<th>Text</th>
				<th>Status</th>
				<th>Type</th>
				<th>Label</th>
				<th>Author</th>
				<th>CreateTime</th>
				<th>Option</th>
			</tr>
		<?php
			$odd = true;
			include 'db.inc.php';
			$result = $redis->lrange("submit.post", 0, -1);
			foreach ($result as $key => $value) {
				if($value !== ""){

					$title = $redis->get("post:$value:title");
					$text = $redis->get("post:$value:text");
					$status = $redis->get("post:$value:status");
					$author = $redis->get("post:$value:author");
					$created = $redis->get("post:$value:created");
					$type = implode(',', $redis->smembers("post:$value:type"));
					$label = implode(',', $redis->smembers("post:$value:label"));

					echo ($odd == true) ? '<tr class="odd_row">' : '<tr class="even_row">';
					$odd = !$odd;
					echo '<td>'.$title.'</td>';
					echo '<td>'.$text.'</td>';
					echo '<td>'.$status.'</td>';
					echo '<td>'.$type.'</td>';
					echo '<td>'.$label.'</td>';
					echo '<td>'.$author.'</td>';
					echo '<td>'.$created.'</td>';
					echo '<td><a href="contents.php?action=edit&id='.$value.'"> [edit] </a>';
					echo '<a href="delete.php?id='.$value.'"> [delete] </a></td>';
					echo '</tr>';
				}
			}
				echo '</table>';
		}else{
			echo '<p>You have not logged in. Please <a href="login.php">click here to log in</a></p>';
		}
		?>
	</div>
		<?php
			include 'foot.inc.php';
		?>
