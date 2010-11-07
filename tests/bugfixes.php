<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<title>Bugfix plugin test</title>
		<?php
			include('../inc/turbine.php');
			turbine('../css.php', array(
				'tests/bugfixes.cssp'
			), 'html');
		?>
	</head>
	<body>
		<h1>:hover</h1>
		<p><img src="bugfixes.png"></p>
	</body>
</html>
