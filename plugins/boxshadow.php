<?php


	/**
	 * Easy box shadow
	 * Adds vendor-specific versions of box-shadow
	 * 
	 * Usage:   #foo { box-shadow: 2px 2px 8px #666; }
	 * Result:  #foo { box-shadow: 2px 2px 8px #666; -moz-box-shadow: 2px 2px 8px #666; -webkit-box-shadow: 2px 2px 8px #666; }
	 * Status:  Stable
	 * Version: 1.1
	 * 
	 * @param mixed &$parsed
	 * @return void
	 */
	function boxshadow(&$parsed){
		global $cssp;
		foreach($parsed as $block => $css){
			foreach($parsed[$block] as $selector => $styles){
				if(isset($parsed[$block][$selector]['box-shadow'])){
					// $before keeps track of the previous property in the loop, which is the position we want the new
					// box-shadow properties to be inserted
					$before = null;
					foreach($styles as $property => $value){
						if($property == 'box-shadow'){
							$shadow_properties = array();
							// Build prefixed properties
							$prefixes = array('-moz-', '-webkit-', '');
							foreach($prefixes as $prefix){
								$shadow_properties[$prefix.'box-shadow'] = $parsed[$block][$selector]['box-shadow'];
							}
							// Get IE filters, merge them with the other new properties and insert everything
							$filter_properties = boxshadow_filters($value);
							$shadow_properties = array_merge($shadow_properties, $filter_properties);
							$cssp->insert_rules($shadow_properties, $block, $selector, $before);
							// Comment the newly inserted properties
							foreach($shadow_properties as $shadow_property => $shadow_value){
								CSSP::comment($parsed[$block][$selector], $shadow_property, 'Added by box shadow plugin');
							}
							$before = array_pop(array_keys($shadow_properties));
						}
						else{
							$before = $property;
						}
					}
				}
			}
		}
	}


	/**
	 * boxshadow_filters
	 * 
	 * @return array $filter_properties The new filter properties
	 */
	function boxshadow_filters($value){
		$filter_properties = array();
		if(preg_match('/([0-9]+)\D+([0-9]+)\D+([0-9]+)\D+#([0-9A-F]{3,6})+/i', trim($value), $matches) == 1){
			$xoffset = intval($matches[1]);
			$yoffset = intval($matches[2]);
			$blur = intval($matches[3]);
			$color = $matches[4];
			if(strlen($color) == 3){
				$color = substr($color,0,1).substr($color,0,1).substr($color,1,1).substr($color,1,1).substr($color,2,1).substr($color,2,1);
			}
			$median_offset = round(($xoffset + $yoffset) / 2);
			$opacity = (($median_offset - $blur) > 0) ? (($median_offset - $blur) / $median_offset) : 0.05;
			$color_opacity = strtoupper(str_pad(dechex(round(hexdec(substr($color,0,2)) * $opacity)),2,'0',STR_PAD_LEFT).str_pad(dechex(round(hexdec(substr($color,2,2)) * $opacity)),2,'0',STR_PAD_LEFT).str_pad(dechex(round(hexdec(substr($color,4,2)) * $opacity)),2,'0',STR_PAD_LEFT));
			$direction = 135;
			$direction_factor = abs($xoffset) / abs($yoffset);
			if($direction_factor == 1){
				if($xoffset > 0 && $yoffset > 0){
					$direction = 135;
				}
				elseif($xoffset > 0 && $yoffset < 0){
					$direction = 45;
				}
				elseif($xoffset < 0 && $yoffset > 0){
					$direction = 315;
				}
				else{
					$direction = 225;
				}
			}
			elseif($direction_factor > 1){
				if($xoffset > 0){
					$direction = 90;
				}
				else{
					$direction = 270;
				}
			}
			else{
				if($yoffset > 0){
					$direction = 180;
				}
				else{
					$direction = 0;
				}
			}
			// Hard Shadow
			if($blur == 0){
				$filter = 'progid:DXImageTransform.Microsoft.dropshadow(OffX='.$xoffset.',OffY='.$yoffset.',Color=\'#'.strtoupper(str_pad(dechex(round($opacity * 255)),2,'0',STR_PAD_LEFT)).$color.'\',Positive=\'true\')';
			}
			// Soft Shadow
			else{
				$filter = 'progid:DXImageTransform.Microsoft.Shadow(Color=\'#'.$color.'\',Direction='.$direction.',Strength='.$median_offset.')';
			}
			//IE8-compliance (note: value inside apostrophes!)
			$filter_properties['-ms-filter'] = '"'.$filter.'"';
			//Legacy IE-compliance
			$filter_properties['filter'] = $filter;
			$filter_properties['zoom'] = 1;
		}
		return $filter_properties;
	}


	/**
	 * Register the plugin
	 */
	$cssp->register_plugin('before_glue', 0, 'boxshadow');


?>