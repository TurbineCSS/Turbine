<?php

/**
 * This file is part of Turbine
 * http://github.com/SirPepe/Turbine
 * 
 * Copyright Peter Kröner
 * Licensed under GNU LGPL 3, see license.txt or http://www.gnu.org/licenses/
 */


/**
 * Easy box shadow
 * Adds vendor-specific versions of background-origin
 * 
 * Usage:   #foo { background-origin: border-box; }
 * Result:  #foo { background-origin: border-box; -moz-background-origin: border-box; -webkit-background-origin: border-box; }
 * Status:  Stable
 * Version: 1.0
 * 
 * @param mixed &$parsed
 * @return void
 */
function backgroundorigin(&$parsed){
	global $cssp;
	foreach($parsed as $block => $css){
		foreach($parsed[$block] as $selector => $styles){
			if(isset($parsed[$block][$selector]['background-origin'])){
				foreach($styles as $property => $values){
					if($property == 'background-origin'){
						$shadow_properties = array();
						// Build prefixed properties
						$prefixes = array('-moz-', '-webkit-');
						foreach($prefixes as $prefix){
							$shadow_properties[$prefix.'background-origin'] = $parsed[$block][$selector]['background-origin'];
						}
						// Insert everything
						$cssp->insert_properties($shadow_properties, $block, $selector, null, 'background-origin');
						// Comment the newly inserted properties
						foreach($shadow_properties as $shadow_property => $shadow_value){
							CSSP::comment($parsed[$block][$selector], $shadow_property, 'Added by background-origin plugin');
						}
					}
				}
			}
		}
	}
}


/**
 * Register the plugin
 */
$cssp->register_plugin('before_glue', 0, 'backgroundorigin');


?>
