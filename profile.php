<?php
	session_start();
    if($_SESSION['username']){
        echo '<div class="logheader"><p class="Welcome">Welcome back, '.$_SESSION['username'].'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="admin.php">Manage Blog</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="profile.php">Profiles</a></p></div>';
?>
<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>Profiles</title>
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


<?php
    include 'db.inc.php';

    if (isset($_GET['error']) && $_GET['error'] != '') {
		echo ' <div id="error"> ' . $_GET['error'] . ' </div> ';
	}
?>
<div class="contents" >
	<h3>OPTIONS</h3>
	
	<div class="about option">
		<h5>Change About</h5>
	<?php
		$title = $redis->get("about:1:title");
		$text = $redis->get("about:1:text");
		$created = $redis->get("about:1:created");
		$modified = $redis->get("about:1:modified");
		
		if($title !== NULL){
			echo '<form action="update.php?action=changeabout&id=1" method="post"> ';
			echo '<table>';
			echo '<tr><td>Title</td><td><input type="text" name="title" value="'.$title.'" size="30" /></td></tr>';
			echo '<tr><td>Content</td><td><textarea name="text" rows="25" cols="34.5">'. $text .'</textarea></td></tr>';
			echo '</table>';
		}
		else{
			echo '<form action="update.php?action=changeabout" method="post">';
			echo '<table>';
			echo '<tr><td>Title</td><td><input type="text" name="title" class="long" /></td></tr>';
			echo '<tr><td>Update About</td><td><textarea name="text" ></textarea></td></tr>';
			echo '</table>';
		}
	?>
			<div class="button aboutbutton"><input type="submit" value="Update" /><input type="reset" value="Reset" /></div>
		</form>
	</div>
	<div class="changePass option">
		<h5>Change Password</h5>
		<form action="update.php?action=changepass" method="post">
			<table>
			<tr>
				<td>Old Password:</td>
				<td><input type="password" name="oldPass" size="20" /></td>
			</tr>
			<tr>
				<td> New Password:</td>
				<td><input type="password" name="newPass1" size="20" /></td>
			</tr>
			<tr>
				<td> New Again:</td>
				<td><input type="password" name="newPass2" size="20" /></td>
			</tr>
			<tr>
				<td><input type="submit" value="Change" /></td>
				<td><input type="reset" value="Reset" /></td>
			</tr>
			</table>
		</form>
	</div>	
	<?php
		if ($_SESSION['authorId'] ==2){
	?>
	<div class="adduser option">
		<h5>Add User</h5>
		<form action="update.php?action=adduser" method="post"> 
			<table>
			<tr>
				<td>Username</td>
				<td><input type="text" name="username" size="20" /></td>
			</tr>
			<tr>
				<td>Password</td>
				<td><input type="password" name="password" size="20" /></td>
			</tr>
			<tr>
				<td>Role</td>
				<td>
					<select name="authorId">
						<option value="2">Administrator</option>
						<option value="1">Editor</option>
					</select>
				</td>
			</tr>
			<tr>
				<td><input type="submit" value="Add User" /></td>
				<td><input type="reset" value="Reset" /></td>
			</tr>
			</table>
		</form>
	</div>

<?php
		}
	}else{
    	header ('Refresh: 1; URL= login.php');
		echo ' <p> You have not logged in. You will be redirected to login page. </p> ';
        echo ' <p> If your browser doesn\'t redirect you properly ' . 
                'automatically, <a href="login.php" >click here </a> . </p> ';
    }
?>
	<div class="clear"></div>
</div>
	
	<div id="footer">&copy; 2013</div>
</div>

</body>
</html>