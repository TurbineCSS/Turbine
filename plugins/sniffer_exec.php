<?php

/**
 * This file is part of Turbine
 * http://github.com/SirPepe/Turbine
 * 
 * Copyright Peter Kröner
 * Licensed under GNU LGPL 3, see license.txt or http://www.gnu.org/licenses/
 */


/**
 * Sniffer (2nd edition)
 * The new and improved browser and platform sniffer
 * 
 * This file is responsible for actually removing properties and values. The
 * "main" plugin function only builds the list of targets
 */


/**
 * sniffer2_exec
 * Loops through $sniffer_tokill and eliminates the targets from $parsed
 * TODO Take care of @font-face
 * @param mixed &$parsed
 * @return void
 */
function sniffer_exec(&$parsed){
	global $sniffer_tokill;
	// Look for a kill rule in @turbine
	if(isset($sniffer_tokill['global']['@turbine'])){
		// Empty $parsed
		$parsed = array();
		// Kill off all other plugins
		global $plugin_list;
		$plugin_list = array();
		return false;
	}
	// Loop through the kill list
	foreach($sniffer_tokill as $block => $css){
		foreach($sniffer_tokill[$block] as $selector => $rules){
			// Loop through @font-face
			if($selector == '@font-face'){
				// TODO
				/*foreach($styles as $fontindex => $style){
					
				}*/
			}
			// Process the rest
			else{
				foreach($rules as $property => $values){
					foreach($values as $value){
						$search = array_search($value, $parsed[$block][$selector][$property]);
						if($search !== false){
							unset($parsed[$block][$selector][$property][$search]);
						}
					}
				}
			}
		}
	}
	// Cleanup
	call_user_func_array('sniffer_exec_cleanup', array(&$parsed));
}





/**
 * sniffer_exec_cleanup
 * Removes any remaining sniffer properties
 * @param array $parsed The parse tree to clean up
 * @return void
 */
function sniffer_exec_cleanup(&$parsed){
	$sniffer_properties = array('browser', 'engine', 'device', 'os');
	foreach($parsed as $block => $css){
		foreach($parsed[$block] as $selector => $rules){
			foreach($parsed[$block][$selector] as $property => $values){
				if(in_array($property, $sniffer_properties)){
					unset($parsed[$block][$selector][$property]);
				}
			}
		}
	}
}


// Register the plugin function
$cssp->register_plugin('before_compile', 9999, 'sniffer_exec');


?>
