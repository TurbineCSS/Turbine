<?php

/**
 * This file is part of Turbine
 * http://github.com/SirPepe/Turbine
 * 
 * Copyright Peter Kröner
 * Licensed under GNU LGPL 3, see license.txt or http://www.gnu.org/licenses/
 */


/**
 * Utility
 * Meta plugin, activates all other plugins that add helpful utilities
 * 
 * Usage: See the individual plugins' descriptions
 * Example: -
 * Status: Stable
 * Version: 1.0
 * 
 * @param array &$css The style lines (unused)
 * @return void
 */
function utility(&$css){
	global $plugin_list;
	$utilityplugins = array(
		'fontface',
		'quotes'
	);
	$plugin_list = array_merge($plugin_list, $utility);
}


/**
 * Register the plugin
 */
$cssp->register_plugin('before_parse', 1000, 'utility');


?>
