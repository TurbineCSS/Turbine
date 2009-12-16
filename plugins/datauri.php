<?php

	/**
	 * DataURI
	 * Injects all images smaller than 24KB right inside the CSS for all dataURI-capable browsers
	 * 
	 * Usage: TODO
	 * Example: TODO
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
				if(isset($parsed[$block][$selector]['background']) || isset($parsed[$block][$selector]['background-image']))
				{
					if(
						($browser->family == 'MSIE' && floatval($browser->familyversion) >= 8) || 
						$browser->family == 'Firefox' || 
						$browser->family == 'Opera' || 
						$browser->family == 'WebKit' || 
						$browser->family == 'KHTML'
					)
					{
						$regex = '/(url\()[\'"]*([^\'"\)]+)[\'"]*(\))/i';
						if(isset($parsed[$block][$selector]['background'])) 
						{
							echo "/* ".$parsed[$block][$selector]['background']." */\r\n";
							if(preg_match($regex,$parsed[$block][$selector]['background'],$matches) > 0)
							{
								$imagefile = dirname($file).'/'.$matches[2];
								echo "/* ".$imagefile." */\r\n";
								if(file_exists($imagefile))
								{
									$pathinfo = pathinfo($imagefile);
									$imagetype = strtolower($pathinfo['extension']);
									$imagedata = base64_encode(file_get_contents($imagefile));
									$parsed[$block][$selector]['background'] = preg_replace($regex,'$1\'data:image/'.$imagetype.';base64,'.$imagedata.'\'$3',$parsed[$block][$selector]['background']);
								}
							}
						}
						if(isset($parsed[$block][$selector]['background-image'])) 
						{
							echo "/* ".$parsed[$block][$selector]['background-image']." */\r\n";
							if(preg_match($regex,$parsed[$block][$selector]['background-image'],$matches) > 0)
							{
								$imagefile = dirname($file).'/'.$matches[2];
								echo "/* ".$imagefile." */\r\n";
								if(file_exists($imagefile))
								{
									$pathinfo = pathinfo($imagefile);
									$imagetype = strtolower($pathinfo['extension']);
									$imagedata = base64_encode(file_get_contents($imagefile));
									$parsed[$block][$selector]['background-image'] = preg_replace($regex,'$1\'data:image/'.$imagetype.';base64,'.$imagedata.'\'$3',$parsed[$block][$selector]['background-image']);
								}
							}
						}
					} 
				}
			}
		}
	}

?>