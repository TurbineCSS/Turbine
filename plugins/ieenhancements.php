<?php


	/**
	 * A bunch of general IE enhancements
	 * 
	 * Usage: Nobrainer, just switch it on
	 * Example: -
	 * Status: Beta
	 * 
	 * @param mixed &$parsed
	 * @return void
	 */
	function ie6enhancements(&$parsed){
		global $browser;
		if($browser->engine == 'MSIE' && floatval($browser->engineversion) < 7){
			// Missing :hover-property on every tag except link-tag, see http://www.xs4all.nl/~peterned/csshover.html
			$htc_path = rtrim(dirname($_SERVER['SCRIPT_NAME']),'/').'/plugins/ieenhancements/csshover3.htc';
			$parsed['global']['body']['behavior'][] = 'url("'.$htc_path.'")';
			// Fix transparent PNGs, see http://www.twinhelix.com/css/iepngfix/
			$htc_path = rtrim(dirname($_SERVER['SCRIPT_NAME']),'/').'/plugins/ieenhancements/iepngfix.htc';
			$parsed['global']['img']['behavior'][] = 'url("'.$htc_path.'")';
		}
	}


	/**
	 * Register the plugin
	 */
	$cssp->register_plugin('before_compile', 0, 'ie6enhancements');


?>