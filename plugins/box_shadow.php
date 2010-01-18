<?php


	/**
	 * Easy box shadow
	 * Adds vendor-specific versions of box-shadow
	 * 
	 * Usage:   #foo { box-shadow: 2px 2px 8px #666; }
	 * Result:  #foo { box-shadow: 2px 2px 8px #666; -moz-box-shadow: 2px 2px 8px #666; -webkit-box-shadow: 2px 2px 8px #666; }
	 * Status:  Stable
	 * Version: 1.0
	 * 
	 * @param mixed &$parsed
	 * @return void
	 */
	function box_shadow(&$parsed){
		foreach($parsed as $block => $css){
			foreach($parsed[$block] as $selector => $styles){
				if(isset($parsed[$block][$selector]['box-shadow'])){
					$value = $parsed[$block][$selector]['box-shadow'];
					$parsed[$block][$selector]['-moz-box-shadow'] = $value;
					$parsed[$block][$selector]['-webkit-box-shadow'] = $value;
				}
			}
		}
	}


?>