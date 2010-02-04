<?php


/**
 * CSSP - CSS Preprocessor
 * A new way to write CSS
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
 * CSSP
 * CSS Preprocessor
 */
class Cssp extends Parser2 {


	/**
	 * Constructor
	 * @param string $query String of Files to load, sepperated by ;
	 * @return void
	 */
	public function __construct($query = NULL){
		global $browser;
		if($query){
			$this->load_file($query);
		}
	}


	/**
	 * compile
	 * This is where the magic happens :)
	 * @return void
	 */
	public function compile(){
		$this->apply_aliases();
		$this->apply_inheritance();
		$this->apply_constants();
		$this->cleanup();
	}


	/**
	 * apply_constants
	 * Applies constants to the stylesheet
	 * @return void
	 */
	public function apply_constants(){
		// Apply global constants to all blocks
		$this->apply_global_constants();
		// Apply constants, if present, to the global block
		if(isset($this->parsed['global']['@constants'])){
			foreach($this->parsed as $block => $css){
				$this->apply_block_constants($this->parsed['global']['@constants'], $block);
			}
		}
		// Apply constants for @media blocks
		foreach($this->parsed as $block => $css){
			if(isset($this->parsed[$block]['@constants'])){
				$this->apply_block_constants($this->parsed[$block]['@constants'], $block);
			}
		}
	}


	/**
	 * apply_block_constants
	 * Applies a set of constants to a specific block of css
	 * @param array $constants Array of constants
	 * @param string $block Block key to apply the constants to
	 * @return void
	 */
	protected function apply_block_constants($constants, $block){
		foreach($constants as $constant => $value){
			foreach($this->parsed[$block] as $selector => $styles){
				foreach($styles as $css_property => $css_value){
					$this->parsed[$block][$selector][$css_property] = preg_replace('/(\$'.$constant.')\b/', $value, $css_value);
				}
			}
		}
	}


	/**
	 * apply_global_constants
	 * Applies global constants to all blocks
	 * @return void
	 */
	protected function apply_global_constants(){
		global $global_constants;
		foreach($global_constants as $g_constant => $g_value){
			foreach($this->parsed as $block => $css){
				foreach($this->parsed[$block] as $selector => $styles){
					foreach($styles as $property => $value){
						$this->parsed[$block][$selector][$property] = preg_replace('/(_'.$g_constant.')\b/', $g_value, $value);
					}
				}
			}
		}
	}


	/**
	 * apply_aliases
	 * Applies selector aliases
	 * @return void
	 */
	public function apply_aliases(){
		// Apply global aliases, if present, to all blocks
		if(isset($this->parsed['global']['@aliases'])){
			foreach($this->parsed as $block => $css){
				$this->apply_block_aliases($this->parsed['global']['@aliases'], $block);
			}
		}
		// Apply aliases for @media blocks
		foreach($this->parsed as $block => $css){
			if(isset($this->parsed[$block]['@aliases'])){
				$this->apply_block_aliases($this->parsed[$block]['@aliases'], $block);
			}
		}
	}


	/**
	 * apply_block_aliases
	 * Applies a set of aliases to a specific block of css
	 * @param array $aliases Array of aliases
	 * @param string $block Block key to apply the aliases to
	 * @return void
	 */
	protected function apply_block_aliases($aliases, $block){
		foreach($aliases as $alias => $value){
			foreach($this->parsed[$block] as $selector => $styles){
				// Add a new element with the full selector and delete the old one
				$newselector = preg_replace('/(\$'.$alias.')\b/', $value, $selector);
				if($newselector != $selector){
					$elements = array($newselector => $styles);
					$this->insert($elements, $block, $selector);
					unset($this->parsed[$block][$selector]);
				}
			}
		}
	}


	/**
	 * apply_inheritance
	 * Applies inheritance and property copying to the stylesheet
	 * @return void
	 */
	public function apply_inheritance(){
		foreach($this->parsed as $block => $css){
			foreach($this->parsed[$block] as $selector => $styles){
				// Full inheritance
				if(isset($this->parsed[$block][$selector]['extends'])){
					// Extract ancestor
					$ancestor = $this->parsed[$block][$selector]['extends'];
					if($this->parsed[$block][$ancestor]){
						$this->parsed[$block][$selector] = $this->merge_rules(
							$this->parsed[$block][$selector],
							$this->parsed[$block][$ancestor],
							array(),
							false
						);
					}
					unset($this->parsed[$block][$selector]['extends']);
				}
				// Selective copying via "copy(selector property)"
				$inheritance_pattern = "/copy\((.*)[\s]+(.*)\)/";
				foreach($styles as $property => $value){
					// Properties that have multiple values as an array are possible too.. this may be slow, but it's simple
					if(!is_array($value)){
						$value = array($value);
					}
					foreach($value as $val){
						if(preg_match($inheritance_pattern, $val)){
							preg_match_all($inheritance_pattern, $val, $matches);
							if(isset($this->parsed[$block][$matches[1][0]][$matches[2][0]])){
								$this->parsed[$block][$selector][$property] = $this->parsed[$block][$matches[1][0]][$matches[2][0]];
							}
						}
					}
				}
			}
		}
	}


	/***
	 * merge_rules
	 * Merges possible conflicting css rules.
	 * Overloads the parsers native merge_rules method to make exclusion of certain properties possible
	 * @param mixed $old The OLD rules (overridden by the new rules)
	 * @param mixed $new The NEW rules (override the old rules)
	 * @param array $exclude A list of properties NOT to merge
	 * @param array $allow_overwrite Allow new rules to overwrite old ones?
	 * @return mixed $rule The new, merged rule
	 */
	public function merge_rules($old, $new, $exclude = array(), $allow_overwrite = true){
		$rule = $old;
		foreach($new as $property => $value){
			if(!in_array($property, $exclude)){
				if(isset($rule[$property])){
					$tokens = $this->tokenize($rule[$property]);
					if($allow_overwrite == true && !in_array('!important', $tokens)){
						$rule[$property] = $value;
					}
				}
				else{
					$rule[$property] = $value;
				}
			}
		}
		return $rule;
	}


	/**
	 * cleanup
	 * Deletes empty elements, templates and cssp-only elements
	 * @return void
	 */
	public function cleanup(){
		// Remove @constants and @aliases blocks
		foreach($this->parsed as $block => $css){
			if(isset($this->parsed[$block]['@constants'])){
				unset($this->parsed[$block]['@constants']);
			}
			if(isset($this->parsed[$block]['@aliases'])){
				unset($this->parsed[$block]['@aliases']);
			}
			// Remove empty elements and templates
			foreach($this->parsed[$block] as $selector => $styles){
				if(empty($styles) || $selector{0} == '?'){
					unset($this->parsed[$block][$selector]);
				}
			}
		}
	}


	/**
	 * insert
	 * Inserts an element at a specific position in the block
	 * @param mixed $element The element to insert
	 * @param string $block The block to insert into
	 * @param string $before The element after which the new element is inserted
	 * @return void
	 */
	protected function insert($elements, $block, $before){
		$newblock = array();
		foreach($this->parsed[$block] as $selector => $styles){
			$newblock[$selector] = $styles;
			if($selector == $before){
				foreach($elements as $element_selector => $element_styles){
					$newblock[$element_selector] = $element_styles;
				}
			}
		}
		$this->parsed[$block] = $newblock;
	}


}


?>