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
 * Adds vendor-specific versions of box-shadow
 * 
 * Usage:   #foo { box-shadow: 2px 2px 8px #666; }
 * Result:  #foo { box-shadow: 2px 2px 8px #666; -moz-box-shadow: 2px 2px 8px #666; -webkit-box-shadow: 2px 2px 8px #666; }
 * Status:  Stable
 * Version: 1.2
 * 
 * @param mixed &$parsed
 * @return void
 */
function boxshadow(&$parsed){
	global $cssp;
	$settings = Plugin::get_settings('boxshadow');
	foreach($parsed as $block => $css){
		foreach($parsed[$block] as $selector => $styles){
			if(isset($parsed[$block][$selector]['box-shadow'])){
				foreach($styles as $property => $values){
					if($property == 'box-shadow'){
						$shadow_properties = array();
						// Build prefixed properties
						$prefixes = array('-moz-', '-webkit-');
						foreach($prefixes as $prefix){
							$shadow_properties[$prefix.'box-shadow'] = $parsed[$block][$selector]['box-shadow'];
						}
						// Get IE filters, merge them with the other new properties and insert everything
						if(is_array($settings) && !in_array('noie', $settings)){
							$filter_properties = boxshadow_filters($values);
							$shadow_properties = array_merge($shadow_properties, $filter_properties);
						}
						// Insert the new properties
						$cssp->insert_properties($shadow_properties, $block, $selector, null, 'box-shadow');
						// Comment the newly inserted properties
						foreach($shadow_properties as $shadow_property => $shadow_value){
							CSSP::comment($parsed[$block][$selector], $shadow_property, 'Added by box shadow plugin');
						}
					}
				}
			}
		}
	}
}


/**
 * boxshadow_filters
 * Builds filter properties for IE
 * @return array $filter_properties The new filter properties
 */
function boxshadow_filters($values){
	global $cssp;
	include($cssp->config['turbine_dir'].'lib/utility.php');
	// Get the relevant box shadow value
	$value = $cssp->get_final_value($values);
	$filter_properties = array();
	// Extract the important values
	if(preg_match('/([-0-9]+)\D+([-0-9]+)\D+([-0-9]+)\D+((?:#|rgb(:?a)?\(|hsl(:?a)?\()(?:.*))/i', $value, $matches) == 1){
		$xoffset = intval($matches[1]);
		$yoffset = intval($matches[2]);
		$blur = intval($matches[3]);
		$color = Utility::any2rgba($matches[4]);
		// Build the filter value. Disable filters if box-shadow value is "none" of if offset and blur are both 0
		if($value == 'none' || ($xoffset == 0 && $yoffset == 0 && $blur == 0)){
			$filters = array(
				'progid:DXImageTransform.Microsoft.dropshadow(enabled:false)',
				'progid:DXImageTransform.Microsoft.Shadow(enabled:false)',
				'progid:DXImageTransform.Microsoft.Glow(enabled:false)'
			);
			$filter_properties['-ms-filter'] = $filters;
			$filter_properties['filter'] = $filters;
			$filter_properties['zoom'] = array('1');
		}
		// Else build the filters
		else{
			// Use glow filter if the offset is 0
			if($xoffset == 0 && $yoffset == 0){
				$filters = array(
					'progid:DXImageTransform.Microsoft.Glow(color=\''.Utility::hexsyntax($color).'\', Strength=\''.$blur.'\')'
				);
				$filter_properties['-ms-filter'] = $filters;
				$filter_properties['filter'] = $filters;
				$filter_properties['zoom'] = array('1');
			}
			// Else use either drop shadow or shadow filter, depending on blur
			else{
				$median_offset = round((abs($xoffset) + abs($yoffset)) / 2);
				// Calculate direction
				$direction = rad2deg(atan2($yoffset, $xoffset * -1));
				// Hard Shadow
				if($blur == 0){
					$filter = 'progid:DXImageTransform.Microsoft.dropshadow(OffX='.$xoffset.',OffY='.$yoffset.',Color=\''.Utility::hexsyntax($color).'\',Positive=\'true\')';
				}
				// Soft Shadow
				else{
					$strength = ($median_offset + $blur) / 2;
					$filter = 'progid:DXImageTransform.Microsoft.Shadow(Color=\''.Utility::hexsyntax($color).'\',Direction='.$direction.',Strength='.$strength.')';
				}
				$filter_properties['-ms-filter'] = array('"'.$filter.'"');
				$filter_properties['filter'] = array($filter);
				$filter_properties['zoom'] = array('1');
			}
		}
	}
	return $filter_properties;
}


/**
 * Register the plugin
 */
$cssp->register_plugin('boxshadow', 'boxshadow', 'before_glue', 0);


?>
