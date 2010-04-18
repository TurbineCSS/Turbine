<?php


	/**
	 * A bunch of general browser bugfixes
	 * 
	 * Usage: Nobrainer, just switch it on
	 * Example: -
	 * Status: Beta
	 * 
	 * @param mixed &$parsed
	 * @return void
	 */
	function bugfixes(&$parsed){
		global $cssp, $browser;
		$changed = array();

		// IE 6 global bugfixes
		if($browser->engine == 'MSIE' && floatval($browser->engineversion) < 7){
			// Image margin bottom bug
			$changed['img']['vertical-align'][] = 'bottom';
			// Background image flickers on hover
			$changed['html']['filter'][] = 'expression(document.execCommand("BackgroundImageCache",false,true))';
		}

		// IE 6 + 7 global bugfixes
		if($browser->engine == 'MSIE' && floatval($browser->engineversion) < 8){
			// Enable full styleability for IE-buttons, see http://www.sitepoint.com/forums/showthread.php?t=547059
			$changed['button']['overflow'][] = 'visible';
			$changed['button']['width'][] = 'auto';
			$changed['button']['white-space'][] = 'nowrap';
		}

		// Firefox global bugfixes
		if($browser->engine == 'Gecko'){
			// Ghost margin around buttons, see http://www.sitepoint.com/forums/showthread.php?t=547059
			$changed['button::-moz-focus-inner']['padding'][] = '0';
			$changed['button::-moz-focus-inner']['border'][] = 'none';
		}

		// Insert the global bugfixes
		$cssp->insert($changed, 'global');
		// print_r($changed);

/*
			foreach($parsed[$block] as $selector => $styles){

				// IE 6 local bugfixes
				if($browser->family == 'MSIE' && floatval($browser->familyversion) < 7){
					// Float double margin bug, fixed with a behavior as this only affects the floating object and no descendant of it
					if(isset($parsed[$block][$selector]['float']) && $parsed[$block][$selector]['float'] != 'none'){
						$htc_path = rtrim(dirname($_SERVER['SCRIPT_NAME']),'/').'/plugins/bugfixes/doublemargin.htc';
						if(!isset($parsed[$block][$selector]['behavior'])){
							$parsed[$block][$selector]['behavior'] = 'url("'.$htc_path.'")';
						}
						else{
							if(!strpos($parsed[$block][$selector]['behavior'],'url("'.$htc_path.'")')){
								$parsed[$block][$selector]['behavior'] .= ', url("'.$htc_path.'")';
							}
						}
					}
				}
			
				// IE 6 + 7 local bugfixes
				if($browser->family == 'MSIE' && floatval($browser->familyversion) < 8){
					// Enable overflow:hidden, if present
					if(isset($parsed[$block][$selector]['overflow']) && $parsed[$block][$selector]['overflow'] == 'hidden' && !isset($parsed[$block][$selector]['position'])){
						$parsed[$block][$selector]['position'] = 'relative';
						CSSP::comment($parsed[$block][$selector], 'position', 'Added by bugfix plugin');
					}
				}

			}

		}*/
	}


	/**
	 * Register the plugin
	 */
	$cssp->register_plugin('before_compile', 0, 'bugfixes');


?>