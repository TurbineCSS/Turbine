<?php

	/**
	 * OS
	 * Implements the "-cssp-os" property for operating system detection
	 * 
	 * Usage: -cssp-os:myos myotheros;
	 * 
	 * Example 1: -cssp-os:windows; - CSS rules only apply on windows (Simple detection)
	 * Example 2: -cssp-os:^windows; - CSS rules apply everywhere but windows (Simple exclusion)
	 * Example 3: -cssp-os:windows<5.1; - CSS rules only apply on windows versions older than XP (detection by version number)
	 * Example 4: -cssp-os:windows<=5.1; - CSS rules only apply on windows versions older than or equal to XP (detection by version number)
	 * Example 5: -cssp-os:windows!=5.1; - CSS rules only apply on windows versions other than XP (detection by version number)
	 * Example 6: -cssp-os:windows=5.1; - CSS rules only apply on windows XP (detection by version number)
	 * Example 7: -cssp-os:windows linux; - CSS rules only apply on windows and linux (Multi-Detection)
	 * 
	 * In the case of contradicting statements, the last defines statement wins, eg -cssp-os:^linux linux; only applys on
	 * linux systems (^linux is overruled)
	 * 
	 * 
	 * Status: Alpha
	 */


	/**
	 * os
	 * Main plugin function
	 * 
	 * @param mixed &$parsed
	 * @return void
	 */
	function os(&$parsed){
		foreach($parsed as $block => $css){
			foreach($parsed[$block] as $selector => $styles){
				// Loop through @font-face
				if($selector == '@font-face'){
					foreach($styles as $index => $style){
						$osparsed = os_parse_os($style);
						if($osparsed){
							$parsed[$block][$selector][$index] = $osparsed;
						}
						else {
							unset($parsed[$block][$selector][$index]);
						}
					}
				}
				// Parse the rest
				else{
					$osparsed = os_parse_os($styles);
					if($osparsed){
						$parsed[$block][$selector] = $osparsed;
					}
					else {
						unset($parsed[$block][$selector]);
					}
				}
			}
		}
	}


	/**
	 * os_parse_os
	 * Looks for the "-cssp-os" property in an element and parses it
	 * 
	 * @param object $styles
	 * @return 
	 */
	function os_parse_os($styles){
		global $browser;
		$match = true;
		// Find os property
		if(isset($styles['-cssp-os'])){
			// Split up any multiple os rules in order to check them one by one
			$osrules = explode(' ', $styles['-cssp-os']);
			// Check each os rule
			foreach($osrules as $osrule){
				preg_match('/([\^]?)(mac|linux|unix|windows)([!=><]{0,2})([0-9]*\.?[0-9]*]*)/i', $osrule, $matches);
				// If the useragent's detected os/platform is found in the current rule
				if(strstr(strtolower($matches[2]),strtolower($browser->platform)))
				{
					// For the time being set $match to true in case a preceeding rule has set it to false
					$match = true;
					// If we found a logical operator and a version number
					if($matches[3] != '' && $matches[4] == floatval($matches[4]))
					{
						// Turn a single =-operator into a PHP-interpretable ==-operator
						if($matches[3] == '=') $matches[3] = '==';
						// Filter and run the detected rule through the PHP-interpreter
						eval('if('.floatval($browser->platformversion).$matches[3].floatval($matches[4]).') $match = true; else $match = false;';
					}
					// Check if we had a negotiating operator at the beginning and in case flip result
					if($matches[1] == '^') $match = ($match == true) ? false : true;
				}
			}
		}
		// Keep the styles, unset os property
		if($match){
			unset($styles['-cssp-os']);
			return $styles;
		}
		// Remove the element
		else {
			return false;
		}
	}

?>