<!DOCTYPE html>
<html class="shell">
	<head>
		<meta charset="utf-8">
		<title>Turbine Shell</title>
		<!--<link rel="stylesheet" href="../../css.php?files=docs/docs.cssp">-->
		<link rel="stylesheet" href="../../docs/docs.css">
	</head>
	<!--[if IE 6]><body class="shell" id="ie6"><![endif]-->
	<!--[if IE 7]><body class="shell" id="ie7"><![endif]-->
	<!--[if IE 8]><body class="shell" id="ie8"><![endif]-->
	<!--[if !IE]><!--><body class="shell"><!-- <![endif]-->


<div id="header">
	<h1><span>Turbine Shell</span></h1>
</div>

<?php
	include('../../lib/base.php');
	include('../../lib/browser.php');
	$browser = new Browser();
?>

<div id="wrapper" class="shellwrapper">


<div class="shellcell fl">
	<h2>Paste Turbine code here</h2>
	<textarea id="cssp">@cssp
	compress:0
	plugins:borderradius

@constants
	radius:1em

@aliases
	foo:#foo

body
	background:#FFF

$foo
	border-radius:$radius
	background:red</textarea>
</div>
<div class="shellcell fr">
	<h2>Paste HTML code here</h2>
	<textarea id="html"><p id="foo">
	Lorem Ipsum
</p></textarea>
</div>
<div class="shellcell fl">
	<h2>Resulting CSS</h2>
	<textarea id="css"></textarea>
</div>
<div class="shellcell fr">
	<h2>Resulting CSS + HTML</h2>
	<iframe id="result" src="display.html"></iframe>
</div>
<p id="browservars">
	<label>Browser: <input value="<?php echo $browser->name ?>" id="browser" name="browser" type="text"></label>
	<label>Version: <input value="<?php echo $browser->version ?>" id="browserversion" name="browserversion" class="version" type="text"></label>
	<label>Family: <input value="<?php echo $browser->family ?>" id="family" name="family" type="text"></label>
	<label>Version: <input value="<?php echo $browser->familyversion ?>" id="familyversion" name="familyversion" class="version" type="text"></label>
	<label>Engine: <input value="<?php echo $browser->engine ?>" id="engine" name="engine" type="text"></label>
	<label>Version: <input value="<?php echo $browser->engineversion ?>" id="engineversion" name="engineversion" class="version" type="text"></label>
	<label>Platform: <input value="<?php echo $browser->platform ?>" id="platform" name="platform" type="text"></label>
	<label>Version: <input value="<?php echo $browser->platformversion ?>" id="platformversion" name="platformversion" class="version" type="text"></label>
	<label>Type: <input value="<?php echo $browser->platformtype ?>" id="platformtype" name="platformtype" class="type" type="text"></label>
</p>
<p>
	<button id="go">Go!</button>
</p>


</div>

<script src="http://ajax.googleapis.com/ajax/libs/mootools/1.2.4/mootools-yui-compressed.js"></script>
<script src="shell.js"></script>

</body>
</html>