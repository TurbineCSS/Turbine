<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<title>Box shadow test</title>
		<?php
			include('../inc/turbine.php');
			turbine('../css.php', array(
				'tests/labeling.cssp'
			), 'html');
		?>
	</head>
	<body>
		<h1>Expected output</h1>
		<pre>#foo {
	color: red;
}
#bar { /* Inherited properties from: "#foo" */
	background: green;
	color: red;
}
#text { /* Inherited properties from: "#foo" */
	color: red;
}
</pre>
	</body>
</html>
