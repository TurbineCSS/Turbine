<?php

	/**
	 * Ronded corner shorthand
	 * Implements the "rounded" property, serving as a shortcut for all the browser-specific border-radius properties
	 * 
	 * Usage: rounded[-top/-bottom/-left/-right/-top-left/...]: value;
	 * Example: rounded-top:4px;
	 * Example: rounded-bottom-left:2em;
	 * Status: Beta
	 * 
	 * @param mixed &$parsed
	 * @return void
	 */
	function rounded_corner_shorthand(&$parsed){
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
				// Bottom only
				if($parsed[$block][$selector]['rounded-bottom']){
					$value = $parsed[$block][$selector]['rounded-bottom'];
					$parsed[$block][$selector]['-moz-border-radius-bottomleft'] = $value;
					$parsed[$block][$selector]['-moz-border-radius-bottomright'] = $value;
					$parsed[$block][$selector]['-webkit-border-bottom-left-radius'] = $value;
					$parsed[$block][$selector]['-webkit-border-bottom-right-radius'] = $value;
					$parsed[$block][$selector]['border-bottom-left-radius'] = $value;
					$parsed[$block][$selector]['border-bottom-right-radius'] = $value;
					unset($parsed[$block][$selector]['rounded-bottom']);
				}
				// Left only
				if($parsed[$block][$selector]['rounded-left']){
					$value = $parsed[$block][$selector]['rounded-left'];
					$parsed[$block][$selector]['-moz-border-radius-topleft'] = $value;
					$parsed[$block][$selector]['-moz-border-radius-bottomleft'] = $value;
					$parsed[$block][$selector]['-webkit-border-top-left-radius'] = $value;
					$parsed[$block][$selector]['-webkit-border-bottom-left-radius'] = $value;
					$parsed[$block][$selector]['border-top-left-radius'] = $value;
					$parsed[$block][$selector]['border-bottom-left-radius'] = $value;
					unset($parsed[$block][$selector]['rounded-left']);
				}
				// Right only
				if($parsed[$block][$selector]['rounded-right']){
					$value = $parsed[$block][$selector]['rounded-right'];
					$parsed[$block][$selector]['-moz-border-radius-topright'] = $value;
					$parsed[$block][$selector]['-moz-border-radius-bottomright'] = $value;
					$parsed[$block][$selector]['-webkit-border-top-right-radius'] = $value;
					$parsed[$block][$selector]['-webkit-border-bottom-right-radius'] = $value;
					$parsed[$block][$selector]['border-top-left-radius'] = $value;
					$parsed[$block][$selector]['border-bottom-left-radius'] = $value;
					unset($parsed[$block][$selector]['rounded-right']);
				}
				// Top left only
				if($parsed[$block][$selector]['rounded-top-left']){
					$value = $parsed[$block][$selector]['rounded-top-left'];
					$parsed[$block][$selector]['-moz-border-radius-topleft'] = $value;
					$parsed[$block][$selector]['-webkit-border-top-left-radius'] = $value;
					$parsed[$block][$selector]['border-top-left-radius'] = $value;
					unset($parsed[$block][$selector]['rounded-top-left']);
				}
				// Top right only
				if($parsed[$block][$selector]['rounded-top-right']){
					$value = $parsed[$block][$selector]['rounded-top-right'];
					$parsed[$block][$selector]['-moz-border-radius-topright'] = $value;
					$parsed[$block][$selector]['-webkit-border-top-right-radius'] = $value;
					$parsed[$block][$selector]['border-top-right-radius'] = $value;
					unset($parsed[$block][$selector]['rounded-top-right']);
				}
				// Bottom left only
				if($parsed[$block][$selector]['rounded-bottom-left']){
					$value = $parsed[$block][$selector]['rounded-bottom-left'];
					$parsed[$block][$selector]['-moz-border-radius-bottomleft'] = $value;
					$parsed[$block][$selector]['-webkit-border-bottom-left-radius'] = $value;
					$parsed[$block][$selector]['border-bottom-left-radius'] = $value;
					unset($parsed[$block][$selector]['rounded-bottom-left']);
				}
				// Bottom right only
				if($parsed[$block][$selector]['rounded-bottom-right']){
					$value = $parsed[$block][$selector]['rounded-bottom-right'];
					$parsed[$block][$selector]['-moz-border-radius-bottomright'] = $value;
					$parsed[$block][$selector]['-webkit-border-bottom-right-radius'] = $value;
					$parsed[$block][$selector]['border-bottom-right-radius'] = $value;
					unset($parsed[$block][$selector]['rounded-bottom-right']);
				}
			}
		}
	}

?>