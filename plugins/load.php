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
		$new = array();
		$matches = array();
		foreach($css as $line){
			if(preg_match('/^[\s]*@load[\s]+url\((.*?)\)/', $line, $matches)){
				if(count($matches) == 2){
					$filepath = $matches[1];
					// Apply global path constants
					global $global_constants;
					foreach($global_constants as $g_constant => $g_value){
						$filepath = preg_replace('/(\$_'.$g_constant.')\b/', $g_value, $filepath);
					}
					// Import the new lines
					$import = file($filepath);
					foreach($import as $imported){
						$new[] = $imported;
					}
					$matches = array();
				}
			}
			else{
				$new[] = $line;
			}
		}
		$css = $new;
	}


	/**
	 * Register the plugin
	 */
	register_plugin('before_parse', 1000, 'load');


?>