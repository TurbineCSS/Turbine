<?php

	/**
	 * Browser
	 * Implements the "browser", "engine" and "device" properties for browser detection
	 * 
	 * Usage: browser:mybrowser myotherbrowser;
	 * Usage: engine:myengine myotherengine;
	 * Usage: device:mydevice myotherdevice;
	 * 
	 * Example 1: browser:firefox; - CSS rules only apply on firefox (Simple detection)
	 * Example 2: browser:^firefox; - CSS rules apply everywhere but firefox (Simple exclusion)
	 * Example 3: browser:firefox<3.5; - CSS rules only apply on firefox versions older than 3.5 (detection by version number)
	 * Example 4: browser:firefox opera; - CSS rules only apply on firefox and opera (Multi-Detection)
	 * 
	 * In the case of contradicting statements, the last defines statement wins, eg os:^opera opera; only applys on
	 * opera ("^opera" is overruled)
	 * 
	 * 
	 * Status: -
	 * 
	 * @param mixed &$parsed
	 * @return void
	 */
	function browser(&$parsed){
		global $browser;
		foreach($parsed as $block => $css){
			foreach($parsed[$block] as $selector => $styles){
				// Do stuff
			}
		}
	}

?>