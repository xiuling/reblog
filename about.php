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
	 ?>
	<div class="main">
		<div class="contents"> 
			     
		<?php
			$name = (isset($_GET['name'])) ? trim($_GET['name']) : '';
		    $email = (isset($_GET['email'])) ? trim($_GET['email']) : '';
		    $content = (isset($_GET['content'])) ? $_GET['content'] : '';
		   
			$title = $redis->get("about:1:title");
			$text = $redis->get("about:1:text");
			$author = $redis->get("about:1:author");
			$created = $redis->get("about:1:created");
			$modified = $redis->get("about:1:modified");
				
		
				echo '<h3><a href="about.php"> '.$title.'</a></h3>';

			    if($modified !== NULL){		        	
			        echo ' <p><span class="small"> author:&nbsp;' . $author . '&nbsp;created:&nbsp;' 
			        . $created . '&nbsp;&nbsp;modified:&nbsp;' . $modified . '</span></p>';
			    } else{
			        echo ' <p><span class="small"> author:&nbsp;' . $author . ' created:&nbsp;' 
			        . $created . '</span></p>';
			    }
			    echo ' <div> ' . $text . ' </div> ';
						
			echo '</div>';

			echo ' <div class="comments"> ';
			$result = $redis->lrange("about:1:comment", 0, -1);
	    	if($result !== NULL){
       			echo '<h3>Commemts Here:</h3>'; 
            	foreach ($result as $key => $value) {
            		$name = $redis->get("aboutcomment:1:name");
					$email = $redis->get("aboutcomment:1:email");
					$created = $redis->get("aboutcomment:1:created");
					$content = $redis->get("aboutcomment:1:content");
            		echo '<div class="eachComment" style="margin-bottom: 10px;">';
	                echo '<p><span class="small">author:&nbsp;' . $name .'&nbsp;&nbsp; mail:&nbsp;' . $email . '&nbsp;&nbsp; created:&nbsp;' . $created . '</span></p>';
	                echo ' <div> ' . $content . ' </div> </div>';
                }            
	    	}else{        
		        echo '<p>There is no comments yet.</p>';
		        echo '<p>You can give a conmments.</p>';
		    }
	    	echo '</div>';
		    

		    if (isset($_GET['error']) && $_GET['error'] != '') {
		        echo ' <div id="error"> ' . $_GET['error'] . ' </div> ';
		    }
?>
    
    <div class="addComments">
	    <form action="postcomment.php?type=about" method="post">
	        <table>
				<tr>
					<td>Name:</td>
					<td><input type="text" name="name" value="<?php echo $name; ?>" size="30" /></td>
				</tr>
				<tr>
					<td>Email:</td>
					<td><input type="text" name="email" value="<?php echo $email; ?>" size="30" /></td>
				</tr>
				<tr>
					<td>Content:</td>
					<td><textarea name="content" rows="20" cols="50"><?php echo $content; ?></textarea></td>
				</tr>
	       		<tr>
					<td><input type="submit" value="Post" /></td>
					<td><input type="reset" value="Reset" /></td>
				</tr>
			</table>
	    </form>
    </div>
</div>

<?php    
    include 'sidebar.php';
    include 'foot.inc.php';
?>
