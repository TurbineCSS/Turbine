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
		<div class="softoffset1">Soft shadow with offset, drop shadow filter in IE</div>
		<div class="softnooffset1">Soft shadow without offset, glow filter in IE</div>
		<div class="hardoffset1">Hard shadow with offset, shadow filter in IE</div>
		<div class="hardnooffset1">Hard shadow without offset, deactivating filters in IE</div>
		<br style="clear:both">
		<div class="rgb">RGB</div>
		<div class="rgba">RGBA</div>
		<div class="hsl">HSL</div>
		<div class="hsla">HSLA</div>
	</body>
</html>
