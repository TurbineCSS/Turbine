<?php


	/**
	 * Automatic bulletproof @font-face syntax
	 * Usage:
	 * Example:
	 * 
	 * @param mixed $parsed
	 * @return void
	 */
	function bp_fontface($parsed){
		foreach($parsed as $block => $css){
			foreach($parsed[$block] as $selector => $styles){
				// Find @font-Declarations
				if(substr($selector, 0, 5) == '@font'){
					// Begin a new @font-face element
					$fontface = array();
					// Extract font name
					preg_match_all('/@font\s+(.*)/', $selector, $matches);
					$fontname = $matches[1][0];
					// Build @font-face-rule for each style
					foreach($styles as $style => $urls){
						// Add font family
						$fontface['font-family'] = $fontname;
						// Add style information
						$fontstyles = explode(' ', $style);
						if($fontstyles[0]){ // Style
							if($fontstyle == 'all' || $fontstyle == 'italic' || $fontstyle == 'oblique'){
								$fontface['font-style'] = $fontstyle;
							}
						}
						if($fontstyles[1]){ // Weight
							if($fontstyle == 'bold' || $fontstyle == 'bolder' || $fontstyle == 'lighter' || preg_match('/^[1-9]00$/', $fontstyle)){
								$fontface['font-weight'] = $fontstyle;
							}
						}
						
						
						print_r($fontface);
					}
				}
			}
		}
	}


?>