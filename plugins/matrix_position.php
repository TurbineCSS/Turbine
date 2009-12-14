<?php


	/**
	 * Automatic position für image matrix
	 * 
	 * Usage: TODO
	 * Example: TODO
	 * Status: Work in progress
	 * 
	 * @param mixed &$parsed
	 * @return void
	 */
	function matrix_position(&$parsed){
		foreach($parsed as $block => $css){
			foreach($parsed[$block] as $selector => $styles){
				if(isset($parsed[$block][$selector]['matrix']) && isset($parsed[$block][$selector]['matrix-position'])){
					$matrix = explode(' ', trim($parsed[$block][$selector]['matrix']));
					$sectionWidth = intval($matrix[0]);
					$sectionHeight = intval($matrix[1]);
					$sections = explode(' ', trim($parsed[$block][$selector]['matrix-position']));
					$x = ($sectionWidth * $sections[0] - $sectionWidth) *-1;
					$y = ($sectionHeight * $sections[1] - $sectionHeight) *-1;
					if($x != 0){
						$x .= 'px';
					}
					if($y != 0){
						$y .= 'px';
					}
					$parsed[$block][$selector]['background-position'] = $x.' '.$y;
					unset($parsed[$block][$selector]['matrix']);
					unset($parsed[$block][$selector]['matrix-position']);
				}
			}
		}
	}


?>