<?php

/**
 * This file is part of Turbine
 * http://github.com/SirPepe/Turbine
 * 
 * Copyright Peter Kröner
 * Licensed under GNU LGPL 3, see license.txt or http://www.gnu.org/licenses/
 */


/*
 * css.php
 * Loads Turbine
 * @var string $_GET['files'] A list of css files, separated by ;
 */


// Benchmark start time
$start = microtime(true);


// Constants
define('TURBINEVERSION', (file_exists('version.txt')) ? trim(file_get_contents('version.txt')) : 'unknown');
define('TURBINEPATH', dirname($_SERVER['SCRIPT_NAME']));


// Gzip output for faster transfer to client
ini_set('zlib.output_compression', 2048);
ini_set('zlib.output_compression_level', 4);
if(isset($_SERVER['HTTP_ACCEPT_ENCODING']) &&
	substr_count($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip') &&
	function_exists('ob_gzhandler') &&
	!ini_get('zlib.output_compression') &&
	((!ini_get('zlib.output_compression') || intval(ini_get('zlib.output_compression')) == 0))
){
	ob_start('ob_gzhandler');
}
else{
	ob_start();
}


// Load libraries
include('lib/browser/browser.php');
include('lib/cssmin/cssmin.php');
include('lib/base.php');
include('lib/parser.php');
include('lib/cssp.php');


// Create the Turbine instance
$cssp = new CSSP();


// Get and store browser properties
$browser = new Browser();
$browser->parse();


// Set global path constant SCRIPTPATH for use in the special constant $_SCRIPTPATH
$cssp->global_constants['SCRIPTPATH'] = TURBINEPATH;


// Plugin loading state
$plugins_loaded = false;


// List of available plugins
$plugins_available = array();


// Process files
if($_GET['files']){


	// Split multiple semicolon-separated files into an array
	$files = explode(';', $_GET['files']);
	// Complete the paths
	$num_files = count($files);
	for($i=0; $i<$num_files; $i++){
		$files[$i] = $cssp->config['css_base_dir'].$files[$i];
	}


	// Client-side cache: Preparing caching-mechanism using eTags by creating a combined fingerprint of the files involved...
	$fingerprint = '';
	foreach($files as $file){
		if(file_exists($file)){
			$fingerprint .= $file.filemtime($file);
		}
	}
	$etag = md5($fingerprint);
	// ...and check if client sends eTag to compare it with our eTag-fingerprint
	if($cssp->config['debug_level'] == 0 && isset($_SERVER['HTTP_IF_NONE_MATCH']) && $_SERVER['HTTP_IF_NONE_MATCH'] === $etag){
		// Browser already has the file so we tell him nothing changed and exit
		header('HTTP/1.1 304 Not Modified');
		exit();
	}


	// Else parse the files and add the rusulting CSS code to $css
	$css = '';


	foreach($files as $file){
		if(file_exists($file)){

			// CSSP or CSS?
			$fileinfo = pathinfo($file);

			// For security reasons do not allow processing of files from above the base dir
			if(strpos(realpath($fileinfo['dirname']), realpath($cssp->config['css_base_dir'])) !== 0){
				$cssp->report_error('Path of '.$file.' is not in the base directory. File not processed for security reasons.');
				continue;
			}

			if($fileinfo['extension'] == 'css'){
				// Simply include normal css files in the output. Minify if not debugging and configured to minify
				if($cssp->config['debug_level'] == 0 && $cssp->config['minify_css'] == true){
					$css .= cssmin::minify(file_get_contents($file));
				}
				else{
					$css .= file_get_contents($file);
				}
			}
			else{


				$incache = false;    // Server-side cache: Has file already been parsed?
				$cachedir = 'cache'; // Cache directory


				// Server-side cache: Check if cache-directory has been created
				if(!is_dir($cachedir)){
					if(!@mkdir($cachedir, 0777)){
						$cssp->report_error('The cache directory doesn\'t exist!');
					}
				}
				elseif(!is_writable($cachedir)){
					if(!@chmod($cachedir, 0777)){
						$cssp->report_error('The cache directory is not writeable!');
					}
				}


				// Server-side cache: Create a name for the new cache file
				$cachefile = md5(
					$browser->platform.
					$browser->platform_version.
					$browser->platform_type.
					$browser->engine.
					$browser->engine_version.
					$browser->browser.
					$browser->browser_version.
					$file
				).'.txt';


				// Server-side cache: Check if a cached version of the file already exists
				if(file_exists($cachedir.'/'.$cachefile) && filemtime($cachedir.'/'.$cachefile) >= filemtime($file)){
					$incache = true;
				}


				// Server-side cache: Cached version of the file does not yet exist
				if(!$incache){


					// Load plugins (if not already loaded)
					if(!$plugins_loaded){
						$plugindir = 'plugins';
						if($handle = opendir($plugindir)){
							while(false !== ($pluginfile = readdir($handle))){
								if($pluginfile != '.' && $pluginfile != '..' && is_file($plugindir.'/'.$pluginfile) && pathinfo($plugindir.'/'.$pluginfile,PATHINFO_EXTENSION) == 'php' && !function_exists(substr($pluginfile, 0, -4))){
									include($plugindir.'/'.$pluginfile);               // Include the plugin
									$plugins_available[] = substr($pluginfile, 0, -4); // Add the plugin to the list of available plugins
								}
							}
							closedir($handle);
						}
						$plugins_loaded = true;
					}


					// Load the file into cssp
					$cssp->load_file($file, true);


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
					foreach($cssp->code as $line){
						if(!$found){
							if(preg_match('/^[\s\t]*@turbine/i',$line) == 1){
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


					// Check if there is any plugin in the list that doesn't actually exist
					$plugin_diff = array_diff($plugin_list, $plugins_available);
					if(!empty($plugin_diff)){
						$cssp->report_error('The following plugins are not present in your Turbine installation: '.ucfirst(implode(', ', $plugin_diff)));
					}


					$cssp->set_indention_char();                                         // Set the character(s) used for code indention
					$cssp->apply_plugins('before_parse', $plugin_list, $cssp->code);     // Apply plugins for before parse
					$cssp->parse();                                                      // Parse the code
					$cssp->apply_plugins('before_compile', $plugin_list, $cssp->parsed); // Apply plugins for before compile
					$cssp->compile();                                                    // Do the Turbine magic
					$cssp->apply_plugins('before_glue', $plugin_list, $cssp->parsed);    // Apply plugins for before glue

					// Set compression mode
					if(isset($cssp->parsed['global']['@turbine']['compress'][0])){
						$compress = (bool) $cssp->parsed['global']['@turbine']['compress'][0];
					}
					else{
						$compress = false;
					}


					unset($cssp->parsed['global']['@turbine']);                   // Remove configuration @-rule
					$output = $cssp->glue($compress);                             // Glue css output
					$cssp->apply_plugins('before_output', $plugin_list, $output); // Apply plugins for before output
					$cssp->reset();                                               // Reset the parser


					// Add output to cache
					if($cssp->config['debug_level'] == 0){
						file_put_contents($cachedir.'/'.$cachefile, $output);
					}


				}
				else{
					// Server-side cache: read the cached version of the file
					$output = file_get_contents($cachedir.'/'.$cachefile);
				}

			}

			// Add to final css
			$css .= $output;
		}


		// File not found, report error
		else{
			$cssp->report_error('Style file '.$file.' not found. Is the base path in config.php configured correctly?');
		}


	}

	// Show errors
	if($cssp->config['debug_level'] > 0 && !empty($cssp->errors)){
		$error_message = implode('\\00000A', $cssp->errors);
		$css = $css.'body:before { content:"'.$error_message.'" !important; font-family:Verdana, Arial, sans-serif !important;
			font-weight:bold !important; color:#000 !important; background:#F4EA9F !important; display:block !important;
			border-bottom:1px solid #D5CA6E; padding:8px !important; white-space:pre; }';
	}


	// Send headers
	header('Content-Type: text/css');
	if($cssp->config['debug_level'] > 0){
		header('Cache-Control: must-revalidate, pre-check=0, no-store, no-cache, max-age=0, post-check=0');
	}
	else{
		header('Cache-Control: no-cache, must-revalidate');
		header('Expires: '.gmdate('D, d M Y H:i:s').' GMT');
		header('Content-type: text/css'); 
		header('ETag: '.$etag);
	}


	// End benchmark
	$end = microtime(true);


	// Output header line
	echo "/*\r\n\tStylesheet generated by Turbine - http://turbine.peterkroener.de/";


	// Output debugging info
	if($cssp->config['debug_level'] > 0){
		$debugginginfo = array(
			'Version' => TURBINEVERSION,
			'Path' => TURBINEPATH,
			'Benchmark' => $end - $start,
			'Browser' => $browser->browser,
			'Browser version' => $browser->browser_version,
			'Browser engine' => $browser->engine,
			'Browser engine version' => $browser->engine_version,
			'Platform' => $browser->platform,
			'Platform version' => $browser->platform_version,
			'Platform type' => $browser->platform_type
		);
		foreach($debugginginfo as $key => $value){
			echo "\r\n\t$key: $value";
		}
	}


	// Close comment, output CSS
	echo "\r\n*/\r\n".$css;

}


?>
