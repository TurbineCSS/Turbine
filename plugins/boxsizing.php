<?php

	/**
	 * Cross-browser-box-sizing
	 * TODO: Make more robust in IE
	 * 
	 * Usage:     box-sizing:inherit|content-box|border-box
	 * Status:    Beta
	 * Version:   1.1
	 * 
	 * @param mixed &$parsed
	 * @return void
	 */
	function boxsizing(&$parsed){
		global $cssp;
		$htc_path = rtrim(dirname($_SERVER['SCRIPT_NAME']),'/').'/plugins/boxsizing/boxsizing.htc';
		foreach($parsed as $block => $css){
			foreach($parsed[$block] as $selector => $styles){
				if(isset($styles['box-sizing'])){
					// Create the vendor-specific rules and insert them
					$boxsizing_rules = array(
						'-moz-box-sizing' => $styles['box-sizing'],
						'-webkit-box-sizing' => $styles['box-sizing'],
						'behaviour' => array('url('.$htc_path.')')
					);
					$cssp->insert_properties($boxsizing_rules, $block, $selector, null, 'box-sizing');
					// Comment the newly inserted properties
					foreach($boxsizing_rules as $border_property => $border_value){
						CSSP::comment($parsed[$block][$selector], $border_property, 'Added by box-sizing plugin');
					}
				}
			}
		}
	}

	/**
	 * Register the plugin
	 */
	$cssp->register_plugin('before_glue', 0, 'boxsizing');


?>
