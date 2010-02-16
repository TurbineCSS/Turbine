<?php


	/**
	 * A bunch of general browser ie6_enhancements
	 * 
	 * Usage: Nobrainer, just switch it on
	 * Example: -
	 * Status: Beta
	 * 
	 * @param mixed &$parsed
	 * @return void
	 */
	function ie6_enhancements(&$parsed){
		global $browser;
		foreach($parsed as $block => $css){
			// IE 6 enhancements
			if($browser->family == 'MSIE' && floatval($browser->familyversion) < 7)
			{
				// Missing :hover-property on every tag except link-tag
				// Fix found on http://www.xs4all.nl/~peterned/csshover.html
				$htc_path = rtrim(dirname($_SERVER['SCRIPT_NAME']),'/').'/plugins/ie6_enhancements/csshover3.htc';
				if(!isset($parsed[$block]['body'])) $parsed[$block]['body'] = array();	
				if(!isset($parsed[$block]['body']['behavior'])) $parsed[$block]['body']['behavior'] = 'url("'.$htc_path.'")';
				else if(!strpos($parsed[$block]['body']['behavior'],'url("'.$htc_path.'")')) $parsed[$block]['body']['behavior'] .= ', url("'.$htc_path.'")';
				
				// Missing :hover-property on every tag except link-tag
				// Fix found on http://www.twinhelix.com/css/iepngfix/
				$htc_path = rtrim(dirname($_SERVER['SCRIPT_NAME']),'/').'/plugins/ie6_enhancements/iepngfix.htc';
				if(!isset($parsed[$block]['img'])) $parsed[$block]['img'] = array();	
				if(!isset($parsed[$block]['img']['behavior'])) $parsed[$block]['img']['behavior'] = 'url("'.$htc_path.'")';
				else if(!strpos($parsed[$block]['img']['behavior'],'url("'.$htc_path.'")')) $parsed[$block]['img']['behavior'] .= ', url("'.$htc_path.'")';
			}
		}
	}


	/**
	 * Register the plugin
	 */
	$cssp->register_plugin('before_compile', 0, 'ie6_enhancements');


?>