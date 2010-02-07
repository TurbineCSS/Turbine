<?php

	/**
	 * Easy and extended multicolumn
	 * Adds vendor-specific versions of multicolumn commands (width, gap, count, rule)
	 * 
	 * Usage:     {column-width: value; column-gap: value; column-count: value; column: rule: value}
	 * Example1:  p {column-width: 13em; column-gap: 1em;}
	 * Example2:  p {column-count: 3; column-gap: 1em; column-rule: 1px solid black;}
	 * Status:    Stable
	 * Version:   1.0
	 * 
	 * @param mixed &$parsed
	 * @return void
	 */
	function multicolumn(&$parsed){
		foreach($parsed as $block => $css){
			foreach($parsed[$block] as $selector => $styles){
				// column-width
				if(isset($parsed[$block][$selector]['column-width'])){
					$value = $parsed[$block][$selector]['column-width'];
					$parsed[$block][$selector]['-moz-column-width'] = $value;
					$parsed[$block][$selector]['-webkit-column-width'] = $value;
				}
				// column-gap
				if(isset($parsed[$block][$selector]['column-gap'])){
					$value = $parsed[$block][$selector]['column-gap'];
					$parsed[$block][$selector]['-moz-column-gap'] = $value;
					$parsed[$block][$selector]['-webkit-column-gap'] = $value;
				}
				// column-count
				if(isset($parsed[$block][$selector]['column-count'])){
					$value = $parsed[$block][$selector]['column-count'];
					$parsed[$block][$selector]['-moz-column-count'] = $value;
					$parsed[$block][$selector]['-webkit-column-count'] = $value;
				}
				// column-rule
				if(isset($parsed[$block][$selector]['column-rule'])){
					$value = $parsed[$block][$selector]['column-rule'];
					$parsed[$block][$selector]['-moz-column-rule'] = $value;
					$parsed[$block][$selector]['-webkit-column-rule'] = $value;
				}
			}
		}
	}


	/**
	 * Register the plugin
	 */
	register_plugin('before_compile', 0, 'multicolumn');


?>