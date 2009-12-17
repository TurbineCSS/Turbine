<?php

	/**
	 * Hexcrusher
	 * Shortens long hey color codes like #FFFFFF to #FFF
	 * Usage: Nobrainer, just switch it on
	 * Status: Beta
	 * 
	 * @param mixed &$parsed
	 * @return void
	 */
	function hexcrusher(&$parsed){
		global $browser;
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
		foreach($parsed as $block => $css){
			foreach($parsed[$block] as $selector => $styles){
				foreach($colorproperties as $colorproperty){
					if($parsed[$block][$selector][$colorproperty]){
						$parsed[$block][$selector][$colorproperty] =
							preg_replace($pattern, '#\1\2\3', $parsed[$block][$selector][$colorproperty]);
					}
				}
			}
		}
	}

?>