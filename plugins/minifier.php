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
		$color_properties = array(
			'color',
			'background',
			'background-color',
			'border',
			'border-color',
			'border-top',
			'border-left',
			'border-bottom',
			'border-right',
			'box-shadow'
		);
		$tokenized_properties = array(
			'font-family'
		);
		foreach($parsed as $block => $css){
			foreach($parsed[$block] as $selector => $styles){
				// Ignore @font-face
				if($selector != '@font-face'){
					// Optimize hex colors
					foreach($color_properties as $property){
						if(isset($parsed[$block][$selector][$property])){
							foreach($parsed[$block][$selector][$property] as $key => $value){
								$parsed[$block][$selector][$property][$key] = preg_replace($colorpattern, '#\1\2\3', $value);
							}
						}
					}
					// Optimize tokenized strings
					foreach($tokenized_properties as $property){
						if(isset($parsed[$block][$selector][$property])){
							foreach($parsed[$block][$selector][$property] as $key => $value){
								$parsed[$block][$selector][$property][$key] = implode(',', $cssp->tokenize($value, ','));
							}
						}
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