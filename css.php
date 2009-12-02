<?php


	/**
	 * css.php
	 * Loads CSSP
	 * @var string $_GET['files'] A list of css files, seperated by ;
	 * @var string $_GET['config'] Path to the plugin configuration file
	 * @var int $_GET['compress'] Minimize output?
	 */
	if($_GET['files']){

		// Set debug mode
		$debug = 0;
		if($debug){
			error_reporting(E_ALL);
		}

		// Load libraries
		include('lib/browser_class_inc.php');
		include('lib/parser.php');
		include('lib/cssp.php');

		// Get and store browser properties
		$b = new browser();
		$browserproperties = $b->whatbrowser();

		// Parse files
		$css = '';
		$files = explode(';', $_GET['files']);
		foreach($files as $file){
			$cssp = new Cssp($file);
			// Apply plugins
			if(isset($cssp->parsed['css']['@cssp']['plugins'])){ // TODO; What if the configuration element is not in the global block?
				$plugin_dir = 'plugins';
				$plugins = preg_split("/\s+/", $cssp->parsed['css']['@cssp']['plugins']);
				foreach($plugins as $plugin){
					$pluginfile = $plugin_dir.'/'.$plugin.'.php';
					if(file_exists($pluginfile)){
						@include($pluginfile);
						if(function_exists($plugin)){
							call_user_func($plugin, &$cssp->parsed);
						}
					}
				}
			}
			// Set compression mode
			if(isset($cssp->parsed['css']['@cssp']['compress'])){
				$compress = (bool) $cssp->parsed['css']['@cssp']['compress'];
			}
			else{
				$compress = false;
			}
			// Remove configuration @-rule
			unset($cssp->parsed['css']['@cssp']);
			// Add to css output
			$css .= $cssp->glue($compress);
		}

		// Send headers
		if($debug == 1){
			header('Cache-Control: must-revalidate, pre-check=0, no-store, no-cache, max-age=0, post-check=0');
			header('Content-Type: text/html');
		}
		else {
			header('Content-Type: text/css');
		}

		// Output css
		echo $css;

	}


?>