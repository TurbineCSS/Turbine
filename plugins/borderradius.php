<?php

	/**
	 * Easy and extended border radius
	 * Adds vendor-specific versions of border-radius and adds border-top-radius,
	 * border-bottom-radius, border-left-radius and border-right-radius
	 * For IE, only general radius works. And make sure, border-radius declaration 
	 * comes before any (box-)shadow-declaration in CSS-code!
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
		global $cssp;
		foreach($parsed as $block => $css){
			foreach($parsed[$block] as $selector => $styles){
				// Everywhere
				if(isset($parsed[$block][$selector]['border-radius'])){
					$properties = array('-moz-border-radius', '-khtml-border-radius', '-webkit-border-radius', 'border-radius');
					$value = $parsed[$block][$selector]['border-radius'];
					unset($parsed[$block][$selector]['border-radius']);
					foreach($properties as $property){
						$parsed[$block][$selector][$property] = $value;
						CSSP::comment($parsed[$block][$selector], $property, 'Added by border radius plugin');
					}
				}
				// Top only
				if(isset($parsed[$block][$selector]['border-top-radius'])){
					$properties = array('-moz-border-radius-topleft', '-moz-border-radius-topright', '-khtml-border-top-left-radius',
						'-khtml-border-top-right-radius', '-webkit-border-top-left-radius', '-webkit-border-top-right-radius',
						'border-top-left-radius', 'border-top-right-radius');
					$value = $parsed[$block][$selector]['border-top-radius'];
					unset($parsed[$block][$selector]['border-top-radius']);
					foreach($properties as $property){
						$parsed[$block][$selector][$property] = $value;
					}
				}
				// Bottom only
				if(isset($parsed[$block][$selector]['border-bottom-radius'])){
					$properties = array('-moz-border-radius-bottomleft', '-moz-border-radius-bottomright', '-khtml-border-bottom-left-radius',
						'-khtml-border-bottom-right-radius', '-webkit-border-bottom-left-radius', '-webkit-border-bottom-right-radius',
						'border-bottom-left-radius', 'border-bottom-right-radius');
					$value = $parsed[$block][$selector]['border-bottom-radius'];
					unset($parsed[$block][$selector]['border-bottom-radius']);
					foreach($properties as $property){
						$parsed[$block][$selector][$property] = $value;
					}
				}
				// Left only
				if(isset($parsed[$block][$selector]['border-left-radius'])){
					$properties = array('-moz-border-radius-topleft', '-moz-border-radius-bottomleft', '-khtml-border-top-left-radius',
						'-khtml-border-bottom-left-radius', '-webkit-border-top-left-radius', '-webkit-border-bottom-left-radius',
						'border-top-left-radius', 'border-bottom-left-radius');
					$value = $parsed[$block][$selector]['border-left-radius'];
					unset($parsed[$block][$selector]['border-left-radius']);
					foreach($properties as $property){
						$parsed[$block][$selector][$property] = $value;
					}
				}
				// Right only
				if(isset($parsed[$block][$selector]['border-right-radius'])){
					$properties = array('-moz-border-radius-topright', '-moz-border-radius-bottomright', '-khtml-border-top-right-radius',
						'-khtml-border-bottom-right-radius', '-webkit-border-top-right-radius', '-webkit-border-bottom-right-radius',
						'border-top-right-radius', 'border-bottom-right-radius');
					$value = $parsed[$block][$selector]['border-right-radius'];
					unset($parsed[$block][$selector]['border-right-radius']);
					foreach($properties as $property){
						$parsed[$block][$selector][$property] = $value;
					}
				}
				// Top left only
				if(isset($parsed[$block][$selector]['border-top-left-radius'])){
					$value = $parsed[$block][$selector]['border-top-left-radius'];
					$properties = array('-moz-border-radius-topleft', '-khtml-border-top-left-radius', '-webkit-border-top-left-radius', 'border-top-left-radius');
					unset($parsed[$block][$selector]['border-top-left-radius']);
					foreach($properties as $property){
						$parsed[$block][$selector][$property] = $value;
						CSSP::comment($parsed[$block][$selector], $property, 'Added by border radius plugin');
					}
				}
				// Top right only
				if(isset($parsed[$block][$selector]['border-top-right-radius'])){
					$value = $parsed[$block][$selector]['border-top-right-radius'];
					$properties = array('-moz-border-radius-topright', '-khtml-border-top-right-radius', '-webkit-border-top-right-radius', 'border-top-right-radius');
					unset($parsed[$block][$selector]['border-top-right-radius']);
					foreach($properties as $property){
						$parsed[$block][$selector][$property] = $value;
						CSSP::comment($parsed[$block][$selector], $property, 'Added by border radius plugin');
					}
				}
				// Bottom left only
				if(isset($parsed[$block][$selector]['border-bottom-left-radius'])){
					$value = $parsed[$block][$selector]['border-bottom-left-radius'];
					$properties = array('-moz-border-radius-bottomleft', '-khtml-border-bottom-left-radius', '-webkit-border-bottom-left-radius', 'border-bottom-left-radius');
					unset($parsed[$block][$selector]['border-bottom-left-radius']);
					foreach($properties as $property){
						$parsed[$block][$selector][$property] = $value;
						CSSP::comment($parsed[$block][$selector], $property, 'Added by border radius plugin');
					}
				}
				// Bottom right only
				if(isset($parsed[$block][$selector]['border-bottom-right-radius'])){
					$value = $parsed[$block][$selector]['border-bottom-right-radius'];
					$properties = array('-moz-border-radius-bottomright', '-khtml-border-bottom-right-radius', '-webkit-border-bottom-right-radius', 'border-bottom-right-radius');
					unset($parsed[$block][$selector]['border-bottom-right-radius']);
					foreach($properties as $property){
						$parsed[$block][$selector][$property] = $value;
						CSSP::comment($parsed[$block][$selector], $property, 'Added by border radius plugin');
					}
				}
			}
		}
	}


	/**
	 * Register the plugin
	 */
	$cssp->register_plugin('before_glue', 0, 'borderradius');


?>