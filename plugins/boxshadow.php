<?php


	/**
	 * Easy box shadow
	 * Adds vendor-specific versions of box-shadow
	 * 
	 * Usage:   #foo { box-shadow: 2px 2px 8px #666; }
	 * Result:  #foo { box-shadow: 2px 2px 8px #666; -moz-box-shadow: 2px 2px 8px #666; -webkit-box-shadow: 2px 2px 8px #666; }
	 * Status:  Stable
	 * Version: 1.0
	 * 
	 * @param mixed &$parsed
	 * @return void
	 */
	function boxshadow(&$parsed){
		foreach($parsed as $block => $css){
			foreach($parsed[$block] as $selector => $styles){
				if(isset($parsed[$block][$selector]['box-shadow'])){
					$value = $parsed[$block][$selector]['box-shadow'];
					$parsed[$block][$selector]['-moz-box-shadow'] = $value;
					$parsed[$block][$selector]['-webkit-box-shadow'] = $value;
					if(preg_match('/([0-9]+)\D+([0-9]+)\D+([0-9]+)\D+#([0-9A-F]{3,6})+/i',trim($value),$matches) == 1 && (isset($parsed[$block][$selector]['background']) || isset($parsed[$block][$selector]['background-color'])))
					{
						$xoffset = intval($matches[1]);
						$yoffset = intval($matches[2]);
						$blur = intval($matches[3]);
						$color = $matches[4];
						if(strlen($color) == 3) $color = substr($color,0,1).substr($color,0,1).substr($color,1,1).substr($color,1,1).substr($color,2,1).substr($color,2,1);

						$median_offset = round(($xoffset + $yoffset) / 2);
						$opacity = (($median_offset - $blur) > 0) ? (($median_offset - $blur) / $median_offset) : 0.05;
						$color_opacity = strtoupper(str_pad(dechex(round(hexdec(substr($color,0,2)) * $opacity)),2,'0',STR_PAD_LEFT).str_pad(dechex(round(hexdec(substr($color,2,2)) * $opacity)),2,'0',STR_PAD_LEFT).str_pad(dechex(round(hexdec(substr($color,4,2)) * $opacity)),2,'0',STR_PAD_LEFT));
						$direction = 135;
						$direction_factor = abs($xoffset) / abs($yoffset);
						if($direction_factor == 1)
						{
							if($xoffset > 0 && $yoffset > 0) $direction = 135;
							elseif($xoffset > 0 && $yoffset < 0) $direction = 45;
							elseif($xoffset < 0 && $yoffset > 0) $direction = 315;
							else $direction = 225;
						}
						elseif($direction_factor > 1)
						{
							if($xoffset > 0) $direction = 90;
							else $direction = 270;
						}
						else
						{
							if($yoffset > 0) $direction = 180;
							else $direction = 0;
						}
						
						if($blur == 0)
						{
							// Hard Shadow
							$filter = 'progid:DXImageTransform.Microsoft.dropshadow(OffX='.$xoffset.',OffY='.$yoffset.',Color=\'#'.strtoupper(str_pad(dechex(round($opacity * 255)),2,'0',STR_PAD_LEFT)).$color.'\',Positive=\'true\')';
						}
						else
						{
							// Soft Shadow
							$filter = 'progid:DXImageTransform.Microsoft.Shadow(Color=\'#'.$color.'\',Direction='.$direction.',Strength='.$median_offset.')';
						}
						
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
							$parsed[$block][$selector]['zoom'] = 1;
						}
					}
				}
			}
		}
	}


	/**
	 * Register the plugin
	 */
	$cssp->register_plugin('before_compile', 0, 'boxshadow');


?>