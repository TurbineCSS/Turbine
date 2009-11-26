<?php

	/**
	 * Ronded corner shorthand
	 * 
	 * Implements the "rounded" property, serving as a shortcut for all the browser-specific
	 * border-radius properties
	 * 
	 * @todo Implement top-left etc
	 * @param mixed $parsed
	 * @return void
	 */
	function rounded_corner_shorthand($parsed){
		foreach($parsed as $block => $css){
			foreach($parsed[$block] as $selector => $styles){
				// Everywhere
				if($parsed[$block][$selector]['rounded']){
					$value = $parsed[$block][$selector]['rounded'];
					$parsed[$block][$selector]['-moz-border-radius'] = $value;
					$parsed[$block][$selector]['-webkit-border-radius'] = $value;
					$parsed[$block][$selector]['border-radius'] = $value;
					unset($parsed[$block][$selector]['rounded']);
				}
				// Top only
				if($parsed[$block][$selector]['rounded-top']){
					$value = $parsed[$block][$selector]['rounded-top'];
					$parsed[$block][$selector]['-moz-border-radius-topleft'] = $value;
					$parsed[$block][$selector]['-moz-border-radius-topright'] = $value;
					$parsed[$block][$selector]['-webkit-border-top-left-radius'] = $value;
					$parsed[$block][$selector]['-webkit-border-top-right-radius'] = $value;
					$parsed[$block][$selector]['border-top-left-radius'] = $value;
					$parsed[$block][$selector]['border-top-right-radius'] = $value;
					unset($parsed[$block][$selector]['rounded-top']);
				}
				// TODO: Left only
				// TODO: Right only
			}
		}
	}

?>