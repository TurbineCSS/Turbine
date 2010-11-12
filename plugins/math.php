<?php

/**
 * This file is part of Turbine
 * http://github.com/SirPepe/Turbine
 * 
 * Copyright Peter Kröner
 * Licensed under GNU LGPL 3, see license.txt or http://www.gnu.org/licenses/
 */
 
 /**
 * Math plugin created by Raymond Geerts
 * Calculates values of same unit
 * 
 * Usage:   Any property that can contain measurement units 
 * 			will be able to calculate its final value from
 * 			units of the same type.
 * Example: width: 100px+150px
 * Example: height: 15px / 3 * 2
 * Example: margin: 2px+2px 1em+0.5em 2px+2px 1px+5px
 * Example: padding: 6px+3px (-1px) 5px (-1px)
 * Example: background-positon: 12px * 2 12px / 2
 * Status:  Stable
 * Version: 1.1
 * 
 * @param mixed &$parsed
 * @return void
 */
 
function math(&$parsed){
 	global $cssp;
 	$units = array('%', 'in', 'cm', 'mm', 'em', 'ex', 'pt', 'pc', 'px');
 	
 	$unitspattern = '/('.implode("|", $units).')/i';
	
 	$properties = array(
 		/* Background -> xpos ypos */
		'background', 'background-position', 
		/* Border -> length */
		'border', 'border-width', 'border-bottom', 'border-bottom-width', 'border-left', 'border-left-width', 'border-right', 'border-right-width', 'border-top', 'border-top-width', 'border-spacing', 
		/* Dimension -> length */
		'height', 'line-height', 'max-height', 'min-height', 'width', 'max-width', 'min-width', 
		/* Font -> length */
		'font', 'font-size', 
		/* List -> length */
		'marker-offset', 
		/* Margin -> length */
		'margin', 'margin-bottom', 'margin-left', 'margin-right', 'margin-top', 
		/* Outline -> length */
		'outline', 'outline-width',
		/* Padding -> length */
		'padding', 'padding-bottom', 'padding-left', 'padding-right', 'padding-top',
		/* Positioning -> length */
		'bottom', 'left', 'right', 'top',
		/* Text -> length */
		'letter-spacing', 'text-indent', 'word-spacing'
	 );
	// For every possible property...
	foreach($properties as $search){
		// ... loop through the css...
		foreach($parsed as $block => $css){
			foreach($parsed[$block] as $selector => $styles){
				if($selector != '@turbine' && isset($parsed[$block][$selector][$search])){
					$num_values = count($parsed[$block][$selector][$search]);
					// ... loop through the values
					for($i = 0; $i < $num_values; $i++){
						// generate empty array
						$occurences = array();
						if(preg_match('/[\s]*[*\/+-][\s]*/',$parsed[$block][$selector][$search][$i])){
							// remove spaces inbetween values to calculate
							$parsed[$block][$selector][$search][$i] = preg_replace('/[\s]*([*\/+-])[\s]*/','$1',$parsed[$block][$selector][$search][$i]);
							// put each part with its own key in an array
							$occurences = preg_split('/[\s]+/',$parsed[$block][$selector][$search][$i]);
							// generate empty array's
							$unit = array();
							$unitcheck = array();
							foreach($occurences AS $key=>$occurence){
								$count = 0;
								if (preg_match_all($unitspattern, $occurence, $matches, PREG_PATTERN_ORDER)){
									$count = count(array_unique($matches[0]));
									$unitcheck[$key] = $count;
									$unit[$key] = ($count<2)?$matches[0][0]:null;
								}
								else
								{
									$unitcheck[$key] = '0';
									$unit[$key] = null;
								}
							}
							$comment = null;
							$output = array();
							foreach($occurences AS $key=>$occurence){
								if ($unitcheck[$key]==0) {
									$output[$key] = $occurence;
								}
								else if ($unitcheck[$key]==1) {
									$occurence = preg_replace($unitspattern,'',$occurence);
									$occurence = preg_replace('/[$a-zA-Z_]/','',$occurence);
									$output[$key] = @eval("return (" . $occurence . ");").$unit[$key];
								}
								else {
									$output[$key] = $occurence;
									$unique = array();
									foreach($unit AS $u) {
										if($u) $unique[] = $u;
									}
									$comment = 'math plugin: incompatible units '.implode(', ', array_unique($unique));
								}
							}
						
							$cssp->parsed[$block][$selector][$search][0] = implode(' ', $output);
							
							if(!$comment){
								CSSP::comment(
									$cssp->parsed[$block][$selector], 
									$search, 
									'math plugin: calcutated value'
								);
							}
							else {
								CSSP::comment(
									$cssp->parsed[$block][$selector], 
									$search, 
									$comment
								);
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
$cssp->register_plugin('before_glue', 0, 'math');


?>