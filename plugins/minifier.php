<?php

/**
 * This file is part of Turbine
 * http://github.com/SirPepe/Turbine
 * 
 * Copyright Peter Kröner
 * Licensed under GNU LGPL 3, see license.txt or http://www.gnu.org/licenses/
 */


/**
 * Minifier
 * Performs a number of micro-optimizations
 * 
 * Usage:   Nobrainer, just switch it on
 * 
 * Status:  Stable
 * Version: 1.3
 * @param mixed &$parsed
 * @return void
 */
function minifier(&$parsed){
	global $browser, $cssp;
	// For shortening colors
	$color_shortable_pattern = '/\#([A-F0-9])\1([A-F0-9])\2([A-F0-9])\3\b/i';
	$color_properties = array(
		'color',
		'background',
		'background-color',
		'border',
		'border-color',
		'border-top',
		'border-left',
		'border-bottom',
		'border-right',
		'box-shadow'
	);
	// Named colors with a length shorter than their hex equivalent
	$color_pattern = '/(?:^|\b)(#[A-F0-9]{6})(?:$|\b)/i';
	$color_names = array(
		'#CD853F' => 'peru',
		'#FFD700' => 'gold',
		'#D2B48C' => 'tan',
		'#FFC0CB' => 'pink',
		'#DDA0DD' => 'plum',
		'#000080' => 'navy',
		'#008080' => 'teal',
		'#808000' => 'olive',
		'#008000' => 'green',
		'#FFFAFA' => 'snow',
		'#F0FFFF' => 'azure',
		'#FFFFF0' => 'ivory',
		'#FAF0E6' => 'linen',
		'#F5F5DC' => 'beige',
		'#F5DEB3' => 'wheat',
		'#808080' => 'gray'
	);
	// Comma-sepparated properties
	$tokenized_properties = array(
		'font-family'
	);
	// Optimize zeros and float values
	$float_pattern = '/((\b|-)0(\.[0-9]*)(em|ex|px|in|cm|mm|pt|pc))\b/';
	$zero_pattern = '/\b(0(?:em|ex|px|in|cm|mm|pt|pc))\b/';
	$zero_properties = array(
		'margin', 'margin-top', 'margin-left', 'margin-bottom', 'margin-right',
		'padding', 'padding-top', 'padding-left', 'padding-bottom', 'padding-right',
		'border', 'border-top', 'border-left', 'border-bottom', 'border-right'
	);
	foreach($parsed as $block => $css){
		foreach($parsed[$block] as $selector => $styles){
			// Ignore @font-face
			if($selector != '@font-face'){
				foreach($parsed[$block][$selector] as $property => $values){
					foreach($parsed[$block][$selector][$property] as $key => $value){
						// Optimize hex colors
						if(in_array($property, $color_properties)){
							if(preg_match($color_shortable_pattern, $value)){
								$parsed[$block][$selector][$property][$key] = preg_replace($color_shortable_pattern, '#\1\2\3', $value);
							}
							elseif(preg_match($color_pattern, $value, $color_matches)){
								if(array_key_exists($color_matches[1], $color_names)){
									$parsed[$block][$selector][$property][$key] = preg_replace($color_pattern, $color_names[$color_matches[1]], $value);
								}
							}
						}
						// Optimize tokenized strings
						if(in_array($property, $tokenized_properties)){
							$parsed[$block][$selector][$property][$key] = implode(',', $cssp->tokenize($value, ','));
						}
						// Optimize zeros and floats
						if(in_array($property, $zero_properties)){
							$parsed[$block][$selector][$property][$key] = preg_replace($zero_pattern, '0', $value);
							$parsed[$block][$selector][$property][$key] = preg_replace($float_pattern, '\2\3\4', $parsed[$block][$selector][$property][$key]);
						}
						// Shorten long margins and paddings
						if($property == 'margin' || $property == 'padding'){
							preg_match_all('/((?:\b|-)[0-9.]*(?:em|ex|px|in|cm|mm|pt|pc)?\b)/', $value, $matches);
							// Filter out empty values
							$matches[0] = array_values(array_filter($matches[0], create_function('$element', 'return strlen($element) > 0;')));
							if(count($matches[0]) == 2){
								if($matches[0][0] == $matches[0][1]){
									$parsed[$block][$selector][$property][$key] = $matches[0][0];
								}
							}
							elseif(count($matches[0]) == 4){
								if($matches[0][0] == $matches[0][1] && $matches[0][0] == $matches[0][2] && $matches[0][0] == $matches[0][3]){
									$parsed[$block][$selector][$property][$key] = $matches[0][0];
								}
								elseif($matches[0][0] == $matches[0][2] && $matches[0][1] == $matches[0][3]){
									$parsed[$block][$selector][$property][$key] = $matches[0][0].' '.$matches[0][1];
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
$cssp->register_plugin('before_glue', 0, 'minifier');


?>
