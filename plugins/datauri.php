<?php

	/**
	 * Data-URI
	 * Injects all images smaller than 24KB right inside the CSS for all dataURI-capable browsers
	 * 
	 * Usage: Nobrainer, just switch it on
	 * Example: -
	 * Status: Beta
	 * 
	 * @todo Don't generate MHTML files for non-IE-browsers
	 * 
	 * @param mixed &$parsed
	 * @return void
	 */
	function datauri(&$parsed){
		global $browser;
		global $file;
		$mhtmlmd5 = md5(
			$browser->platform.
			$browser->platformversion.
			$browser->platformtype.
			$browser->engine.
			$browser->engineversion.
			$browser->family.
			$browser->familyversion.
			$browser->name.
			$browser->version.
			$file
		);
		// Start preparing MHTML
		$mhtmlfile = 'cache/'.$mhtmlmd5.'_mhtml.txt';
		$mhtmlarray = array();
		$mhtmlcontent = "Content-Type: multipart/related; boundary=\"_ANY_STRING_WILL_DO_AS_A_SEPARATOR\"\r\n\r\n";
		foreach($parsed as $block => $css){
			foreach($parsed[$block] as $selector => $styles){
				// Check for backgrounds, ignore @font-face
				if($selector != '@font-face' && (isset($parsed[$block][$selector]['background']) || isset($parsed[$block][$selector]['background-image']))){
					$regex = '/(url\()[\'"]*([^\'"\)]+)[\'"]*(\))/i';
					$properties = array('background','background-image','src');
					$basedirectories = array('', dirname($file), str_replace('\\','/',dirname(realpath($_SERVER['SCRIPT_FILENAME']))),str_replace('\\','/',dirname(__FILE__)));
					foreach($properties as $property){
						foreach($basedirectories as $basedirectory){
							if(isset($parsed[$block][$selector][$property])){
								// Loop through values
								$num_values = count($parsed[$block][$selector][$property]);
								for($i = 0; $i < $num_values; $i++){
									if(preg_match($regex, $parsed[$block][$selector][$property][$i], $matches) > 0){
										$imagefile = ($basedirectory) ? $basedirectory.'/'.$matches[2] : $matches[2];
										if(file_exists($imagefile) && filesize($imagefile) <= 24000){
											$pathinfo = pathinfo($imagefile);
											$imagetype = strtolower($pathinfo['extension']);
											$imagedata = base64_encode(file_get_contents($imagefile));
											if(
												($browser->family == 'MSIE' && floatval($browser->familyversion) >= 8) || 
												$browser->engine == 'Gecko' || 
												$browser->family == 'Opera' || 
												$browser->family == 'WebKit' || 
												$browser->family == 'KHTML'
											){
												$parsed[$block][$selector][$property][$i] = preg_replace($regex, '$1\'data:image/'.$imagetype.';base64,'.$imagedata.'\'$3', $parsed[$block][$selector][$property][$i]);
											}
											elseif(
												$browser->family == 'MSIE' && 
												(
													(floatval($browser->familyversion) < 7 && $browser->platform == 'Windows') || 
													(floatval($browser->familyversion) < 8 && $browser->platform == 'Windows'  && $browser->platformversion < 6)
												) 
											){
												// Calculate identifier and anchor-tag for the MHTML-file
												$imagetag = 'img'.md5($imagefile);
												// Look up in our list if we did not already process that exact file, if not append it
												if(!isset($mhtmlarray[$imagetag])) {
													$mhtmlcontent .= "--_ANY_STRING_WILL_DO_AS_A_SEPARATOR\r\n";
													$mhtmlcontent .= "Content-Location:".$imagetag."\r\n";
													$mhtmlcontent .= "Content-Transfer-Encoding:base64\r\n\r\n";
													$mhtmlcontent .= base64_encode(file_get_contents($imagefile))."==\r\n";
													// Put file on our processed-list
													$mhtmlarray[$imagetag] = 1;
												}
												if(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'https'){
													$protocol = 'https';
												}
												else{
													$protocol = 'http';
												}
												// Find host, even in strange evironments (Strato hosting)
												if(isset($_SERVER['SCRIPT_URI'])){
													$host = parse_url($_SERVER['SCRIPT_URI'], PHP_URL_HOST);
												}
												else{
													$host = $_SERVER['HTTP_HOST'];
												}
												// Set the data URI
												$parsed[$block][$selector][$property][$i] = preg_replace($regex, '$1\'mhtml:'.$protocol.'://'.$host.rtrim(dirname($_SERVER['SCRIPT_NAME']),'/').'/plugins/datauri/mhtml.php?cache='.$mhtmlmd5.'!'.$imagetag.'\'$3', $parsed[$block][$selector][$property][$i]);
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
		$mhtmlcontent .= "\r\n\r\n";
		// Store the MHTML cache-file
		file_put_contents($mhtmlfile,$mhtmlcontent);
		chmod($mhtmlfile,0777);
	}


	/**
	 * Register the plugin
	 */
	$cssp->register_plugin('before_glue', 0, 'datauri');


?>