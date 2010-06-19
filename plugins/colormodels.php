<?php

/**
 * This file is part of Turbine
 * http://github.com/SirPepe/Turbine
 * 
 * Copyright Peter Kröner
 * Licensed under GNU LGPL 3, see license.txt or http://www.gnu.org/licenses/
 */


/**
 * Colormodels
 * HSL(A) and RGBA support for most older browsers
 * 
 * Usage:   Nobrainer, just switch it on
 * Example: -
 * Status:  Beta
 * Version: 1.0
 * 
 * @param mixed &$parsed
 * @return void
 */
function colormodels(&$parsed){
	$models = array('rgba', 'hsla', 'hsl');
	$hslapattern = '/(hsl(?:a)?)\([\s]*(.*?)[\s]*,[\s]*(.*?)%[\s]*,[\s]*(.*?)%[\s]*(:?,[\s]*(.*?)[\s]*)?\)/i';
	$rgbapattern = '/(rgba)\([\s]*(.*?)[\s]*,[\s]*(.*?)[\s]*,[\s]*(.*?)[\s]*,[\s]*(.*?)[\s]*\)/i';
	$properties = array('background', 'background-color', 'color', 'border', 'border-color', 'border-top', 'border-left', 'border-bottom', 'border-right', 'text-shadow', 'box-shadow');
	$capabilities = colormodels_get_browser_capabilities();
	// Only continue if we can be sure to know anything about the browser's capabilities
	if(!empty($capabilities)){
		// For every possible property...
		foreach($properties as $search){
			// ... loop through the css...
			foreach($parsed as $block => $css){
				foreach($parsed[$block] as $selector => $styles){
					if($selector != '@turbine' && isset($parsed[$block][$selector][$search])){
						$num_values = count($parsed[$block][$selector][$search]);
						// ... loop through the values
						for($i = 0; $i < $num_values; $i++){
							// Found something that we may have to replace?
							if(preg_match($hslapattern, $parsed[$block][$selector][$search][$i], $matches) || preg_match($rgbapattern, $parsed[$block][$selector][$search][$i], $matches)){
								// See if the browser supports the color model, convert if not
								$rgba = colormodels_to_rgba($matches);
								foreach($models as $model){
									if($model == $matches[1]){
										if(!isset($capabilities[$model])){
											$recalculated = colormodels_recalculate($model, $rgba, $capabilities, $search);
											if(!empty($recalculated)){
												// Apply the recalculated properties and values to $parsed
												foreach($recalculated as $property => $value){
													if(!isset($parsed[$block][$selector][$property])){
														$parsed[$block][$selector][$property][$i] = $value;
														CSSP::comment($parsed[$block][$selector], $property, 'Added by colormodels plugin');
													}
													else{
														$parsed[$block][$selector][$property][$i] = str_replace($matches[0], $value, $parsed[$block][$selector][$property][$i]);
														CSSP::comment($parsed[$block][$selector], $property, 'Modified by colormodels plugin');
													}
												}
											}
										}
									}
								}
							}
						}
					}
				}
			}
		}
	}
}


/*
 * colormodels_recalculate
 * Tries to return the best possible fallback for browsers not campatible with $model
 * @param string $model Color Model. Can be "rgba", "hsla" or "hsl"
 * @param array $rgba Contains the RGBA color values
 * @param array $capabilities Browser color model support
 * @param string $property The current css property being processed
 */
function colormodels_recalculate($model, $rgba, $capabilities, $property){
	$recalculated = array();
	switch($model){
		case 'rgba':
			// If this is a background and we can use filters, do this. Fall back to solid RGB otherwise
			if(($property == 'background' || $property == 'background-color') && isset($capabilities['filter'])){
				$filteropacity = strtoupper(str_pad(dechex(round(floatval($rgba['a']) * 255)),2,'0',STR_PAD_LEFT));
				$filtercolor_r = strtoupper(str_pad(dechex(floatval($rgba['r'])),2,'0',STR_PAD_LEFT));
				$filtercolor_g = strtoupper(str_pad(dechex(floatval($rgba['g'])),2,'0',STR_PAD_LEFT));
				$filtercolor_b = strtoupper(str_pad(dechex(floatval($rgba['b'])),2,'0',STR_PAD_LEFT));
				$filter = 'progid:DXImageTransform.Microsoft.gradient(startColorstr=#'.$filteropacity.$filtercolor_r.$filtercolor_g.$filtercolor_b.',endColorstr=#'.$filteropacity.$filtercolor_r.$filtercolor_g.$filtercolor_b.')';
				$recalculated['background'] = 'none';
				$recalculated['filter'] = $filter;
				$recalculated['zoom'] = '1';
			}
			else{
				$recalculated = colormodels_recalculate('hsl', $rgba, $capabilities, $property);
			}
		break;
		case 'hsla':
			// No HSLA? Try RGBA instad
			if(isset($capabilities['rgba'])){
				$recalculated[$property] = 'rgba('.$rgba['r'].', '.$rgba['g'].', '.$rgba['b'].', '.$rgba['a'].')';
			}
			else{
				$recalculated = colormodels_recalculate('rgba', $rgba, $capabilities, $property);
			}
		break;
		case 'hsl':
			// Recalulate HSL to RGB
			$recalculated[$property] = 'rgb('.$rgba['r'].', '.$rgba['g'].', '.$rgba['b'].')';
		break;
	}
	return $recalculated;
}


/*
 * colormodels_get_browser_capabilities
 * Find out which color models can be used in the current browser
 * @return array $capabilities A list of possible color models
 */
function colormodels_get_browser_capabilities(){
	global $browser;
	$capabilities = array();
	if($browser->engine == 'ie'){
		$capabilities['filter'] = true;
	}
	elseif($browser->engine == 'gecko'){
		$capabilities['rgba'] = true;
		if($browser->engine_version >= 1.81){
			$capabilities['hsl'] = true;
			if($browser->engine_version >= 1.9){
				$capabilities['hsla'] = true;
			}
		}
	}
	elseif($browser->engine == 'webkit'){
		$capabilities['rgba'] = true;
		if($browser->engine_version >= 522.11){
			$capabilities['hsl'] = true;
			$capabilities['hsla'] = true;
		}
	}
	elseif($browser->engine == 'opera'){
		$capabilities['rgba'] = true;
		if($browser->engine_version >= 10){
			$capabilities['hsl'] = true;
			$capabilities['hsla'] = true;
		}
	}
	return $capabilities;
}


/*
 * colormodels_to_rgba
 * Convert anything into integer-style RGBA
 * @param array $input
 * @return array $output
 */
function colormodels_to_rgba($input){
	$output = array();
	switch($input[1]){
		// Convert percentage-style RGBA values to integer
		case 'rgba':
			if(strpos($input[2], '%')){
				$output['r'] = floor(255/100*$input[2]);
				$output['g'] = floor(255/100*$input[3]);
				$output['b'] = floor(255/100*$input[4]);
				$output['a'] = array_pop($input);
			}
			else{
				$output['r'] = $input[2];
				$output['g'] = $input[3];
				$output['b'] = $input[4];
				$output['a'] = array_pop($input);
			}
			break;
		// Convert HSLA values to RGBA
		case 'hsla':
			$output = colormodels_hsl_to_rgb($input[2], $input[3], $input[4]);
			$output['a'] = floatval(array_pop($input));
			break;
		// Convert HSL values to RGBA
		case 'hsl':
			$output = colormodels_hsl_to_rgb($input[2], $input[3], $input[4]);
			$output['a'] = 0;
			break;
	}
	return $output;
}


/*
 * colormodels_hsl_to_rgb
 * Transforms HSL to RGB
 * Stolen from here: http://monc.se/kitchen/119/working-with-hsl-in-css
 * @param mixed $h Hue
 * @param mixed $s Saturation
 * @param mixed $l Lightness
 * @return array $rgb The RGB value
 */
function colormodels_hsl_to_rgb($h, $s, $l){
	$rgb = array();
	$h = intval($h)/360;
	$s = intval($s)/100;
	$l = intval($l)/100;
	if($s == 0.0){
		$r = $g = $b = $l;
	}
	else{
		if($l<=0.5){
			$m2 = $l*($s+1);
		}
		else{
			$m2 = $l+$s-($l*$s);
		}
		$m1 = $l*2 - $m2;
		$rgb['r'] = floor(colormodels_hue($m1, $m2, ($h+1/3))*255);
		$rgb['g'] = floor(colormodels_hue($m1, $m2, $h)*255);
		$rgb['b'] = floor(colormodels_hue($m1, $m2, ($h-1/3))*255);
	}
	return $rgb;
}


/*
 * colormodels_hue
 * Applies hue value
 * Stolen from here: http://monc.se/kitchen/119/working-with-hsl-in-css
 * @param float $m1
 * @param float $m2
 * @param float $h
 * @return float $m1
 */
function colormodels_hue($m1, $m2, $h){
	if($h < 0){ $h = $h+1; }
	if ($h > 1){ $h = $h-1; }
	if ($h*6 < 1){ return $m1+($m2-$m1)*$h*6; }
	if ($h*2 < 1){ return $m2; }
	if ($h*3 < 2){ return $m1+($m2-$m1)*(2/3-$h)*6; }
	return $m1;
}


/**
 * Register the plugin
 */
$cssp->register_plugin('before_glue', -100, 'colormodels');


?>
