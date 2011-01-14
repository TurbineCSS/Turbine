<?php

/**
 * insert
 * Inserts the specified stylesheet the the exact position without parsing through turbine
 * 
 * Usage: @insert url(path/relative/to/css.php/foo.css)
 * Example: -
 * Status: Stable
 * Version: 1.1
 * 
 * Version history:
 * 1.0 Initial Stable Version
 * 1.1 Added auto-indention-fixing
 * 
 * @param array &$css
 * @return void
 */
function insert(&$css){
	$css = insert_apply($css);
}


/**
 * insert_apply
 * Finds @insert lines, includes the files
 * @param array $lines The lines to prcoess
 * @return array $new The new lines with the loaded files included
 */
function insert_apply($lines){
	global $cssp;
	$new = array();
	foreach($lines as $line){
		if(preg_match('/^[\s]*@insert[\s]+url\((.*?)\)/', $line, $matches)){
			if(count($matches) == 2){
				$loadfilepath = $matches[1];
				// Apply global path constants;
				foreach($cssp->global_constants as $g_constant => $g_value){
					$loadfilepath = preg_replace('/(\$_'.$g_constant.')\b/', $g_value, $loadfilepath);
				}
				$basedir = dirname($loadfilepath);
				// Load the file
				if(file_exists($loadfilepath)){
					$newlines = file($loadfilepath);
					$newlines_indention_char = Parser2::get_indention_char($newlines);
					// Fix the indention of the new lines
					if($cssp->indention_char != $newlines_indention_char){
						$newlines = insert_fix_indention($newlines, $cssp->indention_char, $newlines_indention_char);
					}
					// Apply the basedir to $_FILEPATH
					$newlines = insert_fix_filepath($newlines, $basedir);
					// Apply the loader plugin to the loaded files
					$newlines = insert_apply($newlines);
					// Import the new lines
					foreach($newlines as $imported){
						$new[] = "@css ".$imported;
					}
				}
				else{
					$cssp->report_error('Insert plugin could not find file '.$loadfilepath.'.');
				}
			}
		}
		else{
			$new[] = $line;
		}
	}
	return $new;
}


/**
 * insert_fix_indention
 * Fix the indention of the new lines
 * @param array $lines The lines to fix
 * @param $newchar The new indention char
 * @param $oldchar The old indention char
 * @return array $newlines The fixed lines
 */
function insert_fix_indention($lines, $newchar, $oldchar){
	$newlines = array();
	foreach($lines as $line){
		if(preg_match('/^([\s]+)(.+)$/', $line, $parts)){
			$line = str_replace($oldchar, $newchar, $parts[1]).$parts[2];
		}
		$newlines[] = $line;
	}
	return $newlines;
}


/**
 * insert_fix_filepath
 * Fixes the $_FILEPATH var with the relative basedir
 * @param array $lines The lines to fix
 * @param array $basedir The basedir
 * @return array $newlines The fixed lines
 */
function insert_fix_filepath($lines, $basedir){
	$newlines = array();
	foreach($lines as $line){
		$line = preg_replace('/\$_FILEPATH/' , $basedir.'/' , $line);
		$newlines[] = $line;
	}
	return $newlines;
}


/**
 * Register the plugin
 */
$cssp->register_plugin('before_parse', 1000, 'insert');


?>
