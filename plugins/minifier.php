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
		$color_pattern = '/#([A-F0-9])\1([A-F0-9])\2([A-F0-9])\3/i';
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
		$zero_pattern = '/(0(?:em|ex|px|in|cm|mm|pt|pc))/';
		$zero_properties = array(
			'margin', 'margin-top', 'margin-left', 'margin-bottom', 'margin-right',
			'padding', 'padding-top', 'padding-left', 'padding-bottom', 'padding-right',
			'border', 'border-top', 'border-left', 'border-bottom', 'border-right'
		);
		foreach($parsed as $block => $css){
			foreach($parsed[$block] as $selector => $styles){
				// Ignore @font-face
				if($selector != '@font-face'){
					foreach($parsed[$block][$selector] as $property => $values){
						foreach($parsed[$block][$selector][$property] as $key => $value){
							// Optimize hex colors
							if(in_array($property, $color_properties)){
								$parsed[$block][$selector][$property][$key] = preg_replace($color_pattern, '#\1\2\3', $value);
							}
							// Optimize tokenized strings
							if(in_array($property, $tokenized_properties)){
								$parsed[$block][$selector][$property][$key] = implode(',', $cssp->tokenize($value, ','));
							}
							// Optimize zeros
							if(in_array($property, $zero_properties)){
								$parsed[$block][$selector][$property][$key] = preg_replace($zero_pattern, '0', $value);
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
	$cssp->register_plugin('before_glue', 0, 'minifier');

?>