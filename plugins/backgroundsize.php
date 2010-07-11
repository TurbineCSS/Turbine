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
 * Adds vendor-specific versions of background-size
 * 
 * Usage:   #foo { background-size: 100% 100%; }
 * Result:  #foo { background-size: 100% 100%; -moz-background-size: 100% 100%; -webkit-background-size: 100% 100%; -o-background-size: 100% 100%; }
 * Status:  Stable
 * Version: 1.0
 * 
 * @param mixed &$parsed
 * @return void
 */
function backgroundsize(&$parsed){
	global $cssp;
	foreach($parsed as $block => $css){
		foreach($parsed[$block] as $selector => $styles){
			if(isset($parsed[$block][$selector]['background-size'])){
				foreach($styles as $property => $values){
					if($property == 'background-size'){
						$shadow_properties = array();
						// Build prefixed properties
						$prefixes = array('-moz-', '-webkit-', '-o-');
						foreach($prefixes as $prefix){
							$shadow_properties[$prefix.'background-size'] = $parsed[$block][$selector]['background-size'];
						}
						// Insert everything
						$cssp->insert_properties($shadow_properties, $block, $selector, null, 'background-size');
						// Comment the newly inserted properties
						foreach($shadow_properties as $shadow_property => $shadow_value){
							CSSP::comment($parsed[$block][$selector], $shadow_property, 'Added by background-size plugin');
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
$cssp->register_plugin('before_glue', 0, 'backgroundsize');


?>
