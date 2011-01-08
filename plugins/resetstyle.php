<?php

/**
 * This file is part of Turbine
 * http://github.com/SirPepe/Turbine
 * 
 * Copyright Peter Kröner
 * Licensed under GNU LGPL 3, see license.txt or http://www.gnu.org/licenses/
 */


/**
 * Resetstyle
 * Implements a reset stylesheet
 * 
 * Usage: Simply include the plugin in @cssp
 * Status: Stable
 * Version: 1.0
 * 
 * Version history:
 * 1.0 Initial Stable Version
 * 1.1 Added support for custom stylesheets
 * 
 * @param mixed &$output
 * @return void
 */
function resetstyle(&$output){
	global $cssp;
	$settings = Plugin::get_settings('resetstyle');
	// Get the reset stylesheet. Use the custom file if it exists
	if(file_exists($cssp->config['turbine_dir'].'plugins/resetstyle/custom.css')){
		$reset_stylesheet = file_get_contents($cssp->config['turbine_dir'].'plugins/resetstyle/custom.css');
	}
	elseif(file_exists($cssp->config['turbine_dir'].'plugins/resetstyle/default.css')){
		$reset_stylesheet = file_get_contents($cssp->config['turbine_dir'].'plugins/resetstyle/default.css');
	}
	if(!empty($reset_stylesheet)){
		// Compress the styles
		$reset_stylesheet = cssmin::minify($reset_stylesheet);
		// Force a scrollbar?
		if(is_array($settings) && in_array('force-scrollbar', $settings)){
			$reset_stylesheet .= 'html{overflow-y:scroll}';
		}
		// Add the reset stylesheet to the output. Done!
		$output = $reset_stylesheet.$output;
	}
	else{
		global $cssp;
		$cssp->report_error('Resetstyle plugin couldn\'t find a stylesheet to include in '.realpath($cssp->config['turbine_dir'].'plugins/resetstyle'));
	}
}


/**
 * Register the plugin
 */
$cssp->register_plugin('resetstyle', 'resetstyle', 'before_output', 0);


?>
