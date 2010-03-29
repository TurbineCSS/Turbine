<?php 

	/**
	 * CSS3 Plugin
	 * Meta plugin, activates all other plugins that enable CSS3 properties
	 * 
	 * Usage: See the individual plugins' descriptions
	 * Example: -
	 * Status: Beta
	 * 
	 * @param array &$css The style lines (unused)
	 * @return void
	 */
	function css3(&$css){
		global $plugin_list;
		$css3plugins = array(
			'borderradius',
			'boxshadow',
			'colormodels',
			'transform'
		);
		$plugin_list = array_merge($plugin_list, $css3plugins);
	}


	/**
	 * Register the plugin
	 */
	$cssp->register_plugin('before_parse', 1000, 'css3');


?>