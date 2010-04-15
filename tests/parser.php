<!doctype html>
<html>
<head>
	<meta charset="utf-8">
	<title>Parser tests</title>
	<style>
		body { font-family:monospace; font-size:14px; }
	</style>
</head>
<body>


<?php

	define('VERBOSE', false);
	error_reporting(E_ALL);
	include('../lib/base.php');
	include('../lib/parser.php');

	// Testing main function
	function test($input, $output, $subject, $title){
		$result = false;
		switch($subject){
			// Test the parsing result. Serialized output is used for comparison
			case 'parsed':
				$parser = new Parser2();
				$parser->load_string($input);
				$parser->parse();
				$result = (serialize($parser->parsed) == $output);
				if(VERBOSE){
					print_r($parser->parsed);
					echo '<br>';
					echo serialize($parser->parsed);
					echo '<br>';
				}
			break;
		}
		// Output the result
		echo $title.': ';
		echo ($result) ? '<b style="color:green">Pass</b>' : '<b style="color:red">Fail</b>';
		echo '<br>';
	}


	// Execute all the tests
	if($handle = opendir('parser')){
		while(false !== ($file = readdir($handle))){
			if($file != '.' && $file != '..'){
				include('parser/'.$file);
			}
		}
		closedir($handle);
	}

?>


</body>
</html>