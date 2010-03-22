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


class CsspConverter extends CssParser {


	public $indention_char = "\t";
	public $colon_space = '';


	/**
	 * Factory
	 * @param string $indention_char Indention Char
	 * @param int $indention_count Indention level
	 * @param bool $colon_space Insert a space after the property colon?
	 * @return object CsspConverter
	 */
	public static function factory($indention_char, $indention_count, $colon_space){
		return new CsspConverter($indention_char, $indention_count, $colon_space);
	}


	/**
	 * Constructor
	 * Sets up all the whitespace stuff
	 * @param string $indention_char Indention Char
	 * @param int $indention_count Indention level
	 * @param bool $colon_space Insert a space after the property colon?
	 * @return void
	 */
	public function __construct($indention_char, $indention_count, $colon_space){
		// Generate indention char
		$indention_char = ($indention_char == 'tab') ? "\t" : ' ';
		$char = '';
		$count = (int) $indention_count;
		for($i = 0; $i < $count; $i++){
			$char .= $indention_char;
		}
		$this->indention_char = $char;
		// Set colon Space
		if($colon_space){
			$this->colon_space = ' ';
		}
	}


	/**
	 * Converter
	 * Parses the intput css and prints out the turbine code
	 * @return unknown_type
	 */
	public function convert(){
		foreach($this->parsed as $block => $css){
			if($block != 'css'){
				echo $block;
				echo "\n\n";
			}
			foreach($this->parsed[$block] as $selector => $styles){
				echo "\n";
				echo $selector;
				echo "\n";
				foreach($styles as $property => $value){
					echo $this->indention_char;
					echo $property.':'.$this->colon_space.$value;
					echo "\n";
				}
			}
			if($block != 'css'){
				echo "\n\n";
				echo '// Ende von '.$block;
				echo "\n\n";
			}
		}
	}


}

?>