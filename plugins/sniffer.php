<?php

/**
 * This file is part of Turbine
 * http://github.com/SirPepe/Turbine
 * 
 * Copyright Peter Kröner
 * Licensed under GNU LGPL 3, see license.txt or http://www.gnu.org/licenses/
 */


/**
 * Sniffner
 * Browser and platform sniffer
 * 
 * Usage: Complicated, see docs
 * Example: -
 * Status:  Probably unstable as hell
 * Version: ?
 * 
 * sniffer
 * Main plugin function
 * @param mixed &$parsed
 * @return void
 */
function sniffer(&$parsed){
	// Look for a browser rule in @turbine
	if(sniffer_apply_rules($parsed['global']['@turbine']) === false){
		// Empty $parsed on mismatch
		$parsed = array();
		// Kill off all other plugins
		global $plugin_list;
		$plugin_list = array();
	}
	// Loop through the blocks
	foreach($parsed as $block => $css){
		foreach($parsed[$block] as $selector => $styles){
			// Loop through @font-face
			if($selector == '@font-face'){
				foreach($styles as $fontindex => $style){
					if(sniffer_apply_rules($parsed[$block]['@font-face'][$fontindex]) === false){
						unset($parsed[$block]['@font-face'][$fontindex]);
					}
					else{
						$parsed[$block]['@font-face'][$fontindex] = sniffer_cleanup($parsed[$block]['@font-face'][$fontindex]);
					}
				}
			}
			// Process the rest
			else{
				if(sniffer_apply_rules($parsed[$block][$selector]) === false){
					unset($parsed[$block][$selector]);
				}
				else{
					$parsed[$block][$selector] = sniffer_cleanup($parsed[$block][$selector]);
				}
			}
		}
	}
}


/**
 * sniffer_apply_rules
 * Looks for sniffer properties and parses them
 * @param mixed $element
 * @return bool $result
 */
function sniffer_apply_rules($element){
	global $cssp;
	$result = true;
	// Get rules
	$sniffer_properties = array('browser', 'engine', 'device', 'os');
	foreach($element as $property => $values){
		if(in_array($property, $sniffer_properties)){
			$value = $cssp->get_final_value($values);
			$rules = preg_split('/\s+/', $value);
			$result = call_user_func('sniffer_parse_'.$property, $rules);
		}
	}
	return $result;
}


/**
 * sniffer_parse_browser
 * Parses browser rules
 * @param array $rules
 * @return bool $match
 */
function sniffer_parse_browser($rules){
	global $browser;
	$match = false;
	foreach($rules as $rule){
		preg_match('/([\^]?)([a-z\-_0-9]+)([!=><]{0,2})([0-9]*\.?[0-9]*]*)/i', $rule, $matches);
		// If the useragent's detected browser is found in the current rule
		if(strstr(strtolower($matches[2]), $browser->browser)){
			// For the time being set $submatch to true
			$submatch = true;
			// If we found a logical operator and a version number
			if($matches[3] != '' && $matches[4] == floatval($matches[4])){
				// Turn a single =-operator into a PHP-interpretable ==-operator
				if($matches[3] == '='){
					$matches[3] = '==';
				}
				// Filter and run the detected rule through the PHP interpreter
				eval('if('.floatval($browser->browser_version).$matches[3].floatval($matches[4]).') $submatch = true; else $submatch = false;');
			}
		}
		else{
			// Set $submatch to false
			$submatch = false;
		}
		// Check if we had a negating operator at the beginning and in case flip result
		if($matches[1] == '^'){
			$submatch = ($submatch == true) ? false : true;
		}
		// Check the final state of $submatch and set $match only to true if $submatch is true
		if($submatch){
			$match = true;
		}
	}
	return $match;
}


/**
 * sniffer_parse_engine
 * Parses engine rules
 * @param array $rules
 * @return bool $match
 */
function sniffer_parse_engine($rules){
	global $browser;
	$match = false;
	foreach($rules as $rule){
		preg_match('/([\^]?)([a-z\-_0-9]+)([!=><]{0,2})([0-9]*\.?[0-9]*]*)/i', $rule, $matches);
		// If the useragent's detected engine is found in the current rule
		if(strstr(strtolower($matches[2]), $browser->engine)){
			// For the time being set $submatch to true
			$submatch = true;
			// If we found a logical operator and a version number
			if($matches[3] != '' && $matches[4] == floatval($matches[4])){
				// Turn a single =-operator into a PHP-interpretable ==-operator
				if($matches[3] == '='){
					$matches[3] = '==';
				}
				// Filter and run the detected rule through the PHP interpreter
				eval('if('.floatval($browser->engine_version).$matches[3].floatval($matches[4]).') $submatch = true; else $submatch = false;');
			}
		}
		else{
			// Set $submatch to false
			$submatch = false;
		}
		// Check if we had a negating operator at the beginning and in case flip result
		if($matches[1] == '^'){
			$submatch = ($submatch == true) ? false : true;
		}
		// Check the final state of $submatch and set $match only to true if $submatch is true
		if($submatch){
			$match = true;
		}
	}
	return $match;
}


/**
 * sniffer_parse_device
 * Parses device rules
 * @param array $rules
 * @return bool $match
 */
function sniffer_parse_device($rules){
	global $browser;
	$match = false;
	foreach($rules as $rule){
		if(preg_match('/'.$browser->platform_type.'/', strtolower($rule))){
			$submatch = true;
		}
		else{
			$submatch = false;
		}
		// Check if we had a negating operator at the beginning and in case flip result
		if($rule{0} == '^'){
			$submatch = ($submatch) ? false : true;
		}
		// Check the final state of $submatch and set $match only to true if $submatch is true
		if($submatch){
			$match = true;
		}
	}
	return $match;
}


/**
 * sniffer_parse_os
 * Parses os rules
 * @param array $rules
 * @return bool $match
 */
function sniffer_parse_os($rules){
	global $browser;
	$match = false;
	// Prepare special array for Windows name to version mapping
	$os_windowsnames = array();
	$os_windowsnames['95'] = 4.0;
	$os_windowsnames['nt4'] = 4.0;
	$os_windowsnames['98'] = 4.1;
	$os_windowsnames['me'] = 4.9;
	$os_windowsnames['2000'] = $os_windowsnames['2k'] = 5.0;
	$os_windowsnames['xp'] = 5.1;
	$os_windowsnames['2003'] = $os_windowsnames['2k3'] = 5.2;
	$os_windowsnames['vista'] = 6.0;
	$os_windowsnames['windows7'] = $os_windowsnames['win7'] = $os_windowsnames['7'] = 6.1;
	foreach($rules as $rule){
		preg_match('/([\^]?)([a-z\-_0-9]+)([!=><]{0,2})([a-z0-9]*\.?[0-9]*]*)/i', $rule, $matches);
		// If the useragent's detected os/platform is found in the current rule
		if(strstr(strtolower($matches[2]), $browser->platform)){
			// For the time being set $submatch to true
			$submatch = true;
			// If we found a logical operator and a version number
			if($matches[3] != '' && (isset($os_windowsnames[$matches[4]]) || $matches[4] == floatval($matches[4]))){
				// Turn a single =-operator into a PHP-interpretable ==-operator
				if($matches[3] == '='){
					$matches[3] = '==';
				}
				// Check for Windows name mapping and apply
				if(isset($os_windowsnames[$matches[4]])){
					$matches[4] = $os_windowsnames[$matches[4]];
				}
				// Filter and run the detected rule through the PHP interpreter
				eval('if('.floatval($browser->platform_version).$matches[3].floatval($matches[4]).') $submatch = true; else $submatch = false;');
			}
		}
		else{
			// Set $submatch to false
			$submatch = false;
		}
		// Check if we had a negating operator at the beginning and in case flip result
		if($matches[1] == '^'){
			$submatch = ($submatch == true) ? false : true;
		}
		// Check the final state of $submatch and set $match only to true if $submatch is true
		if($submatch){
			$match = true;
		}
	}
	return $match;
}


/**
 * sniffer_cleanup
 * Removes any remaining sniffer properties
 * @param array $element The Element to clean up
 * @return array $element
 */
function sniffer_cleanup($element){
	$sniffer_properties = array('browser', 'engine', 'device', 'os');
	foreach($sniffer_properties as $property){
		unset($element[$property]);
	}
	return $element;
}


/**
 * Register the plugin
 */
$cssp->register_plugin('before_compile', 1000, 'sniffer');


?>
