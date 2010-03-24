<?php

/**
 * Turbine
 * http://github.com/SirPepe/Turbine
 * 
 * Copyright (C) 2009 Peter Kröner, Christian Schaefer
 * 
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Library General Public
 * License as published by the Free Software Foundation; either
 * version 3 of the License, or (at your option) any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
 * Library General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
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
	'minify_css' => true


);

?>