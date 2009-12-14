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
		global $browserproperties;
		foreach($parsed as $block => $css){
			foreach($parsed[$block] as $selector => $styles){
				// Everywhere
				if($parsed[$block][$selector]['display'] && $parsed[$block][$selector]['display'] == 'inline-block'){
					if($browserproperties['browsertype'] == 'MSIE' && floatval($browserproperties['browsertype']) < 8)
					{
						$parsed[$block][$selector]['display'] = 'inline';
						$parsed[$block][$selector]['zoom'] = '1';
					} 
					elseif($browserproperties['browsertype'] == 'Firefox')
					{
						$parsed[$block][$selector]['display'] = '-moz-inline-stack';
					}
				}
			}
		}
	}

?>