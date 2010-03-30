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
	 * @var string $indention_char The Whitespace character(s) used for indention
	 */
	public $indention_char = false;


	/**
	 * @var string $token The current token
	 */
	private $token = '';

	/**
	 * @var unknown_type
	 */
	private $prev_line = array(
		'type' => false,
		'indention' => null
	);


	/**
	 * 
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
		$this->get_indention_char();
		// Loop through the lines
		$loc = count($this->code);
		for($i = 0; $i < $loc; $i++){
			$this->token = '';
			$line = $this->code[$i];
			// If the current line is empty, ignore it and reset the previous line and the selector stack
			if($line == ''){
				$this->prev_line = array(
					'type' => false,
					'indention' => null
				);
				$this->selector_stack = array();
				echo "RE<br>";
			}
			// Else parse the line
			else{
				// Line begins with "@media" = parse this as a @media-line
				if(substr(trim($line), 0, 6) == '@media'){
					echo "ME | $line<br>";
				}
				// Line begins with "@import" = Parse @import rule
				elseif(substr(trim($line), 0, 7) == '@import'){
					echo "IM | $line<br>";
				}
				// Else parse normal line
				else{
					// Get the next line's indention level
					if($this->code[1+$i] != ''){
						$nextlevel = $this->get_indention_level($this->code[1+$i]);
					}
					// Next line is indented = parse this as a selector
					if($this->code[1+$i] != '' && $nextlevel > $this->get_indention_level($this->code[$i])){
						echo "SE | $line<br>";
					}
					// Else parse as a property-value-pair
					else{
						echo "KV | $line<br>";
					}
				}
			}
		}
		return $this;
	}


	/**
	 * get_indention_char
	 * Find out which whitespace char(s) are used for indention
	 * @return void
	 */
	protected function get_indention_char(){
		$loc = count($this->code);
		for($i = 0; $i < $loc; $i++){ // For each line...
			$line = $this->code[$i];
			$nextline = $this->code[1+$i];
			if($line != '' && $nextline != ''){ // ...if the line and the following line are not empty..
				preg_match('/^([\s]+).*?$/', $nextline, $matches); // ...find the whitespace used for indention
				if(count($matches) == 2 && strlen($matches[1]) > 0){
					$this->indention_char = $matches[1];
					return;
				}
			}
		}
	}


	/**
	 * preprocess
	 * Strip whitespace from empty lines and remove comment lines
	 * @return void
	 */
	protected function preprocess(){
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
	 * get_indention_level
	 * Returns the indention level for a line
	 * @param string $line The line to get the indention level for
	 * @return int $level The indention level
	 */
	protected function get_indention_level($line){
		$level = 0;
		if(substr($line, 0, strlen($this->indention_char)) == $this->indention_char){
			$level = 1 + $this->get_indention_level(substr($line, strlen($this->indention_char)));
		}
		return $level;
	}


}

?>