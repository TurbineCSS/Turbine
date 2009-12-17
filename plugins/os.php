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
	 * Status: -
	 * 
	 * @param mixed &$parsed
	 * @return void
	 */
	function os(&$parsed){
		global $browser;
		foreach($parsed as $block => $css){
			foreach($parsed[$block] as $selector => $styles){
				// Do stuff
			}
		}
	}

?>