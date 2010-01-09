<?php


	/**
	 * Automatic bulletproof @font-face syntax
	 * Source: http://paulirish.com/2009/bulletproof-font-face-implementation-syntax/
	 * 
	 * Usage:
	 * 1) Add bp_font_face to @cssp plugin list
	 * 2) Build a normal @font-face-rule
	 * 3) Omit "src"-attribute
	 * 4) Add properties "-cssp-eot", "-cssp-local" and "-cssp-ttf" or "-cssp-otf"
	 * 
	 * Example:
	 * @font-face {
	 *     font-family:'Graublau Web';
	 *     -cssp-local:'Graublau Web Regular';
	 *     -cssp-otf:url('GraublauWeb.otf');
	 *     -cssp-eot:url('GraublauWeb.eot');
	 *     font-weight:bold;
	 *     font-style:italic;
	 * }
	 * Result:
	 * @font-face {
	 *     font-family: 'Graublau Web';
	 *     font-weight: bold;
	 *     font-style: italic;
	 *     src: url('GraublauWeb.eot');
	 *     src: local('Graublau Web Regular'), local('Graublau Web'), url('GraublauWeb.otf') format(opentype);
	 * }
	 * 
	 * Status:  Stable
	 * Version: 1.0
	 * 
	 * @todo Add support for svg and woff
	 * @param mixed &$parsed
	 * @return void
	 */
	function bp_font_face(&$parsed){
		foreach($parsed as $block => $css){
			if(isset($parsed[$block]['@font-face'])){
				foreach($parsed[$block]['@font-face'] as $key => $font){
					// Properties present?
					if(isset($font['-cssp-local']) && isset($font['-cssp-eot']) && (isset($font['-cssp-ttf']) || isset($font['-cssp-otf']))){
						// New font array
						$newfont = array();
						// Default format and url
						$main_format = NULL;
						$main_url = NULL;
						// Copy all properties that are not plugin specific to the new font array
						foreach($font as $property => $value){
							if(substr($property, 0, 6) != '-cssp-'){
								$newfont[$property] = $value;
							}
						}
						// Select main font format
						if(isset($font['-cssp-ttf'])){
							$main_format = 'truetype';
							$main_url = $font['-cssp-ttf'];
						}
						elseif(isset($font['-cssp-otf'])){
							$main_format = 'opentype';
							$main_url = $font['-cssp-otf'];
						}
						// Create eot src string
						$newfont['src'][] = $font['-cssp-eot'];
						// Create main src string
						$newfont['src'][] = 'local('.$font['-cssp-local'].'), local('.$font['font-family'].'), '.$main_url.' format('.$main_format.')';
						// Replace the old @font-face definition
						$parsed[$block]['@font-face'][$key] = $newfont;
					}
				}
			}
		}
	}


?>