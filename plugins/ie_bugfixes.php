<?php

	/**
	 * A bunch of general IE Bugfixes
	 * Implements the "display: inline-block" property for all current browsers
	 * 
	 * Usage: TODO
	 * Example: TODO
	 * Status: Beta
	 * 
	 * @param mixed &$parsed
	 * @return void
	 */
	function ie_bugfixes(&$parsed){
		global $browser;
		foreach($parsed as $block => $css){
			foreach($parsed[$block] as $selector => $styles){
				// Float double margin bug
				if($parsed[$block][$selector]['float'] && ($parsed[$block][$selector]['margin'] || $parsed[$block][$selector]['margin-left'] || $parsed[$block][$selector]['margin-right'] || $parsed[$block][$selector]['margin-top'] || $parsed[$block][$selector]['margin-bottom'])){
					if($browser->family == 'MSIE' && floatval($browser->familyversion) < 7)
					{
						$parsed[$block][$selector]['display'] = 'inline';
					} 
				}
			}
		}
	}

?>