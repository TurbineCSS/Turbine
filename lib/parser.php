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
 * Parser2
 * Turbine syntax parser
 */
class Parser2 extends Base{


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
	public $token = '';


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
	 * @return object $this The CSS Parser object
	 */
	public static function factory(){
		return new Parser2();
	}


	/**
	 * Constructor
	 */
	public function __construct(){
		parent::__construct();
	}


	/**
	 * load_string
	 * Loads a css string line by line and appends it to the unparsed css code if $overwrite is false
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
		$this->preprocess();
		$linecount = count($this->css);

		// Loop through the lines
		for($i = 0; $i < $linecount; $i++){
			$line = $newline = null;
			$this->token = '';

			// Get current line
			$line = $this->css[$i];

			// Get next line. Must always be present, must not contain whitespace when empty, must not be a comment
			if(isset($this->css[1+$i])){
				if(trim($this->css[1+$i]) == ''){
					$this->css[1+$i] = '';
				}
				$nextline = $this->css[1+$i];
			}
			else{
				$nextline = '';
			}

			// Parse @media switch
			if(substr(trim($line), 0, 6) == '@media'){
				$this->parse_media_line($line);
			}

			// Parse @import rule
			elseif(substr(trim($line), 0, 7) == '@import'){
				$this->parse_import_line($line);
			}

			// Parse normal style line
			else{

				// Ignore comment lines
				if(!preg_match('~^[\s]*//.*?$~', $line)){
					// Get the current and next indention level
					if(isset($this->css[$i-1]) && trim($this->css[$i-1]) == ''){
						$level = 0;
						$this->nesting = array();
					}
					else{
						$level = $this->get_indention_level($line);
					}
					$nextlevel = $this->get_indention_level($nextline);
	
					// Parse as selector when the next line is indented
					if($nextlevel > $level){
						$this->parse_selector_line($line, $level);
					}
	
					// Else parse as property/value pair
					else{
						$this->parse_property_line($line);
						if($nextlevel < $level){
							if(isset($this->nesting[count($this->nesting) - $nextlevel])){
								$this->current['se'] = $this->nesting[count($this->nesting) - $nextlevel];
							}
						}
					}

				}

			}

		}
		return $this;
	}


	/**
	 * set_indention_char
	 * Sets the indention char
	 * @param string $char The whitespace char(s) used for indention
	 * @param unknown_type $char
	 */
	public function set_indention_char($char = null){
		if(!$char){
			$char = Parser2::get_indention_char($this->css);
		}
		$this->options['indention_char'] = $char;
	}


	/**
	 * get_indention_char
	 * Find out which whitespace char(s) are used for indention
	 * @param array $lines The code in question
	 * @return void
	 */
	public static function get_indention_char($lines){
		$linecount = count($lines);
		for($i = 0; $i < $linecount; $i++){ // For each line...
			$line = $lines[$i];
			$nextline = $lines[1+$i];
			if(trim($line) != '' && trim($nextline) != ''){ // ...if the line and the following line are not empty..
				preg_match('/^([\s]+).*?$/', $nextline, $matches); // ...find the whitespace used for indention
				if(count($matches) == 2 && strlen($matches[1]) > 0){
					return $matches[1];
				}
			}
		}
	}


	/**
	 * preprocess
	 * Strip comment lines
	 * @return void
	 */
	protected function preprocess(){
		$processed = array();
		$linecount = count($this->css);
		for($i = 0; $i < $linecount; $i++){
			if(!preg_match('~^[\s]*//.*?$~', $this->css[$i])){
				$processed[] = $this->css[$i];
			}
		}
		$this->css = $processed;
	}


	/**
	 * get_indention_level
	 * Returns the indention level for a line
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
	 * parse_media_line
	 * Parses an @media line
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
	 * Parses an @import line
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
	 * Parses a selector line
	 * @param string $line A line containing a selector
	 * @param int $level The lines indention level
	 * @return void
	 */
	protected function parse_selector_line($line, $level){
		$line = trim($line);
		$len = strlen($line);
		if($len > 0){
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
				$selector = $this->merge_selectors($this->array_get_previous($this->nesting, $level), $selector);
			}
			// Increase font-face index
			if($selector == '@font-face'){
				$this->current['fi']++;
			}
			$this->current['se'] = $selector; // Use as current selector
			$this->nesting[$level] = $selector; // Add to the nesting stack
		}
	}


	/**
	 * parse_property_line
	 * Parses a property/value line
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
			elseif($this->state == 'pr' && $line{$i} == ':'){ // End property state on colon
				$this->current['pr'] = trim($this->token);
				$this->state = 'va';
				$this->token = '';
			}
			elseif($i + 1 == $len || ($this->state != 'st' && $line{$i} == ';')){ // End pair on line end or semicolon
				if($i + 1 == $len && $line{$i} != ';'){
					$this->token .= $line{$i};
				}
				$this->current['va'] = trim($this->token);
				$this->state = 'pr';
				$this->token = '';
				$this->merge();
			}
			else{
				$this->token .= $line{$i};
			}
		}
	}


	/**
	 * merge_selectors
	 * Merges two selectors
	 * @param array $parent
	 * @param array $child
	 * @return array $selectors
	 */
	protected function merge_selectors($parent, $child){
		$parent = $this->tokenize($parent, ',');
		$child = $this->tokenize($child, ',');
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
		$tokens = $this->tokenize($dest);
		if(!in_array('!important', $tokens)){
			$dest = $va;
		}
	}


	/**
	 * comment
	 * Adds a comment
	 * @param array &$item
	 * @param mixed $property
	 * @param string $comment
	 * @return void
	 */
	public static function comment(&$item, $property = null, $comment){
		if(!$property){
			$property = 'selector';
		}
		if(!isset($item['_comments'][$property])){
			$item['_comments'][$property] = $comment;
		}
		else{
			$item['_comments'][$property] .= ' | '.$comment;
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
			$t = "\t";
			$n = "\r\n";
			if($compressed){
				$s = $t = $n = NULL;
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
					$comments = array();
					if(isset($rule['_comments'])){
						$comments = $rule['_comments'];
					}
					$output .= $prefix . $selector . $s .'{' . $n;
					$output .= $this->glue_properties($rule, $prefix, $s, $t, $n, $compressed, $comments);
					$output .= '}' .$n;
				}
			}
		}
		else{ // Normal elements
			// Strip whitespace from selectors
			if($compressed){
				$selector = implode(',', $this->tokenize($selector, ','));
			}
			// Get comments
			$comments = array();
			if(isset($rules['_comments'])){
				$comments = $rules['_comments'];
			}
			$output .= $prefix . $selector . $s;
			$output .= '{' . $n;
			$output .= $this->glue_properties($rules, $prefix, $s, $t, $n, $compressed, $comments);
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
	 * 
	 * @return string $output Formatted CSS
	 */
	public function glue_properties($values, $prefix, $s, $t, $n, $compressed, $comments){
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
			if($property != '_comments'){ // Ignore comments
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
					// Don't add semicolon this for the last rule
					if(!$compressed || $i != $values_num){
						$output .= ';';
					}
					// Add comments
					if(!$compressed){
						if(isset($comments[$property])){
							$output .= ' /* ';
							$output .= $comments[$property];
							$output .=' */';
						}
					}
					$output .= $n;
				}
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
	 * tokenize
	 * Tokenizes $str, respecting css string delimeters
	 * @param string $str
	 * @param mixed $separator
	 * @return array $tokens
	 */
	public function tokenize($str, $separator = array(' ', '	')){
		$tokens = array();
		$current = '';
		$string_delimeters = array('"', "'", '(');
		$current_string_delimeter = null;
		if(!is_array($separator)){
			$separator = array($separator);
		}
		$strlen = strlen($str);
		for($i = 0; $i < $strlen; $i++){
			if($current_string_delimeter === null){
				// End current token
				if(in_array($str{$i}, $separator)){
					$token = trim($current);
					if(strlen($token) > 0 && !in_array($token, $separator)){
						$tokens[] = $token;
					}
					$current = '';
					$i++;
				}
				// Begin string state
				elseif(in_array($str{$i}, $string_delimeters)){
					$current_string_delimeter = $str{$i};
				}
			}
			else{
				// End string state
				if($str{$i} === $current_string_delimeter || ($current_string_delimeter == '(' && $str{$i} === ')')){
					$current_string_delimeter = null;
				}
			}
			// Add to the current token
			$current .= $str{$i};
			// Handle the last token
			if($i == $strlen - 1){
				$lasttoken = trim($current);
				if($lasttoken){
					$tokens[] = $lasttoken;
				}
			}
		}
		return $tokens;
	}


	/**
	 * reset
	 * Resets the parser
	 * @return void
	 */
	public function reset(){
		$this->css = array();
		$this->parsed = array('global' =>
			array(
				'@import' => array(),
				'@font-face' => array()
			)
		);
		$this->state = null;
		$this->prev_state = null;
		$this->string_state = null;
		$this->token = '';
		$this->nesting = array();
		$this->current = array(
			'se' => null,
			'pr' => null,
			'va' => null,
			'at' => 'global',
			'fi' => -1
		);
		$this->options = array(
			'indention_char' => "	"
		);
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