<?php

	/**
	 * Minifier
	 * Performs a number of micro-optimizations
	 * 
	 * Usage: Nobrainer, just switch it on
	 * Status: Beta
	 * 
	 * @param mixed &$parsed
	 * @return void
	 */
	function minifier(&$parsed){
		global $browser;
		// Hex colors
		$pattern = '/#([A-F0-9])\1([A-F0-9])\2([A-F0-9])\3/i';
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
		// TODO: Optimize stuff like margin: 4px 4px 4px 4px;
		foreach($parsed as $block => $css){
			foreach($parsed[$block] as $selector => $styles){
				foreach($colorproperties as $colorproperty){
					if(isset($parsed[$block][$selector][$colorproperty])){
						$parsed[$block][$selector][$colorproperty] =
							preg_replace($pattern, '#\1\2\3', $parsed[$block][$selector][$colorproperty]);
					}
				}
			}
		}
	}


	/**
	 * Register the plugin
	 */
	register_plugin('before_compile', 0, 'minifier');

?>