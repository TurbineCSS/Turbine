<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>CSS to Turbine converter</title>
	<!--<link rel="stylesheet" href="../../css.php?files=docs/docs.cssp">-->
	<link rel="stylesheet" href="../../docs/docs.css">
</head>
<body class="converter">

<div id="header">
	<h1><span>CSS to Turbine converter</span></h1>
</div>


<div id="wrapper" class="converterwrapper">

<?php
	require('parser.php');
	require('converter.php');
?>

<form action="index.php" method="post">
<div class="cell" id="converterIn">
	<h2>CSS</h2>
	<textarea cols="120" rows="20" name="css"><?php if(isset($_POST['css'])){echo stripslashes($_POST['css']);}?></textarea>
</div>
<div class="cell" id="converterout">
	<h2>Turbine</h2>
	<textarea cols="120" rows="20" name="cssp"><?php if(isset($_POST['css'])){ CsspConverter::factory()->load_string(stripslashes($_POST['css']))->parse()->convert(); } ?></textarea>
</div>
<p>
<input type="submit" value="Convert!" id="convert" />
</p>
</form>

</div>

</body>
</html>