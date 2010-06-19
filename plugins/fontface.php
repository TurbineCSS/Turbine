<?php

/**
 * This file is part of Turbine
 * http://github.com/SirPepe/Turbine
 * 
 * Copyright Peter Kröner
 * Licensed under GNU LGPL 3, see license.txt or http://www.gnu.org/licenses/
 */


/**
 * Automatic @font-face syntax
 * 
 * Usage:
 * 1) Add fontface to @turbine plugin list
 * 2) Put all different font-files into one directory and give them the same basename,
 *    e.g. "SaginaMedium": 
 *      SaginawMedium.eot
 *      SaginawMedium.woff
 *      SaginawMedium.otf
 *      SaginawMedium.ttf
 *      SaginawMedium.svg
 * 3) Build a special @font-face-rule with a single src-property pointing not to a real
 *    file but to that common basename, e.g. "src:url('fonts/SaginawMedium')"
 * 4) The plugin will look after any known fontfile format by appending the suffixes
 *    .eot, .woff, .otf, .ttf and .svg.
 *    For IE <= 8 it will serve the .eot if there is a corresponding file.
 *    For the other browser it will serve as many of the other flavors as available.
 *    A truetype-file will only be served when there is no opentype-file available.
 * 
 * Example:
 * @font-face
 *     font-family:'SaginawMedium'
 *     src:url('fonts/SaginawMedium')
 *     font-weight:bold
 *     font-style:italic
 * 
 * Result for IE <= 8:
 * @font-face {
 *     font-family: 'SaginawMedium';
 *     src: url("fonts/SaginawMedium.eot");
 *     font-weight: bold;
 *     font-style: italic;
 * }
 * 
 * Result for all other browsers:
 * @font-face {
 *     font-family: 'SaginawMedium';
 *     src: url("fonts/SaginawMedium.woff") format("woff"),url("fonts/SaginawMedium.ttf") format("truetype"),url("fonts/SaginawMedium.svg#SaginawMedium") format("svg");
 *     font-weight: bold;
 *     font-style: italic;
 * }
 * 
 * Status:  Stable
 * Version: 1.1
 * 
 * @todo Include a fix for webkit? http://paulirish.com/2010/font-face-gotchas/
 * @param mixed &$parsed
 * @return void
 */
function fontface(&$parsed){
	global $browser;
	$basedirectory = str_replace('\\','/',dirname(realpath($_SERVER['SCRIPT_FILENAME'])));
	foreach($parsed as $block => $css){
		if(isset($parsed[$block]['@font-face'])){
			foreach($parsed[$block]['@font-face'] as $key => $font){
				// Check if user has set required src-property
				if(isset($font['src'])){
					$num_src = count($font['src']);
					for($i = 0; $i < $num_src; $i++){
						// Extract common basename for all files
						$fontfile_base = preg_replace('/url\([\'"]*([^\'"]+)[\'"]*\)/i','$1',$font['src'][$i]);
						// Create new src-property storage
						$newfont = '';
						$message = '';
						// If we are dealing with IE <= 8 then check for EOT only
						if($browser->engine == 'ie' && floatval($browser->engine_version) <= 8){
							$fontfile_eot = $fontfile_base.'.eot';
							// If there exists an EOT-file point to it
							if(file_exists($basedirectory.'/'.$fontfile_eot)){
								$newfont .= 'url("'.$fontfile_eot.'")';
							}
							else $message .= 'Missing '.$fontfile_eot.'-file,';
						}
						// If we have another browser, check for all other file formats
						else{
							$fontfile_woff = $fontfile_base.'.woff';
							$fontfile_otf = $fontfile_base.'.otf';
							$fontfile_ttf = $fontfile_base.'.ttf';
							$fontfile_svg = $fontfile_base.'.svg';
							// If there exists an WOFF-file enqueue it
							if(file_exists($basedirectory.'/'.$fontfile_woff)){
								$newfont .= 'url("'.$fontfile_woff.'") format("woff"),';
							}
							else{
								$message .= 'Missing '.$fontfile_woff.'-file,';
							}
							// If there exists an OTF-file enqueue it
							if(file_exists($basedirectory.'/'.$fontfile_otf)){
								$newfont .= 'url("'.$fontfile_otf.'") format("opentype"),';
							}
							// If there is no OTF-file, but it exists an TTF-file enqueue that
							elseif(file_exists($basedirectory.'/'.$fontfile_ttf)){
								$newfont .= 'url("'.$fontfile_ttf.'") format("truetype"),';
								$message .= 'Missing '.$fontfile_otf.'-file,';
							}
							else{
								$message .= 'Missing '.$fontfile_otf.'-file,Missing '.$fontfile_ttf.'-file,';
							}
							// If there exists an SVG-file enqueue it
							if(file_exists($basedirectory.'/'.$fontfile_svg)) {
								$newfont .= 'url("'.$fontfile_svg.'#'.basename($fontfile_base).'") format("svg"),';
							}
							else{
								$message .= 'Missing '.$fontfile_svg.'-file,';
							}
						}
						// If we found at least one font replace old src-property with new one
						if($newfont != ''){
							$parsed[$block]['@font-face'][$key]['src'][$i] = trim($newfont,', ');
						}
						if($message != ''){
							CSSP::comment($parsed[$block]['@font-face'][$key], 'src', trim($message,', '));
						}
					}
				}
			}
		}
	}
}


/**
 * Register the plugin
 */
$cssp->register_plugin('before_glue', 0, 'fontface');


?>
