<?php

	/**
	 * Inline Block
	 * Implements the "display: inline-block" property for all current browsers
	 * 
	 * Usage: TODO
	 * Example: TODO
	 * Status: Beta
	 * 
	 * @param mixed &$parsed
	 * @return void
	 */
	function inline_block(&$parsed){
		global $browser;
		foreach($parsed as $block => $css){
			foreach($parsed[$block] as $selector => $styles){
				// Everywhere
				if($parsed[$block][$selector]['display'] && $parsed[$block][$selector]['display'] == 'inline-block'){
					if($browser->family == 'MSIE' && floatval($browser->familyversion) < 8)
					{
						$parsed[$block][$selector]['display'] = 'inline';
						$parsed[$block][$selector]['zoom'] = '1';
					} 
					elseif($browser->family == 'Firefox')
					{
						$parsed[$block][$selector]['display'] = '-moz-inline-stack';
					}
				}
			}
		}
	}

?>