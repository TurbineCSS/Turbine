<?php

	/**
	 * Browser
	 * Implements the "-cssp-browser", "-cssp-engine" and "-cssp-device" properties for browser detection
	 * 
	 * Usage: -cssp-browser:mybrowser myotherbrowser;
	 * Usage: -cssp-engine:myengine myotherengine;
	 * Usage: -cssp-device:mydevice myotherdevice;
	 * 
	 * Example 1: -cssp-browser:firefox; - CSS rules only apply on firefox (Simple detection)
	 * Example 2: -cssp-browser:^firefox; - CSS rules apply everywhere but firefox (Simple exclusion)
	 * Example 3: -cssp-browser:firefox<3.5; - CSS rules only apply on firefox versions older than 3.5 (detection by version number)
	 * Example 4: -cssp-browser:firefox opera; - CSS rules only apply on firefox and opera (Multi-Detection)
	 * 
	 * In the case of contradicting statements, the last defines statement wins, eg -cssp-browser:^opera opera; only applys on
	 * opera ("^opera" is overruled)
	 */


	/**
	 * browser
	 * Main plugin function
	 * 
	 * @param mixed &$parsed
	 * @return void
	 */
	function browser(&$parsed){
		foreach($parsed as $block => $css){
			foreach($parsed[$block] as $selector => $styles){
				// Loop through @font-face
				if($selector == '@font-face'){
					foreach($styles as $index => $style){
						$browserparsed = browser_parse_browser($style);
						$browserparsed = browser_parse_engine($browserparsed);
						$browserparsed = browser_parse_device($browserparsed);
						if($browserparsed){
							$parsed[$block][$selector][$index] = $browserparsed;
						}
						else {
							unset($parsed[$block][$selector][$index]);
						}
					}
				}
				// Parse the rest
				else{
					$browserparsed = browser_parse_browser($styles);
					$browserparsed = browser_parse_engine($browserparsed);
					$browserparsed = browser_parse_device($browserparsed);
					if($browserparsed){
						$parsed[$block][$selector] = $browserparsed;
					}
					else {
						unset($parsed[$block][$selector]);
					}
				}
			}
		}
	}


	/**
	 * browser_parse_browser
	 * Looks for the "-cssp-browser" property in an element and parses it
	 * 
	 * @param object $styles
	 * @return 
	 */
	function browser_parse_browser($styles){
		global $browser;
		$match = true;
		// Find browser property
		if(isset($styles['-cssp-browser'])){
			$match = false;
			// Split up any multiple browser rules in order to check them one by one
			$browserrules = explode(' ', $styles['-cssp-browser']);
			// Check each browser rule
			foreach($browserrules as $browserrule){
				preg_match('/([\^]?)([a-z\-]+)([!=><]{0,2})([0-9]*\.?[0-9]*]*)/i', $browserrule, $matches);
				// If the useragent's detected browser is found in the current rule
				if(strstr(strtolower($matches[2]),strtolower($browser->name)))
				{
					// For the time being set $match to true
					$match = true;
					// If we found a logical operator and a version number
					if($matches[3] != '' && $matches[4] == floatval($matches[4]))
					{
						// Turn a single =-operator into a PHP-interpretable ==-operator
						if($matches[3] == '=') $matches[3] = '==';
						// Filter and run the detected rule through the PHP-interpreter
						eval('if('.floatval($browser->version).$matches[3].floatval($matches[4]).') $match = true; else $match = false;');
					}
				}
				else
				{
					// Set $match to false
					$match = false;
				}
				// Check if we had a negotiating operator at the beginning and in case flip result
				if($matches[1] == '^') $match = ($match == true) ? false : true;
			}
		}
		// Keep the styles, unset browser property
		if($match){
			unset($styles['-cssp-browser']);
			return $styles;
		}
		// Remove the element
		else {
			return false;
		}
	}


	/**
	 * browser_parse_engine
	 * Looks for the "-cssp-engine" property in an element and parses it
	 * 
	 * @param object $styles
	 * @return 
	 */
	function browser_parse_engine($styles){
		global $browser;
		$match = true;
		// Find engine property
		if(isset($styles['-cssp-engine'])){
			$match = false;
			// Split up any multiple engine rules in order to check them one by one
			$browserrules = explode(' ', $styles['-cssp-engine']);
			// Check each engine rule
			foreach($browserrules as $browserrule){
				preg_match('/([\^]?)([a-z\-]+)([!=><]{0,2})([0-9]*\.?[0-9]*]*)/i', $browserrule, $matches);
				// If the useragent's detected engine is found in the current rule
				if(strstr(strtolower($matches[2]),strtolower($browser->engine)))
				{
					// For the time being set $match to true
					$match = true;
					// If we found a logical operator and a version number
					if($matches[3] != '' && $matches[4] == floatval($matches[4]))
					{
						// Turn a single =-operator into a PHP-interpretable ==-operator
						if($matches[3] == '=') $matches[3] = '==';
						// Filter and run the detected rule through the PHP-interpreter
						eval('if('.floatval($browser->engineversion).$matches[3].floatval($matches[4]).') $match = true; else $match = false;');
					}
				}
				else
				{
					// Set $match to false
					$match = false;
				}
				// Check if we had a negotiating operator at the beginning and in case flip result
				if($matches[1] == '^') $match = ($match == true) ? false : true;
			}
		}
		// Keep the styles, unset engine property
		if($match){
			unset($styles['-cssp-engine']);
			return $styles;
		}
		// Remove the element
		else {
			return false;
		}
	}


	/**
	 * browser_parse_device
	 * Looks for the "-cssp-device" property in an element and parses it
	 * 
	 * @param object $styles
	 * @return 
	 */
	function browser_parse_device($styles){
		global $browser;
		$match = true;
		// Find device property
		if(isset($styles['-cssp-device'])){
			$match = false;
			// Split up any multiple device rules in order to check them one by one
			$browserrules = explode(' ', $styles['-cssp-device']);
			// Check each device rule
			foreach($browserrules as $browserrule){
				if(strtolower($browserrule) == strtolower($browser->platformtype)) $match = true;
				else $match = false;
				// Check if we had a negotiating operator at the beginning and in case flip result
				if($matches[1] == '^') $match = ($match == true) ? false : true;
			}
		}
		// Keep the styles, unset device property
		if($match){
			unset($styles['-cssp-device']);
			return $styles;
		}
		// Remove the element
		else {
			return false;
		}
	}
?>