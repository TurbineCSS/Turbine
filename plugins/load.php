<?php


	/**
	 * load
	 * Loads the specified stylesheet the the exact position
	 * 
	 * Usage: @load path/relative/to/css.php/foo.cssp
	 * Example: -
	 * Status: Experimental
	 * 
	 * @param array &$css
	 * @return void
	 */
	function load(&$css){
		$new = array();
		$matches = array();
		foreach($css as $line){
			if(preg_match('/^[\s]*@load[\s]+(.*)/', $line, $matches)){ // TODO: Take care of comments after the url
				if(count($matches) == 2){
					$import = file($matches[1]);
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
	register_plugin('before_parse', 0, 'load');


?>