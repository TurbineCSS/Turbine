<?php

/**
 * This file is part of Turbine
 * http://github.com/SirPepe/Turbine
 * 
 * Copyright Peter Kröner
 * Licensed under GNU LGPL 3, see license.txt or http://www.gnu.org/licenses/
 */


/**
 * Easy and extended border radius
 * Adds vendor-specific versions of border-radius and adds border-top-radius,
 * border-bottom-radius, border-left-radius and border-right-radius
 * For IE, only general radius works. And make sure, border-radius declaration 
 * comes before any (box-)shadow-declaration in CSS-code!
 * 
 * Usage:     border-radius[-top/-bottom/-left/-right/-top-left/...]: value;
 * Example 1: border-top-radius:4px;
 * Example 2: border-bottom-left-radius:2em;
 * Status:    Stable
 * Version:   1.1
 * 
 * @param mixed &$parsed
 * @return void
 */
function borderradius(&$parsed){
	global $cssp;
	foreach($parsed as $block => $css){
		foreach($parsed[$block] as $selector => $styles){
			foreach($styles as $property => $values){
				if(preg_match('/border(?:-(top|right|bottom|left)(?:-(right|left))*)*-radius/', $property, $matches)){
					// Create the new rules and insert them
					$borderradius_rules = borderradius_glue_rules($matches, $values);
					$cssp->insert_properties($borderradius_rules, $block, $selector, null, $property);
					// Comment the newly inserted properties
					foreach($borderradius_rules as $border_property => $border_value){
						CSSP::comment($parsed[$block][$selector], $border_property, 'Added by border radius plugin');
					}
					// Remove Top/Left/Bottom/Right shortcuts
					if(count($matches) == 2){
						unset($parsed[$block][$selector][$property]);
					}
				}
			}
		}
	}
}


/**
 * borderradius_glue_rules
 * Builds an array containing the new prefixed border radius rules
 * @param array $parts The parsed parts of the original border-radius rule
 * @param string $value The value to use for border-radius
 * @return array $properties The freshly build properties
 */
function borderradius_glue_rules($parts, $value){
	$properties = array();
	$prefixes = array('-moz-', '-webkit-', '-khtml-', '');
	$partnum = count($parts);
	foreach($prefixes as $prefix){
		// Simple border-radius
		if($partnum == 1){
			$properties[$prefix.$parts[0]] = $value;
		}
		// Top/Left/Bottom/Right shortcuts
		elseif($partnum == 2){
			if($parts[1] == 'top' || $parts[1] == 'bottom'){
				if($prefix == '-moz-'){
					$properties[$prefix.'border-radius-'.$parts[1].'right'] = $value;
					$properties[$prefix.'border-radius-'.$parts[1].'left'] = $value;
				}
				else{
					$properties[$prefix.'border-'.$parts[1].'-right-radius'] = $value;
					$properties[$prefix.'border-'.$parts[1].'-left-radius'] = $value;
				}
			}
			elseif($parts[1] == 'left' || $parts[1] == 'right'){
			if($prefix == '-moz-'){
					$properties[$prefix.'border-radius-top'.$parts[1]] = $value;
					$properties[$prefix.'border-radius-bottom'.$parts[1]] = $value;
				}
				else{
					$properties[$prefix.'border-top-'.$parts[1].'-radius'] = $value;
					$properties[$prefix.'border-bottom-'.$parts[1].'-radius'] = $value;
				}
			}
		}
		// Single corners
		elseif($partnum == 3){
			if($prefix == '-moz-'){
				$properties[$prefix.'border-radius-'.$parts[1].$parts[2]] = $value;
			}
			else{
				$properties[$prefix.'border-'.$parts[1].'-'.$parts[2].'-radius'] = $value;
			}
		}
	}
	return $properties;
}


/**
 * Register the plugin
 */
$cssp->register_plugin('before_glue', 0, 'borderradius');


?>
