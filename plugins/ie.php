<?php


	/**
	 * A bunch of general IE Bugfixes
	 * 
	 * Usage: Nobrainer, just switch it on
	 * Example: -
	 * Status: Beta
	 * 
	 * @param mixed &$parsed
	 * @return void
	 */
	function iebugfixes(&$parsed){
		global $browser;
		foreach($parsed as $block => $css){
			foreach($parsed[$block] as $selector => $styles){
				// Float double margin bug
				if($parsed[$block][$selector]['float'] && ($parsed[$block][$selector]['margin'] || $parsed[$block][$selector]['margin-left'] || $parsed[$block][$selector]['margin-right'] || $parsed[$block][$selector]['margin-top'] || $parsed[$block][$selector]['margin-bottom'])){
					if($browser->family == 'MSIE' && floatval($browser->familyversion) < 7){
						$parsed[$block][$selector]['display'] = 'inline';
					}
				}
			}
		}
	}


	/**
	 * Register the plugin
	 */
	register_plugin('before_compile', 0, 'iebugfixes');


?>