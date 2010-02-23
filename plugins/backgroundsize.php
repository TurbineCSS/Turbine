<?php

	/**
	 * Easy and extended background-size
	 * Adds vendor-specific versions of background-size
	 * For IE, only "100% 100%" is supported! (stretching background behind the whole object)
	 * 
	 * Usage:     background-size: value; + needs a background-image defined
	 * Example 1: background-size: 100% 100%; -> stretch across the whole object (supported by IE!)
	 * Example 2: background-size: 50% 25%; -> cover the object's inner 50% x 25% area
	 * Example 3: background-size: cover -> fill out the whole object, unstretched but cut off (supported by Firefox and Webkit)
	 * Example 4: background-size: contain -> fit a the biggest possible background-image size into the object (supported by Firefox and Webkit)
	 * Status:    Stable
	 * Version:   1.0
	 * 
	 * @param mixed &$parsed
	 * @return void
	 */
	function backgroundsize(&$parsed){
		global $browser;
		foreach($parsed as $block => $css){
			foreach($parsed[$block] as $selector => $styles){
				// Everywhere
				if(
					isset($parsed[$block][$selector]['background-size']) && 
					(
						isset($parsed[$block][$selector]['background']) || 
						isset($parsed[$block][$selector]['background-image'])
					)
				){
					$value = $parsed[$block][$selector]['background-size'];
					$parsed[$block][$selector]['-moz-background-size'] = $value;
					$parsed[$block][$selector]['-khtml-background-size'] = $value;
					$parsed[$block][$selector]['-webkit-background-size'] = $value;
					$parsed[$block][$selector]['-o-background-size'] = $value;
					// Fix for IEs for certain commands found via comment in http://requiem4adream.wordpress.com/2006/09/29/css-stretch-background-image/
					if($browser->family == 'MSIE' && floatval($browser->familyversion) <= 8 && trim($value) == '100% 100%')
					{
						$regex = '/url\([\'"]*([^\'"\)]+)[\'"]*\)/i';
						if(isset($parsed[$block][$selector]['background'])) preg_match($regex,$parsed[$block][$selector]['background'],$matches);
						elseif(isset($parsed[$block][$selector]['background-image'])) preg_match($regex,$parsed[$block][$selector]['background-image'],$matches);
						$backgroundimage = rtrim(dirname($_SERVER['SCRIPT_NAME']),'/').'/'.$matches[1];
						$filter = 'progid:DXImageTransform.Microsoft.AlphaImageLoader(src=\''.$backgroundimage.'\', sizingMethod=\'scale\')';
						//IE8-compliance (note: value inside apostrophes!)
						if(!isset($parsed[$block][$selector]['-ms-filter'])) 
						{
							$parsed[$block][$selector]['-ms-filter'] = '"'.$filter.'"';
						}
						else 
						{
							$parsed[$block][$selector]['-ms-filter'] = '"'.trim($parsed[$block][$selector]['-ms-filter'],'"').' '.$filter.'"';
						}
						//Legacy IE-compliance
						if(!isset($parsed[$block][$selector]['filter'])) 
						{	
							$parsed[$block][$selector]['filter'] = $filter;
						}
						else 
						{
							$parsed[$block][$selector]['filter'] .= ' '.$filter;
						}
						if(isset($parsed[$block][$selector]['background'])) $parsed[$block][$selector]['background'] = preg_replace($regex,'',$parsed[$block][$selector]['background']);
						if(isset($parsed[$block][$selector]['background-image'])) unset($parsed[$block][$selector]['background-image']);
						$parsed[$block][$selector]['zoom'] = 1;
					}
				}
			}
		}
	}


	/**
	 * Register the plugin
	 */
	$cssp->register_plugin('before_glue', 0, 'backgroundsize');


?>