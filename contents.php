<?php
	session_start();
    if($_SESSION['username']){
        echo '<div class="logheader"><p class="Welcome">Welcome back, '.$_SESSION['username'].'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="admin.php">Manage Blog</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="profile.php">Profiles</a></p></div>';
?>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>Manage Blogs</title>
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

<?php
	require 'db.inc.php';

	if ($_GET['action'] == 'edit') {
		$id = $_GET['id'];
		$title = $redis->get("post:$id:title");
		$text = $redis->get("post:$id:text");
		$type = implode(' ', $redis->smembers("post:$id:type"));
		$label = $redis->smembers("post:$id:label");
	} else {
		$title = isset($_GET['title']) ? trim($_GET['title']) : '';
		$type = isset($_GET['type']) ? trim($_GET['type']) : '';
		$label = isset($_GET['label']) ?  explode(' ', $_GET['label']) : array();
		$text = isset($_GET['text']) ? trim($_GET['text']) : '';
	}

	if (isset($_GET['error']) && $_GET['error'] != '') {
		echo ' <div id="error"> ' . $_GET['error'] . ' </div> ';
	}
?>
<div class="contents">
	<h2><?php echo ucfirst($_GET['action']); ?> Blog Content</h2>
	<p><a href="category.php">Add Types or Labels</a>
	<form action="commit.php?action=<?php echo $_GET['action']; ?>" method="post" id="form1">
		<table class="left">
			<tr>
				<td> Title </td>
				<td><input type="text" name="title" value="<?php echo $title; ?>" class="long" /> </td>
			</tr>
			<tr>
				<td> Type </td>
				<td><select name="type">
					<option value="">select one type</option>
			<?php
				$types = $redis->smembers('type');
				
				foreach ($types as $type0) {				
					if ($type0 == $type) {
							echo ' <option value="' . $type0 . '" selected="selected"> ';
					} else {
						echo ' <option value="' . $type0 . '" > ';				
					}
					echo $type0 . ' </option> ';
				}
			?>
				</td>
			</tr> 
			 <tr>
				<td> Label </td>
				<td>
				<?php
				$labels  = $redis->smembers('label');
				
				foreach ($labels as $label0) {
					// [string/...] in array
					if(in_array($label0, $label)){
						echo '<input type="checkbox" name="label[]" value="'.$label0.'" checked="checked" />'.$label0.'&nbsp;';
					} else{
						echo '<input type="checkbox" name="label[]" value="'.$label0.'" />'.$label0.'&nbsp;';
					}							
				}
				
			?> </td>
			</tr>  
			<tr>
				<td> Content </td>
				<td><textarea name="text" cols="50" rows="20"><?php echo $text;?></textarea></td>
			</tr>
			
			<tr>
				<td colspan="2">
			<?php
				if ($_GET['action'] == 'edit') {
					echo '<input type="hidden" value="' . $id . '" name="id" />';
				}
			?>
				<input type="submit" name="submit" value="<?php echo ucfirst($_GET['action']); ?>" />&nbsp;&nbsp;&nbsp;
                <input type="reset" value="Reset" />&nbsp;&nbsp;&nbsp;
                <input type="button" id="saveDraft" value="Save Draft" />
				</td>
			</tr>
		</table>
	</form>
	<script type="text/javascript">
		jQuery(window).load(function(){
		});

		jQuery(function(){ 
			jQuery('#saveDraft').click(function(){
				jQuery.ajax({
					type:'POST',
					url:'commit.php?action=saveDraft',
					dataType:'json',
					data:jQuery("#form1").serialize(),
					success: function(data){
					}
				});
			});
		});
	</script>	

<?php
	}else{
    	header ('Refresh: 1; URL= login.php');
		echo ' <p> You have not logged in. You will be redirected to login page. </p> ';
            echo ' <p> If your browser doesn\'t redirect you properly ' . 
                'automatically, <a href="login.php" >click here </a> . </p> ';
    }
?>

	</div>
	<div class="clear"></div>
	<div id="footer">&copy; 2013</div>

</div>
</body>
</html>
