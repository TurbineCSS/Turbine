<?php
ini_set("display_errors", 1);
error_reporting(E_ALL);
$direction = (isset($_GET['direction']) && $_GET['direction'] == 'top' ||$_GET['direction'] == 'left' ? $_GET['direction'] : 'top');
$startcolor = (isset($_GET['startcolor']) ? preg_replace('/[^#0-9A-F\(\),RG]/i','',$_GET['startcolor']) : '#FFFFF');
$endcolor = (isset($_GET['endcolor']) ? preg_replace('/[^#0-9A-F\(\),RG]/i','',$_GET['endcolor']) : '#000000');
$startopacity = 1;
$endopacity = 1;
$etag = md5($direction.$startcolor.$endcolor);

if(isset($_SERVER['HTTP_IF_NONE_MATCH']) && $_SERVER['HTTP_IF_NONE_MATCH'] === $etag){
	// Browser already has the file so we tell him nothing changed and exit
	header('HTTP/1.1 304 Not Modified');
	exit();
}

header('Cache-Control: no-cache, must-revalidate');
header('Expires: '.gmdate('D, d M Y H:i:s').' GMT');
header('Content-type: image/svg+xml'); 
header('ETag: '.$etag);

// Convert direction to SVG-attributes
if($direction == 'top'){
	$svg_direction = 'x1="0%" y1="0%" x2="0%" y2="100%"';
}
else{
	$svg_direction = 'x1="0%" y1="0%" x2="100%" y2="0%"';
}

// Expand shorthand colors
$shorthandpattern = '/^#([0-9A-F]{1})([0-9A-F]{1})([0-9A-F]{1})$/i';
if(preg_match($shorthandpattern,$startcolor,$shorthandmatches)){
	$startcolor = '#'.strtoupper($shorthandmatches[1].$shorthandmatches[1].$shorthandmatches[2].$shorthandmatches[2].$shorthandmatches[3].$shorthandmatches[3]);
	$startopacity = 1;
}
if(preg_match($shorthandpattern,$endcolor,$shorthandmatches)){
	$endcolor = '#'.strtoupper($shorthandmatches[1].$shorthandmatches[1].$shorthandmatches[2].$shorthandmatches[2].$shorthandmatches[3].$shorthandmatches[3]);
	$endopacity = 1;
}
// Convert from RGB colors
$rgbpattern = '/rgb\([\s]*(.+?)[\s]*,[\s]*(.+?)[\s]*,[\s]*(.+?)[\s]*\)/i';
if(preg_match($rgbpattern,$startcolor,$rgbmatches)){
	$startcolor = '#'.strtoupper(dechex(intval($rgbmatches[1])).dechex(intval($rgbmatches[2])).dechex(intval($rgbmatches[3])));
	$startopacity = 1;
}
if(preg_match($rgbpattern,$endcolor,$rgbmatches)){
	$endcolor = '#'.strtoupper(dechex(intval($rgbmatches[1])).dechex(intval($rgbmatches[2])).dechex(intval($rgbmatches[3])));
	$endopacity = 1;
}
// Convert from RGBA colors
$rgbapattern = '/rgba\([\s]*(.+?)[\s]*,[\s]*(.+?)[\s]*,[\s]*(.+?)[\s]*,[\s]*(.+?)[\s]*\)/i';
if(preg_match($rgbapattern,$startcolor,$rgbamatches)){
	$startcolor = '#'.strtoupper(dechex(intval($rgbamatches[1])).dechex(intval($rgbamatches[2])).dechex(intval($rgbamatches[3])));
	$startopacity = floatval($rgbamatches[4]);
}
if(preg_match($rgbapattern,$endcolor,$rgbamatches)){
	$endcolor = '#'.strtoupper(dechex(intval($rgbamatches[1])).dechex(intval($rgbamatches[2])).dechex(intval($rgbamatches[3])));
	$endopacity = floatval($rgbamatches[4]);
}

echo '<?xml version="1.0" standalone="no"?>
<!DOCTYPE svg PUBLIC "-//W3C//DTD SVG 1.1//EN" "http://www.w3.org/Graphics/SVG/1.1/DTD/svg11.dtd">
<svg width="100%" height="100%" version="1.1" xmlns="http://www.w3.org/2000/svg">
        <defs>
                <linearGradient id="gradient" '.$svg_direction.'>
                        <stop offset="0%" style="stop-color:'.$startcolor.'; stop-opacity:'.$startopacity.'" />
                        <stop offset="100%" style="stop-color:'.$endcolor.'; stop-opacity:'.$endopacity.'" />
                </linearGradient>
        </defs>
        <rect width="100%" height="100%" style="fill:url(#gradient)"/>
</svg>';
?>