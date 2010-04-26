<?php


	/**
	 * Inline Block
	 * Implements the "display: inline-block" property for all current browsers
	 * 
	 * Usage: Nobrainer, just switch it on
	 * Example: -
	 * Status: Stable
	 * Version: 1.0
	 * 
	 * @param mixed &$parsed
	 * @return void
	 */
	function inlineblock(&$parsed){
		global $browser, $cssp;
		foreach($cssp->parsed as $block => $css){
			foreach($cssp->parsed[$block] as $selector => $styles){
				// Everywhere
				if(isset($cssp->parsed[$block][$selector]['display']) && $cssp->get_final_value($cssp->parsed[$block][$selector]['display'], 'display') == 'inline-block'){
					if($browser->engine == 'MSIE' && floatval($browser->engineversion) < 8){
						$cssp->parsed[$block][$selector]['display'][] = 'inline';
						$cssp->parsed[$block][$selector]['zoom'][] = '1';
					} 
					elseif($browser->engine == 'Gecko' && floatval($browser->engineversion) < 1.9){
						$cssp->parsed[$block][$selector]['display'][] = '-moz-inline-stack';
					}
				}
			}
		}
	}


	/**
	 * Register the plugin
	 */
	$cssp->register_plugin('before_glue', 0, 'inlineblock');


?>