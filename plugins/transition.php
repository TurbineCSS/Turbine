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
 * Adds vendor-specific versions of transition
 * 
 * Usage:   #foo { transition: height 0.5s ease-in; }
 * Result:  #foo { transition: height 0.5s ease-in; -moz-transition: height 0.5s ease-in; -webkit-transition: height 0.5s ease-in; -o-transition: height 0.5s ease-in; }
 * Status:  Stable
 * Version: 1.0
 * 
 * @param mixed &$parsed
 * @return void
 */
function transition(&$parsed){
	global $cssp;
	foreach($parsed as $block => $css){
		foreach($parsed[$block] as $selector => $styles){
			if(isset($parsed[$block][$selector]['transition'])){
				foreach($styles as $property => $values){
					if($property == 'transition'){
						$shadow_properties = array();
						// Build prefixed properties
						$prefixes = array('-moz-', '-webkit-', '-o-');
						foreach($prefixes as $prefix){
							$shadow_properties[$prefix.'transition'] = $parsed[$block][$selector]['transition'];
						}
						// Insert everything
						$cssp->insert_properties($shadow_properties, $block, $selector, null, 'transition');
						// Comment the newly inserted properties
						foreach($shadow_properties as $shadow_property => $shadow_value){
							CSSP::comment($parsed[$block][$selector], $shadow_property, 'Added by transition plugin');
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
$cssp->register_plugin('before_glue', 0, 'transition');


?>
