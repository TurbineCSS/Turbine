<?php

	/**
	 * Minifier
	 * Performs a number of micro-optimizations
	 * 
	 * Usage: Nobrainer, just switch it on
	 * Status: Stable
	 * 
	 * @todo: Optimize stuff like margin: 4px 4px 4px 4px;
	 * @todo: Remove units from zero-values like 0em
	 * @param mixed &$parsed
	 * @return void
	 */
	function minifier(&$parsed){
		global $browser, $cssp;
		$colorpattern = '/#([A-F0-9])\1([A-F0-9])\2([A-F0-9])\3/i';
		$colorproperties = array(
			'color',
			'background',
			'background-color',
			'border',
			'border-color',
			'border-top',
			'border-left',
			'border-bottom',
			'border-right'
		);
		$tokenizedproperties = array(
			'font-family'
		);
		foreach($parsed as $block => $css){
			foreach($parsed[$block] as $selector => $styles){
				// Optimize hex colors
				foreach($colorproperties as $property){
					if(isset($parsed[$block][$selector][$property])){
						$parsed[$block][$selector][$property] =
							preg_replace($colorpattern, '#\1\2\3', $parsed[$block][$selector][$property]);
					}
				}
				// Optimize tokenized strings
				foreach($tokenizedproperties as $property){
					if(isset($parsed[$block][$selector][$property])){
						$parsed[$block][$selector][$property] =
							implode(',', $cssp->tokenize($parsed[$block][$selector][$property], ','));
					}
				}
			}
		}
	}


	/**
	 * Register the plugin
	 */
	$cssp->register_plugin('before_compile', 0, 'minifier');

?>