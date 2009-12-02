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

		// Load plugins
		if($_GET['config'] && file_exists($_GET['config'])){
			$plugins = file($_GET['config']);
		}
		else{
			$plugins = file('plugins.conf');
		}
		$plugin_functions = array();
		$plugin_dir = 'plugins';
		foreach($plugins as $plugin){
			$plugin = trim($plugin);
			$pluginfile = $plugin_dir.'/'.$plugin.'.php';
			if($plugin{0} != '#' && file_exists($pluginfile)){
				include($pluginfile);
			}
		}

		// Fetch and store Browser Properties
		$b = new browser();
		$browserproperties = $b->whatbrowser();

		// Parse files
		$css = '';
		$files = explode(';', $_GET['files']);
		foreach($files as $file){
			$cssp = new Cssp($file);
			// Apply plugins
			foreach($plugins as $plugin){
				$plugin = trim($plugin);
				if($plugin{0} != '#' && function_exists($plugin)){
					call_user_func($plugin, &$cssp->parsed);
				}
			}
			if(isset($_GET['compress'])){
				$compress = (bool) $_GET['compress'];
			}
			else{
				$compress = false;
			}
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