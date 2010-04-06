<?php


	/**
	 * load
	 * Loads the specified stylesheet the the exact position
	 * 
	 * Usage: @load path/relative/to/css.php/foo.cssp
	 * Example: -
	 * Status: Beta
	 * 
	 * @param array &$css
	 * @return void
	 */
	function load(&$css){
		global $cssp;
		$new = array();
		foreach($css as $line){
			if(preg_match('/^[\s]*@load[\s]+url\((.*?)\)/', $line, $matches)){
				if(count($matches) == 2){
					$filepath = $matches[1];
					// Apply global path constants;
					foreach($cssp->global_constants as $g_constant => $g_value){
						$filepath = preg_replace('/(\$_'.$g_constant.')\b/', $g_value, $filepath);
					}
					// Load the file
					if(file_exists($filepath)){
						$newlines = file($filepath);
						$newlines_indention_char = Parser2::get_indention_char($newlines);
						// Fix the indention of the new lines
						if($cssp->options['indention_char'] != $newlines_indention_char){
							$newlines = load_fix_indention($newlines, $cssp->options['indention_char'], $newlines_indention_char);
						}
						// Import the new lines
						foreach($newlines as $imported){
							$new[] = $imported;
						}
					}
					else{
						$cssp->report_error('Loader plugin could not find file '.$filepath.'.');
					}
				}
			}
			else{
				$new[] = $line;
			}
		}
		$css = $new;
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
	 * Register the plugin
	 */
	$cssp->register_plugin('before_parse', 1000, 'load');


?>