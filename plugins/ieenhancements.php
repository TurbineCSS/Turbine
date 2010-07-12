<?php

/**
 * This file is part of Turbine
 * http://github.com/SirPepe/Turbine
 * 
 * Copyright Peter Kröner
 * Licensed under GNU LGPL 3, see license.txt or http://www.gnu.org/licenses/
 */


/**
 * IE enhancements
 * Enables a bunch of usually absent features in IE 6 and 7
 * 
 * Usage: Nobrainer, just switch it on
 * Example: -
 * Status: Stable
 * Version: 1.0
 * 
 * @param mixed &$parsed
 * @return void
 */
function ieenhancements(&$parsed){
	global $browser, $cssp;
	if($browser->engine == 'ie'){
		// Fixes for IE 6 and 7
		if(floatval($browser->engine_version) < 8){
			// Fixes for IE 6 only
			if(floatval($browser->engine_version) < 7){
				// Missing :hover-property on every tag except link-tag, see http://www.xs4all.nl/~peterned/csshover.html
				$htc_path = trim(dirname($_SERVER['SCRIPT_NAME']),'/').'/plugins/ieenhancements/csshover3.htc';
				$parsed['global']['body']['behavior'][] = 'url("'.$htc_path.'")';
				CSSP::comment($parsed['global']['body'], 'behavior', 'Modified/Added by IE enhancements plugin');
				// Fix transparent PNGs, see http://www.twinhelix.com/css/iepngfix/
				$htc_path = trim(dirname($_SERVER['SCRIPT_NAME']),'/').'/plugins/ieenhancements/iepngfix.htc';
				$parsed['global']['img']['behavior'][] = 'url("'.$htc_path.'")';
				CSSP::comment($parsed['global']['img'], 'behavior', 'Modified/Added by IE enhancements plugin');
			}
		}
	}
}


/**
 * Register the plugin
 */
$cssp->register_plugin('before_compile', 0, 'ieenhancements');


?>
