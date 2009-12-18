<?php

	/**
	 * OS
	 * Implements the "os" property for operating system detection
	 * 
	 * Usage: os:myos myotheros;
	 * 
	 * Example 1: os:windows; - CSS rules only apply on windows (Simple detection)
	 * Example 2: os:^windows; - CSS rules apply everywhere but windows (Simple exclusion)
	 * Example 3: os:windows<5.1; - CSS rules only apply on windows versions older than XP (detection by version number)
	 * Example 4: os:windows linux; - CSS rules only apply on windows and linux (Multi-Detection)
	 * 
	 * In the case of contradicting statements, the last defines statement wins, eg os:^linux linux; only applys on
	 * linux systems (^linux is overruled)
	 * 
	 * 
	 * Status: Alpha
	 * @todo Implement version number matching for windows
	 * @todo Implement multiple rules
	 */


	/**
	 * @var array $os_windows_versions List of windows versions
	 */
	$os_windows_versions = array(
		'2k' => 5.0,
		'2000' => 5.0,
		'xp' => 5.1,
		'Vista' => 6.0,
		'7' => 6.1
	);


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
	 * Looks for the "os" property in an element and parses it
	 * 
	 * @param object $styles
	 * @return 
	 */
	function os_parse_os($styles){
		global $browser;
		$match = true;
		// Find os property
		if(isset($styles['os'])){
			$osrules = explode(' ', $styles['os']);
			foreach($osrules as $osrule){
				$matches = array();
				preg_match_all('/(\^)?(mac|linux|unix|windows)(:?(\<|\>|=)(\S*))?/i', $osrule, $matches);
				// Os rule properties
				$negate = ($matches[1][0] == '^') ? true:false;
				$system = $matches[2][0];
				$operator = $matches[4][0];
				$version = $matches[5][0];
				// Translate shotcuts
				if(strtolower($system) == 'mac'){
					$system = 'macintosh';
				}
				// Os match
				if(strtolower($system) == strtolower($browser->platform)){
					// TODO: Version match
					// TODO: Version mismatch
					if($negate){
						$match = false;
					}
					else{
						$match = true;
					}
				}
				// Os mismatch
				else{
					if($negate){
						$match = false;
					}
					else{
						$match = true;
					}
				}
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

?>