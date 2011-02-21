<?php

/**
 * This file is part of Turbine
 * http://github.com/SirPepe/Turbine
 * 
 * Copyright Peter Kröner
 * Licensed under GNU LGPL 3, see license.txt or http://www.gnu.org/licenses/
 */


/**
 * Color
 * Provides HSL(A) support for all browsers
 * 
 * Usage:   Nobrainer, just switch it on
 * Example: -
 * Status:  Beta
 * Version: 2.0
 * 
 * @param mixed &$cssp->parsed
 * @return void
 */
function color(){
	include('lib/utility.php');
	global $browser, $cssp;
	$properties = array('background', 'background-color', 'color', 'border', 'border-color', 'border-top', 'border-left', 'border-bottom', 'border-right', 'text-shadow', 'box-shadow');
	// For every possible property...
	foreach($properties as $search){
		// ... loop through the css...
		foreach($cssp->parsed as $block => $css){
			foreach($cssp->parsed[$block] as $selector => $styles){
				if($selector != '@turbine' && isset($cssp->parsed[$block][$selector][$search])){
					$num_values = count($cssp->parsed[$block][$selector][$search]);
					// ... loop through the values
					for($i = 0; $i < $num_values; $i++){
						// Found something that we may have to replace?
						$hslamatch = preg_match(Utility::$hslapattern, $cssp->parsed[$block][$selector][$search][$i]);
						$rgbamatch = preg_match(Utility::$rgbapattern, $cssp->parsed[$block][$selector][$search][$i]);
						if($hslamatch || $rgbamatch){
							$rgba = Utility::any2rgba($cssp->parsed[$block][$selector][$search][$i]);
							// For HSL recalculate to RGBA just to be sure the color will work everywhere
							if($hslamatch){
								$replacement = Utility::rgbasyntax($rgba);
								$cssp->parsed[$block][$selector][$search][$i] = preg_replace(Utility::$hslapattern, $replacement, $cssp->parsed[$block][$selector][$search][$i]);
								CSSP::comment($cssp->parsed[$block][$selector], $search, 'Modified by color plugin');
							}
							// If we detect IE and work with a background, try filters...
							if($browser->browser == 'ie' && $browser->browser_version < 9 && ($search == 'background' || $search == 'background-color')){
								$filter = color_get_filter($rgba, $search);
								$cssp->insert_properties($filter, $block, $selector, NULL, $search);
								foreach($filter as $filter_property => $filter_value){
									CSSP::comment($cssp->parsed[$block][$selector], $filter_property, 'Modified by color plugin');
								}
							}
							// Otherwise just provide an ugly, automatic fallback
							else{
								$fallback = Utility::rgbsyntax($rgba);
								array_unshift($cssp->parsed[$block][$selector][$search], preg_replace(Utility::$rgbapattern, $fallback, $cssp->parsed[$block][$selector][$search][$i]));
								CSSP::comment($cssp->parsed[$block][$selector], $search, 'Modified by color plugin');
							}
						}
					}
				}
			}
		}
	}
}


/*
 * color_get_filter
 * Returns a gradient filter that acts as poor man's transparent background
 * @param array $color RGBA color array to use
 * @return array $properties The filter properties
 */
function color_get_filter($color, $property){
	$properties = array();
	$color = Utility::hexsyntax($color, true);
	$filter = 'progid:DXImageTransform.Microsoft.gradient(startColorstr='.$color.',endColorstr='.$color.')';
	$properties[$property] = array('none');
	$properties['filter'][] = $filter;
	$properties['-ms-filter'][] = $filter;
	$properties['zoom'][] = '1';
	return $properties;
}


/**
 * Register the plugin
 */
$cssp->register_plugin('color', 'color', 'before_glue', -100);


?>
