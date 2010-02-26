<?php 

	/**
	 * Utility
	 * Meta plugin, activates all other plugins that add helpful utilities
	 * 
	 * Usage: See the individual plugins' descriptions
	 * Example: -
	 * Status: Beta
	 * 
	 * @param array &$css The style lines (unused)
	 * @return void
	 */
	function utility(&$css){
		global $plugin_list;
		$utilityplugins = array(
			'colortools',
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