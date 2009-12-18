<?php
/**
 * CSS Parser
 * A tool for parsing and manipulating stylesheets.
 * 
 * Copyright (C) 2009 Peter Kröner
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
 * @todo Support @import
 */
class CssParser {


	/**
	 * @var bool $debug
	 */
	public $debug;


	/**
	 * @var string $css The loaded css code (before parsing)
	 */
	public $css;


	/**
	 * @var array $parsed The parsed css
	 */
	public $parsed = array('css' => array());


	/**
	 * @var array $state The parser state
	 * se = in selector
	 * pr = in property
	 * va = in value
	 * st = in string
	 * co = in comment
	 * at = in @-block
	 * im = in @import
	 * ff = in @font-face
	 */
	public $state = null;


	/**
	 * @var array $prev_state The previous parser state
	 * se = in selector
	 * pr = in property
	 * va = in value
	 * st = in string
	 * co = in comment
	 * at = in @media
	 * im = in @import
	 * ff = in @font-face
	 */
	public $prev_state = null;


	/**
	 * @var string $string_state The current string character (", ' or paranthesis)
	 */
	public $string_state = null;


	/**
	 * @var string $token The current token
	 */
	public $token;


	/**
	 * @var int $n The current nesting depth
	 */
	public $n = 0;


	/**
	 * @var array $current Holds the current values
	 * se = Selector
	 * pr = Property
	 * va = Value
	 * at = @-Rule
	 * fi = @font-face index
	 */
	public $current = array(
		'se' => null,
		'pr' => null,
		'va' => null,
		'at' => 'css',
		'fi' => null
	);


	/**
	 * Factory
	 * Creates and returns a new CSS Parser object
	 * @param string $code CSS code to load
	 * @param bool $debug Debug mode switch
	 * @return object $this The CSS Parser object
	 */
	public static function factory(){
		return new CssParser();
	}


	/**
	 * Constructor
	 */
	public function __construct(){
	}


	/**
	 * load_string
	 * Loads a css string
	 * @param string $string the css to load
	 * @param bool $overwrite overwrite already loaded css or append the new string?
	 * @return object $this The CSS Parser object
	 */
	public function load_string($string, $overwrite = false){
		if($overwrite){
			$this->css = $string;
		} else {
			$this->css .= $string;
		}
		return $this;
	}


	/**
	 * load_file
	 * Loads a file
	 * @param string $file The css files to load
	 * @param bool $overwrite overwrite already loaded css or append the new string?
	 * @return object $this The CSS Parser object
	 */
	public function load_file($file, $overwrite = false){
		$this->load_string(file_get_contents($file), $overwrite);
		return $this;
	}


	/**
	 * load_files
	 * Loads a number of files
	 * @param string $files File(s) to load, sepperated by ;
	 * @return object $this The CSS Parser object
	 */
	public function load_files($files){
		$files = explode(';', $files);
		foreach($files as $file){
			$this->load_file($file, false);
		}
		return $this;
	}


	/**
	 * parse
	 * Reads the loaded css into $this->parsed
	 * @return object $this The CSS Parser object
	 */
	public function parse(){
		$len = strlen($this->css);
		for($i = 0; $i < $len; $i++ ){


			// Begin comment state
			if($this->state != 'st' && $this->state != 'co'){
				if($this->css{$i} == '/' && $this->css{$i+1} == '*'){
					$this->prev_state = $this->state;
					$this->state = 'co';
				}
			}


			// Begin string state
			if($this->state != 'co' && $this->state != 'st'){
				$strings = array('"', "'", '(');
				if(in_array($this->css{$i}, $strings)){
					$this->prev_state = $this->state;
					$this->string_state = $this->css{$i};
					$this->state = 'st';
					$this->token .= $this->css{$i};
					$i++;
				}
			}


			switch($this->state){


				// End comment state
				case 'co';
					if($this->css{$i} == '/' && $this->css{$i-1} == '*'){
						$this->state = $this->prev_state;
					}
				break;


				// End string state
				case 'st';
					if($this->css{$i} == $this->string_state || ($this->css{$i} == ')' && $this->string_state == '(')){
						$this->state = $this->prev_state;
						$this->string_state = null;
					}
					$this->token .= $this->css{$i};
				break;


				// @import state
				case 'im';
					if($this->css{$i} == ';'){
						$this->state = null;
						$this->parsed[$this->current['at']]['@import'][] = trim($this->token);
						$this->token = '';
					}
					else {
						$this->token .= $this->css{$i};
					}
				break;


				// @font-face
				case 'ff';
					// End @font-face
					if($this->css{$i} == '}'){
						$this->parsed[$this->current['at']]['@font-face'][$this->current['fi']] = trim($this->token);
						$this->state = null;
						$this->current['fi'] = null;
					}
					// Begin property
					elseif($this->css{$i} == '{'){
						$this->state = 'pr';
						$this->token = '';
					}
				break;


				// At-rule state
				case 'at';
					// Begin selector, end at-rule
					if($this->css{$i} == '{'){
						$this->state = 'se';
						$this->current['at'] = $this->token;
						$this->token = '';
					}
					else {
						$this->token .= $this->css{$i};
					}
				break;


				// Selector state
				case 'se';
					// @import
					if($this->css{$i} == '@' && substr($this->css, $i, 7) == '@import'){
						$this->state = 'im';
						$this->current['se'] = '@import';
						$i = $i + 7;
					}
					// Leave at-rule
					elseif($this->css{$i} == '}'){
						if($this->current['at'] != 'css'){
							$this->state = null;
						}
					}
					// End selector, begin property
					elseif($this->css{$i} == '{'){
						$this->state = 'pr';
						$this->current['se'] = $this->token;
						$this->token = '';
					}
					else {
						$this->token .= $this->css{$i};
					}
				break;


				// Property state
				case 'pr';
					// Begin value
					if($this->css{$i} == ':'){
						$this->state = 'va';
						$this->current['pr'] = $this->token;
						$this->token = '';
					} else {
						// End property, return to selector or null state, add to parsed array
						if($this->css{$i} == '}'){
							if($this->current['at'] == 'css'){
								$this->state = null;
							}
							else {
								$this->state = 'se';
							}
							$this->merge();
						} else {
							$this->token .= $this->css{$i};
						}
					}
				break;


				// Value state
				case 'va';
					// End value, return to property, null or selector
					if($this->css{$i} == ';' || $this->css{$i} == '}'){
						// Semicolon: return to property state if not nested
						if($this->css{$i} == ';'){
							if($this->n == 0){
								$this->state = 'pr';
								$this->current['va'] = $this->token;
								$this->merge();
								$this->token = '';
							}
							else{
								$this->token .= $this->css{$i};
							}
						}
						// Closing curly brace: return to null state (if not in @-rule) or selector state if not nested
						elseif($this->css{$i} == '}') {
							if($this->n == 0){
								if($this->current['at'] == 'css'){
									$this->state = null;
								}
								else {
									$this->state = 'se';
								}
								$this->current['va'] = $this->token;
								$this->merge();
								$this->token = '';
							}
							else{
								$this->n--;
								$this->token .= $this->css{$i};
							}
						}
					}
					else{
						if($this->css{$i} == '{'){
							$this->n++;
						}
						$this->token .= $this->css{$i};
					}
				break;


				// No state
				case null:
				default;
					// Do nothing if whitespace
					if(!preg_match('[^\s]', $this->css{$i})){
						// Begin @-block
						if($this->css{$i} == '@'){
							// @media
							if(substr($this->css, $i, 6) == '@media'){
								$this->state = 'at';
								$this->current['at'] = '';
							}
							// @import
							elseif(substr($this->css, $i, 7) == '@import'){
								$this->state = 'im';
								$this->current['se'] = '@import';
								$i = $i + 7;
							}
							// @font-face
							elseif(substr($this->css, $i, 10) == '@font-face'){
								$this->state = 'ff';
								$this->current['se'] = '@font-face';
								$this->current['fi'] = count(@$this->parsed[$this->current['at']]['@font-face']);
								$i = $i + 10;
							}
						}
						// Begin selector
						else{
							$this->state = 'se';
							$this->current['at'] = 'css';
						}
						$this->token .= $this->css{$i};
					}
				break;


			}


			// Debug
			if($this->debug){
				echo ' | ';
				echo (preg_match('[^\s]', $this->css{$i})) ? '&nbsp;': $this->css{$i};
				echo ' | ';
				echo ($this->state) ? $this->state : 'nl';
				echo ' | ';
				echo trim($this->token);
				echo '<br>';
			}


		}


		return $this;


	}


	/**
	 * merge
	 * Merges the current values into $this->parsed
	 * @return void
	 */
	protected function merge(){
		// Trim the current and probably very messy values
		$at = trim($this->current['at']);
		$se = trim($this->current['se']);
		$pr = trim($this->current['pr']);
		$va = trim($this->current['va']);
		$fi = $this->current['fi'];
		// Special treatment for @font-face
		if($se == '@font-face'){
			$dest =& $this->parsed[$at][$se][$fi][$pr];
		}
		else{
			$dest =& $this->parsed[$at][$se][$pr];
		}
		// Take care of !important on merge
		// FIXME: This should NOT be done this way for obvious reasons
		if(!@stripos($this->parsed[$at][$se][$pr], '!important')){
			$dest = $va;
		}
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
					$output .= $this->glue_rule($index, $rule, $prefix, $s, $t, $n, $compressed);
				}
				// If @media-block, close block
				if($media_at_rule){
					$output .= '}'.$n.$n;
				}
			}
			return $output;
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
	public function glue_rule($selector, $rules, $prefix, $s, $t, $n, $compressed){
		$output = '';
		// Special treatment für @import - simply append to literal value
		if($selector == '@import'){
			foreach($rules as $value){
				$output .= $prefix . $selector . ' ' . $value.';' . $n;
			}
		}
		// Special treatment for @font-face - build from sub-arrays
		// TODO: This should really NOT be a special treatment for @font-face but rather the default. There
		// is no reason for this to be exclusive to @font-face and the copy-and-pase-code is stupid too
		// Fix this (lines 516-564) asap.
		elseif($selector == '@font-face'){
			foreach($rules as $values){
				if(!empty($values)){
					$output .= $prefix . $selector . $s .'{' . $n;
					foreach($values as $property => $value){
						if(is_array($value)){
							// If array, output multiple properties in a loop
							foreach($value as $val){
								$output .= $prefix.$t;
								$output .= $property.':'.$s;
								$output .= trim($val);
								$output .= ';'.$n; // TODO: Remove this for the last value when compressing
							}
						}
						else{
							$output .= $prefix.$t;
							$output .= $property.':'.$s;
							$output .= trim($value);
							$output .= ';'.$n; // TODO: Remove this for the last rule
						}
					}
					$output .= $n . '}' .$n;
				}
			}
		}
		else{
			$output .= $prefix . $selector . $s;
			$output .= '{' . $n;
			// Read properties and values
			foreach($rules as $property => $value){
				// If array, output multiple properties in a loop
				if(is_array($value)){
					$lastval = reset($value);
					foreach($value as $val){
						$output .= $prefix.$t;
						$output .= $property.':'.$s;
						$output .= trim($val);
						$output .= ';'.$n; // TODO: Remove this for the last value when compressing
					}
				}
				else{
					$output .= $prefix.$t;
					$output .= $property.':'.$s;
					$output .= trim($value);
					$output .= ';'.$n; // TODO: Remove this for the last value when compressing
				}
			}
			$output .= $prefix.'}'.$n;
		}
		return $output;
	}


	/**
	 * set_debug
	 * Sets the debug mode
	 * @return object $this The CSS Parser object
	 */
	public function set_debug($mode){
		$this->debug = (bool) $mode;
		return $this;
	}


	/**
	 * dump
	 * print_r()s $this->parsed
	 * @return object $this The CSS Parser object
	 */
	public function dump(){
		echo '<pre>';
		print_r($this->parsed);
		echo '</pre>';
		return $this;
	}


}

?>