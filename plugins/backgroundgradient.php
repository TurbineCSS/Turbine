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
	$urlregex = '/linear-gradient\s*\(\s*(top|left)\s*,\s*(#[0-9A-F]+|rgba*\([0-9,]+\))\s*,\s*(#[0-9A-F]+|rgba*\([0-9,]+\))\s*\)/i';
	// In which properties to search
	$urlproperties = array('background', 'background-image');
	// Loop through the array
	foreach($parsed as $block => $css){
		foreach($parsed[$block] as $selector => $styles){
			foreach($urlproperties as $property){
				if(isset($parsed[$block][$selector][$property])){
					$num_values = count($parsed[$block][$selector][$property]);
					for($i = 0; $i < $num_values; $i++){
						if(preg_match($urlregex, $parsed[$block][$selector][$property][$i], $matches) > 0){

							// For all non-ie browsers, sniff the engine and use the appropriate syntax/hack
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
									$svg_params .= '&startcolor='.str_replace('#','%23',strtolower($matches[2]));
									$svg_params .= '&endcolor='.str_replace('#','%23',strtolower($matches[3]));
									$parsed[$block][$selector][$property][$i] = preg_replace(
										$urlregex,
										'url('.$svg_path.'?'.$svg_params.')',
										$parsed[$block][$selector][$property][$i]
									);
									CSSP::comment($parsed[$block][$selector], $property, 'Modified by background-gradient plugin');
								break;

							} // End switch

							// Use filter fallbacks in IE
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
							// TODO: Convert from HSL colors. See colormodels for converting algorithm
							// TODO: Convert from HSLA colors. See colormodels for converting algorithm
							$filter = 'progid:DXImageTransform.Microsoft.gradient(startColorstr='.$matches[2].',endColorstr='.$matches[3].',gradientType='.$ie_gradienttype.')';
							// Legacy IE compliance
							$filter_properties['filter'] = array($filter);
							// IE8 compliance
							$filter_properties['-ms-filter'] = array($filter);
							// Salvage any background information that is NOT gradient stuff and preserve it in a form IE can handle
							$background_rest = preg_replace(
								'/(-moz-|-webkit-)*linear-gradient\s*\(.*?\)/i',
								'',
								$parsed[$block][$selector][$property][$i]
							);
							$background_rest = trim($background_rest);
							if($background_rest != ''){
								$filter_properties['*background'] = array($background_rest);            // IE7 and 6
								$filter_properties['background /*\**/'] = array($background_rest.'\9'); // IE8
							}
							// hasLayout stuff
							$filter_properties['zoom'] = array('1');
							// Insert all
							$cssp->insert_properties($filter_properties, $block, $selector, NULL, $property);
							foreach($filter_properties as $filter_property => $filter_value){
								CSSP::comment($parsed[$block][$selector], $filter_property, 'Added by background-gradient plugin');
							}

						}
						else{
							// This is fucking ugly, but must be done to keep thing sane for css developers. In order to have background
							// declarations that don't contain any gradient _remove_ gradients that the element may have inherited
							// we have to explicitly add disabled filters for IE - always. So in the end what this does is adding three
							// lines of css code for every background property as long as it doesn't add a gradient. Talk about bloated
							// code... but hey, blame the IE team for their fucked up browsers, not us...
							$filter_properties = array();
							$filter = 'progid:DXImageTransform.Microsoft.gradient(enabled:false)';
							$filter_properties['zoom'] = array('1');
							$filter_properties['filter'] = array($filter);
							$filter_properties['-ms-filter'] = array($filter);
							$cssp->insert_properties($filter_properties, $block, $selector, NULL, $property);
							foreach($filter_properties as $filter_property => $filter_value){
								CSSP::comment($parsed[$block][$selector], $filter_property, 'Added by background-gradient plugin');
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
