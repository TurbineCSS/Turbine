<?php

	/**
	 * AlphaBG - Cross-browser transparent backgrounds
	 * 
	 * Usage:   alpha-background:rgba(red [0-255], green [0-255], blue [0-255], alpha [0-1]);
	 * Example: alpha-background:rgba(0, 255, 20, 0.25);
	 * Status:  Stable
	 * Version: 1.0
	 * 
	 * @todo Add gradient filter for IE6
	 * @param mixed &$parsed
	 * @return void
	 */
	function alphabg(&$parsed){
		global $browser;
		foreach($parsed as $block => $css){
			foreach($parsed[$block] as $selector => $styles){
				if(isset($parsed[$block][$selector]['alpha-background'])){
					// Get RGBA values
					$values = array();
					$rgbapattern = '/rgba\([\s]*(.*?)[\s]*,[\s]*(.*?)[\s]*,[\s]*(.*?)[\s]*,[\s]*(.*?)[\s]*\)/i';
					preg_match_all($rgbapattern, $parsed[$block][$selector]['alpha-background'], $values);
					// Solid-color fallback
					$fallback = 'rgb('.$values[1][0].','.$values[2][0].','.$values[3][0].')';
					// PNG-Data-URI for modern browsers
					if($browser->family != 'MSIE' || floatval($browser->familyversion) > 7){
						$alpha = 127 - 127 * $values[4][0];
						$i = imagecreatetruecolor(1, 1);
						$c = imagecolorallocatealpha($i, $values[1][0], $values[2][0], $values[3][0], $alpha);
						imagefill($i, 0, 0, $c);
						imagealphablending($i, false);
						imagesavealpha($i, true);
						ob_start();
						imagepng($i);
						$imagestring = ob_get_clean();
						$imagestring = base64_encode($imagestring);
						$alphabg = "url('data:image/png;base64,".$imagestring."')";
					}
					// TODO: Gradient filter for IE < 8
					else{
						$alphabg = '';
					}
					// Set as background
					$parsed[$block][$selector]['background'] = array(
						$fallback,
						$alphabg
					);
					// Unset original transparent-backgrounds-property
					unset($parsed[$block][$selector]['alpha-background']);
				}
			}
		}
	}


	/**
	 * Register the plugin
	 */
	$cssp->register_plugin('before_compile', 0, 'alphabg');


?>