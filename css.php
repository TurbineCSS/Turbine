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
 * css.php
 * Loads CSSP
 * @var string $_GET['files'] A list of css files, separated by ;
 * @var string $_GET['config'] Path to the plugin configuration file
 * @var int $_GET['compress'] Minimize output?
 */


// Gzipping Output for faster transfer to client
@ini_set('zlib.output_compression', 2048);
@ini_set('zlib.output_compression_level', 4);
if(isset($_SERVER['HTTP_ACCEPT_ENCODING']) &&
	substr_count($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip') &&
	function_exists('ob_gzhandler') &&
	!ini_get('zlib.output_compression') &&
	((!ini_get('zlib.output_compression') || intval(ini_get('zlib.output_compression')) == 0))
){
	@ob_start('ob_gzhandler');
}
else{
	@ob_start();
}


// Global constants
$global_constants = array(
	'CSSPPATH' => dirname($_SERVER['SCRIPT_NAME']),
	'FILEPATH' => ''
);


// Plugin hooks
$plugins_before_parse = array();    // Before the initial code is touced at all by cssp
$plugins_before_compile = array();  // After parsing, before adding any cssp features (like inheritance)
$plugins_before_glue = array();     // After cssp features, before compiling the parsed array to css
$plugins_before_output = array();   // Before adding the processed css code to the output file


// Plugin register function
function register_plugin($hook, $priority, $function){
	global $plugins_before_parse, $plugins_before_compile, $plugins_before_glue, $plugins_before_output;
	if($hook == 'before_parse'){
		$plugins_before_parse[$function] = $priority;
	}
	elseif($hook == 'before_compile'){
		$plugins_before_compile[$function] = $priority;
	}
	elseif($hook == 'before_glue'){
		$plugins_before_glue[$function] = $priority;
	}
	elseif($hook == 'before_output'){
		$plugins_before_output[$function] = $priority;
	}
}


// Begin parsing
if($_GET['files']){

	// Starttime
	$start = microtime(true);

	// Set debug mode
	$debug = 0;
	if($debug){
		error_reporting(E_ALL);
	}

	// Load libraries
	include('lib/browser.php');
	include('lib/parser.php');
	include('lib/cssp.php');

	// Get and store browser properties
	$browser = new browser();

	// Transform multiple semicolon-separated files into an array
	$files = explode(';', $_GET['files']);

	// Client-side cache: Preparing caching-mechanism using eTags by creating fingerprint of CSS-files
	$fingerprint = '';
	foreach($files as $file){
		$fingerprint .= $file.@filemtime($file);
	}
	$etag = md5($fingerprint);

	// Client-side cache: now check if client sends eTag, and compare it with our eTag-fingerprint
	if($debug == 0 && @$_SERVER['HTTP_IF_NONE_MATCH'] === $etag){
		// Client-side cache: Success! Browser already has the file so we tell him nothing changed and exit
		header('HTTP/1.1 304 Not Modified');
		exit();
	}

	// Parse files
	$css = '';
	foreach($files as $file){
		if(file_exists($file)){

			// CSSP or CSS?
			$fileinfo = pathinfo($file);
			if($fileinfo['extension'] == 'css'){
				// Simply include normal css files in the output
				$css .= file_get_contents($file);
			}
			else{

				// Server-side cache: Has file already been parsed?
				$incache = false;

				// Server-side cache: Where to store parsed files
				$cachedir = 'lib/cssp_cache';

				// Server-side cache: Check if cache-directory has been created
				if(!is_dir($cachedir)){
					@mkdir($cachedir, 0777);
				}

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
				if(file_exists($cachedir.'/'.$cachefile) && @filemtime($cachedir.'/'.$cachefile) >= @filemtime($file)){
					$incache = true;
				}

				// Server-side cache: Cached version of the file does not yet exist
				if(!$incache){

					$cssp = new Cssp($file);

					// Set filepath
					$filepath = dirname($file);
					if($filepath != '.'){
						$global_constants['FILEPATH'] = $filepath;
					}
					else{
						$global_constants['FILEPATH'] = '';
					}

					// Load all plugins
					$plugindir = 'plugins';
					if($handle = opendir($plugindir)){
						while(false !== ($pluginfile = readdir($handle))){
							if($pluginfile != '.' && $pluginfile != '..' && !function_exists(substr($pluginfile, 0, -4))){
								include($plugindir.'/'.$pluginfile);
							}
						}
						closedir($handle);
					}

					// Get plugin settings for the before parse hook
					$before_parse_plugin_settings = array();
					$found = false;
					foreach($cssp->css as $line){
						if(!$found){
							if($line == '@cssp'){
								$found = true;
							}
						}
						else{
							preg_match('/^\s+plugins:(.*)$/', $line, $matches);
							if(count($matches) == 2){
								$before_parse_plugin_settings = $cssp->tokenize($matches[1], ',');
								break;
							}
						}
					}

					// Apply plugins for before parse
					asort($plugins_before_parse);
					$plugins_before_parse = array_reverse($plugins_before_parse);
					foreach($plugins_before_parse as $plugin => $priority){
						if(in_array($plugin, $before_parse_plugin_settings) && function_exists($plugin)){
							call_user_func_array($plugin, array(&$cssp->css));
						}
					}

					// Parse the code, read the stylesheet-specific plugins
					$cssp->parse();
					$stylesheet_plugins = array();
					if(isset($cssp->parsed['global']['@cssp']['plugins'])){
						$stylesheet_plugins = $cssp->tokenize($cssp->parsed['global']['@cssp']['plugins'], ',');
					}

					// Apply plugins for before compile
					asort($plugins_before_compile);
					$plugins_before_compile = array_reverse($plugins_before_compile);
					foreach($plugins_before_compile as $plugin => $priority){
						if(in_array($plugin, $stylesheet_plugins) && function_exists($plugin)){
							call_user_func_array($plugin, array(&$cssp->parsed));
						}
					}

					// Do the cssp magic
					$cssp->compile();

					// Apply plugins for before glue
					asort($plugins_before_glue);
					$plugins_before_glue = array_reverse($plugins_before_glue);
					foreach($plugins_before_glue as $plugin => $priority){
						if(in_array($plugin, $stylesheet_plugins) && function_exists($plugin)){
							call_user_func_array($plugin, array(&$cssp->parsed));
						}
					}

					// Set compression mode
					if(isset($cssp->parsed['global']['@cssp']['compress'])){
						$compress = (bool) $cssp->parsed['global']['@cssp']['compress'];
					}
					else{
						$compress = false;
					}

					// Remove configuration @-rule
					unset($cssp->parsed['global']['@cssp']);

					// Glue css output
					$output = $cssp->glue($compress);

					// Apply plugins for before output
					asort($plugins_before_output);
					$plugins_before_output = array_reverse($plugins_before_output);
					foreach($plugins_before_output as $plugin => $priority){
						if(in_array($plugin, $stylesheet_plugins) && function_exists($plugin)){
							call_user_func_array($plugin, array(&$output));
						}
					}

					// Add to css output
					@file_put_contents($cachedir.'/'.$cachefile, $output);
				}
				else{
					// Server-side cache: read the cached version of the file
					$output = @file_get_contents($cachedir.'/'.$cachefile);
				}

			}

			// Add to final css
			$css .= $output;
		}
	}


	// Endtime
	$end = microtime(true);

	// Send headers
	if($debug){
		header('Cache-Control: must-revalidate, pre-check=0, no-store, no-cache, max-age=0, post-check=0');
		header('Content-Type: text/html');
	}
	else{
		header('Content-Type: text/css');
		header('Cache-Control: no-cache, must-revalidate');
		header('Expires: '.gmdate('D, d M Y H:i:s').' GMT');
		header('Content-type: text/css'); 
		header('ETag: '.$etag);
	}

	// Output css
	echo "/* Generated by CSSP - CSS Preprocessor - http://github.com/SirPepe/CSSP - Time taken: ".($end - $start)." */\r\n".$css;

}


?>