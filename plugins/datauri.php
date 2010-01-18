<?php

	/**
	 * Data-URI
	 * Injects all images smaller than 24KB right inside the CSS for all dataURI-capable browsers
	 * 
	 * Usage: Nobrainer, just switch it on
	 * Example: -
	 * Status: Beta
	 * 
	 * @param mixed &$parsed
	 * @return void
	 */
	function datauri(&$parsed){
		global $browser;
		global $file;
		foreach($parsed as $block => $css){
			foreach($parsed[$block] as $selector => $styles){
				// Check for backgrounds
				if(isset($parsed[$block][$selector]['background']) || isset($parsed[$block][$selector]['background-image'])){
					if(
						($browser->family == 'MSIE' && floatval($browser->familyversion) >= 8) || 
						$browser->family == 'Firefox' || 
						$browser->family == 'Opera' || 
						$browser->family == 'WebKit' || 
						$browser->family == 'KHTML'
					){
						$regex = '/(url\()[\'"]*([^\'"\)]+)[\'"]*(\))/i';
						$properties = array('background','background-image','src');
						$basedirectories = array(dirname($file),str_replace('\\','/',dirname(realpath($_SERVER['SCRIPT_FILENAME']))),str_replace('\\','/',dirname(__FILE__)));
						foreach($properties as $property){
							foreach($basedirectories as $basedirectory){
								if(isset($parsed[$block][$selector][$property])) {
									if(preg_match($regex,$parsed[$block][$selector][$property],$matches) > 0){
										$imagefile = $basedirectory.'/'.$matches[2];
										if(file_exists($imagefile) && filesize($imagefile) <= 24000){
											$pathinfo = pathinfo($imagefile);
											$imagetype = strtolower($pathinfo['extension']);
											$imagedata = base64_encode(file_get_contents($imagefile));
											$parsed[$block][$selector][$property] = preg_replace($regex,'$1\'data:image/'.$imagetype.';base64,'.$imagedata.'\'$3',$parsed[$block][$selector][$property]);
										}
									}
								}
							}
						}
					} 
				}
			}
		}
	}

?>