<?php

	/**
	 * Cross-browser-box-sizing
	 * 
	 * Usage:     box-sizing:inherit|content-box|border-box
	 * Status:    Beta
	 * Version:   1.0
	 * 
	 * @param mixed &$parsed
	 * @return void
	 */
	function boxsizing(&$parsed){
		global $browser, $cssp;
		$htc_path = rtrim(dirname($_SERVER['SCRIPT_NAME']),'/').'/plugins/boxsizing/boxsizing.htc';
		foreach($parsed as $block => $css){
			foreach($parsed[$block] as $selector => $styles){
				if(isset($styles['box-sizing'])){
					// Create the vendor-specific rules and insert them
					$boxsizing_rules = array(
						'-moz-box-sizing' => $styles['box-sizing'],
						'-webkit-box-sizing' => $styles['box-sizing']
					);
					// Check for IE <= 7 and then append behavior
					if($browser->engine == 'ie' && floatval($browser->engine_version) < 8)
					{
						$boxsizing_rules['behavior'] = array('url('.$htc_path.')');
					}
					$cssp->insert_properties($boxsizing_rules, $block, $selector, null, 'box-sizing');
					// Comment the newly inserted properties
					foreach($boxsizing_rules as $property => $value){
						CSSP::comment($parsed[$block][$selector], $property, 'Added by box-sizing plugin');
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
