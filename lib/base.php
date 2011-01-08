<?php

/**
 * This file is part of Turbine
 * http://github.com/SirPepe/Turbine
 * 
 * Copyright Peter Kröner
 * Licensed under GNU LGPL 3, see license.txt or http://www.gnu.org/licenses/
 */


/**
 * Base
 * Turbine base class
 */
class Base {


/**
 * @var array $config The (default) configuration
 */
public $config = array(
	'turbine_dir' => '',
	'debug_level' => 0,
	'css_base_dir' => '',
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
	'while_parsing' => array(),
	'before_compile' => array(),
	'before_glue' => array(),
	'before_output' => array()
);


/**
 * @var array $global_constants
 */
public $global_constants = array(
	'SCRIPTPATH' => '',
	'FILEPATH' => ''
);


/**
 * Constructor
 * @return void
 */
public function __construct(){}


/**
 * load_config
 * Loads the configuration array into class variables
 * @return void
 */
public function load_config(){
	global $config;
	if(isset($config)){
		foreach($config as $key => $setting){
			$this->config[$key] = $setting;
		}
	}
	else{
		// Else print a comment reporting an error into the css. Real error reporting won't work here as the default debug value is 0
		echo "/* Notice: Configuration file config.php not found - using default configuration */\r\n\r\n";
	}
	// Apppend the final slash to the base dir if it is missing
	if($this->config['css_base_dir'] != ''){
		$this->config['css_base_dir'] = rtrim($this->config['css_base_dir'], '/').'/';
	}
	// Set error output
	if($this->config['debug_level'] == 2){
		error_reporting(E_ALL);
	}
	else{
		error_reporting(0);
	}
}


/**
 * Plugin register function.
 * The plugin's name
 * @param $function The plugin's main function OR an array containing arrays with several functions, hooks and priorities
 * @param string $hook [optional] The plugin hook. Can be omitted if $function is an array
 * @param int $priority The execution priority. Higher number = earlier execution. Can be omitted if $function is an array
 * @return void
 */
public function register_plugin($plugin, $function, $hook = null, $priority = 0){
	if(is_array($function)){
		foreach($function as $func){
			$this->register_plugin($plugin, $func[0], $func[1], $func[2]);
		}
	}
	else{
		if(isset($this->plugins[$hook])){
			$this->plugins[$hook][$function] = array('plugin' => $plugin, 'prioritiy' => $priority);
		}
	}
}


/**
 * Sort the registered plugins
 * @return void
 */
public function sort_plugins(){
	foreach($this->plugins as $hook => $plugins){
		uasort($this->plugins[$hook], array($this, 'sort_plugins_callback'));
	}
}


/**
 * Callback for plugin sorting
 * @param array $a
 * @param array $b
 * @return int
 */
private function sort_plugins_callback($a, $b){
	if($a['prioritiy'] == $b['prioritiy']){
		return 0;
	}
	return ($a['prioritiy'] > $b['prioritiy']) ? -1 : 1;
}


/**
 * Applies plugins
 * @param string $hook Plugin hook
 * @param array $list The list of stylesheet-defined plugins to apply
 * @param mixed &$subject1 The first subject to apply the plugins to
 * @param mixed &$subject2 The second subject to apply the plugins to, if there is one
 * @return void
 */
public function apply_plugins($hook, $list, &$subject1, &$subject2 = NULL){
	foreach($this->plugins[$hook] as $function => $properties){
		if(function_exists($function) && in_array($properties['plugin'], $list)){
			call_user_func_array($function,
				($subject2 === NULL) ? array(&$subject1) : array(&$subject1, &$subject2)
			);
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


/**
 * array_get_previous
 * Searches the array $array for the value (or the key) before the key $search
 * @param array $array The array to search in
 * @param mixed $search The key before the searched value
 * @param bool $returnkey Return the key insted of the value?
 * @return mixed $previous The search result
 */
public function array_get_previous($array, $search, $returnkey = false){
	$previous = null;
	foreach($array as $key => $value){
		if($key == $search){
			return ($returnkey) ? $key : $previous;
		}
		$previous = $value;
	}
	return ($returnkey) ? $key : $previous;
}


}


?>
