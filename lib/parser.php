<?php


/**
 * CSSP - CSS Preprocessor
 * A new way to write CSS
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
 * Parser2
 * CSSP syntax parser
 */
class Parser2 {


	/**
	 * @var bool $debug
	 */
	public $debug = false;


	/**
	 * @var array $css The loaded css code (before parsing)
	 */
	public $css = array();


	/**
	 * @var array $parsed The parsed css
	 */
	public $parsed = array('global' =>
		array(
			'@import' => array(),
			'@font-face' => array()
		)
	);


	/**
	 * @var array $state The parser state
	 * @todo: Some of these are unused now
	 * se = in selector
	 * pr = in property
	 * va = in value
	 * st = in string
	 * co = in comment
	 * at = in @-block
	 * im = in @import
	 */
	public $state = null;


	/**
	 * @var array $prev_state The previous parser state
	 * @todo: Some of these are unused now
	 * se = in selector
	 * pr = in property
	 * va = in value
	 * st = in string
	 * co = in comment
	 * at = in @media
	 * im = in @import
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
	 * @var array $nesting The nesting stack
	 */
	public $nesting = array();


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
		'at' => 'global',
		'fi' => -1
	);


	/*
	 * @var array $options Parser options
	 */
	public $options = array(
		'indention_char' => "	"
	);


	/**
	 * Factory
	 * Creates and returns a new CSS Parser object
	 * @param string $code CSS code to load
	 * @param bool $debug Debug mode switch
	 * @return object $this The CSS Parser object
	 */
	public static function factory(){
		return new Parser2();
	}


	/**
	 * Constructor
	 */
	public function __construct(){
	}


	/**
	 * load_string
	 * Loads a css string line by line and appends it to the unparsed css code if $overwirite is false
	 * @param string $string the css to load
	 * @param bool $overwrite overwrite already loaded css or append the new string?
	 * @return object $this The CSS Parser object
	 */
	public function load_string($string, $overwrite = false){
		$lines = explode("\n", $string);
		if($overwrite){
			$this->css = $lines;
		}
		else{
			$this->css = array_merge($this->css, $lines);
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
	 * Reads the loaded css line by line into $this->parsed
	 * @return object $this The CSS Parser object
	 */
	public function parse(){

		/* DEBUG */  echo ($this->debug) ? '<table border="1" cellpadding="4"><tr><th>Zeile</th><th>@-Block</th><th>Selektor</th><th>Eigenschaft / Wert</th></tr>' : '';

		$linecount = count($this->css);
		for($i = 0; $i < $linecount; $i++){
			$this->token = '';
			$line = $this->css[$i];
			
			// Setup next line. Must always be present, must not contain whitespace when empty
			if(isset($this->css[1+$i])){
				if(trim($this->css[1+$i]) == ''){
					$this->css[1+$i] = '';
				}
				$nextline = $this->css[1+$i];
			}
			else{
				$nextline = '';
			}

			// Ignore comment lines
			if(substr(trim($line), 0, 2) != '//'){

				if(substr(trim($line), 0, 6) == '@media'){ // Parse @media switch
					$this->parse_media_line($line);
				}
				elseif(substr(trim($line), 0, 7) == '@import'){
					$this->parse_import_line($line);
				}
				else{
					$level = $this->get_indention_level($line); // TODO: Level should be always 0 when there's no line before this line
					if($this->get_indention_level($nextline) == $level + 1){ // Parse as selector when the next line is further indented
						$this->parse_selector_line($line, $level);
					}
					// ...or else as property/value pair
					else{
						$this->parse_property_line($line);
					}
				}

				/* DEBUG */  echo ($this->debug) ? '<tr>' : '';
				/* DEBUG */  echo ($this->debug) ? '<td><pre>'.$line.'</pre></td>' : '';
				/* DEBUG */  echo ($this->debug) ? '<td><pre>'.$this->current['at'].'</pre></td>' : '';
				/* DEBUG */  echo ($this->debug) ? '<td><pre>'.$this->current['se'].'</pre></td>' : '';
				/* DEBUG */  echo ($this->debug) ? '<td><pre>'.$this->current['pr'].' / '.$this->current['va'].'</pre></td>' : '';
				/* DEBUG */  echo ($this->debug) ? '</tr>' : '';

			}
		}

		/* DEBUG */  echo ($this->debug) ? '</table>' : '';

		return $this;
	}


	/**
	 * parse_media_line
	 * 
	 * @param string $line A line containing an @media switch
	 * @return void
	 */
	protected function parse_media_line($line){
		$this->current['at'] = '';
		$this->current['se'] = '';
		$this->current['pr'] = '';
		$this->current['va'] = '';
		$line = trim(substr($line, 6));
		$len = strlen($line);
		for($i = 0; $i < $len; $i++ ){
			$this->switch_string_state($line{$i});
			if($this->state != 'st' && $line{$i} == '/' && $line{$i+1} == '/'){ // Break on comment
				break;
			}
			$this->token .= $line{$i};
		}
		$this->current['at'] = trim(preg_replace('/[\s]+/', ' ', $this->token)); // Trim whitespace, use as current @media
	}


	/**
	 * parse_import_line
	 * 
	 * @param string $line A line containing @import
	 * @return void
	 */
	protected function parse_import_line($line){
		$line = trim(substr($line, 7));
		$len = strlen($line);
		for($i = 0; $i < $len; $i++ ){
			$this->switch_string_state($line{$i});
			if($this->state != 'st' && $line{$i} == '/' && $line{$i+1} == '/'){ // Break on comment
				break;
			}
			$this->token .= $line{$i};
		}
		$this->parsed['global']['@import'][] = $this->token;
	}


	/**
	 * parse_selector_line
	 * 
	 * @param string $line A line containing a selector
	 * @param
	 * @return void
	 */
	protected function parse_selector_line($line, $level){
		$line = trim($line);
		$len = strlen($line);
		for($i = 0; $i < $len; $i++ ){
			$this->switch_string_state($line{$i});
			if($this->state != 'st' && $line{$i} == '/' && $line{$i+1} == '/'){ // Break on comment
				break;
			}
			$this->token .= $line{$i};
		}
		// Trim whitespace
		$selector = trim(preg_replace('/[\s]+/', ' ', $this->token));
		// Combine selector with the nesting stack
		if($level > 0){
			//$selector = $this->nesting[$level-1].' '.$selector; // TODO Comma combinations
			$selector = $this->merge_selectors($this->nesting[$level-1], $selector);
		}
		// Increase font-face index
		if($selector == '@font-face'){
			$this->current['fi']++;
		}
		$this->current['se'] = $selector; // Use as current selector
		$this->nesting[(int)$level] = $selector; // Add to the nesting stack
	}


	/**
	 * parse_property_line
	 * 
	 * @param string $line A line containing one (or more) property-value-pairs
	 * @return void
	 */
	protected function parse_property_line($line){
		$line = trim($line);
		$len = strlen($line);
		$this->state = 'pr';
		for($i = 0; $i < $len; $i++ ){
			$this->switch_string_state($line{$i});
			if($this->state != 'st' && $line{$i} == '/' && $line{$i+1} == '/'){ // Break on comment
				if(trim($this->token) != ''){
					$this->current['va'] = trim($this->token);
					$this->token = '';
					$this->merge();
				}
				break;
			}
			elseif($this->state != 'st' && $line{$i} == ':'){ // End property state on colon
				$this->current['pr'] = trim($this->token);
				$this->state = 'va';
				$this->token = '';
			}
			elseif($i + 1 == $len || ($this->state != 'st' && $line{$i} == ';')){ // End pair on line end or semicolon
				if($i + 1 == $len && $line{$i} != ';'){
					$this->token .= $line{$i};
				}
				$this->current['va'] = trim($this->token);
				$this->token = '';
				$this->merge();
			}
			else{
				$this->token .= $line{$i};
			}
		}
	}


	/**
	 * 
	 */
	protected function merge_selectors($parent, $child){
		// FIXME This is obviously just a quick hack and should be replaced by real parsing
		$parent = explode(',', $parent);
		$parent = array_map('trim', $parent);
		$child = explode(',', $child);
		$child = array_map('trim', $child);
		// Merge the split selectors
		$selectors = array();
		foreach($parent as $p){
			$selector = array();
			foreach($child as $c){
				$selector[] = $p.' '.$c;
			}
			$selectors[] = $selector;
		}
		$num = count($selectors);
		for($i = 0; $i < $num; $i++){
			$selectors[$i] = implode(', ', $selectors[$i]);
		}
		return implode(', ', $selectors);
	}


	/**
	 * switch_string_state
	 * Manages the string state
	 * 
	 * @param string $char A single char
	 * @return void
	 */
	protected function switch_string_state($char){
		$strings = array('"', "'", '(');
		if($this->state != 'st'){ // Enter string state
			if(in_array($char, $strings)){
				$this->prev_state = $this->state;
				$this->string_state = $char;
				$this->state = 'st';
			}
		}
		else{ // Leave string state
			if($char == $this->string_state || ($char == ')' && $this->string_state == '(')){
				$this->string_state = null;
				$this->state = $this->prev_state;
			}
		}
	}


	/**
	 * get_indention_level
	 * 
	 * @param string $line The line to get the indention level for
	 * @return int $level The indention level
	 */
	protected function get_indention_level($line){
		$level = 0;
		if(substr($line, 0, strlen($this->options['indention_char'])) == $this->options['indention_char']){
			$level = 1 + $this->get_indention_level(substr($line, strlen($this->options['indention_char'])));
		}
		return $level;
	}


	/**
	 * merge
	 * Merges the current values into $this->parsed
	 * @return void
	 */
	protected function merge(){
		// The current values
		$at = ($this->current['at'] !== 'global') ? '@media '.$this->current['at']: $this->current['at'];
		$se = $this->current['se'];
		$pr = $this->current['pr'];
		$va = $this->current['va'];
		$fi = $this->current['fi'];
		// Special treatment for @font-face
		if($se == '@font-face'){
			$dest =& $this->parsed[$at][$se][$fi][$pr];
		}
		else{
			$dest =& $this->parsed[$at][$se][$pr];
		}
		// Take care of !important on merge
		$tokens = array();
		if(isset($this->parsed[$at][$se][$pr])){
			$tokens = preg_split('/[\s]/', $this->parsed[$at][$se][$pr]);
		}
		if(!in_array('!important', $tokens)){
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
	 * @param bool $compressed Compress CSS? (removes whitespace)
	 * @return string $output Formatted CSS
	 */
	public function glue_rule($selector, $rules, $prefix, $s, $t, $n, $compressed){
		$output = '';
		if($selector == '@import' || $selector == '@font-face'){ // Special cases
			foreach($rules as $rule){
				if($selector == '@import'){ // @import rule
					$output .= $prefix . $selector . ' ' . $rule.';' . $n;
				}
				elseif($selector == '@font-face'){ // @font-face rule
					$output .= $prefix . $selector . $s .'{' . $n;
					$output .= $this->glue_properties($rule, $prefix, $s, $t, $n, $compressed);
					$output .= '}' .$n;
				}
			}
		}
		else{ // normal elements
			$output .= $prefix . $selector . $s;
			$output .= '{' . $n;
			$output .= $this->glue_properties($rules, $prefix, $s, $t, $n, $compressed);
			$output .= $prefix.'}'.$n;
		}
		return $output;
	}


	/**
	 * glue_properties
	 * Combine property sets
	 * @param mixed $values Value contents
	 * @param string $prefix Prefix 
	 * @param string $s Whitespace character
	 * @param string $t Whitespace character
	 * @param string $n Whitespace character
	 * @param bool $compressed Compress CSS? (removes whitespace)
	 * @return string $output Formatted CSS
	 */
	public function glue_properties($values, $prefix, $s, $t, $n, $compressed){
		$output = '';
		$i = 0;
		$values_num = count($values);
		// Process rules always as arrays, output multiple properties in a loop
		if(!is_array($values)){
			$values = array($values);
		}
		else{
			$valcount = count($values);
			$values_num = $values_num + $valcount - 1; // Increases $rule_num for multi-value-arrays
		}
		foreach($values as $property => $val){
			$i++;
			if(!is_array($val)){
				$val = array($val);
			}
			else{
				$valcount = count($values);
				$values_num = $values_num + $valcount - 1; // Increases $rule_num for multi-value-arrays
			}
			foreach($val as $value){
				$output .= $prefix.$t;
				$output .= $property.':'.$s;
				$output .= trim($value);
				if(!$compressed || $i != $values_num){ // Remove semicolon this for the last rule
					$output .= ';';
				}
				$output .= $n;
			}
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