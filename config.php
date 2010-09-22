<?php

/**
 * This file is part of Turbine
 * http://github.com/SirPepe/Turbine
 * 
 * Copyright Peter Kröner
 * Licensed under GNU LGPL 3, see license.txt or http://www.gnu.org/licenses/
 */


$config = array(


	// Sets debugging off (0), on (1), or in developer mode (2)
	// Mode 0 hides all error messages
	// Mode 1 displays error messages related to the style sheets (like elements trying to inherit properties that don't exist)
	// Mode 2 additionally displays php developer messages and sets error_reporting to E_ALL
	'debug_level' => 2,


	// Base path to cssp and css files relative to css.php
	'css_base_dir' => '',


	// Minify regular css files (true) oder include them completely unchanged (false)
	'minify_css' => true,


	// Set expire header expire_in_future seconds after the request
	'expire_in_future' => 0


);



// For multiple sites using the same Turbine installation, add the base dirs and the conditions of their use here. Example:
// if($_SERVER['SERVER_NAME'] == 'localhost'){
// 	$config['css_base_dir'] = '/my/local/base/dir';
// }
// elseif($_SERVER['SERVER_NAME'] == 'www.production.com'){
// 	$config['css_base_dir'] = '/production/base/dir';
// }



?>
