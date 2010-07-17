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
		global $cssp;
		$settings = array();
		// Are there settings for this plugin?
		if(isset($cssp->parsed['global']['@turbine'][$plugin])){
			// Read the settings and return them
			$settings_string = $cssp->get_final_value($cssp->parsed['global']['@turbine'][$plugin]);
			$settings = $cssp->tokenize($settings_string, ',');
			return $settings;
		}
	}


}


?>
