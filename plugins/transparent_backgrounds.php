<?php

	/**
	 * Cross-browser transparent backgrounds
	 * 
	 * @param mixed $parsed
	 * @return void
	 */
	function transparent_backgrounds($parsed){
		foreach($parsed as $block => $css){
			foreach($parsed[$block] as $selector => $styles){
				if($parsed[$block][$selector]['transparent-background']){
					// Get RGBA values
					$values = array();
					$rgbapattern = '/rgba\([\s]*(.*?)[\s]*,[\s]*(.*?)[\s]*,[\s]*(.*?)[\s]*,[\s]*(.*?)[\s]*\)/i';
					preg_match_all($rgbapattern, $parsed[$block][$selector]['transparent-background'], $values);
					// Solid-color fallback
					$fallback = 'rgb('.$values[1][0].','.$values[2][0].','.$values[3][0].')';
					// Calculate alpha value
					$alpha = 127 - 127 * $values[4][0];
					// Create image
					$i = imagecreatetruecolor(1, 1);
					$c = imagecolorallocatealpha($i, $values[1][0], $values[2][0], $values[3][0], $alpha);
					imagefill($i, 0, 0, $c);
					imagealphablending($i, false);
					imagesavealpha($i, true);
					ob_start();
					imagepng($i);
					$imagestring = ob_get_clean();
					$imagestring = base64_encode($imagestring);
					// Set as background
					$parsed[$block][$selector]['background'] = array(
						$fallback,
						"url('data:image/png;base64,".$imagestring."')"
					);
					// Unset original transparent-backgrounds-property
					unset($parsed[$block][$selector]['transparent-background']);
				}
			}
		}
	}

?>