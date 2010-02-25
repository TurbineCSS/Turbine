<?php


	/**
	 * Automatic support for HTML5 elements - even in IE!
	 * 
	 * Usage: Nobrainer, just switch it on
	 * Example: -
	 * Status: Beta
	 * 
	 * html5
	 * Registers sub functions
	 * @param array &$css The style lines (unused)
	 * @return void
	 */
	function html5(&$css){
		global $browser, $cssp, $plugin_list;
		$plugin_list[] = 'html5styles';
		$cssp->register_plugin('before_output', 0, 'html5styles');
		if($browser->family == 'MSIE' && floatval($browser->familyversion) < 9){
			$plugin_list[] = 'html5elements';
			$cssp->register_plugin('before_compile', 0, 'html5elements');
		}
	}


	/*
	 * html5elements
	 * Enables HTML5 elements in IE
	 * @param mixed &$parsed
	 * @return void
	 */
	function html5elements(&$parsed){
		foreach($parsed as $block => $css){
			// Registration of HTML5 elements
			// Fix inspired by http://code.google.com/p/html5shiv/
			$htc_path = rtrim(dirname($_SERVER['SCRIPT_NAME']),'/').'/plugins/html5/html5.htc';
			if(!isset($parsed[$block]['body'])){
				$parsed[$block]['body'] = array();
			}
			if(!isset($parsed[$block]['body']['behavior'])){
				$parsed[$block]['body']['behavior'] = 'url("'.$htc_path.'")';
			}
			else{
				if(!strpos($parsed[$block]['body']['behavior'],'url("'.$htc_path.'")')){
					$parsed[$block]['body']['behavior'] .= ', url("'.$htc_path.'")';
				}
			}
		}
	}


	/**
	 * html5styles
	 * Adds the correct default styles for HTML5 elements
	 * Source: http://www.whatwg.org/specs/web-apps/current-work/multipage/rendering.html#the-css-user-agent-style-sheet-and-presentational-hints
	 * @param string $output
	 * @return void
	 */
	function html5styles(&$output){
		$styles = "command,datalist,source{display:none}article,aside,figure,figcaption,footer,header,hgoup,menu,nav,section,summary{display:block}figure,menu{margin-top:1em;margin-bottom:1em}dir menu,dl menu,menu dir,menu dl,menu menu,menu ol,menu ul{margin-top:0;margin-bottom:0}";
		$output = $styles.$output;
	}


	/**
	 * Register the plugin
	 */
	$cssp->register_plugin('before_parse', 0, 'html5');


?>