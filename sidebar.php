<div id="sidebar">
	<div class="category side">
		<h4>Category</h4>
		<?php 
			include 'db.inc.php';
			$types = $redis->smembers('type');
			foreach ($types as $type) {
				echo '<p><a href="search.php?type='.$type.'">'.$type.'</a></p>';
			}

		?>
	</div>
	<div class="label side">
		<h4>Label</h4>
		<?php 
			$labels = $redis->smembers('label');
			foreach ($labels as $label) {
				echo '<p><a href="search.php?label='.$label.'">'.$label.'</a></p>';
			}
		?>
	</div>
	<div class="function side">
		<h4>Function</h4>
		<p><a href="login.php">Log in</a></p>
		<p><a href="logout.php">Log out</a></p>
	</div>
</div>