<?php
/**
 * CSS Parser
 * A tool for parsing and manipulating stylesheets.
 * 
 * Copyright (C) 2009 Peter Kröner, Christian Schaefer, Anton Pawlik
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
 * CssParser
 * General purpose CSS parser
 */
class CssParser {


	/**
	 * @var string $css The loaded css code (before parsing)
	 */
	public $css;


	/**
	 * @var array $parsed The parsed css
	 */
	public $parsed;


	/**
	 * @var array $unsafe_chars
	 */
	public $unsafe_chars = array(
		'__doubleslash'				=> '//',
		'__bigcopen'				=> '/*',
		'__bigcclose'				=> '*/',
		'__doubledot'				=> ':',
		'__semicolon'				=> ';',
		'__curlybracketopen'		=> '{',
		'__curlybracketclosed'		=> '}',
	);


	/**
	 * load_string
	 * Loads a css string
	 * @param string $string the css to load
	 * @param bool $overwrite overwrite already loaded css or append the new string?
	 */
	public function load_string($string, $overwrite = false){
		if($overwrite){
			$this->css = $string;
		} else {
			$this->css .= $string;
		}
	}


	/**
	 * load_file
	 * Loads a file
	 * @param string $file The css files to load
	 * @param bool $overwrite overwrite already loaded css or append the new string?
	 */
	public function load_file($file, $overwrite = false){
		$this->load_string(file_get_contents($file), $overwrite);
	}


	/**
	 * load_files
	 * Loads a number of files
	 * @param string $files File(s) to load, sepperated by ;
	 */
	public function load_files($files){
		$files = explode(';', $files);
		foreach($files as $file){
			$this->load_file($file, false);
		}
	}


	/**
	 * parse
	 * Parses some CSS into an array
	 * @param bool $rebuild Rebuild the array after parsing?
	 */
	public function parse($rebuild = true){
		$css = $this->mask_unsafe_chars($this->css);
		// Remove HTML before, after and between (and including) <script> tags
		$css = preg_replace('/\<\/style\>(.*?)\<style.*?\>/ms', NULL, $css);
		$css = preg_replace('/^(.*?)\<style.*?\>/ms', NULL, $css);
		$css = preg_replace('/\<\/style\>(.*)$/ms', NULL, $css);
		// Remove CSS comments
		$css = preg_replace('/\/\*.*?\*\//ms', NULL, $css);
		// Remove HTML comments
		$css = preg_replace('/([^\'"]+?)(\<!--|--\>)([^\'"]+?)/ms', '$1$3', $css);
		// Extract @media-blocks into $blocks
		preg_match_all('/@media.+?\{.*?\}[\s]*\}/ms', $css, $blocks);
		// Append the rest to $blocks
		$blocks[0][] = str_replace($blocks[0], NULL, $css);
		$ordered = array();
		$block_count = count($blocks[0]);
		for($i = 0; $i<$block_count; $i++){
			// If @media-block, strip declaration and parenthesis
			if((strlen($blocks[0][$i]) > 5) && (substr_compare($blocks[0][$i], '@media', 0, 6, FALSE) === 0)){
				$ordered_key = trim(preg_replace('/^(@media[^\{]+)\{.*\}$/ms', '$1', $blocks[0][$i]));
				$ordered_value = trim(preg_replace('/^@media[^\{]+\{(.*)\}$/ms', '$1', $blocks[0][$i]));
			}
			else{
				$ordered_key = 'css';
				$ordered_value = trim($blocks[0][$i]);
			}
			// Extract @imports
			$imports = array();
			$import_syntax = '/@import url\((.*?)\)(.*?);/ms'; // TODO: Match @imports without braces
			preg_match_all($import_syntax, $ordered_value, $import_values);
			$num_imports = count($import_values[0]);
			for($j = 0; $j < $num_imports; $j++){
				$imports[] = array(
					'url' => trim($import_values[1][$j]),
					'media' => trim($import_values[2][$j])
				);
			}
			$ordered_value = preg_replace($import_syntax, NULL, $ordered_value);
			if($imports){
				$ordered['@import'] = $imports;
			}
			// Split by parenthesis, ignoring those inside content-quotes
			$rules = preg_split('/([^\'"\{\}]*?[\'"].*?(?<!\\\)[\'"][^\'"\{\}]*?)[\{\}]|([^\'"\{\}]*?)[\{\}]/', trim($ordered_value," \r\n\t"), -1, PREG_SPLIT_NO_EMPTY|PREG_SPLIT_DELIM_CAPTURE);
			$rules_count = count($rules);
			// Group pairs of selectors and rules, split rules
			for($j = 0; $j < $rules_count; $j++){
				$selector = trim($rules[$j]);
				if(isset($rules[1+$j])){
					$rule = trim($rules[++$j]);
				}
				if($selector && $rule){
					$ordered[$ordered_key][] = array($selector => $this->split_rules($rule));
				}
			}
		}
		$this->parsed = $ordered;
		// Rebuild array?
		if($rebuild == true){
			$this->rebuild();
		}
	}


	/**
	 * mask_unsafe_chars
	 * @param string $str css with unmasked chars
	 * @return string css with masked chars
	 */
	public function mask_unsafe_chars($str){
		foreach($this->unsafe_chars as $mask => $unsafe_char){
			$masked_unsafe_char = str_replace(array('*', '/'), array('\*', '\/'), $unsafe_char);
			$patterns[] = '/content(.*:.*(\'|").*)('.$masked_unsafe_char.')(.*(\'|"))/';
			$replacements[] = 'content$1'.$mask.'$4';
		}
		return preg_replace($patterns, $replacements, $str);
	}


	/**
	 * split_rules
	 * Splits multiple rules into an array
	 * @param string $rules The CSS rules to split
	 * @return array Rules as an array
	 */
	public function split_rules($rules){
		$split = array();
		$rules = explode(';' ,$rules); // TODO: Breaks when ; is used inside strings
		foreach($rules as $rule){
			$rule = trim($rule, " \r\n\t");
			if(!empty($rule)){
				$rule = array_reverse(explode(':', $rule));
				$property = trim(array_pop($rule), " \r\n\t");
				$value = trim(implode(':', array_reverse($rule)));
				if(!isset($split[$property]) || !(strpos($split[$property], '!important') !== FALSE)){
					$split[$property] = $value;
				}
				elseif((strpos($split[$property], '!important') !== FALSE) && (strpos($value, '!important') !== FALSE)){
					$split[$property] = $value;
				}
			}
		}
		return $split;
	}


	/**
	 * rebuild
	 * Merges duplicate selectors and @media-blocks
	 * @param array $array The array to rebuild
	 * @return array The new, slim array
	 */
	public function rebuild(){
		if($this->parsed){
			$rebuilt = array();
			foreach($this->parsed as $block => $content){
				// Only rebuild normal css and @media blocks
				if($block === 'css' || ((strlen($block) > 5) && (substr_compare($block, '@media', 0, 6, FALSE) === 0))){
					// Reduce array to rules only, merge where needed
					foreach($content as $rule){
						if(isset($rebuilt[$block][key($rule)])){
							$rebuilt[$block][key($rule)] = $this->merge_rules($rebuilt[$block][key($rule)], current($rule));
						}
						else{
							$rebuilt[$block][key($rule)] = current($rule);
						}
					}
				}
				else{
					$rebuilt[$block] = $content;
				}
			}
			$this->parsed = $rebuilt;
		}
	}


	/***
	 * merge_rules
	 * Merges possible conflicting css rules
	 * @param mixed $old The OLD rules (overridden by the new rules)
	 * @param mixed $new The NEW rules (override the old rules)
	 * @return mixed $rule The new, merged rule
	 */
	public function merge_rules($old, $new){
		$rule = $old;
		foreach($new as $property => $value){
			if($rule[$property]){
				if(!strpos($rule[$property], ' !important')){  // TODO: This should be protected against the unlikly case that "!important" gets used inside strings
					$rule[$property] = $value;
				}
			}
			else{
				$rule[$property] = $value;
			}
		}
		return $rule;
	}


	/**
	 * glue
	 * Turn the current array back into CSS code
	 * @param bool $compressed Compress CSS? (removes whitespace)
	 * @return string $output The final CSS code
	 */
	public function glue($compressed = false){
		if($this->parsed){
			$output = NULL;
			// Whitspace characters
			$s = ' ';
			$n = "\n";
			$t = "\t";
			if($compressed){
				$s = $n = $t = NULL;
			}
			foreach($this->parsed as $block => $content){
				$prefix = NULL;
				// Is current block an @media-block?
				$media_at_rule = (bool) ((strlen($block) > 5) && (substr_compare($block, '@media', 0, 6, FALSE) === 0));
				// If @media-block, open block
				if($media_at_rule){
					$output .= $block.$s;
					$output .= '{'.$n;
					$prefix = $t;
				}
				// Read contents
				foreach($content as $index => $rule){
					// Is current block the @import-block?
					if($block === '@import'){
						$output .= '@import url('.$rule['url'].')';
						if(!empty($rule['media'])){
							$output .= ' '.$rule['media'];
						}
						$output .= ';'.$n;
					}
					// Print normal CSS
					else{
						// If the index is not a selector string, we are working with a not-rebuilt array
						if(is_int($index)){
							$output .= $this->glue_rule(key($rule), current($rule), $prefix, $s, $t, $n);
						}
						else{
							$output .= $this->glue_rule($index, $rule, $prefix, $s, $t, $n);
						}
					}
				}
				// If @media-block, close block
				if($media_at_rule){
					$output .= '}'.$n.$n;
				}
			}
			return strtr($output, $this->unsafe_chars);
		}
	}


	/**
	 * glue_rule
	 * Turn rules into css output
	 * @param string $selector Selector to use for this css rule
	 * @param mixed $rules Rule contents
	 * @param string $prefix Prefix 
	 * @param string $s Whitespace character
	 * @param string $t Whitespace character
	 * @param string $n Whitespace character
	 * @return string $output Formatted CSS
	 */
	public function glue_rule($selector, $rules, $prefix, $s, $t, $n){
		$output = $prefix . $selector . $s;
		$output .= '{' . $n;
		// Read properties and values
		foreach($rules as $property => $value){
			// If array, output properties in a loop
			if(is_array($value)){
				foreach($value as $val){
					$output .= $prefix.$t;
					$output .= $property.':'.$s;
					$output .= trim($val);
					$output .= ';'.$n;
				}
			}
			else{
				$output .= $prefix.$t;
				$output .= $property.':'.$s;
				$output .= trim($value);
				$output .= ';'.$n;
			}
		}
		$output .= $prefix.'}'.$n;
		return $output;
	}

}