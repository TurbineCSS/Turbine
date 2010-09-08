<?php

/**
 * This file is part of Turbine
 * http://github.com/SirPepe/Turbine
 * 
 * Copyright Peter Kröner
 * Licensed under GNU LGPL 3, see license.txt or http://www.gnu.org/licenses/
 *
 *
 * transition.php
 * Copyright Christopher Marquardt
 * Licensed under the CC-GNU LGPL version 2.1 or later.
 */


/**
 * Adds vendor-specific versions transition
 * No Fallback for IE
 * 
 * Usage:     transition: [property] [time] [type]
 * Example 1: transition: opacity 50ms linear
 * Example 2: transition: height 3s ease-out
 * Status:    Beta
 * Version:   0.2
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
						$transition_properties = array();
						// Build prefixed properties
						$prefixes = array('-moz-', '-webkit-','-o-');
						foreach($prefixes as $prefix){
							$transition_properties[$prefix.'transition'] = $parsed[$block][$selector]['transition'];
						}
						$cssp->insert_properties($transition_properties, $block, $selector, null, 'transition');
						// Comment the newly inserted properties
						foreach($transition_properties as $transition_property => $transition_value){
							CSSP::comment($parsed[$block][$selector], $transition_property, 'Added by transition plugin');
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