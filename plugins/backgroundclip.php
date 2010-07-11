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
 * Adds vendor-specific versions of background-clip
 * 
 * Usage:   #foo { background-clip: border-box; }
 * Result:  #foo { background-clip: border-box; -moz-background-clip: border-box; -webkit-background-clip: border-box; }
 * Status:  Stable
 * Version: 1.0
 * 
 * @param mixed &$parsed
 * @return void
 */
function backgroundclip(&$parsed){
	global $cssp;
	foreach($parsed as $block => $css){
		foreach($parsed[$block] as $selector => $styles){
			if(isset($parsed[$block][$selector]['background-clip'])){
				foreach($styles as $property => $values){
					if($property == 'background-clip'){
						$shadow_properties = array();
						// Build prefixed properties
						$prefixes = array('-moz-', '-webkit-');
						foreach($prefixes as $prefix){
							$shadow_properties[$prefix.'background-clip'] = $parsed[$block][$selector]['background-clip'];
						}
						// Insert everything
						$cssp->insert_properties($shadow_properties, $block, $selector, null, 'background-clip');
						// Comment the newly inserted properties
						foreach($shadow_properties as $shadow_property => $shadow_value){
							CSSP::comment($parsed[$block][$selector], $shadow_property, 'Added by background-clip plugin');
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
$cssp->register_plugin('before_glue', 0, 'backgroundclip');


?>
