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
					if($browser->family != 'MSIE' || ($browser->family == 'MSIE' && floatval($browser->familyversion) >= 8)){
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
						// Set as background
						$parsed[$block][$selector]['background'] = array(
							$fallback,
							$alphabg
						);
					}
					// Gradient filter for IE < 8
					else{
						$alphabg = '';
						$filteropacity = strtoupper(str_pad(dechex(round(floatval($values[4][0]) * 255)),2,'0',STR_PAD_LEFT));
						$filtercolor_r = strtoupper(str_pad(dechex(floatval($values[1][0])),2,'0',STR_PAD_LEFT));
						$filtercolor_g = strtoupper(str_pad(dechex(floatval($values[2][0])),2,'0',STR_PAD_LEFT));
						$filtercolor_b = strtoupper(str_pad(dechex(floatval($values[3][0])),2,'0',STR_PAD_LEFT));
						$filter = 'progid:DXImageTransform.Microsoft.gradient(startColorstr=#'.$filteropacity.$filtercolor_r.$filtercolor_g.$filtercolor_b.',endColorstr=#'.$filteropacity.$filtercolor_r.$filtercolor_g.$filtercolor_b.')';
						if(!isset($parsed[$block][$selector]['filter'])) 
						{
							$parsed[$block][$selector]['filter'] = $filter;
						}
						else 
						{
							if(!strpos($parsed[$block][$selector]['filter'],$filter)) $parsed[$block][$selector]['filter'] .= ' '.$filter;
						}
						$parsed[$block][$selector]['zoom'] = 1;
					}
					// Unset original transparent-backgrounds-property
					unset($parsed[$block][$selector]['alpha-background']);
				}
			}
		}
	}


	/**
	 * Register the plugin
	 */
	$cssp->register_plugin('before_glue', 0, 'alphabg');


?>