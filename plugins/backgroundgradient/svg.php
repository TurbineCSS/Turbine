<?php

/**
 * This file is part of Turbine
 * http://github.com/SirPepe/Turbine
 * 
 * Copyright Peter Kröner
 * Licensed under GNU LGPL 3, see license.txt or http://www.gnu.org/licenses/
 */

/**
 * The file creates simple svg gradients that can be used as a background in Opera unil it supports pure css gradients
 */

// Get the 
$direction = (isset($_GET['direction']) && $_GET['direction'] == 'top' ||$_GET['direction'] == 'left' ? $_GET['direction'] : 'top');
$startcolor = (isset($_GET['startcolor']) ? $_GET['startcolor'] : '#FFFFF');
$endcolor = (isset($_GET['endcolor']) ? $_GET['endcolor'] : '#000000');
$etag = md5($direction.$startcolor.$endcolor);


// Exit if the file is already in the browser's cache
if(isset($_SERVER['HTTP_IF_NONE_MATCH']) && $_SERVER['HTTP_IF_NONE_MATCH'] === $etag){
	header('HTTP/1.1 304 Not Modified');
	exit();
}


// Set the file headers
header('Cache-Control: no-cache, must-revalidate');
header('Expires: '.gmdate('D, d M Y H:i:s').' GMT');
header('Content-type: image/svg+xml'); 
header('ETag: '.$etag);


// Convert direction to SVG attributes
if($direction == 'top'){
	$svg_direction = 'x1="0%" y1="0%" x2="0%" y2="100%"';
}
else{
	$svg_direction = 'x1="0%" y1="0%" x2="100%" y2="0%"';
}


// Output the svg xml
echo '<?xml version="1.0" standalone="no"?>
<!DOCTYPE svg PUBLIC "-//W3C//DTD SVG 1.1//EN" "http://www.w3.org/Graphics/SVG/1.1/DTD/svg11.dtd">
<svg width="100%" height="100%" version="1.1" xmlns="http://www.w3.org/2000/svg">
        <defs>
                <linearGradient id="gradient" '.$svg_direction.'>
                        <stop offset="0%" style="stop-color:'.$startcolor.';" />
                        <stop offset="100%" style="stop-color:'.$endcolor.';" />
                </linearGradient>
        </defs>
        <rect width="100%" height="100%" style="fill:url(#gradient)"/>
</svg>';


?>
