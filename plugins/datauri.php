<?php

/**
 * This file is part of Turbine
 * http://github.com/SirPepe/Turbine
 * 
 * Copyright Peter Kröner
 * Licensed under GNU LGPL 3, see license.txt or http://www.gnu.org/licenses/
 */


/**
 * Data-URI
 * Injects all images smaller than 24KB right inside the CSS for all dataURI-capable browsers
 * 
 * Usage: Nobrainer, just switch it on
 * Example: -
 * Status:  Probably unstable as hell
 * Version: ?
 * 
 * 
 * datauri
 * @param mixed &$parsed
 * @return void
 */
function datauri(&$parsed){
	// Find out if we have to use mhtml or normal data uris
	$mode = datauri_get_mode();
	// Process the array
	if($mode !== NULL){
		// Setup the mhtml-specific stuff
		if($mode == 'mhtml'){
			$mhtmlmd5 = datauri_get_mhtmlhash();
			$mhtmlfile = 'cache/'.$mhtmlmd5.'_mhtml.txt';
			$mhtmlarray = array();
			$mhtmlcontent = "Content-Type: multipart/related; boundary=\"_ANY_STRING_WILL_DO_AS_A_SEPARATOR\"\r\n\r\n";
		}
		$urlregex = '/(url\()[\'"]*([^\'"\)]+)[\'"]*(\))/i';
		$urlproperties = array('background', 'background-image', 'src');
		// Loop through the array
		foreach($parsed as $block => $css){
			foreach($parsed[$block] as $selector => $styles){
				if($selector != '@font-face'){// Ignore @font-face
					foreach($urlproperties as $property){
						if(isset($parsed[$block][$selector][$property])){
							$num_values = count($parsed[$block][$selector][$property]);
							for($i = 0; $i < $num_values; $i++){
								if(preg_match($urlregex, $parsed[$block][$selector][$property][$i], $matches) > 0){
									$file = datauri_get_file($matches[2]);
									if($file !== NULL){
										// Use a normal datauri
										if($mode == 'datauri'){
											$parsed[$block][$selector][$property][$i] = preg_replace($urlregex, '$1\'data:image/'.$file['imagetype'].';base64,'.$file['imagedata'].'\'$3', $parsed[$block][$selector][$property][$i]);
										}
										// Use a mhtml file
										elseif($mode == 'mhtml'){
											// Calculate identifier and anchor-tag for the MHTML-file
											$imagetag = 'img'.md5($file['imagedata']);
											// Look up in our list if we did not already process that exact file, if not append it
											if(!isset($mhtmlarray[$imagetag])) {
												$mhtmlcontent .= "--_ANY_STRING_WILL_DO_AS_A_SEPARATOR\r\n";
												$mhtmlcontent .= "Content-Location:".$imagetag."\r\n";
												$mhtmlcontent .= "Content-Transfer-Encoding:base64\r\n\r\n";
												$mhtmlcontent .= $file['imagedata']."==\r\n";
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
											$parsed[$block][$selector][$property][$i] = preg_replace($urlregex, '$1\'mhtml:'.$protocol.'://'.$host.rtrim(dirname($_SERVER['SCRIPT_NAME']),'/').'/plugins/datauri/mhtml.php?cache='.$mhtmlmd5.'!'.$imagetag.'\'$3', $parsed[$block][$selector][$property][$i]);
										}
									}
								}
							}
						}
					}
				}
			}
		}
		// Save the mhtml file, if there is one to save
		if($mode == 'mhtml' && !empty($mhtmlarray)){
			$mhtmlcontent .= "\r\n\r\n";
			file_put_contents($mhtmlfile, $mhtmlcontent);
			chmod($mhtmlfile, 0777);
		}
	}
}


/*
 * datauri_get_mode
 * Find out if we have to use mhtml or normal data uris
 * @return string $mode The mode to use (NULL, 'datauri' or 'mhtml')
 */
function datauri_get_mode(){
	global $browser;
	$mode = NULL;
	if(
		($browser->engine == 'ie' && floatval($browser->engine_version) >= 8) || 
		$browser->engine == 'gecko' ||
		$browser->engine == 'opera' ||
		$browser->engine == 'webkit' ||
		$browser->engine == 'khtml'
	){
		$mode = 'datauri';
	}
	elseif(
		$browser->engine == 'ie' &&
		(
			(floatval($browser->engine_version) < 7 && $browser->platform == 'windows') || 
			(floatval($browser->engine_version) < 8)
		)
	){
		$mode = 'mhtml';
	}
	return $mode;
}



/*
 * datauri_get_file
 * Find the file $filename, return an array containing the encoded file and file information
 * @param string $filename
 * @return array $file
 */
function datauri_get_file($filename){
	$file = NULL;
	$basedirectories = array(
		'',
		dirname($file),
		str_replace('\\','/',
		dirname(realpath($_SERVER['SCRIPT_FILENAME']))),
		str_replace('\\','/',
		dirname(__FILE__))
	);
	foreach($basedirectories as $basedirectory){
		$imagefile = ($basedirectory) ? $basedirectory.'/'.$filename : $filename;
		if(file_exists($imagefile) && filesize($imagefile) <= 24000){
			$pathinfo = pathinfo($imagefile);
			$file = array(
				'pathinfo' => $pathinfo,
				'imagetype' => strtolower($pathinfo['extension']),
				'imagedata' => base64_encode(file_get_contents($imagefile)),
			);
			break;
		}
	}
	return $file;
}


/*
 * datauri_get_mhtmlhash
 * Gets the browser-unique mhtml hash for the current file
 * @return string $mhtmlmd5
 */
function datauri_get_mhtmlhash(){
	global $browser, $file;
	$mhtmlmd5 = md5(
		$browser->platform.
		$browser->platform_version.
		$browser->platform_type.
		$browser->engine.
		$browser->engine_version.
		$browser->browser.
		$browser->browser_version.
		$file
	);
	return $mhtmlmd5;
}


/**
 * Register the plugin
 */
$cssp->register_plugin('before_glue', 0, 'datauri');


?>
