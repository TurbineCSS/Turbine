<?php


	/**
	 * Automatic registration of HTML5 elements for IE 6 - 8
	 * 
	 * Usage: Nobrainer, just switch it on
	 * Example: -
	 * Status: Beta
	 * 
	 * @param mixed &$parsed
	 * @return void
	 */
	function html5elements(&$parsed){
		global $browser;
		foreach($parsed as $block => $css){
			if($browser->family == 'MSIE' && floatval($browser->familyversion) < 9)
			{
				// Registration of HTML5 elements
				// Fix inspired by http://code.google.com/p/html5shiv/
				$htc_path = rtrim(dirname($_SERVER['SCRIPT_NAME']),'/').'/plugins/html5/html5.htc';
				if(!isset($parsed[$block]['body'])) $parsed[$block]['body'] = array();	
				if(!isset($parsed[$block]['body']['behavior'])) $parsed[$block]['body']['behavior'] = 'url("'.$htc_path.'")';
				else if(!strpos($parsed[$block]['body']['behavior'],'url("'.$htc_path.'")')) $parsed[$block]['body']['behavior'] .= ', url("'.$htc_path.'")';
			}
		}
	}


	/**
	 * Register the plugin
	 */
	$cssp->register_plugin('before_compile', 0, 'html5elements');


?>