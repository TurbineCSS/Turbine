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
		// The reset stylesheet
		$reset_stylesheet = "*{margin:0;padding:0;border:0;outline:0;font-size:100%;vertical-align:baseline;background:transparent;color:inherit;text-decoration:none;font-weight:normal}body{line-height:1}ol,ul{list-style:none}blockquote,q{quotes:none}blockquote:before,blockquote:after,q:before,q:after{content:'';content:none}:focus{outline:0}table{border-collapse:collapse;border-spacing:0}";
		// Add the reset stylesheet to the output. Done!
		$output = $reset_stylesheet.$output;
	}


	/**
	 * Register the plugin
	 */
	register_plugin('before_output', 0, 'resetstyle');


?>