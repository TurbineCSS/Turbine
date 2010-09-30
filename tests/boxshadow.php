<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<title>Box shadow test</title>
		<?php
			include('../inc/turbine.php');
			turbine('../css.php', array(
				'tests/boxshadow.cssp'
			), 'html');
		?>
	</head>
	<body>
		<div id="softoffset">Soft shadow with offset, drop shadow filter in IE</div>
		<div id="softnooffset">Soft shadow without offset, glow filter in IE</div>
		<div id="hardoffset">Hard shadow with offset, shadow filter in IE</div>
		<div id="hardnooffset">Hard shadow without offset, deactivating filters in IE</div>
	</body>
</html>
