<?php

/**
 * This file is part of Turbine
 * http://github.com/SirPepe/Turbine
 * 
 * Copyright Peter Kröner
 * Licensed under GNU LGPL 3, see license.txt or http://www.gnu.org/licenses/
 */


/**
 * Plugin
 * Turbine plugin utilities
 */
class Plugin {


	/*
	 * get_settings
	 * Reads the plugin settings and returns them as an array
	 * @param string $plugin The plugin's name
	 * @return array $settings The plugin settings
	 */
	static function get_settings($plugin){
		global $cssp, $plugin_settings;
		$settings = array();
		// Are there settings for this plugin?
		if(isset($plugin_settings[$plugin])){
			// Tokenize the settings and return them
			$settings = $cssp->tokenize($plugin_settings[$plugin], ',');
			return $settings;
		}
	}


}


?>
