<?php
/**
 * CSS to CSSP converter
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


class CsspConverter extends CssParser {


	public static function factory(){
		return new CsspConverter();
	}


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
					echo "\t";
					echo $property.': '.$value;
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