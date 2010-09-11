<?php

/**
 * This file is part of Turbine
 * http://github.com/SirPepe/Turbine
 * 
 * Copyright Peter Kröner
 * Licensed under GNU LGPL 3, see license.txt or http://www.gnu.org/licenses/
 */


/**
 * Utility
 * Shared functionality for plugins and core
 */
class Utility {


/* Color patterns */
public static $hexpattern = '/(#((?:[A-Fa-f0-9]{3})(?:[A-Fa-f0-9]{3})?))/i';
public static $rgbapattern = '/(rgb(?:a)?)\([\s]*([0-9]+%?)[\s]*,[\s]*([0-9]+%?)[\s]*,[\s]*([0-9]+%?)[\s]*(?:,[\s]*([0-1]\.[0-9]*)[\s]*)?\)/i';
public static $hslapattern = '/(hsl(?:a)?)\([\s]*([0-9]+)[\s]*,[\s]*([0-9]+%)[\s]*,[\s]*([0-9]+%)[\s]*(?:,[\s]*([0-1]\.[0-9]*)[\s]*)?\)/i';


/*
 * any2rgba
 * Convert any color declaration to RGBA
 * @param string $input Color input
 * @return array
 */
public static function any2rgba($input){
	$input = trim($input);
	if(preg_match(self::$hexpattern, $input, $matches)){
		return self::hex2rgba($input, $matches);
	}
	elseif(preg_match(self::$hslapattern, $input, $matches)){
		return self::hsla2rgba($input, $matches);
	}
	elseif(preg_match(self::$rgbapattern, $input, $matches)){
		return self::rgba2rgba($input, $matches);
	}
	else{
		return false;
	}
}


/*
 * hex2rgba
 * Convert a hex color declaration to RGBA
 * @param string $input Color input
 * @param string $matches [optional] Regex matches for the color pattern
 * @return array $rgba The RGBA array
 */
public static function hex2rgba($input, $matches = array()){
	$rgba = array();
	if(empty($matches)){
		preg_match(self::$hexpattern, $input, $matches);
	}
	// Short hex
	if(strlen($matches[2]) == 3){
		$rgba['r'] = hexdec(str_repeat(substr($matches[2], 0, 1), 2));
		$rgba['g'] = hexdec(str_repeat(substr($matches[2], 1, 1), 2));
		$rgba['b'] = hexdec(str_repeat(substr($matches[2], 2, 1), 2));
		$rgba['a'] = 1;
	}
	// Normal hex
	else{
		$hexdec = hexdec($matches[2]);
		$rgba['r'] = 0xFF & ($hexdec >> 0x10);
		$rgba['g'] = 0xFF & ($hexdec >> 0x8);
		$rgba['b'] = 0xFF & $hexdec;
		$rgba['a'] = 1;
	}
	return $rgba;
}


/*
 * hsla2rgba
 * Convert a HSL(A) color declaration to RGBA
 * @param string $input Color input
 * @param string $matches [optional] Regex matches for the color pattern
 * @return array $rgba The RGBA array
 */
public static function hsla2rgba($input, $matches = array()){
	$rgba = array();
	if(empty($matches)){
		preg_match(self::$hslapattern, $input, $matches);
	}
	$h = intval($matches[2]) / 360;
	$s = intval($matches[3]) / 100;
	$l = intval($matches[4]) / 100;
	if($s == 0){
		$rgba['r'] = $rgba['g'] = $rgba['b'] = 0;
	}
	else{
		if($l <= 0.5){
			$m2 = $l * ($s + 1);
		}
		else{
			$m2 = $l + $s - ($l * $s);
		}
		$m1 = $l * 2 - $m2;
		$rgba['r'] = floor(self::hslhue($m1, $m2, ($h + 1/3)) * 255);
		$rgba['g'] = floor(self::hslhue($m1, $m2, $h) * 255);
		$rgba['b'] = floor(self::hslhue($m1, $m2, ($h - 1/3)) * 255);
	}
	$rgba['a'] = (isset($matches[5])) ? $matches[5] : 1;
	return $rgba;
}


/*
 * hslhue
 * Applies hue value
 * Stolen from here: http://monc.se/kitchen/119/working-with-hsl-in-css
 * @param float $m1
 * @param float $m2
 * @param float $h
 * @return float $m1
 */
private static function hslhue($m1, $m2, $h){
	if ($h < 0){ $h = $h+1; }
	if ($h > 1){ $h = $h-1; }
	if ($h*6 < 1){ return $m1 + ($m2 - $m1) * $h * 6; }
	if ($h*2 < 1){ return $m2; }
	if ($h*3 < 2){ return $m1 + ($m2 - $m1) * (2/3 - $h) * 6; }
	return $m1;
}


/*
 * hsla2rgba
 * Convert a RGB(A) color declaration to RGBA
 * @param string $input Color input
 * @param string $matches [optional] Regex matches for the color pattern
 * @return array $rgba The RGBA array
 */
public static function rgba2rgba($input, $matches = array()){
	$rgba = array();
	if(empty($matches)){
		preg_match(self::$rgbapattern, $input, $matches);
	}
	$rgba['r'] = (substr($matches[2], -1) == '%') ? round(255 / 100 * $matches[2]) : $matches[2];
	$rgba['g'] = (substr($matches[3], -1) == '%') ? round(255 / 100 * $matches[3]) : $matches[3];
	$rgba['b'] = (substr($matches[4], -1) == '%') ? round(255 / 100 * $matches[4]) : $matches[4];
	$rgba['a'] = (isset($matches[5])) ? $matches[5] : 1;
	return $rgba;
}


/*
 * rgbasyntax
 * Convert a RGBA array to css rgb(a) color syntax
 * @param array $rgba The RGBA array
 * @param bool $force Force ouptput of the alpha value even if it's 1?
 * @return string $syntax The rgb(a) value
 */
public function rgbasyntax($rgba, $force = false){
	$syntax = 'rgb';
	if($rgba['a'] !== 1 || $force){
		$syntax .= 'a';
	}
	else{
		unset($rgba['a']);
	}
	$syntax .= '(';
	$syntax .= implode(',', $rgba);
	$syntax .= ')';
	return $syntax;
}


/*
 * hexsyntax
 * Convert a RGBA array to css hex color syntax
 * @param array $rgba The RGBA array
 * @param bool $force Force ouptput of the alpha value even if it's 1?
 * @return string $syntax The hex value
 */
public function hexsyntax($rgba, $force = false){
	$syntax = '#';
	if($rgba['a'] !== 1 || $force){
		$syntax .= dechex($rgba['a'] * 255);
	}
	$syntax .= dechex($rgba['r']);
	$syntax .= dechex($rgba['g']);
	$syntax .= dechex($rgba['b']);
	return $syntax;
}


}


?>
