<?php


	/**
	 * Implements a reset stylesheet
	 * 
	 * Usage: Simply include the plugin in @cssp
	 * Status: Stable
	 * 
	 * @param mixed &$output
	 * @return void
	 */
	function resetstyle(&$output){
		// Get the reset stylesheet. Use the custom one if it exists
		if(file_exists('plugins/resetstyle/custom.css')){
			$reset_stylesheet = file_get_contents('plugins/resetstyle/custom.css');
		}
		else{
			$reset_stylesheet = file_get_contents('plugins/resetstyle/default.css');
		}
		// Compress the styles
		$reset_stylesheet = cssmin::minify($reset_stylesheet);
		// Add the reset stylesheet to the output. Done!
		$output = $reset_stylesheet.$output;
	}


	/**
	 * Register the plugin
	 */
	$cssp->register_plugin('before_output', 0, 'resetstyle');


?>