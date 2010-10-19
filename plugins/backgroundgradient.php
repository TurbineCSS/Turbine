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
 * Status:    Stable
 * Version:   2.0
 * 
 * 
 * backgroundgradient
 * @param mixed &$parsed
 * @return void
 */
function backgroundgradient(&$parsed){
	global $cssp, $browser;
	include('lib/utility.php');
	$settings = Plugin::get_settings('backgroundgradient');
	// Searches for W3C-style two-stepped linear gradient
	$gradientregex = '/linear-gradient\s*?\(\s*?(top|left)\s*?,\s*(\#[0-9A-F]+|(?:rgb|hsl)(?:a)*\s*\(.+\)),\s*(.*)\s*\)/i';
	// In which properties to search
	$urlproperties = array('background', 'background-image');
	// Loop through the array
	foreach($parsed as $block => $css){
		foreach($parsed[$block] as $selector => $styles){
			foreach($urlproperties as $property){
				if(isset($parsed[$block][$selector][$property])){
					$num_values = count($parsed[$block][$selector][$property]);
					for($i = 0; $i < $num_values; $i++){
						if(preg_match($gradientregex, $parsed[$block][$selector][$property][$i], $matches) > 0){
							// Recalculate the matched colors to rgba for maximum compatibility
							$matches[2] = Utility::rgbasyntax(Utility::any2rgba($matches[2]));
							$matches[3] = Utility::rgbasyntax(Utility::any2rgba($matches[3]));

							// Gecko
							$parsed[$block][$selector][$property][] = preg_replace(
								$gradientregex,
								'-moz-linear-gradient('.$matches[1].','.$matches[2].','.$matches[3].')',
								$parsed[$block][$selector][$property][$i]
							);

							// Webkit and KHTML
							if(strtolower($matches[1]) == 'top'){
								$webkit_gradientdirection = 'left top,left bottom';
							}
							else{
								$webkit_gradientdirection = 'left top,right top';
							}
							$parsed[$block][$selector][$property][] = preg_replace(
								$gradientregex,
								'-webkit-gradient(linear,'.$webkit_gradientdirection.',from('.$matches[2].'),to('.$matches[3].'))',
								$parsed[$block][$selector][$property][$i]
							);
							$parsed[$block][$selector][$property][] = preg_replace(
								$gradientregex,
								'-khtml-gradient(linear,'.$webkit_gradientdirection.',from('.$matches[2].'),to('.$matches[3].'))',
								$parsed[$block][$selector][$property][$i]
							);

							// Use a SVG background for Opera
							if($browser->engine == 'opera'){
								$svg_path = rtrim(dirname($_SERVER['SCRIPT_NAME']),'/').'/plugins/backgroundgradient/svg.php';
								$svg_params = 'direction='.strtolower($matches[1]);
								$svg_params .= '&startcolor='.urlencode($matches[2]);
								$svg_params .= '&endcolor='.urlencode($matches[3]);
								$parsed[$block][$selector][$property][] = preg_replace(
									$gradientregex,
									'url('.$svg_path.'?'.$svg_params.')',
									$parsed[$block][$selector][$property][$i]
								);
							}

							// Add comment for the background property
							CSSP::comment($parsed[$block][$selector], $property, 'Modified by background-gradient plugin');

							// Use filter fallbacks in IE
							if(!in_array('noie', $settings)){
								$filter_properties = array();
								if(strtolower($matches[1]) == 'top'){
									$ie_gradienttype = '0';
								}
								else{
									$ie_gradienttype = '1';
								}
								// Convert colors to hex
								$matches[2] = Utility::hexsyntax(Utility::any2rgba($matches[2]), true);
								$matches[3] = Utility::hexsyntax(Utility::any2rgba($matches[3]), true);
								// Build filter
								$filter = 'progid:DXImageTransform.Microsoft.gradient(startColorstr='.$matches[2].',endColorstr='.$matches[3].',gradientType='.$ie_gradienttype.')';
								// Legacy IE compliance
								$filter_properties['filter'] = array($filter);
								// IE8 compliance
								$filter_properties['-ms-filter'] = array($filter);
								// Salvage any background information that is NOT gradient stuff and preserve it in a form IE can handle
								$background_rest = preg_replace(
									'/(-moz-|-webkit-|-khtml-)*linear-gradient\s*?\(\s*?(top|left)\s*?,\s*(\#[0-9A-F]+|(?:rgb|hsl)(?:a)*\s*\(.+\)),\s*(.*)\s*\)/i',
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

								// Insert all filters
								$cssp->insert_properties($filter_properties, $block, $selector, NULL, $property);
								foreach($filter_properties as $filter_property => $filter_value){
									CSSP::comment($parsed[$block][$selector], $filter_property, 'Modified by background-gradient plugin');
								}
							} // End if(!in_array('noie', $settings))

						// Remove the original value
						unset($parsed[$block][$selector][$property][$i]);

						}
						else{
							/*.............................=MMMMMMMMMMMMMMMM,...............................
							............................ MMMMMMMMMMMMMMMMMMMMMM.............................
							...........................MMMMMMMMMMMMMMMMMMMMMMMMMM...   . . .... .. .. .. .. 
							................. ... ...IMMMMMMMMMMMMMMMMMMMMMMMMMMMM     . . ....  . ..  .  . 
							............  ....   ...8MMMMMMMMMMMMMMMMMMMMMMMMMMMMMM.  . .          . . .    
							............   ...  ...NMMMM.MMMMMMMMMMMMMMMMMMMMMMMMMMM       .. .    ..  .    
							............ ..........MMMM.IMMMMMMMMMMMMMMMMMMMMMMMMMMM ........ . .  ..... .. 
							............    .     =MMM..MMMMMMMMMMMMMMMMMMMMMMMMMMMMM      .. .  . ..  .  . 
							............ . ..     ZMM:.MMMMMMMMMMMMMMMMMMMMMMMMMMMMMM. . ...... .. ..... ...
							............    .     ZMM.=MMMMMMMMMMMMMMMMMMMMMMMMMMMMMM         .    ..  .    
							...........  . ..     ZMM.MMMMMMMMMMMMMMMMMMMMMMMMMMMMMMM       ...    ... .    
							............ .  ..   .ZMM MMMM .....MMMMMMM .....MMMMMMMM ...  .... .  ... . . .
							............   ..     ~MM.MMM........MMMMM .. .  .MMM MM8    . .. .  . ..  .    
							............    .      MM.MM8........MMMMM .....  MMM MM. . .  .. .    ... . .. 
							 . ... .   .............M.MM+    .  ZMMMMMM  . .  7MM M=  . .  .....     . . .. 
							...         .. .........  MMN    ..8MMM MMMM ...  MMM..   . . ... .      .   .. 
							.... .    ............. MMMMMD  .MMMMM..MMMMMO  ?MMMMM$ ........................
							........  .............MMMMMMMMMMMMMMM. .MMMMMMMMMMMMMMM..................... . 
							...  . .. ..............MMMMMMMMMMMMM....MMMMMMMMMMMMMM,........................
							..........................MMMMMMMMMMM.NM.MMMMMMMMMMMM.... . .  ..... ..  .. ... 
							.... . ..  ................ .MMMMMMMMMMMMMMMMMMMMM. ............................
							.... ................... ... . :MMMMMMMMMMMMMMMM .......... ........ ..... .....
							.... . .  ................  MZ  ,MMMMMMMMMMMMM8. $M............. ..... ..... .. 
							...... . +M=................MMM .. . .   .    ..MMM ............................
							.... . MMMMMM............ . MMM$ .M=DM=M.M M.. MMMM ....  ... ......MM~. ... ...
							 . . 7MMMMMMMM$......... .. MMMM .. :,.  ,.. ..MMMM ..    . .  . .MMMMMMM. . .. 
							...MMMMMMMMMMMMD........... MMMM . ... . ..  .ZMMM~...... . .  . MMMMMMMMM . ...
							.MMMMMMMMMMMMMMMM...........:MMMN.M OO$M..M.. MMMM............. MMMMMMMMMMM.....
							MMMMMMMMMMMMMMMMMMN..... ..  NMMMM  ...M. ? +MMMM+ ...........NMMMMMMMMMMMMMM7..
							MMMMMMMM.MMMMMMMMMMM+.........NMMMMMMMMMMMMMMMMM............:MMMMMMMMMMMMMMMMMD 
							MMMMMMMM .~MMMMMMMMMMMM ... ....MMMMMMMMMMMMMMM ........ .MMMMMMMMMMMMMMMMMMMMM 
							 MMMMMMMMMMMI =MMMMMMMMMMM...... NMMMMMMMMMMMO...  ..  ZMMMMMMMMMMMMMM,MMMMMMMM=
							   ..8MMMMMMMMMMMMMMMMMMMMMMM. ...................  MMMMMMMMMMMMM. ,MMMMMMMMMMM 
							 . .........$MMMMMMMMMMMMMMMMMM8  ............. ~MMMMMMMMMMMMMMMMMMMMMMMMMMM8...
							 . ...........   MMMMMMMMMMMMMMMMM?..........ZMMMMMMMMMMMMMMMMMMMMMI. ......... 
							   . ......... ......MMMMMMMMMMMMMMMM. . .MMMMMMMMMMMMMMMMMMM~........ .........
							....  .....     ... ... =MMMMMMMMMMMMMMM. MMMMMMMMMMMMMMZ  .....................
							 . ........................ NMMMMMMMMMMMMMM.+MMMMMMM+. .........................
							   .  ..        ... .... .. .. DMMMMMMMMMMMMM7.O+...................... ..  ....
							 ........... . ......... ...~MMMN OMMMMMMMMMMMMM,  ....   . ......... .. . . ...
							 ... ................... DMMMMMMMMM, MMMMMMMMMMMMMM........ ............ .......
							.... ................NMMMMMMMMMMMMMMMM. MMMMMMMMMMMMMM..... . .......... . .....
							. MMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMM...... .MMMMMMMMMMMMMMM  ....................
							MMMMMMMMMMMMMMMMMMMMMMMMMMMMMMM. ........... ,MMMMMMMMMMMMMMMMMMMMMMMN8+  ......
							MMMMMDNMMMMMMMMMMMMMMMMMMMMZ.................. .:MMMMMMMMMMMMMMMMMMMMMMMMMMM ...
							MMMMMMMMMMMMMMMMMMMMMMMM  ...................... .. MMMMM.$MMMMMMMMMMMM=MMMMM ..
							 .MMMMMMMDMMMMMMMMMMM ... . .... . ... ..... . ... ..  ZMMMN:MMMMMMMMMMMMMMMM...
							....=MMMM.MMOMMMMMM ..... ... .. ....  .  .. . . .  .... NMMM~7MMMMMMMMMMMMMN...
							.....MMMMMMMMMMM+..........................................MMMM~IMMMMMMMNM, ....
							......MMMMMMMM ............................................. MMMMMZMMMM,........
							 . . .......... ................................ ............. MMMMMMM........*/
							// This is fucking ugly, but must be done to keep things sane for css developers. In order to have background
							// declarations that don't contain any gradient _remove_ gradients that the element may have inherited,
							// we have to explicitly add disabled filters for IE - always. So in the end what this does is adding three
							// lines of css code for every background property as long as it doesn't add a gradient. Talk about bloated
							// code... but hey, blame the IE team for their fucked up browsers, not us...
							if(!in_array('noie', $settings)){
								$filter_properties = array();
								$filter = 'progid:DXImageTransform.Microsoft.gradient(enabled:false)';
								$filter_properties['zoom'] = array('1');
								$filter_properties['filter'] = array($filter);
								$filter_properties['-ms-filter'] = array($filter);
								$cssp->insert_properties($filter_properties, $block, $selector, NULL, $property);
								foreach($filter_properties as $filter_property => $filter_value){
									CSSP::comment($parsed[$block][$selector], $filter_property, 'Modified by background-gradient plugin');
								}
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
