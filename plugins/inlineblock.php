<?php


	/**
	 * Inline Block
	 * Implements the "display: inline-block" property for all current browsers
	 * 
	 * Usage: Nobrainer, just switch it on
	 * Example: -
	 * Status: Beta
	 * 
	 * @param mixed &$parsed
	 * @return void
	 */
	function inlineblock(&$parsed){
		global $browser;
		foreach($parsed as $block => $css){
			foreach($parsed[$block] as $selector => $styles){
				// Everywhere
				if($parsed[$block][$selector]['display'] && $parsed[$block][$selector]['display'] == 'inline-block'){
					if($browser->family == 'MSIE' && floatval($browser->familyversion) < 8){
						$parsed[$block][$selector]['display'] = 'inline';
						$parsed[$block][$selector]['zoom'] = '1';
					} 
					elseif($browser->engine == 'Gecko' && floatval($browser->engineversion) < 1.9){
						$parsed[$block][$selector]['display'] = '-moz-inline-stack';
					}
				}
			}
		}
	}


	/**
	 * Register the plugin
	 */
	$cssp->register_plugin('before_compile', 0, 'inlineblock');


?>