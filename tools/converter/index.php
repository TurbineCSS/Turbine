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
	<textarea cols="120" rows="20" name="cssp"><?php if(isset($_POST['css'])){ CsspConverter::factory($_POST['ichar'], $_POST['icount'], (isset($_POST['colonspace'])) ? true : false)->load_string(stripslashes($_POST['css']))->parse()->convert(); } ?></textarea>
</div>

<p>

	<label for="ichar">Indention char</label>
	<select id="ichar" name="ichar">
		<option value="tab"<?php if(!isset($_POST) || (isset($_POST['ichar']) && $_POST['ichar'] != 'space')){ echo ' selected'; } ?>>Tabs</option>
		<option value="space"<?php if(isset($_POST['ichar']) && $_POST['ichar'] == 'space'){ echo ' selected'; } ?>>Spaces</option>
	</select>

	<label for="icount">Idention level</label>
	<input type="text" name="icount" id="icount" value="<?php if(isset($_POST['icount'])){ echo $_POST['icount']; } else { echo '1'; } ?>">

	<label>
		<input type="checkbox" name="colonspace" value="1" id="colonspace" <?php if(isset($_POST['colonspace']) && $_POST['colonspace'] == '1'){ echo 'checked'; } ?>>
		Space after property colon?
	</label>

	<input type="submit" value="Convert!" id="convert">

</p>

</form>

</div>

</body>
</html>