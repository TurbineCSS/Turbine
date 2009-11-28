<?php

	/**
	 * A bunch of general IE Bugfixes
	 * 
	 * Implements the "display: inline-block" property for all current browsers
	 * 
	 * @param mixed $parsed
	 * @return void
	 */
	function ie_bugfixes($parsed){
		global $browserproperties;
		foreach($parsed as $block => $css){
			foreach($parsed[$block] as $selector => $styles){
				// Float double margin bug
				if($parsed[$block][$selector]['float'] && ($parsed[$block][$selector]['margin'] || $parsed[$block][$selector]['margin-left'] || $parsed[$block][$selector]['margin-right'] || $parsed[$block][$selector]['margin-top'] || $parsed[$block][$selector]['margin-bottom'])){
					if($browserproperties['browsertype'] == 'MSIE' && floatval($browserproperties['browsertype']) < 7)
					{
						$parsed[$block][$selector]['display'] = 'inline';
					} 
				}
			}
		}
	}

?>