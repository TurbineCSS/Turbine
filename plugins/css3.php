<?php 

/**
 * This file is part of Turbine
 * http://github.com/SirPepe/Turbine
 * 
 * Copyright Peter Kröner
 * Licensed under GNU LGPL 3, see license.txt or http://www.gnu.org/licenses/
 */


/**
 * CSS3 Plugin
 * Meta plugin, activates all other plugins that enable CSS3 properties
 * 
 * Usage: See the individual plugins' descriptions
 * Example: -
 * Status: Stable
 * Version: 1.0
 * 
 * @param array &$css The style lines (unused)
 * @return void
 */
function css3(&$css){
	global $plugin_list;
	$css3plugins = array(
		'backgroundgradient',
		'borderradius',
		'boxshadow',
		'boxsizing',
		'colormodels',
		'opacity',
		'transform'
	);
	$plugin_list = array_merge($plugin_list, $css3plugins);
}


/**
 * Register the plugin
 */
$cssp->register_plugin('before_parse', 1000, 'css3');


?>
