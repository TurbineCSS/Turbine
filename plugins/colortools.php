<?php


	/**
	 * Colortools
	 * 
	 * 
	 * Usage:
	 * Result:
	 * Status: Experimental
	 * Version: 0.1
	 * 
	 * @param mixed &$parsed
	 * @return void
	 */
	function colortools(&$parsed){
		$properties = array('background', 'background-color', 'color', 'border', 'border-color',
			'border-top', 'border-left', 'border-bottom', 'border-right', 'text-shadow', 'box-shadow');
		foreach($properties as $search){
			foreach($parsed as $block => $css){
				foreach($parsed[$block] as $selector => $styles){
					if(isset($parsed[$block][$selector][$search])){
						// Search for colortool expressions
						$pattern = '/&(darker|lighter)[\s]*\([\s]*(.*?)[\s]*,[\s]*(.*?)%[\s]*\)/';
						$parsed[$block][$selector][$search] = preg_replace_callback($pattern, colortools_parse_expressions, $parsed[$block][$selector][$search]);
					}
				}
			}
		}
	}


	/**
	 * colortools_parse_expressions
	 * Callback for managing the different color operations
	 * @param array $matches The values returned by preg_replace_callback
	 * @return string The result of the colortools_* operation
	 */
	function colortools_parse_expressions($matches){
		return call_user_func('colortools_'.$matches[1], $matches[2], $matches[3]);
	}


	/**
	 * colortools_darker
	 * Darkens $color by $percentage
	 * @param string $color The color to darken
	 * @package int $percentage The percentage to darken by
	 */
	function colortools_darker($color, $percentage){
		$rgb = colortools_hexrgb($color);
		$hex = "#";
		foreach($rgb as $c){
			$diff = $c/100*$percentage;
			$hex .= ($c <= $diff) ? '00' : dechex($c - $diff);
		}
		return $hex;
	}


	/**
	 * colortools_lighter
	 * Lightens $color by $percentage
	 * @param string $color The color to darken
	 * @package int $percentage The percentage to darken by
	 */
	function colortools_lighter($color, $percentage){
		$rgb = colortools_hexrgb($color);
		$hex = "#";
		foreach($rgb as $c){
			$diff = $c/100*$percentage;
			$hex .= ($diff + $c >= 255) ? 'FF' : dechex($c + $diff);
		}
		return $hex;
	}


	/**
	 * colortools_hexrgb
	 * Converts hex color to rgb
	 * 
	 * @todo Expand shorthand hex like #f00 to #ff0000
	 * @todo Process rgb[a] colors too
	 * @return array $rgb The RGB values
	 */
	function colortools_hexrgb($hex){
		if(strlen($hex) == 4){
			$hex = '#'.$hex{1}.$hex{1}.$hex{2}.$hex{2}.$hex{3}.$hex{3};
		}
		$int = hexdec($hex);
		$rgb = array('r' => 0xFF & ($int >> 0x10), 'g' => 0xFF & ($int >> 0x8), 'b' => 0xFF & $int);
		return $rgb;
	}


	/**
	 * Register the plugin
	 */
	register_plugin('before_glue', 0, 'colortools');


?>