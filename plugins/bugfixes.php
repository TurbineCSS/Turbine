<?php


	/**
	 * A bunch of general browser bugfixes
	 * 
	 * Usage: Nobrainer, just switch it on
	 * Example: -
	 * Status: Beta
	 * 
	 * @param mixed &$parsed
	 * @return void
	 */
	function bugfixes(&$parsed){
		global $browser;
		foreach($parsed as $block => $css){
			foreach($parsed[$block] as $selector => $styles){
				// IE 6 bugfixes
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
				}
				// IE 6 + 7 bugfixes
				if($browser->family == 'MSIE' && floatval($browser->familyversion) < 8)
				{
					// Enable full styleability for IE-buttons
					// See http://www.sitepoint.com/forums/showthread.php?t=547059
					if(!$parsed[$block]['button']) $parsed[$block]['button'] = array();	
					$parsed[$block]['button']['overflow'] = 'visible';
					$parsed[$block]['button']['width'] = 'auto';
					$parsed[$block]['button']['white-space'] = 'nowrap';
				}
				// Firefox bugfixes
				if($browser->engine == 'Gecko')
				{
					// Ghost margin around buttons
					// See http://www.sitepoint.com/forums/showthread.php?t=547059
					if(!$parsed[$block]['button::-moz-focus-inner']) $parsed[$block]['button::-moz-focus-inner'] = array();	
					$parsed[$block]['button::-moz-focus-inner']['padding'] = '0';
					$parsed[$block]['button::-moz-focus-inner']['border'] = 'none';
				}
			}
		}
	}


	/**
	 * Register the plugin
	 */
	register_plugin('before_compile', 0, 'bugfixes');


?>