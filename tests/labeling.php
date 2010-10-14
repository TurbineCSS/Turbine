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
		<pre>@media print {
	#pleasedontwork {
		border: 1px solid blue;
	}
}
@media screen {
	#some #deep #nesting #foo {
		color: red;
	}
	#bar { /* Inherited properties from: "#some #deep #nesting #foo" */
		background: green;
		color: red;
	}
	#baz {
		font-weight: bold;
	}
	#test1 { /* Inherited properties from: "#some #deep #nesting #foo" */
		color: red;
	}
	div#very > div.complicated[asdf=asdf] + #selector {
		border-radius: 2em;
	}
	#test2 { /* Inherited properties from: "#baz", "#some #deep #nesting #foo", "div#very > div.complicated[asdf=asdf] + #selector" */
		font-weight: bold;
		color: red;
		border-radius: 2em;
	}
}</pre>
	</body>
</html>
