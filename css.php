<?php


	/**
	 * css.php
	 * Loads CSSP
	 * @var string $_GET['files'] A list of css files, seperated by ;
	 * @var string $_GET['config'] Path to the plugin configuration file
	 * @var int $_GET['compress'] Minimize output?
	 */

	// Gzipping Output for faster transfer to client
	@ini_set('zlib.output_compression',2048);
	@ini_set('zlib.output_compression_level',4);
	if (
	isset($_SERVER['HTTP_ACCEPT_ENCODING']) 
	&& substr_count($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip') 
	&& function_exists('ob_gzhandler') 
	&& !ini_get('zlib.output_compression')
	) @ob_start('ob_gzhandler');
	else @ob_start();

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
		include('lib/browser_class_inc.php');
		include('lib/parser.php');
		include('lib/cssp.php');

		// Get and store browser properties
		$browser = new browser();

		// Transform multiple semicolon-separated files into an array
		$files = explode(';', $_GET['files']);

		// Client-side cache: Preparing caching-mechanism using eTags by creating fingerprint of CSS-files
		$fingerprint = '';
		foreach($files as $file) $fingerprint .= $file.filemtime($file);
		$etag = md5($fingerprint);

		// Client-side cache: now check if client sends eTag, and compare it with our eTag-fingerprint
		if($debug == 0 && @$_SERVER['HTTP_IF_NONE_MATCH'] === $etag) 
		{
			// Client-side cache: Success! Browser already has the file so we tell him nothing changed and exit
			header('HTTP/1.1 304 Not Modified');
			exit();
		}
		// Parse files
		$css = '';
		foreach($files as $file){
			if(file_exists($file))
			{
				// Server-side cache: Has file already been parsed?
				$incache = false;
				// Server-side cache: Where to store parsed files
				$cachedir = 'lib/cssp_cache';
				// Server-side cache: Check if cache-directory has been created
				if(!is_dir($cachedir)){
					@mkdir($cachedir, 0777);
				}
				$cachefile = $browser->family.$browser->familyversion.preg_replace('/[^0-9A-Za-z\-\._]/','',str_replace(array('\\','/'),'.',$file));
				// Server-side cache: Check if a cached version of the file already exists
				if(file_exists($cachedir.'/'.$cachefile) && filemtime($cachedir.'/'.$cachefile) >= filemtime($file)) $incache = true;
				// Server-side cache: Cached version of the file does not yet exist
				if(!$incache){
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
									call_user_func_array($plugin, array(&$cssp->parsed));
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
					@file_put_contents($cachedir.'/'.$cachefile,$cssp->glue($compress));
				}
				// Server-side cache: read the cached version of the file
				$css .= @file_get_contents($cachedir.'/'.$cachefile);
			}
		}

		// Endtime
		$end = microtime(true);

		// Send headers
		if($debug == 1){
			header('Cache-Control: must-revalidate, pre-check=0, no-store, no-cache, max-age=0, post-check=0');
			header('Content-Type: text/html');
		}
		else {
			header('Content-Type: text/css');
			header("Cache-Control: no-cache, must-revalidate");
			header("Expires: ".gmdate('D, d M Y H:i:s')." GMT");
			header("Content-type: text/plain"); 
			header("ETag: ".$etag);
		}

		// Output css
		echo "/* Time taken: ".($end - $start)." */\r\n".$css;

	}


?>