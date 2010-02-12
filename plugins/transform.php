<?php

	/**
	 * Easy and extended transform
	 * Adds vendor-specific versions of transform
	 * 
	 * Usage:     Use any currently known transform-property wihtout vendor-prefixes,  
	 *            BUT NOT YET! transform-origin.
	 *            For IE you need to define width and height dimensions and use the same units (px/em)
	 *            for dimensions and translation
	 * Example 1: width: 100px; height: 100px; transform: translate(2px, 2px);
	 * Example 1: width: 20em; height: 20em; transform: translate(1em, 2em);
	 * Example 3: width: 100px; height: 100px; transform: translate(2px, 2px) rotate(90deg);
	 * Example 4:width: 100px; height: 100px;  transform: skew(15deg,25deg);
	 * Status:    Stable
	 * Version:   1.0
	 * 
	 * @param mixed &$parsed
	 * @return void
	 */
	function transform(&$parsed){
		global $browser;
		foreach($parsed as $block => $css){
			foreach($parsed[$block] as $selector => $styles){
				$offset_x = 0;
				$offset_y = 0;
				$offset_x_unit = 'px';
				$offset_y_unit = 'px';
				$origin_x = 50%;
				$origin_y = 50%;
				$rotate_x = 0;
				$rotate_y = 0;
				$translate_x = 0;
				$translate_y = 0;
				$translate_x_unit = 'px';
				$translate_y_unit = 'px';
				$scale_x = 1;
				$scale_y = 1;
				if(isset($parsed[$block][$selector]['transform'])){
					$value = $parsed[$block][$selector]['transform'];
					$parsed[$block][$selector]['-moz-transform'] = $value;
					$parsed[$block][$selector]['-o-transform'] = $value;
					$parsed[$block][$selector]['-webkit-transform'] = $value;
					
					if(isset($parsed[$block][$selector]['width']) && isset($parsed[$block][$selector]['height']))
					{
						if(preg_match('/([0-9\.]+)([a-z%]*)/i',$parsed[$block][$selector]['width'],$matches) == 1)
						{
							$offset_x = $matches[1] / 2;
							$offset_x_unit = $matches[2];
						}
						if(preg_match('/([0-9\.]+)([a-z%]*)/i',$parsed[$block][$selector]['height'],$matches) == 1)
						{
							$offset_y = $matches[1] / 2;
							$offset_y_unit = $matches[2];
						}
						if(preg_match('/matrix\(\D*([0-9\-]+)\s([0-9\-]+)\s([0-9\-]+)\s([0-9\-]+)\s([0-9\-]+)\s([0-9\-]+)\D*\)/i',$value,$matches) == 1)
						{
							$matrix_a = $matches[1];
							$matrix_b = $matches[2];
							$matrix_c = $matches[3];
							$matrix_d = $matches[4];
						}
						else
						{
							if(preg_match('/translate\(\D*([0-9\-]+)([a-z%]*),\s*([0-9\-]+)([a-z%]*)\D*\)/i',$value,$matches) == 1)
							{
								$translate_x = $matches[1];
								$translate_x_unit = $matches[2];
								$translate_y = $matches[3];
								$translate_y_unit = $matches[4];
							}
							if(preg_match('/rotate\(\D*([0-9\-]+)\D*\)/i',$value,$matches) == 1)
							{
								$rotate_x = $matches[1];
								$rotate_y = $matches[1];
							}
							if(preg_match('/scaleX\(\D*([0-9\-]+)\D*\)/i',$value,$matches) == 1)
							{
								$scale_x = $matches[1];
							}
							if(preg_match('/scaleY\(\D*([0-9\-]+)\D*\)/i',$value,$matches) == 1)
							{
								$scale_y = $matches[1];
							}
							if(preg_match('/scale\(\D*([0-9\-]+)\D*\)/i',$value,$matches) == 1)
							{
								$scale_x = $matches[1];
								$scale_y = $matches[1];
							}
							if(preg_match('/skewX*\(\D*([0-9\-]+)\D*\)/i',$value,$matches) == 1)
							{
								$rotate_x = $matches[1] * -1;
							}
							if(preg_match('/skewY\(\D*([0-9\-]+)\D*\)/i',$value,$matches) == 1)
							{
								$rotate_y = $matches[1] * -1;
							}
							if(preg_match('/skew\(\D*([0-9\-]+)\D*,\D*([0-9\-]+)\D*\)/i',$value,$matches) == 1)
							{
								$rotate_x = $matches[1] * -1;
								$rotate_y = $matches[2] * -1;
							}
						}
						$radian_x = deg2rad($rotate_x);
						$radian_y = deg2rad($rotate_y);
						$matrix_a = floatval(number_format(cos($radian_x),8,'.',''));
						$matrix_b = -1 * floatval(number_format(sin($radian_x),8,'.',''));
						$matrix_c = floatval(number_format(sin($radian_y),8,'.',''));
						$matrix_d = floatval(number_format(cos($radian_y),8,'.',''));
						$filter = 'progid:DXImageTransform.Microsoft.Matrix(Dx=1.0,Dy=1.0,M11='.($matrix_a * $scale_x).',M12='.($matrix_b * $scale_x).',M21='.($matrix_c * $scale_y).',M22='.($matrix_d * $scale_y).',sizingMethod=\'auto expand\')';
						
						//Adjust offset for IEs, needs to come in first
						if($browser->family == 'MSIE' && floatval($browser->familyversion) < 9)
						{
							if(!isset($parsed[$block][$selector]['position'])) 
							{
								$parsed[$block][$selector]['position'] = 'relative';
							}
							if(
								!isset($parsed[$block][$selector]['margin']) && 
								!isset($parsed[$block][$selector]['margin-left']) && 
								!isset($parsed[$block][$selector]['margin-right'])
							) 
							{
								$parsed[$block][$selector]['margin-left'] = (($offset_x * -1 * $scale_x) + ($translate_x * $scale_x)).$offset_x_unit;
							}
							if(
								!isset($parsed[$block][$selector]['margin']) && 
								!isset($parsed[$block][$selector]['margin-top']) && 
								!isset($parsed[$block][$selector]['margin-bottom'])
							) 
							{
								$parsed[$block][$selector]['margin-top'] = (($offset_y * -1 * $scale_y) + ($translate_y * $scale_y)).$offset_y_unit;
							}
						}
							
						//IE8-compliance (note: value inside apostrophes!)
						if(!isset($parsed[$block][$selector]['-ms-filter'])) 
						{
							$parsed[$block][$selector]['-ms-filter'] = '"'.$filter.'"';
						}
						else 
						{
							if(!strpos($parsed[$block][$selector]['-ms-filter'],$filter)) 
							{
								//Needs its filter-value to be put in first place!
								$parsed[$block][$selector]['-ms-filter'] = '"'.$filter.' '.trim($parsed[$block][$selector]['-ms-filter'],'"').'"';
							}
						}
						//Legacy IE-compliance
						if(!isset($parsed[$block][$selector]['filter'])) $parsed[$block][$selector]['filter'] = $filter;
						else if(!strpos($parsed[$block][$selector]['filter'],$filter)) $parsed[$block][$selector]['filter'] = $filter.' '.$parsed[$block][$selector]['filter'];

						//Set hasLayout
						$parsed[$block][$selector]['zoom'] = 1;
					}
				}
			}
		}
	}


	/**
	 * Register the plugin, needs be the last one
	 */
	$cssp->register_plugin('before_compile', 999, 'transform');


?>