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
			// IE 6 global bugfixes
			if($browser->family == 'MSIE' && floatval($browser->familyversion) < 7)
			{
				// Image margin bottom bug
				if(!isset($parsed[$block]['img'])) $parsed[$block]['img'] = array();	
				$parsed[$block]['img']['vertical-align'] = 'bottom';

				// Background image flickers on hover
				if(!isset($parsed[$block]['html'])) $parsed[$block]['html'] = array();	
				if(!isset($parsed[$block]['html']['filter'])) $parsed[$block]['html']['filter'] = 'expression(document.execCommand("BackgroundImageCache",false,true))';
				else if(!strpos($parsed[$block]['html']['filter'],'expression(document.execCommand("BackgroundImageCache",false,true))')) $parsed[$block]['html']['filter'] .= ' expression(document.execCommand("BackgroundImageCache",false,true))';
			}
			// IE 6 + 7 global bugfixes
			if($browser->family == 'MSIE' && floatval($browser->familyversion) < 8)
			{
				// Enable full styleability for IE-buttons
				// See http://www.sitepoint.com/forums/showthread.php?t=547059
				if(!isset($parsed[$block]['button'])) $parsed[$block]['button'] = array();	
				$parsed[$block]['button']['overflow'] = 'visible';
				$parsed[$block]['button']['width'] = 'auto';
				$parsed[$block]['button']['white-space'] = 'nowrap';
			}
			// Firefox global bugfixes
			if($browser->engine == 'Gecko')
			{
				// Ghost margin around buttons
				// See http://www.sitepoint.com/forums/showthread.php?t=547059
				if(!isset($parsed[$block]['button::-moz-focus-inner'])) $parsed[$block]['button::-moz-focus-inner'] = array();	
				$parsed[$block]['button::-moz-focus-inner']['padding'] = '0';
				$parsed[$block]['button::-moz-focus-inner']['border'] = 'none';
			}
			foreach($parsed[$block] as $selector => $styles){
				// IE 6 local bugfixes
				if($browser->family == 'MSIE' && floatval($browser->familyversion) < 7)
				{
					// Float double margin bug
					if(isset($parsed[$block][$selector]['float']) && (isset($parsed[$block][$selector]['margin']) || isset($parsed[$block][$selector]['margin-left']) || isset($parsed[$block][$selector]['margin-right']))) $parsed[$block][$selector]['display'] = 'inline';
				}
				// IE 6 + 7 bugfixes
				if($browser->family == 'MSIE' && floatval($browser->familyversion) < 8)
				{
				}
				// Firefox bugfixes
				if($browser->engine == 'Gecko')
				{
				}
			}
		}
	}


	/**
	 * Register the plugin
	 */
	register_plugin('before_compile', 0, 'bugfixes');


?>