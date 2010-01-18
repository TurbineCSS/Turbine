<?php

	/**
	 * Easy and extended border radius
	 * Adds vendor-specific versions of border-radius and adds border-top-radius,
	 * border-bottom-radius, border-left-radius and border-right-radius
	 * 
	 * Usage:     border-radius[-top/-bottom/-left/-right/-top-left/...]: value;
	 * Example 1: border-top-radius:4px;
	 * Example 2: border-bottom-left-radius:2em;
	 * Status:    Stable
	 * Version:   1.0
	 * 
	 * @param mixed &$parsed
	 * @return void
	 */
	function borderradius(&$parsed){
		foreach($parsed as $block => $css){
			foreach($parsed[$block] as $selector => $styles){
				// Everywhere
				if(isset($parsed[$block][$selector]['border-radius'])){
					$value = $parsed[$block][$selector]['border-radius'];
					$parsed[$block][$selector]['-moz-border-radius'] = $value;
					$parsed[$block][$selector]['-khtml-border-radius'] = $value;
					$parsed[$block][$selector]['-webkit-border-radius'] = $value;
				}
				// Top only
				if(isset($parsed[$block][$selector]['border-top-radius'])){
					$value = $parsed[$block][$selector]['border-top-radius'];
					$parsed[$block][$selector]['-moz-border-radius-topleft'] = $value;
					$parsed[$block][$selector]['-moz-border-radius-topright'] = $value;
					$parsed[$block][$selector]['-khtml-border-top-left-radius'] = $value;
					$parsed[$block][$selector]['-khtml-border-top-right-radius'] = $value;
					$parsed[$block][$selector]['-webkit-border-top-left-radius'] = $value;
					$parsed[$block][$selector]['-webkit-border-top-right-radius'] = $value;
					$parsed[$block][$selector]['border-top-left-radius'] = $value;
					$parsed[$block][$selector]['border-top-right-radius'] = $value;
					unset($parsed[$block][$selector]['border-top-radius']);
				}
				// Bottom only
				if(isset($parsed[$block][$selector]['border-bottom-radius'])){
					$value = $parsed[$block][$selector]['border-bottom-radius'];
					$parsed[$block][$selector]['-moz-border-radius-bottomleft'] = $value;
					$parsed[$block][$selector]['-moz-border-radius-bottomright'] = $value;
					$parsed[$block][$selector]['-khtml-border-bottom-left-radius'] = $value;
					$parsed[$block][$selector]['-khtml-border-bottom-right-radius'] = $value;
					$parsed[$block][$selector]['-webkit-border-bottom-left-radius'] = $value;
					$parsed[$block][$selector]['-webkit-border-bottom-right-radius'] = $value;
					$parsed[$block][$selector]['border-bottom-left-radius'] = $value;
					$parsed[$block][$selector]['border-bottom-right-radius'] = $value;
					unset($parsed[$block][$selector]['border-bottom-radius']);
				}
				// Left only
				if(isset($parsed[$block][$selector]['border-left-radius'])){
					$value = $parsed[$block][$selector]['border-left-radius'];
					$parsed[$block][$selector]['-moz-border-radius-topleft'] = $value;
					$parsed[$block][$selector]['-moz-border-radius-bottomleft'] = $value;
					$parsed[$block][$selector]['-khtml-border-top-left-radius'] = $value;
					$parsed[$block][$selector]['-khtml-border-bottom-left-radius'] = $value;
					$parsed[$block][$selector]['-webkit-border-top-left-radius'] = $value;
					$parsed[$block][$selector]['-webkit-border-bottom-left-radius'] = $value;
					$parsed[$block][$selector]['border-top-left-radius'] = $value;
					$parsed[$block][$selector]['border-bottom-left-radius'] = $value;
					unset($parsed[$block][$selector]['border-left-radius']);
				}
				// Right only
				if(isset($parsed[$block][$selector]['border-right-radius'])){
					$value = $parsed[$block][$selector]['border-right-radius'];
					$parsed[$block][$selector]['-moz-border-radius-topright'] = $value;
					$parsed[$block][$selector]['-moz-border-radius-bottomright'] = $value;
					$parsed[$block][$selector]['-khtml-border-top-right-radius'] = $value;
					$parsed[$block][$selector]['-khtml-border-bottom-right-radius'] = $value;
					$parsed[$block][$selector]['-webkit-border-top-right-radius'] = $value;
					$parsed[$block][$selector]['-webkit-border-bottom-right-radius'] = $value;
					$parsed[$block][$selector]['border-top-right-radius'] = $value;
					$parsed[$block][$selector]['border-bottom-right-radius'] = $value;
					unset($parsed[$block][$selector]['border-right-radius']);
				}
				// Top left only
				if(isset($parsed[$block][$selector]['border-top-left-radius'])){
					$value = $parsed[$block][$selector]['border-top-left-radius'];
					$parsed[$block][$selector]['-moz-border-radius-topleft'] = $value;
					$parsed[$block][$selector]['-khtml-border-top-left-radius'] = $value;
					$parsed[$block][$selector]['-webkit-border-top-left-radius'] = $value;
				}
				// Top right only
				if(isset($parsed[$block][$selector]['border-top-right-radius'])){
					$value = $parsed[$block][$selector]['border-top-right-radius'];
					$parsed[$block][$selector]['-moz-border-radius-topright'] = $value;
					$parsed[$block][$selector]['-khtml-border-top-right-radius'] = $value;
					$parsed[$block][$selector]['-webkit-border-top-right-radius'] = $value;
				}
				// Bottom left only
				if(isset($parsed[$block][$selector]['border-bottom-left-radius'])){
					$value = $parsed[$block][$selector]['border-bottom-left-radius'];
					$parsed[$block][$selector]['-moz-border-radius-bottomleft'] = $value;
					$parsed[$block][$selector]['-khtml-border-bottom-left-radius'] = $value;
					$parsed[$block][$selector]['-webkit-border-bottom-left-radius'] = $value;
				}
				// Bottom right only
				if(isset($parsed[$block][$selector]['border-bottom-right-radius'])){
					$value = $parsed[$block][$selector]['border-bottom-right-radius'];
					$parsed[$block][$selector]['-moz-border-radius-bottomright'] = $value;
					$parsed[$block][$selector]['-khtml-border-bottom-right-radius'] = $value;
					$parsed[$block][$selector]['-webkit-border-bottom-right-radius'] = $value;
				}
			}
		}
	}

?>