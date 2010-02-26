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


/**
 * css.php
 * Loads Turbine
 * @var string $_GET['files'] A list of css files, separated by ;
 * @var string $_GET['config'] Path to the plugin configuration file
 */


// Start time
$start = microtime(true);


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
include('lib/base.php');
include('lib/browser.php');
include('lib/parser.php');
include('lib/cssp.php');
include('lib/cssmin.php');


// New Turbine instance
$cssp = new CSSP();


// Get and store browser properties
$browser = new Browser();


// Set global path constant SCRIPTPATH
$cssp->global_constants['SCRIPTPATH'] = dirname($_SERVER['SCRIPT_NAME']);


// Plugin state
$plugins_loaded = false;


// Precess files
if($_GET['files']){


	// Split multiple semicolon-separated files into an array
	$files = explode(';', $_GET['files']);

	// Complete the paths
	$num_files = count($files);
	for($i=0; $i<$num_files; $i++){
		$files[$i] = $cssp->config['css_base_dir'].$files[$i];
	}


	// Client-side cache: Preparing caching-mechanism using eTags by creating fingerprint of CSS-files
	$fingerprint = '';
	foreach($files as $file){
		$fingerprint .= $file.filemtime($file);
	}
	$etag = md5($fingerprint);

	// Client-side cache: now check if client sends eTag, and compare it with our eTag-fingerprint
	if($cssp->config['debug_level'] == 0 && $_SERVER['HTTP_IF_NONE_MATCH'] === $etag){
		header('HTTP/1.1 304 Not Modified'); // Client-side cache: Browser already has the file so we tell him nothing changed and exit
		exit();
	}

	// Else: parse files
	$css = '';


	foreach($files as $file){
		if(file_exists($file)){

			// CSSP or CSS?
			$fileinfo = pathinfo($file);
			if($fileinfo['extension'] == 'css'){
				// Simply include normal css files in the output. Minify if not debugging or configured not to minify
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
					if(!mkdir($cachedir, 0777)){
						$cssp->report_error('The cache directory doesn\'t exist!');
					}
				}
				elseif(!is_writable($cachedir)){
					if(!chmod($cachedir, 0777)){
						$cssp->report_error('The cache directory is not writeable!');
					}
				}


				// Server-side cache: Create a name for the new cache file
				$cachefile = md5(
					$browser->platform.
					$browser->platformversion.
					$browser->platformtype.
					$browser->engine.
					$browser->engineversion.
					$browser->family.
					$browser->familyversion.
					$browser->name.
					$browser->version.
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
									include($plugindir.'/'.$pluginfile);
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
					foreach($cssp->css as $line){
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


					$cssp->apply_plugins('before_parse', $plugin_list, $cssp->css);      // Apply plugins for before parse
					$cssp->parse();                                                      // Parse the code
					$cssp->apply_plugins('before_compile', $plugin_list, $cssp->parsed); // Apply plugins for before compile
					$cssp->compile();                                                    // Do the Turbine magic
					$cssp->apply_plugins('before_glue', $plugin_list, $cssp->parsed);    // Apply plugins for before glue


					// Set compression mode
					if(isset($cssp->parsed['global']['@turbine']['compress'])){
						$compress = (bool) $cssp->parsed['global']['@turbine']['compress'];
					}
					else{
						$compress = false;
					}


					unset($cssp->parsed['global']['@turbine']);                      // Remove configuration @-rule
					$output = $cssp->glue($compress);                             // Glue css output
					$cssp->apply_plugins('before_output', $plugin_list, $output); // Apply plugins for before output


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


	// End time
	$end = microtime(true);


	// Output css
	echo "/* Generated by Turbine - http://github.com/SirPepe/Turbine - Time taken: ".($end - $start)." */\r\n".$css;


}


?>
