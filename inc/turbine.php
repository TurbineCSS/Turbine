<?php

/**
 * This file is part of Turbine
 * http://github.com/SirPepe/Turbine
 * 
 * Copyright Peter Kröner
 * Licensed under GNU LGPL 3, see license.txt or http://www.gnu.org/licenses/
 */


/**
 * get_turbine
 * Returns an (X)HTML <link> tag for using Turbine's css.php
 * @param string $path The path to css.php
 * @param array $files The list of cssp or css files to use
 * @param string $mode Output the html as xhtml or html
 * @param string $media Content for the media attribute
 * @return string $html The <link> tag
 */
function get_turbine($path = 'css.php', $files = array(), $mode = 'xhtml', $media = ''){
	$query_string = '?files=' . implode(';', $files);
	$media_string = ($media) ? ' media="'.$media.'"' : '';
	$html = '<link rel="stylesheet" href="' . $path . $query_string . '"'.$media_string;
	if($mode == 'xhtml'){
		$html .= ' /';
	}
	$html .= '>';
	return $html;
}


/**
 * turbine
 * Prints an (X)HTML <link> tag for using Turbine's css.php
 * @param string $path The path to css.php
 * @param array $files The list of cssp or css files to use
 * @param string $mode Output the html as xhtml or html
 * @param string $media Content for the media attribute
 * @return string $html The <link> tag
 */
function turbine($path = 'css.php', $files = array(), $mode = 'xhtml', $media = ''){
	echo get_turbine($path, $files, $mode, $media);
}
