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
	 * @var string $token The current token
	 */
	private $token = '';


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
			$line = $this->code[$i];
			$debug['line'] = $line;
			if(isset($this->code[$i + 1])){
				$nextline = $this->code[$i + 1];
			}
			// If the current line is empty, ignore it and reset the selector stack
			if($line == ''){
				$this->selector_stack = array();
				$debug['type'] = 'RE';
				$debug['stack'] = 'Reset';
			}
			// Else parse the line
			else{
				// Line begins with "@media" = parse this as a @media-line
				if(substr(trim($line), 0, 6) == '@media'){
					$this->selector_stack = array();
					$debug['type'] = 'ME';
					$debug['stack'] = 'Reset';
				}
				// Line begins with "@import" = Parse @import rule
				elseif(substr(trim($line), 0, 7) == '@import'){
					$this->selector_stack = array();
					$debug['type'] = 'IM';
					$debug['stack'] = 'Reset';
				}
				// Else parse normal line
				else{
					// Get the next line's indention level
					if($nextline != ''){
						$nextlevel = $this->get_indention_level($nextline);
					}
					// Next line is indented = parse this as a selector
					if($nextline != '' && $nextlevel > $this->get_indention_level($line)){
						$debug['type'] = 'SE';
						$debug['stack'] = $this->selector_stack;
					}
					// Else parse as a property-value-pair
					else{
						$debug['type'] = 'PR';
						$debug['stack'] = $this->selector_stack;
					}
				}
			}
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
			// If the line and the following line are not empty, find the whitespace used for indention
			if($line != '' && trim($nextline) != '' && preg_match('/^([\s]+).*?$/', $nextline, $matches)){
				if(count($matches) == 2 && strlen($matches[1]) > 0){
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
		$processed = array();
		$loc = count($this->code);
		for($i = 0; $i < $loc; $i++){
			if(!preg_match('~^[\s]*//.*?$~', $this->code[$i])){
				if(preg_match('[\S]', $this->code[$i])){
					$processed[] = $this->code[$i];
				}
				else{
					$processed[] = '';
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


}


?>