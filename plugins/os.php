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
	 * 
	 * @param mixed &$parsed
	 * @return void
	 */
	function os(&$parsed){
		print_r($parsed);
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
		$remove = false;
		// Find os property
		if(isset($styles['os'])){
			$osrules = explode(' ', $styles['os']);
			foreach($osrules as $osrule){
				$matches = array();
				preg_match_all('/(\^)?(windows|mac|linux|unix)/i', $osrule, $matches);
				// Os match?
				if(strtolower($matches[2][0]) == strtolower($browser->platform)){
					if($matches[1][0] == '^'){
						$remove = true;
					}
					else{
						$remove = false;
					}
				}
				else{
					if($matches[1][0] == '^'){
						$remove = false;
					}
					else{
						$remove = true;
					}
				}
			}
		}
		if($remove !== true){
			unset($styles['os']);
			return $styles;
		}
		else {
			return false;
		}
	}

?>