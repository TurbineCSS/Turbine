<?php

/**
 * This file is part of Turbine
 * http://github.com/SirPepe/Turbine
 * 
 * Copyright Peter Kröner
 * Licensed under GNU LGPL 3, see license.txt or http://www.gnu.org/licenses/
 */


/**
 * Easy opacity
 * Adds vendor-specific versions of opacity
 * 
 * Usage:   #foo { opacity: 0.5; }
 * Result:  #foo { -moz-opacity: 0.5; -webkit-opacity: 0.5; -kthml-opacity: 0.5; -ms-filter: "alpha(opacity=50)"; filter: alpha(opacity=50); opacity: 0.5;}
 * Status:  Stable
 * Version: 1.1
 * 
 * @param mixed &$parsed
 * @return void
 */
function opacity(&$parsed){
	global $cssp;
	foreach($parsed as $block => $css){
		foreach($parsed[$block] as $selector => $styles){
			if(isset($parsed[$block][$selector]['opacity'])){
				foreach($styles as $property => $values){
					if($property == 'opacity'){
						$opacity_properties = array();
						// Build prefixed properties
						$prefixes = array('-moz-', '-webkit-', '-khtml-');
						foreach($prefixes as $prefix){
							$opacity_properties[$prefix.'opacity'] = $parsed[$block][$selector]['opacity'];
						}
						// Create IE filters and insert everything
						$filter = 'alpha(opacity='.round(floatval($parsed[$block][$selector]['opacity']) * 100).')';
						$opacity_properties['-ms-filter'] = array($filter);
						$opacity_properties['filter'] = array($filter);
						$cssp->insert_properties($opacity_properties, $block, $selector, null, 'opacity');
						// Comment the newly inserted properties
						foreach($opacity_properties as $opacity_property => $opacity_value){
							CSSP::comment($parsed[$block][$selector], $opacity_property, 'Added by opacity plugin');
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
$cssp->register_plugin('before_glue', 0, 'opacity');


?>
