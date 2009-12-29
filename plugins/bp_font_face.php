<?php


	/**
	 * Automatic bulletproof @font-face syntax
	 * Source: http://paulirish.com/2009/bulletproof-font-face-implementation-syntax/
	 * 
	 * Usage:
	 * 1) Add bp_font_face to @cssp plugin list
	 * 2) Build a normal @font-face-rule
	 * 3) Omit "src"-attribute
	 * 4) Add properties "eot", "local" and "ttf" or "otf"
	 * 
	 * Example:
	 * @font-face {
	 *     font-family:'Graublau Web';
	 *     local:'Graublau Web Regular';
	 *     otf:url('GraublauWeb.otf');
	 *     eot:url('GraublauWeb.eot');
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
	 * Status: Beta
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
					if(isset($font['local']) && isset($font['eot']) && (isset($font['ttf']) || isset($font['otf']))){
						// New font array
						$newfont = array();
						// Default format and url
						$main_format = NULL;
						$main_url = NULL;
						// Copy all properties that are not plugin specific to the new font array
						$specific = array('local', 'ttf', 'otf', 'eot');
						foreach($font as $property => $value){
							if(!in_array($property, $specific)){
								$newfont[$property] = $value;
							}
						}
						// Select main font format
						if(isset($font['ttf'])){
							$main_format = 'truetype';
							$main_url = $font['ttf'];
						}
						elseif(isset($font['otf'])){
							$main_format = 'opentype';
							$main_url = $font['otf'];
						}
						// Create eot src string
						$newfont['src'][] = $font['eot'];
						// Create main src string
						$newfont['src'][] = 'local('.$font['local'].'), local('.$font['font-family'].'), '.$main_url.' format('.$main_format.')';
						// Replace the old @font-face definition
						$parsed[$block]['@font-face'][$key] = $newfont;
					}
				}
			}
		}
	}


?>