<?php

	/**
	 * Easy and extended transition
	 * Adds vendor-specific versions of transition
	 * 
	 * Usage:     Use any currently known trnasition-property wihtout vendor-prefixes
	 * Example 1: transition: left 1s linear;
	 * Example 2: transition-property: background-color; transition-duration: 4s;
	 * Status:    Stable
	 * Version:   1.0
	 * 
	 * @param mixed &$parsed
	 * @return void
	 */
	function transition(&$parsed){
		$transition_properties = array(
		'transition',
		'transition-property',
		'transition-duration',
		'transition-delay',
		'transition-timing-function'
		);
		
		foreach($parsed as $block => $css){
			foreach($parsed[$block] as $selector => $styles){
				$parsed[$block][$selector]['arraylength'] = count($transition_properties);
				foreach($transition_properties as $property)
				{
					$parsed[$block][$selector]['schepp'] = $property;
					if(isset($parsed[$block][$selector][$property])){
						$value = $parsed[$block][$selector][$property];
						$parsed[$block][$selector]['-moz-'.$property] = $value;
						$parsed[$block][$selector]['-o-'.$property] = $value;
						$parsed[$block][$selector]['-webkit-'.$property] = $value;
					}
				}
			}
		}
	}


	/**
	 * Register the plugin
	 */
	$cssp->register_plugin('before_compile', 0, 'transition');


?>