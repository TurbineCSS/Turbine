<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>CSS to CSSP Converter</title>
	<style type="text/css">
		html, body, table { margin:0; padding:0; height:100%; }
		textarea { display:block; }
	</style>
</head>
<body>

<?php
	require('parser.php');
	require('converter.php');
?>

<form action="index.php" method="post">
<table>
	<tr>
		<td valign="top">
			<h2>CSS</h2>
<textarea cols="120" rows="50" name="css"><?php if(isset($_POST['css'])){
	echo stripslashes($_POST['css']);
}?></textarea>
		</td>
		<td align="center"><input type="submit" value="&nbsp;&rarr;&nbsp;"></input></td>
		<td valign="top">
			<h2>CSSP</h2>
<textarea cols="120" rows="50" name="cssp">
<?php if(isset($_POST['css'])){ CsspConverter::factory()->load_string(stripslashes($_POST['css']))->parse()->convert(); } ?>
</textarea>
		</td>
	</tr>
</table>
</form>

</body>
</html>