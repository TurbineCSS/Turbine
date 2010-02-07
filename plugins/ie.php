<?php


	/**
	 * A bunch of general IE 6 & 7 Bugfixes
	 * 
	 * Usage: Nobrainer, just switch it on
	 * Example: -
	 * Status: Beta
	 * 
	 * @param mixed &$parsed
	 * @return void
	 */
	function ie(&$parsed){
		global $browser;
		foreach($parsed as $block => $css){
			foreach($parsed[$block] as $selector => $styles){
				// IE 6 Bugfixes
				if($browser->family == 'MSIE' && floatval($browser->familyversion) < 7)
				{
					// Float double margin bug
					if($parsed[$block][$selector]['float'] && ($parsed[$block][$selector]['margin'] || $parsed[$block][$selector]['margin-left'] || $parsed[$block][$selector]['margin-right'] || $parsed[$block][$selector]['margin-top'] || $parsed[$block][$selector]['margin-bottom'])) $parsed[$block][$selector]['display'] = 'inline';
	
					// Image margin bottom bug
					if(!$parsed[$block]['img']) $parsed[$block]['img'] = array();	
					$parsed[$block]['img']['vertical-align'] = 'bottom';
	
					// Background image flickers on hover
					if(!$parsed[$block]['html']) $parsed[$block]['html'] = array();	
					if(!$parsed[$block]['html']['filter']) $parsed[$block]['html']['filter'] = 'expression(document.execCommand("BackgroundImageCache", false, true))';
					else $parsed[$block]['html']['filter'] .= ' expression(document.execCommand("BackgroundImageCache",false,true))';
					
					// Missing :hover-property on every tag except link-tag
					// Fix found on http://www.xs4all.nl/~peterned/csshover.html
					$htc_path = rtrim(dirname($_SERVER['SCRIPT_NAME']),'/').'/../plugins/ie/csshover3.htc';
					if(!$parsed[$block]['body']) $parsed[$block]['body'] = array();	
					if(!$parsed[$block]['body']['behaviour']) $parsed[$block]['body']['behaviour'] = 'url("'.$htc_path.'")';
					else $parsed[$block]['body']['behaviour'] .= ', url("'.$htc_path.'")';
					
					// Missing :hover-property on every tag except link-tag
					// Fix found on http://www.twinhelix.com/css/iepngfix/
					$htc_path = rtrim(dirname($_SERVER['SCRIPT_NAME']),'/').'/../plugins/ie/iepngfix.htc';
					if(!$parsed[$block]['body']) $parsed[$block]['body'] = array();	
					if(!$parsed[$block]['body']['behaviour']) $parsed[$block]['body']['behaviour'] = 'url("'.$htc_path.'")';
					else $parsed[$block]['body']['behaviour'] .= ', url("'.$htc_path.'")';
				}
			}
		}
	}


	/**
	 * Register the plugin
	 */
	register_plugin('before_compile', 0, 'ie');


?>