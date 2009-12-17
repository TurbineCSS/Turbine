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
	 * @todo Walk the @font-face arrays too!
	 * 
	 * @param mixed &$parsed
	 * @return void
	 */
	function os(&$parsed){
		global $browser;
		foreach($parsed as $block => $css){
			foreach($parsed[$block] as $selector => $styles){
				// Find os property
				if($parsed[$block][$selector]['os']){
					$remove = false;
					$osrules = explode(' ', $parsed[$block][$selector]['os']);
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
				if($remove == true){
					unset($parsed[$block][$selector]);
				}
				else{
					unset($parsed[$block][$selector]['os']);
				}
			}
		}
	}

?>