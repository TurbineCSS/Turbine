<?php

/**
 * This file is part of Turbine
 * http://github.com/SirPepe/Turbine
 * 
 * Copyright Peter Kröner
 * Licensed under GNU LGPL 3, see license.txt or http://www.gnu.org/licenses/
 */


/**
 * Cross-browser-box-sizing
 * 
 * Usage:     box-sizing:inherit|content-box|border-box
 * Status:    Beta
 * Version:   1.0
 * 
 * @param mixed &$parsed
 * @return void
 */
function boxsizing(&$parsed){
	global $browser, $cssp;
	$htc_path = rtrim(dirname($_SERVER['SCRIPT_NAME']),'/').'/plugins/boxsizing/boxsizing.htc';
	foreach($parsed as $block => $css){
		foreach($parsed[$block] as $selector => $styles){
			if(isset($styles['box-sizing'])){
				// Create the vendor-specific rules and insert them
				$boxsizing_rules = array(
					'-moz-box-sizing' => $styles['box-sizing'],
					'-webkit-box-sizing' => $styles['box-sizing'],
					'behavior' => 'url('.$htc_path.')'
				);
				$cssp->insert_properties($boxsizing_rules, $block, $selector, null, 'box-sizing');
				// Comment the newly inserted properties
				foreach($boxsizing_rules as $property => $value){
					CSSP::comment($parsed[$block][$selector], $property, 'Added by box-sizing plugin');
				}
			}
		}
	}
}

/**
 * Register the plugin
 */
$cssp->register_plugin('before_glue', 0, 'boxsizing');


?>
