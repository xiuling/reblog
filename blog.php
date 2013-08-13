<?php
session_start();
if($_SESSION['username']){
	echo '<div class="logheader"><p class="welcome">Welcome back, '.$_SESSION['username'].'&nbsp;&nbsp;<a href="admin.php">Manage Page</a>&nbsp;&nbsp;<a href="profile.php">Prifiles</a></p></div>';
} 
?>

<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>Blog</title>
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

$name = (isset($_GET['name'])) ? trim($_GET['name']) : '';
$email = (isset($_GET['email'])) ? trim($_GET['email']) : '';
$content = (isset($_GET['content'])) ? trim($_GET['content']) : '';

$id = $_GET['id'];
$title = $redis->get("post:$id:title");
$text = $redis->get("post:$id:text");
$author = $redis->get("post:$id:author");
$created = $redis->get("post:$id:created");
$type = implode(' ', $redis->smembers("post:$id:type"));
//$label = $redis->smembers("post:$id:label");

?>

<div class="main">	
	<div class="contents">
		<h3><a href="blog.php?id=<?php echo $id; ?>"><?php echo $title; ?></a></h3>
		<p class="small">Author：<?php echo $author ;?> &nbsp;type：<?php echo $type; ?> Created：<?php echo $created; ?></p>
		<div><?php echo $text; ?></div>
	</div>
	
	<div class="comments">
	<?php 
		$result = $redis->lrange("post:$id:comment", 0, -1);

		if($result){
			echo '<h3>comments here:</h3>';
			foreach ($result as $key => $value) {
				$name = $redis->get("comment:$value:name");
				$email = $redis->get("comment:$value:email");
				$created = $redis->get("comment:$value:created");
				$content = $redis->get("comment:$value:content");
				echo '<div class="eachComment" style="margin-bottom: 10px;">';
	                echo '<p><span class="small">author:&nbsp;' . $name .'&nbsp;&nbsp; mail:&nbsp;' . $email . '&nbsp;&nbsp; created:&nbsp;' . $created . '</span></p>';
	                echo ' <div> ' . $content . ' </div> </div>';				
			}
		}else{
			echo '<p>There is no comments yet.</p>';
			echo '<p>You can give a comment.</p>';
		}

	?>
	</div>
	<?php
		if(isset($_GET['error']) && $_GET['error']!== ''){
			echo '<div id="error">'.$_GET['error'].'</div>';
		}
	?>

	<div class="addComments">
		<form action="postcomment.php" method="post">
			<input type="hidden" name="id" value="<?php echo $id; ?>" />
			<table>
				<tr>
				 	<td>Name</td>
				 	<td><input type="text" name="name" size="30" value="<?php echo $name; ?>" /></td>
				</tr>
				<tr>
				 	<td>Email</td>
				 	<td><input type="text" name="email" size="30" value="<?php echo $email; ?>" /></td>
				</tr>
				<tr>
				 	<td>Content</td>
				 	<td><textarea name="content" rows="20" cols="50"><?php echo $content; ?></textarea></td>
				</tr>
				<tr>
				 	<td cols="2"><input type="submit" value="Submit Comment" /></td>
				</tr>
			</table>
		</form>
	</div>
</div>

<?php 
include 'sidebar.php';
include 'foot.inc.php';
?>
