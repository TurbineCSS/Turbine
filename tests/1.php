<?php ini_set('error_reporting', E_ALL); error_reporting(E_ALL); ?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<script src="http://ajax.googleapis.com/ajax/libs/mootools/1.2.4/mootools-yui-compressed.js"></script>
	<script>
		window.addEvent('domready', function(){
			// Char used for indention
			var indention_char = "\t";
			// List of regexps
			var rex = {
				comment: /(.*?)(\/\/(?:.*?))$/
			};
			// Get a line's indention level
			function get_indention_level(line){
				var level = 1;
				if(line){
					if(line.substr(0, indention_char.length) == indention_char){
						level = level + get_indention_level(line.substr(indention_char.length));
					}
				}
				return level;
			}
			// Highlight code areas
			var areas = $$('.highlight');
			areas.each(function(area){
				var newlines = [];
				var lines = area.get('text').split("\n");
				var num = lines.length;
				for(var i = 0; i < num; i++){
					var line = lines[i];
					var nextline = lines[i + 1];
					// @rules
					if(line.substr(0, 6) == '@media'){
						line = '<span class="at media">' + line + '</span>';
					}
					else if(line.substr(0, 7) == '@import'){
						line = '<span class="at import">' + line + '</span>';
					}
					// Selector style line?
					if(get_indention_level(line) + 1 == get_indention_level(nextline)){
						line = line.replace(/,/g, '<span class="ch">,</span>');
						line = '<span class="se">' + line + '</span>';
					}
					else{
						line = line.replace(/:/g, '<span class="ch">:</span>');
						line = line.replace(/;/g, '<span class="ch">;</span>');
					}
					// Strings
					// TODO
					// Comments
					var matches = rex.comment.exec(line);
					if(matches && matches.length == 3){
						line = matches[1] + '<span class="co">' + matches[2] + '</span>';
					}
					newlines.push(line);
				}
				var code = newlines.join("\n")
				// Use spaces instead of tabs
				code = code.replace(/\t/g, '<span class="tab">&raquo;   </span>');
				// Highlight !important
				code = code.replace(/!important/g, '<span class="im">!important</span>');
				area.set('html', code);
			});
		});
	</script>
	<style type="text/css">
		.highlight { font-family:Consolas, Courier, monospace; background:#222; color:#FFF; font-size:100%; padding:0.5em; line-height:1.25em; }
			.highlight .se { color:#FC0; font-weight:bold; font-style:normal; }
			.highlight .co { color:#666; font-weight:normal; font-style:italic; }
			.highlight .at { color:#09F; font-weight:bold; font-style:normal; }
			.highlight .st { color:#00F; font-weight:normal; font-style:normal; }
			.highlight .ch { color:#AAA; font-weight:normal; font-style:normal; }
			.highlight .im { color:#D00; font-weight:normal; font-style:normal; }
			.highlight .tab { color:#333; font-weight:normal; font-style:normal; }
	</style>
</head>
<body>


<?php
	if(isset($_POST['css'])){
		include('../lib/parser.php');
		if(!isset($_POST['debug'])) $_POST['debug'] = 0;
		if(!isset($_POST['compress'])) $_POST['compress'] = 0;
		$parser = Parser2::factory()
			->set_debug($_POST['debug'])
			->load_string(stripslashes($_POST['css']))
			->parse();
	}
?>
<table border="1" cellpadding="4">
	<tr>
		<td valign="top">
			<h2>Original</h2>
			<pre<?php if(isset($_POST['highlight'])) { echo ' class="highlight"'; } ?>><?php if(isset($_POST['css'])) { echo implode("\n", $parser->css); } ?></pre>
		</td>
		<td valign="top">
			<h2>Kompiliert</h2>
			<pre><?php if(isset($_POST['css'])) { echo $parser->glue($_POST['compress']); } ?></pre>
		</td>
		<td valign="top">
			<h2>Geparsed</h2>
			<pre><?php if(isset($_POST['css'])) { print_r($parser->parsed); } ?></pre>
		</td>
	</tr>
</table>

<p>
	Fehlende Features: @font-face, besseres Highlighting (Strings!), Support für Leerzeichen zum Einrücken
</p>
<form action="1.php" method="post">
	<textarea cols="120" rows="20" name="css"><?php if(isset($_POST['css'])): echo stripslashes($_POST['css']); else: ?>@import url(foo.css) screen
@import "bar.css"

@font-face
	font-family: 'Sorts Mill Goudy'
	src: url(OFLGoudyStM.otf)

@font-face
	font-family: 'Sorts Mill Goudy'
	src: url(OFLGoudyStM-Italic.otf)
	font-style: italic


#foo, #bar, #arf
	color: red
	font-weight: bold; font-style: italic
	#spam,  #eggs
		color: green

@media screen

#blubb
	color: blue


// Kommentar!
#foo div.bar
	font-weight: bold; // Semikolons sind erlaubt, aber nicht nötig...
	color: red
	font-style: italic; text-transform: uppercase; vertical-align: middle // ... außer man will mehr in eine Zeile quetschen
	font-family:Verdana, Arial, sans
	ul, ol
		list-style: square
		li
			display: block
			content: "';:e///\{]}"
			b
				font-weight: bold

div.blubbs
	font-weight: normal !important
	color: blue


@media print

div.blubbs
	font-weight: bold<?php endif; ?></textarea>
<br>
<label><input type="checkbox" name="debug" value="1"> Debug</label>
<br>
<label><input type="checkbox" name="compress" value="1"> Kompression</label>
<br>
<label><input type="checkbox" name="highlight" value="1"> Syntaxhighlighting (Experimental)</label>
<br>
<input type="submit">
</form>


</body>
</html>