<?php

/**
 * This file is part of Turbine
 * http://github.com/SirPepe/Turbine
 * 
 * Copyright Peter Kröner
 * Licensed under GNU LGPL 3, see license.txt or http://www.gnu.org/licenses/
 */


/**
 * Parser2
 * Turbine syntax parser
 */
class Parser2 extends Base{


	/**
	 * @var bool $debug Print $this->parsed?
	 */
	public $debug = false;


	/**
	 * @var array $debuginfo Collects parser debugging information
	 */
	public $debuginfo = array();


	/**
	 * @var array $code The loaded turbine code (before parsing)
	 */
	public $code = array();


	/**
	 * @var array $tokenized_properties The list properties where multiple values are to be combined on output using a space character
	 */
	public $tokenized_properties = array('filter');


	/**
	 * @var array $listed_properties The list properties where multiple values are to be combined on output using a comma
	 */
	public $listed_properties = array('plugins', 'behavior', '-ms-filter');


	/**
	 * @var array $quoted_properties The list properties where the value needs to be quoted as a whole before output
	 */
	public $quoted_properties = array('-ms-filter');


	/**
	 * @var array $last_properties The list properties that must be output AFTER all other plugins, in order of output
	 */
	public $last_properties = array('filter', '-ms-filter');


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
		'se' => '',
		'pr' => '',
		'va' => '',
		'me' => 'global',
		'fi' => -1,
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
						$this->parse_property_line($line);
						$this->reset_current_property();
						$debug['type'] = 'Property/Value';
						$debug['stack'] = $this->selector_stack;
					}
				}
			}
			// If the next line is outdented, slice the selector stack accordingly
			if($nextline != '' && $nextlevel < $level){
				$this->selector_stack = array_slice($this->selector_stack, 0, $nextlevel);
				$this->current['se'] = end($this->selector_stack);
			}
			// Debugging stuff
			$debug['media'] = $this->current['me'];
			$this->debuginfo[] = $debug;
			unset($debug);
		}
		// Dump $this->parsed when configured to do so
		if($this->debug){
			print_r($this->parsed);
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
			$nextline = (isset($lines[$i + 1])) ? $lines[$i + 1] : '';
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
			if($this->state != 'st' && $line{$i} == '/' && $line{$i+1} == '/'){     // Break on comment
				break;
			}
			$this->token .= $line{$i};
		}
		$media = trim(preg_replace('/[\s]+/', ' ', $this->token));                      // Trim whitespace from token
		$this->current['me'] = (trim(substr($media, 6)) != 'none') ? $media : 'global'; // Use token as current @media or reset to global
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
		// Increase font-face index if this is an @font-face element
		if($selector == '@font-face'){
			$this->current['fi']++;
		}
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
					$selector[] = $p.' '.$c;
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
		$this->parsed['global']['@import'][][0] = trim($this->token);
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
			'_value' => array(trim($line))
		);
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
	 * merge
	 * Merges the current values into $this->parsed
	 * @return void
	 */
	protected function merge(){
		// The current values
		$me = $this->current['me'];
		$se = $this->current['se'];
		$pr = $this->current['pr'];
		$va = $this->current['va'];
		$fi = $this->current['fi'];
		if($pr !== '' && $va !== ''){
			// Special destination for @font-face
			if($se == '@font-face'){
				$dest =& $this->parsed[$me][$se][$fi][$pr];
			}
			else{
				$dest =& $this->parsed[$me][$se][$pr];
			}
			// Set the value array if not aleady present
			if(!isset($dest)){
				$dest = array();
			}
			// Add the value to the destination
			$dest[] = $va;
		}
	}


	/**
	 * reset_current_property
	 * Empties the current value and property token
	 * @return void
	 */
	private function reset_current_property(){
		$this->current['pr'] = '';
		$this->current['va'] = '';
	}


	/**
	 * check_sanity
	 * Check sanity before output. Complain if somthing stupid comes along, but don't stop it - that's the web developer's job
	 * @return void
	 */
	private function check_sanity(){
		// Loop through the blocks
		foreach($this->parsed as $block => $content){
			// Loop through elements
			foreach($content as $selector => $rules){
				$this->check_sanity_filters($selector, $rules);
			}
		}
	}



	/**
	 * check_sanity_filters
	 * Check if something could potentially prevent IE's filters from working
	 * @param string $selector The selector that is being checked
	 * @param array $rules The rules to check
	 * @return void
	 */
	private function check_sanity_filters($selector, $rules){
		if(isset($rules['overflow']) && (isset($rules['filter']) || isset($rules['-ms-filter']))){
			if($this->get_final_value($rules['overflow'], 'overflow') == 'visible'){
				$this->report_error('Potential problem: Filters and overflow:visible are present at selector '
					. $selector . '. Filter may enforce unwanted overflow:hidden in Internet Explorer.');
			}
		}
	}



	/**
	 * glue
	 * Turn the current parsed array back into CSS code
	 * @param bool $compressed Compress CSS? (removes whitespace)
	 * @return string $output The final CSS code
	 */
	public function glue($compressed = false){
		$this->check_sanity();
		$output = '';
		// Whitspace characters
		$s = ' ';
		$t = "\t";
		$n = "\r\n";
		// Forget the whitespace if we're compressing
		if($compressed){
			$s = $t = $n = '';
		}
		// Loop through the blocks
		foreach($this->parsed as $block => $content){
			$indented = false;
			// Is current block an @media-block? If so, open the block
			$media_block = (substr($block, 0, 6) === '@media');
			if($media_block){
				$output .= $block . $s;
				$output .= '{' . $n;
				$indented = true;
			}
			// Read contents
			foreach($content as $selector => $rules){
				// @import rules
				if($selector == '@import'){
					$output .= $this->glue_import($rules, $compressed);
				}
				// @font-face rules
				elseif($selector == '@font-face'){
					$output .= $this->glue_font_face($rules, $compressed);
				}
				// @css line
				elseif(preg_match('/@css-[0-9]+/', $selector)){
					$output .= $this->glue_css($rules, $indented, $compressed);
				}
				// Normal css rules
				else{
					$output .= $this->glue_rule($selector, $rules, $indented, $compressed);
				}
			}
			// If @media-block, close block
			if($media_block){
				$output .= '}' . $n;
			}
		}
		return $output;
	}


	/**
	 * glue_import
	 * Turn parsed @import lines into output
	 * @param array $imports List of @import statements
	 * @param bool $compressed Compress CSS? (removes whitespace)
	 * @return string $output Formatted CSS
	 */
	private function glue_import($imports, $compressed){
		$output = '';
		$n = ($compressed) ? '' : "\r\n";
		foreach($imports as $import){
			$semicolon = (substr($import[0], -1) == ';') ? '' : ';';
			$output .= '@import ' . $import[0] . $semicolon . $n;
		}
		return $output;
	}


	/**
	 * glue_font_face
	 * Turn parsed @font-face elements into output
	 * @param array $imports List of @import statements
	 * @param bool $compressed Compress CSS? (removes whitespace)
	 * @return string $output Formatted CSS
	 */
	private function glue_font_face($fonts, $compressed){
		$output = '';
		// Whitspace characters
		$s = ' ';
		$n = "\r\n";
		// Forget the whitespace if we're compressing
		if($compressed){
			$s = $n = '';
		}
		// Build the @font-face rules
		foreach($fonts as $font => $styles){
			$output .= '@font-face'.$s.'{'.$n;
			$output .= $this->glue_properties($styles, '', $compressed);
			$output .= '}'.$n;
		}
		return $output;
	}


	/**
	 * glue_css
	 * Turn a parsed @css line into output
	 * @param mixed $contents @css line contents
	 * @param string $indented Indent the rule? (forn use inside @media blocks)
	 * @param bool $compressed Compress CSS? (removes whitespace)
	 * @return string $output Formatted CSS
	 */
	private function glue_css($contents, $indented, $compressed){
		$value = array_pop($contents['_value']);
		// Set the indention prefix
		$prefix = ($indented && !$compressed) ? "\t" : '';
		// Construct and return the result
		$output = $prefix.$value;
		return $output;
	}


	/**
	 * glue_rule
	 * Turn rules into css output
	 * @param string $selector Selector to use for this css rule
	 * @param mixed $rules Rule contents
	 * @param string $indented Indent the rule? (forn use inside @media blocks)
	 * @param bool $compressed Compress CSS? (removes whitespace)
	 * @return string $output Formatted CSS
	 */
	private function glue_rule($selector, $rules, $indented, $compressed){
		$output = '';
		// Whitspace characters
		$s = ' ';
		$t = "\t";
		$n = "\r\n";
		// Forget the whitespace if we're compressing
		if($compressed){
			$s = $t = $n = '';
		}
		// Set the indention prefix
		$prefix = ($indented && !$compressed) ? $t : '';
		// Strip whitespace from selectors when compressing
		if($compressed){
			$selector = implode(',', $this->tokenize($selector, ','));
		}
		// Constuct the selecor
		$output .= $prefix . $selector . $s;
		$output .= '{';
		// Add comments
		if(isset($rules['_comments']['selector']) && !$compressed){
			$output .= ' /* ' . implode(', ', $rules['_comments']['selector']) . ' */';
		}
		$output .= $n;
		// Add the properties
		$output .= $this->glue_properties($rules, $prefix, $compressed);
		$output .= $prefix.'}'.$n;
		return $output;
	}


	/**
	 * glue_properties
	 * Combine property sets
	 * @param mixed $rules Property-value-pairs
	 * @param string $prefix Prefix 
	 * @param bool $compressed Compress CSS? (removes whitespace)
	 * @return string $output Formatted CSS
	 */
	private function glue_properties($rules, $prefix, $compressed){
		$output = '';
		// Whitspace characters
		$s = ' ';
		$t = "\t";
		$n = "\r\n";
		// Forget the whitespace if we're compressing
		if($compressed){
			$s = $t = $n = '';
		}
		// Reorder for output
		foreach($this->last_properties as $property){
			if(isset($rules[$property])){
				$content = $rules[$property]; // Make a copy
				unset($rules[$property]);     // Remove the original
				$rules[$property] = $content; // Re-insert the property at the end
			}
		}
		// Keep count of the properties
		$num_properties = $this->count_properties($rules);
		$count_properties = 0;
		// Build output
		foreach($rules as $property => $values){
			// Ignore empty properties (might happen because of errors in plugins) and non-content-properties
			if(!empty($property) && $property{0} != '_'){
				$count_properties++;
				// Implode values
				$value = $this->get_final_value($values, $property, $compressed);
				// Output property line
				$output .= $prefix . $t . $property . ':' . $s . $value;
				// When compressing, omit the last semicolon
				if(!$compressed || $num_properties != $count_properties){
					$output .= ';';
				}
				// Add comments
				if(isset($rules['_comments'][$property]) && !$compressed){
					$output .= ' /* ' . implode(', ', $rules['_comments'][$property]) . ' */';
				}
				$output .= $n;
			}
		}
		return $output;
	}


	/**
	 * get_final_value
	 * Returns the last and/or most !important value from a list of values
	 * @param array $values A list of values
	 * @param string $property The property the values belong to
	 * @param bool $compressed Compress CSS? (removes whitespace)
	 * @return string $final The final value
	 */
	public function get_final_value($values, $property = NULL, $compressed = false){
		// Remove duplicates
		$values = array_unique($values);
		// If there's only one value, there's only one thing to return
		if(count($values) == 1){
			$final = array_pop($values);
			// Remove quotes in values on quoted properties (important for -ms-filter property)
			if(in_array($property, $this->quoted_properties)){
				$final = str_replace('"', "'", trim($final, '"'));
			}
		}
		// Otherwise find the last and/or most !important value
		else{
			// Whitspace characters
			$s = ' ';
			// Forget the whitespace if we're compressing
			if($compressed){
				$s = '';
			}
			// The final value
			$final = '';
			$num_values = count($values);
			for($i = 0; $i < $num_values; $i++){
				// Tokenized properties
				if(in_array($property, $this->tokenized_properties)){
					if($final != ''){
						$final .= ' ';
					}
					// Remove quotes in values on quoted properties (important for -ms-filter property)
					if(in_array($property, $this->quoted_properties)){
						$values[$i] = str_replace('"',"'",trim($values[$i],'"'));
					}
					$final .= $values[$i];
				}
				// Listed properties
				elseif(in_array($property, $this->listed_properties)){
					if($final != ''){
						$final .= ','.$s;
					}
					// Remove quotes in values on quoted properties (important for -ms-filter property)
					if(in_array($property, $this->quoted_properties)){
						$values[$i] = str_replace('"',"'",trim($values[$i],'"'));
					}
					$final .= $values[$i];
				}
				// Normal properties
				else{
					if(strpos($values[$i], '!important') || !strpos($final, '!important')){
						$final = $values[$i];
					}
				}
			}
		}
		// Add quotes to quoted properties
		if(in_array($property, $this->quoted_properties)){
			$final = '"' . $final . '"';
		}
		$final = trim($final);
		return $final;
	}


	/**
	 * count_properties
	 * Counts properties excluding hidden properties (prefixed with _)
	 * @param array $properties The rules containing the properties to count
	 * @return int $count
	 */
	private function count_properties($properties){
		$count = 0;
		foreach($properties as $property => $value){
			if($property{0} != '_'){
				$count++;
			}
		}
		return $count;
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
			if(isset($str{$i})){
				$current .= $str{$i};
			}
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
			$item['_comments'][$property] = array($comment);
		}
		else{
			$item['_comments'][$property][] = $comment;
		}
	}


	/**
	 * reset
	 * Resets the parser
	 * @return void
	 */
	public function reset(){
		$this->code = array();
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
			'fi' => -1,
			'ci' => 0
		);
		$this->options = array(
			'indention_char' => "	"
		);
	}

}


?>
