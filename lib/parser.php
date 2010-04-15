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
 * @todo document schepp-style selectors
 * @todo document inline css (@css)
 * @todo document block commenting
 * @todo document the fact that the nesting system is smart about pseudo classes
 */
class Parser2 extends Base{


	/**
	 * @var array $code The loaded turbine code (before parsing)
	 */
	public $code = array();


	/**
	 * @var array $debuginfo Parser debugging information
	 */
	public $debuginfo = array();


	/**
	 * @var string $indention_char The Whitespace character(s) used for indention
	 */
	public $indention_char = false;


	/**
	 * @var array $state The parser state
	 * st = in string
	 */
	public $state = null;


	/**
	 * @var array $prev_state The previous parser state
	 * st = in string
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
	 * @var array $current Holds the current values
	 * se = Selector
	 * pr = Property
	 * va = Value
	 * me = @media block
	 * fi = @font-face index
	 * ci = @css line index
	 */
	public $current = array(
		'se' => null,
		'pr' => null,
		'va' => null,
		'me' => 'global',
		'fi' => 0,
		'ci' => 0
	);


	/**
	 * @var array $selector_stack The selectors the current line is nested in
	 */
	private $selector_stack = array();


	/**
	 * @var array $parsed The parsed data structure
	 */
	public $parsed = array('global' =>
		array(
			'@import' => array(),
			'@font-face' => array()
		)
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
			$this->code = $lines;
		}
		else{
			$this->code = array_merge($this->code, $lines);
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
		// Preprocess the code and get the indention char(s)
		$this->preprocess();
		$this->set_indention_char();
		// Loop through the lines
		$loc = count($this->code);
		for($i = 0; $i < $loc; $i++){
			$debug = array();
			$this->token = '';
			$line = $this->code[$i];
			$level = $this->get_indention_level($line); // Get this line's indention level
			$debug['line'] = $line;
			if(isset($this->code[$i + 1])){
				$nextline = $this->code[$i + 1];
				$nextlevel = $this->get_indention_level($nextline); // Get the next line's indention level
			}
			// If the current line is empty, ignore it and reset the selector stack
			if($line == ''){
				$this->selector_stack = array();
				$debug['type'] = 'Reset';
				$debug['stack'] = 'Reset';
			}
			// Else parse the line
			else{
				// Line begins with "@media" = parse this as a @media-line
				if(substr(trim($line), 0, 6) == '@media'){
					$this->parse_media_line($line);
					$this->selector_stack = array();
					$debug['type'] = 'Media';
					$debug['stack'] = 'Reset';
				}
				// Line begins with "@import" = Parse @import rule
				elseif(substr(trim($line), 0, 7) == '@import'){
					$this->parse_import_line($line);
					$this->selector_stack = array();
					$debug['type'] = 'Import';
					$debug['stack'] = 'Reset';
				}
				// Line begins with "@css" = Parse as literal css
				elseif(substr(trim($line), 0, 4) == '@css'){
					$this->parse_css_line($line);
					$this->selector_stack = array();
					$debug['type'] = 'CSS';
					$debug['stack'] = 'Reset';
				}
				// Else parse normal line
				else{
					// Next line is indented = parse this as a selector
					if($nextline != '' && $nextlevel > $level){
						$this->parse_selector_line($line, $level);
						$debug['type'] = 'Selector';
						$debug['stack'] = $this->selector_stack;
					}
					// Else parse as a property-value-pair
					else{
						$debug['type'] = 'Rule';
						$debug['stack'] = $this->selector_stack;
					}
				}
			}
			// If the next line is outdented, slice the selector stack accordingly
			if($nextline != '' && $nextlevel < $level){
				$this->selector_stack = array_slice($this->selector_stack, 0, $nextlevel);
			}
			// Debugging stuff
			$debug['media'] = $this->current['me'];
			$this->debuginfo[] = $debug;
			unset($debug);
		}
		return $this;
	}


	/**
	 * set_indention_char
	 * Sets the indention char
	 * @param string $char The whitespace char(s) used for indention
	 * @return void
	 */
	public function set_indention_char($char = null){
		if(!$char){
			$char = Parser2::get_indention_char($this->code);
		}
		$this->indention_char = $char;
	}


	/**
	 * get_indention_char
	 * Find out which whitespace char(s) are used for indention
	 * @param array $lines The code in question
	 * @return string $matches[1] The whitespace char(s) used for indention
	 */
	public static function get_indention_char($lines){
		$linecount = count($lines);
		for($i = 0; $i < $linecount; $i++){
			$line = $lines[$i];
			$nextline = $lines[$i + 1];
			// If the line and the following line are not empty and not @rules, find the whitespace used for indention
			if($line != '' && trim($nextline) != '' && preg_match('/^([\s]+)(.*?)$/', $nextline, $matches)){
				if(count($matches) == 3 && strlen($matches[1]) > 0 && $matches[2]{0} != '@'){
					return $matches[1];
				}
			}
		}
	}


	/**
	 * preprocess
	 * Clean up the code
	 * @return void
	 */
	protected function preprocess(){
		$this->preprocess_clean();
		$this->preprocess_concatenate_selectors();
	}


	/**
	 * preprocess_clean
	 * Strip comment lines and whitespace
	 * @return void
	 */
	private function preprocess_clean(){
		$processed = array();   // The remaining, cleaned up lines
		$comment_state = false; // The block comment state
		$previous_line = '';    // The line before the line being processed
		$loc = count($this->code);
		for($i = 0; $i < $loc; $i++){
			// Handle block comment lines
			if(trim($this->code[$i]) == '--'){
				$comment_state = ($comment_state) ? false : true;
			}
			// Handle normal lines
			elseif(!$comment_state){
				// Ignore lines containing nothing but a comment
				if(!preg_match('~^[\s]*//.*?$~', $this->code[$i])){
					// Lines containing non-whitespace
					if(preg_match('[\S]', $this->code[$i])){
						$processed[] = $this->code[$i];
						$previous_line = $this->code[$i];
					}
					// Lines with nothing but whitespace
					else{
						// Only add this line if the previous one had any non-whitespace
						if($previous_line != ''){
							$processed[] = '';
							$previous_line = '';
						}
					}
				}
			}
		}
		$this->code = $processed;
	}


	/**
	 * preprocess_concatenate_selectors
	 * Concatenates multiline selectors
	 * @return void
	 */
	private function preprocess_concatenate_selectors(){
		$processed = array();
		$loc = count($this->code);
		for($i = 0; $i < $loc; $i++){
			$line = $this->code[$i];
			if($line != ''){
				while(substr($line, -1) == ','){
					$line .= ' '.$this->code[++$i];
				}
			}
			$processed[] = $line;
		}
		$this->code = $processed;
	}


	/**
	 * get_indention_level
	 * Returns the indention level for a line
	 * @param string $line The line to get the indention level for
	 * @return int $level The indention level
	 */
	public function get_indention_level($line){
		$level = 0;
		if(substr($line, 0, strlen($this->indention_char)) == $this->indention_char){
			$level = 1 + $this->get_indention_level(substr($line, strlen($this->indention_char)));
		}
		return $level;
	}


	/**
	 * switch_string_state
	 * Manages the string state
	 * @param string $char A single char
	 * @return void
	 */
	protected function switch_string_state($char){
		$strings = array('"', "'", '(');
		if($this->state != 'st'){
			if(in_array($char, $strings)){ // Enter string state
				$this->prev_state = $this->state;
				$this->string_state = $char;
				$this->state = 'st';
			}
		}
		else{
			if($char == $this->string_state || ($char == ')' && $this->string_state == '(')){ // Leave string state
				$this->string_state = null;
				$this->state = $this->prev_state;
			}
		}
	}


	 /**
	 * parse_media_line
	 * Parses an @media line
	 * @param string $line A line containing an @media switch
	 * @return void
	 */
	protected function parse_media_line($line){
		$this->current['me'] = '';
		$this->current['se'] = '';
		$this->current['pr'] = '';
		$this->current['va'] = '';
		$line = trim($line);
		$len = strlen($line);
		for($i = 0; $i < $len; $i++ ){
			$this->switch_string_state($line{$i});
			if($this->state != 'st' && $line{$i} == '/' && $line{$i+1} == '/'){ // Break on comment
				break;
			}
			$this->token .= $line{$i};
		}
		$this->current['me'] = trim(preg_replace('/[\s]+/', ' ', $this->token)); // Trim whitespace from token, use it as current @media
	}


	 /**
	 * parse_selector_line
	 * Parses a selector line
	 * @param string $line A line containing a selector
	 * @param int $level The lines' indention level
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
		$selector = $this->merge_selectors($this->array_get_previous($this->selector_stack, $level), $selector);
		// Use as current selector
		$this->current['se'] = $selector;
		// Add to the selector stack
		$this->selector_stack[$level] = $selector;
	}



	/**
	 * merge_selectors
	 * Merges two selectors
	 * @param array $parent
	 * @param array $child
	 * @return array $selectors
	 */
	protected function merge_selectors($parent, $child){
		// If the parent is empty, don't do anything to the child
		if(empty($parent)){
			return $child;
		}
		// Else combine the selectors
		else{
			$parent = $this->tokenize($parent, ',');
			$child = $this->tokenize($child, ',');
			// Merge the tokenized selectors
			$selectors = array();
			foreach($parent as $p){
				$selector = array();
				foreach($child as $c){
					$between = ($c{0} == ':') ? '' : ' '; // Space or no space (if the child is a pseudo class)
					$selector[] = $p.$between.$c;
				}
				$selectors[] = $selector;
			}
			return $this->implode_selectors($selectors);
		}
	}


	/**
	 * implode_selectors
	 * Recursivly combines selectors
	 * @param array $selectors A list of selectors
	 * @return string The combined selector
	 */
	protected function implode_selectors($selectors){
		$num = count($selectors);
		for($i = 0; $i < $num; $i++){
			if(is_array($selectors[$i])){
				$selectors[$i] = $this->implode_selectors($selectors[$i]);
			}
		}
		return implode(', ', $selectors);
	}



	/**
	 * parse_import_line
	 * Parses an @import line
	 * @param string $line A line containing @import
	 * @return void
	 */
	protected function parse_import_line($line){
		$line = trim($line);
		$line = substr($line, 7); // Strip "@import"
		$len = strlen($line);
		for($i = 0; $i < $len; $i++){
			$this->switch_string_state($line{$i});
			if($this->state != 'st' && $line{$i} == '/' && $line{$i+1} == '/'){ // Break on comment
				break;
			}
			$this->token .= $line{$i};
		}
		$this->parsed['global']['@import'][] = $this->token;
	}


	/**
	 * Parse a line of literal css
	 * @param string $line
	 * @return void
	 */
	protected function parse_css_line($line){
		$line = trim($line);
		$line = substr($line, 4);                   // Strip "@css"
		$selector = '@css-'.$this->current['ci']++; // Build the selector using the @css-Index
		$this->parsed[$this->current['me']][$selector] = array(
			'_value' => $line
		);
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
		$this->selector_stack = array();
		$this->current = array(
			'se' => null,
			'pr' => null,
			'va' => null,
			'me' => 'global',
			'fi' => 0,
			'ci' => 0
		);
		$this->options = array(
			'indention_char' => "	"
		);
	}

}


?>