<?php

/**
 * This file is part of Turbine
 * http://github.com/SirPepe/Turbine
 * 
 * Copyright Peter Kröner
 * Licensed under GNU LGPL 3, see license.txt or http://www.gnu.org/licenses/
 */


/**
 * background gradient
 * creates a cross-browser linear vertical or horizontal background gradient (other angles or radial gradient not supported)
 * Adds vendor-specific code for gradients
 * 
 * Usage:     Use simplest possible notation as planned by W3C: http://dev.w3.org/csswg/css3-images/#linear-gradients  
 *            background: linear-gradient([direction:<top|left>],[startcolor<hex|rgb|rgba>],[endcolor<hex|rgb|rgba>])
 *            
 * Example 1: background: linear-gradient(top,#FFF,#000) // vertical gradient, from top to bottom, from white to black
 * Example 2: background-image: linear-gradient(left,rgb(255,255,255),rgb(0,0,0)) // horizontal gradient, from left to right, from white to black
 * Status:    Beta
 * Version:   1.0
 * 
 * 
 * backgroundgradient
 * @param mixed &$parsed
 * @return void
 */
function backgroundgradient(&$parsed){
	global $cssp, $browser;
	// Searches for W3C-style two-stepped linear gradient
	$urlregex = '/linear-gradient\((top|left),(#[0-9A-F]+|rgba*\([0-9,]+\)),(#[0-9A-F]+|rgba*\([0-9,]+\))\)/i';
	// In which properties to searcg
	$urlproperties = array('background', 'background-image');
	// Loop through the array
	foreach($parsed as $block => $css){
		foreach($parsed[$block] as $selector => $styles){
			foreach($urlproperties as $property){
				if(isset($parsed[$block][$selector][$property])){
					$num_values = count($parsed[$block][$selector][$property]);
					for($i = 0; $i < $num_values; $i++){
						if(preg_match($urlregex, $parsed[$block][$selector][$property][$i], $matches) > 0){
							switch($browser->engine){
								// Gecko
								case 'gecko':
								$parsed[$block][$selector][$property][$i] = preg_replace(
									$urlregex,
									'-moz-linear-gradient('.$matches[1].','.$matches[2].','.$matches[3].')',
									$parsed[$block][$selector][$property][$i]
								);
								CSSP::comment($parsed[$block][$selector], $property, 'Modified by background-gradient plugin');
								break;

								// Webkit
								case 'webkit':
								if(strtolower($matches[1]) == 'top'){
									$webkit_gradientdirection = 'left top,left bottom';
								}
								else{
									$webkit_gradientdirection = 'left top,right top';
								}
								$parsed[$block][$selector][$property][$i] = preg_replace(
									$urlregex,
									'-webkit-gradient(linear,'.$webkit_gradientdirection.',from('.$matches[2].'),to('.$matches[3].'))',
									$parsed[$block][$selector][$property][$i]
								);
								CSSP::comment($parsed[$block][$selector], $property, 'Modified by background-gradient plugin');
								break;

								// Konqueror
								case 'khtml':
								if(strtolower($matches[1]) == 'top'){
									$webkit_gradientdirection = 'left top,left bottom';
								}
								else{
									$webkit_gradientdirection = 'left top,right top';
								}
								$parsed[$block][$selector][$property][$i] = preg_replace(
									$urlregex,
									'-khtml-gradient(linear,'.$webkit_gradientdirection.',from('.$matches[2].'),to('.$matches[3].'))',
									$parsed[$block][$selector][$property][$i]
								);
								CSSP::comment($parsed[$block][$selector], $property, 'Modified by background-gradient plugin');
								break;

								// Opera
								case 'opera':
								$svg_path = rtrim(dirname($_SERVER['SCRIPT_NAME']),'/').'/plugins/backgroundgradient/svg.php';
								$svg_params = 'direction='.strtolower($matches[1]);
								$svg_params .= '&startcolor='.strtolower($matches[2]);
								$svg_params .= '&endcolor='.strtolower($matches[3]);
								$parsed[$block][$selector][$property][$i] = preg_replace(
									$urlregex,
									'url('.$svg_path.'?'.$svg_params.') 0 0 repeat',
									$parsed[$block][$selector][$property][$i]
								);
								CSSP::comment($parsed[$block][$selector], $property, 'Modified by background-gradient plugin');
								break;

								// IE
								case 'ie':
								$filter_properties = array();
								if(strtolower($matches[1]) == 'top'){
									$ie_gradienttype = '0';
								}
								else{
									$ie_gradienttype = '1';
								}
								// Expand shorthand colors
								$shorthandpattern = '/^#([0-9A-F]{1})([0-9A-F]{1})([0-9A-F]{1})$/i';
								if(preg_match($shorthandpattern,$matches[2],$shorthandmatches)){
									$matches[2] = '#FF'.strtoupper($shorthandmatches[1].$shorthandmatches[1].$shorthandmatches[2].$shorthandmatches[2].$shorthandmatches[3].$shorthandmatches[3]);
								}
								if(preg_match($shorthandpattern,$matches[3],$shorthandmatches)){
									$matches[3] = '#FF'.strtoupper($shorthandmatches[1].$shorthandmatches[1].$shorthandmatches[2].$shorthandmatches[2].$shorthandmatches[3].$shorthandmatches[3]);
								}
								// Convert from RGB colors
								$rgbpattern = '/rgb\([\s]*(.+?)[\s]*,[\s]*(.+?)[\s]*,[\s]*(.+?)[\s]*\)/i';
								if(preg_match($rgbpattern,$matches[2],$rgbmatches)){
									$matches[2] = '#FF'.strtoupper(dechex(intval($rgbmatches[1])).dechex(intval($rgbmatches[2])).dechex(intval($rgbmatches[3])));
								}
								if(preg_match($rgbpattern,$matches[3],$rgbmatches)){
									$matches[3] = '#FF'.strtoupper(dechex(intval($rgbmatches[1])).dechex(intval($rgbmatches[2])).dechex(intval($rgbmatches[3])));
								}
								// Convert from RGBA colors
								$rgbapattern = '/rgba\([\s]*(.+?)[\s]*,[\s]*(.+?)[\s]*,[\s]*(.+?)[\s]*,[\s]*(.+?)[\s]*\)/i';
								if(preg_match($rgbapattern,$matches[2],$rgbamatches)){
									$matches[2] = '#'.strtoupper(dechex(intval(floatval($rgbamatches[4]) * 255)).dechex(intval($rgbamatches[1])).dechex(intval($rgbamatches[2])).dechex(intval($rgbamatches[3])));
								}
								if(preg_match($rgbapattern,$matches[3],$rgbamatches)){
									$matches[3] = '#'.strtoupper(dechex(intval(floatval($rgbamatches[4]) * 255)).dechex(intval($rgbamatches[1])).dechex(intval($rgbamatches[2])).dechex(intval($rgbamatches[3])));
								}
								$filter = 'progid:DXImageTransform.Microsoft.gradient(startColorstr='.$matches[2].',endColorstr='.$matches[3].',gradientType='.$ie_gradienttype.')';
								// Legacy IE compliance
								if($browser->engine_version < 8){
									$filter_properties['filter'] = array($filter);
									$parsed[$block][$selector][$property][$i] = preg_replace(
										$urlregex,
										'',
										$parsed[$block][$selector][$property][$i]
									);
								}
								// IE8 compliance (note: value inside apostrophes!)
								elseif($browser->engine_version < 9){
									$filter_properties['-ms-filter'] = array($filter);
									$parsed[$block][$selector][$property][$i] = preg_replace(
										$urlregex,
										'',
										$parsed[$block][$selector][$property][$i]
									);
								}
								$cssp->insert_properties($filter_properties, $block, $selector, null, $property);
								foreach($filter_properties as $filter_property => $filter_value){
									CSSP::comment($parsed[$block][$selector], $shadow_property, 'Added by background-gradient plugin');
								}
								break;
							}
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
$cssp->register_plugin('before_glue', 0, 'backgroundgradient');


?>
