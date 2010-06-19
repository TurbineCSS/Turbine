<?php

/**
 * This file is part of Turbine
 * http://github.com/SirPepe/Turbine
 * 
 * Copyright Peter Kröner
 * Licensed under GNU LGPL 3, see license.txt or http://www.gnu.org/licenses/
 */


/**
 * Inline Block
 * Implements the "display: inline-block" property for all current browsers
 * 
 * Usage: Nobrainer, just switch it on
 * Example: -
 * Status: Stable
 * Version: 1.0
 * 
 * @param mixed &$parsed
 * @return void
 */
function inlineblock(&$parsed){
	global $browser, $cssp;
	foreach($cssp->parsed as $block => $css){
		foreach($cssp->parsed[$block] as $selector => $styles){
			// Everywhere
			if(isset($cssp->parsed[$block][$selector]['display']) && $cssp->get_final_value($cssp->parsed[$block][$selector]['display'], 'display') == 'inline-block'){
				if($browser->engine == 'ie' && floatval($browser->engine_version) < 8){
					$cssp->parsed[$block][$selector]['display'][] = 'inline';
					CSSP::comment($cssp->parsed[$block][$selector], 'display', 'Added by inline-block plugin');
					$cssp->parsed[$block][$selector]['zoom'][] = '1';
					CSSP::comment($cssp->parsed[$block][$selector], 'zoom', 'Added by inline-block plugin');
				}
				elseif($browser->engine == 'gecko' && floatval($browser->engine_version) < 1.9){
					$cssp->parsed[$block][$selector]['display'][] = '-moz-inline-stack';
					CSSP::comment($cssp->parsed[$block][$selector], 'display', 'Added by inline-block plugin');
				}
			}
		}
	}
}


/**
 * Register the plugin
 */
$cssp->register_plugin('before_glue', 0, 'inlineblock');


?>
