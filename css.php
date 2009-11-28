<?php


	/**
	 * css.php
	 * Loads CSSP
	 * @var string $_GET['files'] A list of css files, seperated by ;
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
		$plugin_dir = 'plugins';
		$plugins = array();
		if($handle = opendir($plugin_dir)) {
			while(false !== ($file = readdir($handle))){
				if($file != "." && $file != ".."){
					if(substr($file, -4, 4) == '.php'){
						include('plugins/'.$file);
						$plugins[] = substr($file, 0, -4);
					}
				}
			}
			closedir($handle);
		}
		else {
			die('Error: Plugin directory "'.$plugin_dir.'" not found!');
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
				call_user_func($plugin, &$cssp->parsed);
			}
			$css .= $cssp->glue();
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