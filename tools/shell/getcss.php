<?php

// Load libraries
include('../../lib/base.php');
include('../../lib/browser.php');
include('../../lib/parser.php');
include('../../lib/cssp.php');
include('../../lib/cssmin.php');


// New CSSP instance
$cssp = new CSSP();


// Get and store browser properties
$browser = new Browser();


// Set global path constant CSSPPATH
$cssp->global_constants['CSSPPATH'] = dirname($_SERVER['SCRIPT_NAME']);


// Load plugins
$plugindir = '../../plugins';
if($handle = opendir($plugindir)){
	while(false !== ($pluginfile = readdir($handle))){
		if($pluginfile != '.' && $pluginfile != '..' && is_file($plugindir.'/'.$pluginfile) && pathinfo($plugindir.'/'.$pluginfile,PATHINFO_EXTENSION) == 'php' && !function_exists(substr($pluginfile, 0, -4))){
			include($plugindir.'/'.$pluginfile);
		}
	}
	closedir($handle);
}


// Precess input
if($_POST['css']){

	// Load string
	$cssp->load_string($_POST['css'], true);


	// Set global filepath constant for the current file
	$filepath = dirname($file);
	if($filepath != '.'){
		$cssp->global_constants['FILEPATH'] = $filepath;
	}
	else{
		$cssp->global_constants['FILEPATH'] = '';
	}


	// Get plugin settings for the before parse hook
	$plugin_list = array();
	$found = false;
	foreach($cssp->css as $line){
		if(!$found){
			if(preg_match('/^[\s\t]*@cssp/i',$line) == 1){
				$found = true;
			}
		}
		else{
			preg_match('~^\s+plugins:(.*?)(?://|$)~', $line, $matches);
			if(count($matches) == 2){
				$plugin_list = $cssp->tokenize($matches[1], ',');
				break;
			}
		}
	}

	// Apply plugins
	$cssp->apply_plugins('before_parse', $plugin_list, $cssp->css);      // Apply plugins for before parse
	$cssp->parse();                                                       // Parse the code
	$cssp->apply_plugins('before_compile', $plugin_list, $cssp->parsed); // Apply plugins for before compile
	$cssp->compile();                                                     // Do the cssp magic
	$cssp->apply_plugins('before_glue', $plugin_list, $cssp->parsed);    // Apply plugins for before glue


	// Set compression mode
	if(isset($cssp->parsed['global']['@cssp']['compress'])){
		$compress = (bool) $cssp->parsed['global']['@cssp']['compress'];
	}
	else{
		$compress = false;
	}

	// Cleanup
	unset($cssp->parsed['global']['@cssp']);                      // Remove configuration @-rule
	$output = $cssp->glue($compress);                             // Glue css output
	$cssp->apply_plugins('before_output', $plugin_list, $output); // Apply plugins for before output

	// Outpu
	echo $output;

}


?>