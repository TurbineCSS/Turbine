<?php

/**
 * CSSP - CSS Preprocessor - http://github.com/SirPepe/CSSP
 * Constants and inheritance for CSS
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

/**
 * Base
 * CSSP Base Clasc
 */
class Base {


/**
 * @var array $config The (default) configuration
 */
public $config = array(
	'debug_level' => 1,
	'css_base_dir' => '', // TODO: Do something with this
	'minify_css' => true
);


/**
 * @var array $errors
 */
public $errors = array();


/**
* @var array $plugins Plugin hooks
* before_parse
* before_compile
* before_glue
* before_output
 */
public $plugins = array(
	'before_parse' => array(),
	'before_compile' => array(),
	'before_glue' => array(),
	'before_output' => array()
);


/**
 * @var array $global_constants
 */
public $global_constants = array(
	'CSSPPATH' => '',
	'FILEPATH' => ''
);


/**
 * Constructor
 * @return void
 */
public function __construct(){
	// Load config
	include('config.php');
	foreach($config as $key => $setting){
		$this->config[$key] = $setting;
	}
	// Set error output
	if($this->config['debug_level'] == 2){
		error_reporting('E_ALL');
	}
	else{
		error_reporting('E_NONE');
	}
}


/**
 * Plugin register function
 * @param string $hook The plugin hook
 * @param int $priority The execution priority. Higher number = earlier execution
 * @param $function The plugin's mail function
 * @return void
 */
public function register_plugin($hook, $priority, $function){
	if(isset($this->plugins[$hook])){
		$this->plugins[$hook][$function] = $priority;
	}
}


/**
 * Applies plugins
 * @param string $plugins Plugin hook
 * @param array $list List of Plugins to apply
 * @param mixed &$subject The subject to apply the plugins to
 * @return void
 */
public function apply_plugins($plugins, $list, &$subject){
	asort($this->plugins[$plugins]);
	$this->plugins[$plugins] = array_reverse($this->plugins[$plugins]);
	foreach($this->plugins[$plugins] as $plugin => $priority){
		if(in_array($plugin, $list) && function_exists($plugin)){
			call_user_func_array($plugin, array(&$subject));
		}
	}
}


/**
 * Stores an error for output if debug level is > 0
 * @param string $error The error message
 * @return void
 */
public function report_error($error){
	if($this->config['debug_level'] > 0){
		$this->errors[] = $error;
	}
}


}


?>