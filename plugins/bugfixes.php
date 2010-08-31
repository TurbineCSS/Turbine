<?php

/**
 * This file is part of Turbine
 * http://github.com/SirPepe/Turbine
 * 
 * Copyright Peter Kröner
 * Licensed under GNU LGPL 3, see license.txt or http://www.gnu.org/licenses/
 */


/**
 * A bunch of general browser bugfixes
 * 
 * Usage: Nobrainer, just switch it on
 * Example: -
 * Status:  Stable
 * Version: 1.2
 * 
 * @param mixed &$parsed
 * @return void
 */
function bugfixes(&$parsed){
	global $cssp, $browser;
	$changed = array();

	// IE: remove scrollbars from textareas
	$changed['textarea']['overflow'][] = 'auto';

	if($browser->browser == 'ie' && floatval($browser->browser_version) < 7){
		// IE6: Image margin bottom bug
		$changed['img']['vertical-align'][] = 'bottom';
	
		// IE6: Background image flickers on hover
		$changed['html']['filter'][] = 'expression(document.execCommand("BackgroundImageCache",false,true))';
	
		// IE6: Fix transparent PNGs, see http://www.twinhelix.com/css/iepngfix/
		$htc_path = rtrim(dirname($_SERVER['SCRIPT_NAME']),'/').'/plugins/bugfixes/iepngfix.htc';
		$changed['img']['behavior'][] = 'url("'.$htc_path.'")';
		
		// IE6: Input align, see http://tjkdesign.com/ez-css/css/base.css
		$changed['img']['vertical-align'][] = 'text-bottom';
	}	
	// IE 7: resample images bicubic instead of using nearest neighbor method
	$changed['img']['-ms-interpolation-mode'][] = 'bicubic';

	// IE6 and 7: Enable full styleability for buttons, see http://www.sitepoint.com/forums/showthread.php?t=547059
	$changed['button']['overflow'][] = 'visible';
	$changed['button']['width'][] = 'auto';
	$changed['button']['white-space'][] = 'nowrap';

	// IE6 and 7: Missing :hover-property on every tag except a, see http://www.xs4all.nl/~peterned/csshover.html
	// IE8: Reenable cleartype where filters are set
	$htc_path = rtrim(dirname($_SERVER['SCRIPT_NAME']),'/').'/plugins/bugfixes/';
	$changed['body']['behavior'][] = 'url("'.$htc_path.'csshover3.htc") url("'.$htc_path.'cleartypefix.htc")';

	// Firefox: Ghost margin around buttons, see http://www.sitepoint.com/forums/showthread.php?t=547059
	$changed['button::-moz-focus-inner']['padding'][] = '0';
	$changed['button::-moz-focus-inner']['border'][] = 'none';

	// Webkit: better antialiasing, see http://maxvoltar.com/archive/-webkit-font-smoothing
	$changed['html']['-webkit-font-smoothing'][] = 'antialiased';
	
	// Webkit: better kerning, see http://www.aestheticallyloyal.com/public/optimize-legibility/
	$changed['html']['text-rendering'][] = 'optimizeLegibility';

	// Add comments for the global fixes
	foreach($changed as $selector => $styles){
		foreach($styles as $property => $value){
			CSSP::comment($changed[$selector], $property, 'Added by bugfix plugin');
		}
	}

	// Insert the global bugfixes
	$cssp->insert($changed, 'global');

	// Apply per-block-bugfixes
	foreach($cssp->parsed as $block => $css){

		// Firefox: overflow:hidden printing bug
		if(!isset($cssp->parsed[$block]['body']) || !isset($cssp->parsed[$block]['body']['overflow']))
		{
			$cssp->parsed[$block]['body']['overflow'][] = 'visible !important';
			CSSP::comment($cssp->parsed[$block]['body'], 'overflow', 'Added by bugfix plugin');
		}

		// Apply per-element-bugfixes
		foreach($cssp->parsed[$block] as $selector => $styles){

			// IE 6 per-element-bugfixes
			if($browser->browser == 'ie' && floatval($browser->browser_version) < 7){
				// Float double margin bug, fixed with a behavior as this only affects the floating object and no descendant of it
				if(isset($cssp->parsed[$block][$selector]['float']) && $cssp->get_final_value($cssp->parsed[$block][$selector]['float']) != 'none'){
					$htc_path = rtrim(dirname($_SERVER['SCRIPT_NAME']),'/').'/plugins/bugfixes/doublemargin.htc';
					$cssp->parsed[$block][$selector]['behavior'][] = 'url("'.$htc_path.'")';
					CSSP::comment($cssp->parsed[$block][$selector], 'behavior', 'Added by bugfix plugin');
				}
				// Min-height for IE6
				if(isset($cssp->parsed[$block][$selector]['min-height']) && !isset($cssp->parsed[$block][$selector]['height'])){
					$cssp->parsed[$block][$selector]['height'] = $cssp->parsed[$block][$selector]['min-height'];
					CSSP::comment($cssp->parsed[$block][$selector], 'height', 'Added by bugfix plugin');
				}
			}

			// IE 6 + 7 per-element-bugfixes
			if($browser->browser == 'ie' && floatval($browser->browser_version) < 8){
				// Enable overflow:hidden, if present
				if(isset($cssp->parsed[$block][$selector]['overflow']) && $cssp->get_final_value($cssp->parsed[$block][$selector]['overflow']) == 'hidden' && !isset($cssp->parsed[$block][$selector]['position'])){
					$cssp->parsed[$block][$selector]['position'][] = 'relative';
					CSSP::comment($cssp->parsed[$block][$selector], 'position', 'Added by bugfix plugin');
				}
			}
		}
	}

}


/**
 * Register the plugin
 */
$cssp->register_plugin('before_compile', 0, 'bugfixes');


?>
