<?php


	/**
	 * OS
	 * Implements the "os" property for operating system detection
	 * 
	 * Usage: os:myos myotheros;
	 * If there are multiple arguments defined: Any positive submatch with the rule makes a positive match out of the whole rule
	 * 
	 * possible os-names are:
	 * windows
	 * windows_ce
	 * linux
	 * mac
	 * freebsd
	 * openbsd
	 * solaris
	 * nintendo_wii
	 * playstation_3
	 * playstation_portable
	 * 
	 * Example 1: os:windows; - CSS rules only apply on windows (Simple detection)
	 * Example 2: os:^windows; - CSS rules apply everywhere but windows (Simple exclusion)
	 * Example 3: os:windows<5.1; - CSS rules only apply on windows versions older than XP (detection by version number)
	 * Example 4: os:windows<=5.1; - CSS rules only apply on windows versions older than or equal to XP (detection by version number)
	 * Example 5: os:windows!=5.1; - CSS rules only apply on windows versions other than XP (detection by version number)
	 * Example 6: os:windows=5.1; - CSS rules only apply on windows XP (detection by version number)
	 * Example 7: os:windows linux; - CSS rules only apply on windows OR linux (Multi-Detection)
	 */


	/**
	 * os
	 * Main plugin function
	 * @param mixed &$parsed
	 * @return void
	 */
	function os(&$parsed){
		// Look for an os rule in @cssp, empty $parsed on mismatch
		if(isset($parsed['global']['@cssp']['os'])){
			if(os_parse_os($parsed['global']['@cssp']) === false){
				$parsed = array();
			}
		}
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
	 * Looks for the "os" property in an element and parses it
	 * @param object $styles
	 * @return 
	 */
	function os_parse_os($styles){
		global $browser;
		$match = true;
		// Prepare special array for Windows name to version mapping
		$os_windowsnames = array();
		$os_windowsnames['95'] = 4.0;
		$os_windowsnames['NT4'] = 4.0;
		$os_windowsnames['98'] = 4.1;
		$os_windowsnames['ME'] = 4.9;
		$os_windowsnames['2000'] = $os_windowsnames['2K'] = 5.0;
		$os_windowsnames['XP'] = 5.1;
		$os_windowsnames['2003'] = $os_windowsnames['2K3'] = 5.2;
		$os_windowsnames['Vista'] = $os_windowsnames['2008'] = $os_windowsnames['2K8'] = 6.0;
		$os_windowsnames['Windows7'] = $os_windowsnames['Win7'] = $os_windowsnames['2008R2'] = $os_windowsnames['2K8R2'] = 6.1;
		
		// Find os property
		if(isset($styles['os'])){
			$match = false;
			// Split up any multiple os rules in order to check them one by one
			$osrules = preg_split('/\s+/', $styles['os']);
			// Check each os rule
			foreach($osrules as $osrule){
				preg_match('/([\^]?)([a-z\-_0-9]+)([!=><]{0,2})([0-9]*\.?[0-9]*]*)/i', $osrule, $matches);
				// If the useragent's detected os/platform is found in the current rule
				if(strstr(strtolower($matches[2]),strtolower(str_replace(' ','_',$browser->platform)))){
					// For the time being set $submatch to true
					$submatch = true;
					// If we found a logical operator and a version number
					if($matches[3] != '' && (isset($os_windowsnames[$matches[4]]) || $matches[4] == floatval($matches[4]))){
						// Turn a single =-operator into a PHP-interpretable ==-operator
						if($matches[3] == '=') $matches[3] = '==';
						// Check for Windows name mapping and apply
						if(isset($os_windowsnames[$matches[4]])) $matches[4] = $os_windowsnames[$matches[4]];
						// Filter and run the detected rule through the PHP interpreter
						eval('if('.floatval($browser->platformversion).$matches[3].floatval($matches[4]).') $submatch = true; else $submatch = false;');
					}
				}
				else{
					// Set $submatch to false
					$submatch = false;
				}
				// Check if we had a negating operator at the beginning and in case flip result
				if($matches[1] == '^') $submatch = ($submatch == true) ? false : true;
				// Check the final state of $submatch and set $match only to true if $submatch is true
				if($submatch) $match = true;
			}
		}
		// Keep the styles, unset os property
		if($match){
			unset($styles['os']);
			return $styles;
		}
		// Remove the element
		else {
			return false;
		}
	}


	/**
	 * Register the plugin
	 */
	$cssp->register_plugin('before_compile', 1000, 'os');


?>