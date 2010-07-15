<?php

/**
 * This file is part of Turbine
 * http://github.com/SirPepe/Turbine
 * 
 * Copyright Peter Kröner
 * Licensed under GNU LGPL 3, see license.txt or http://www.gnu.org/licenses/
 */


/**
 * load
 * Loads the specified stylesheet the the exact position
 * 
 * Usage: @load url(path/relative/to/css.php/foo.cssp)
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
function load(&$css){
	$css = load_apply($css);
}


/**
 * load_apply
 * Finds @load lines, includes the files
 * @param array $lines The lines to prcoess
 * @return array $new The new lines with the loaded files included
 */
function load_apply($lines){
	global $cssp;
	$new = array();
	foreach($lines as $line){
		if(preg_match('/^[\s]*@load[\s]+url\((.*?)\)/', $line, $matches)){
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
						$newlines = load_fix_indention($newlines, $cssp->indention_char, $newlines_indention_char);
					}
					// Apply the basedir to $_FILEPATH
					$newlines = load_fix_filepath($newlines, $basedir);
					// Apply the loader plugin to the loaded files
					$newlines = load_apply($newlines);
					// Import the new lines
					foreach($newlines as $imported){
						$new[] = $imported;
					}
				}
				else{
					$cssp->report_error('Loader plugin could not find file '.$loadfilepath.'.');
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
 * load_fix_indention
 * Fix the indention of the new lines
 * @param array $lines The lines to fix
 * @param $newchar The new indention char
 * @param $oldchar The old indention char
 * @return array $newlines The fixed lines
 */
function load_fix_indention($lines, $newchar, $oldchar){
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
 * load_fix_filepath
 * Fixes the $_FILEPATH var with the relative basedir
 * @param array $lines The lines to fix
 * @param array $basedir The basedir
 * @return array $newlines The fixed lines
 */
function load_fix_filepath($lines, $basedir){
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
$cssp->register_plugin('before_parse', 1000, 'load');


?>
