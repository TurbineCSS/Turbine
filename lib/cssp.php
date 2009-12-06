<?php


/**
 * CSSP - CSS Preprocessor
 * Constants and inheritance for CSS
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
 * @todo Only process files starting with "CSSP"
 */
class Cssp extends CssParser {


	public $ignore_on_merge = array('flags');


	/**
	 * Constructor
	 * @param string $query String of Files to load, sepperated by ;
	 * @return void
	 */
	public function __construct($query = NULL){
		global $browserproperties;
		if($query){
			#$start = microtime(true);
			// Cache: Has file already been parsed?
			$incache = false;
			// Cache: Where to store parsed files
			$cachedir = str_replace('\\', '/', dirname(__FILE__)) . '/cssp_cache';
			// Cache: Check if cache-directory has been created
			if(!is_dir($cachedir)){
				@mkdir($cachedir, 0777);
			}
			$cachefile = implode('.',$browserproperties).preg_replace('/[^0-9A-Za-z\-\._]/','',str_replace(array('\\','/'),'.',$query));
			// Cache: Check if a cached version of the file already exists
			if(file_exists($cachedir.'/'.$cachefile) && filemtime($cachedir.'/'.$cachefile) >= filemtime($query)) $incache = true;
			if(!$incache){
				#echo "/* nocache */\r\n";
				$this->load_file($query);
				$this->parse();
				$this->apply_children();
				$this->apply_aliases();
				$this->apply_inheritance();
				$this->apply_constants();
				$this->apply_flags();
				$this->cleanup();
				
				// Cache: Write parsed content to file
				if(is_dir($cachedir)){
					file_put_contents($cachedir.'/'.$cachefile,serialize($this->parsed));
				}
			}
			else{
				#echo "/* cache */\r\n";
				$this->parsed = unserialize(file_get_contents($cachedir.'/'.$cachefile));
			}
				
			#$end = microtime(true);
			#echo $end - $start;
		}
	}


	/**
	 * apply_constants
	 * Applies constants to the stylesheet
	 * @return void
	 */
	public function apply_constants(){
		// Apply global constants, if present, to all blocks
		if(isset($this->parsed['css']['@constants'])){
			foreach($this->parsed as $block => $css){
				$this->apply_block_constants($this->parsed['css']['@constants'], $block);
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
					$this->parsed[$block][$selector][$css_property] = str_replace('$'.$constant, $value, $css_value);
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
		if(isset($this->parsed['css']['@aliases'])){
			foreach($this->parsed as $block => $css){
				$this->apply_block_aliases($this->parsed['css']['@aliases'], $block);
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
				$newselector = str_replace('$'.$alias, $value, $selector);
				if($newselector != $selector){
					$this->parsed[$block][$newselector] = $styles; // TODO: This should really be inserted somewhere near the original position
					unset($this->parsed[$block][$selector]);
				}
			}
		}
	}


	/**
	 * apply_inheritance
	 * Applies inheritance to the stylesheet
	 * @return void
	 */
	public function apply_inheritance(){
		foreach($this->parsed as $block => $css){
			foreach($this->parsed[$block] as $selector => $styles){
				// Full inheritance
				if(isset($this->parsed[$block][$selector]['extends'])){
					// Extract parents
					$parents = array();
					preg_match_all('/["\'](.*?)["\']/', $this->parsed[$block][$selector]['extends'], $parents);
					foreach($parents[1] as $parent){
						if($this->parsed[$block][$parent]){
							$this->parsed[$block][$selector] = $this->merge_rules(
								$this->parsed[$block][$selector],
								$this->parsed[$block][$parent],
								$this->ignore_on_merge
							);
						}
					}
					unset($this->parsed[$block][$selector]['extends']);
				}
				// Selective inheritance
				$inheritance_pattern = "/inherit\((.*)[\s]+(.*)\)/";
				foreach($styles as $property => $value){
					if(preg_match($inheritance_pattern, $value)){
						preg_match_all($inheritance_pattern, $value, $matches);
						if(isset($this->parsed[$block][$matches[1][0]][$matches[2][0]])){
							$this->parsed[$block][$selector][$property] = $this->parsed[$block][$matches[1][0]][$matches[2][0]];
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
	 * @return mixed $rule The new, merged rule
	 */
	public function merge_rules($old, $new, $exclude = array()){
		$rule = $old;
		foreach($new as $property => $value){
			if(!in_array($property, $exclude)){
				if($rule[$property]){
					// TODO: This should be protected against the unlikly case that "!important" gets used inside strings
					if(!strpos($rule[$property], ' !important')){
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
	 * apply_children
	 * Applies children
	 * @return void
	 */
	public function apply_children(){
		foreach($this->parsed as $block => $css){
			foreach($this->parsed[$block] as $selector => $styles){
				if(isset($styles['children'])){
					// Remove curly braces around child css
					$styles['children'] = preg_replace('/\{(.*)\}/ms', '$1', $styles['children']);
					$styles['children'] = trim($styles['children']);
					// Parse child css
					$childParser = new Cssp();
					$childParser->load_string($styles['children']);
					$childParser->parse();
					$childParser->apply_combinators();
					$childParser->apply_children();
					$childParser->cleanup();
					$children = $childParser->parsed['css'];
					// Add children to blocks or merge with existing properties
					foreach($children as $child_selector => $child_properties){
						// Build new selector
						// TODO: Take care of commas in strings
						$new_selector = '';
						$child_selectors = explode(',', $child_selector);
						$child_selectors_count = count($child_selectors);
						$parent_selectors = explode(',', $selector);
						$parent_selectors_count = count($parent_selectors);
						for($i = 0; $i < $parent_selectors_count; $i++){
							for($j = 0; $j < $child_selectors_count; $j++){
								$new_selector .= trim($parent_selectors[$i]).' '.trim($child_selectors[$j]);
								if(isset($child_selectors[$j + 1])){
									$new_selector .= ', ';
								}
							}
							if(isset($parent_selectors[$i + 1])){
								$new_selector .= ', ';
							}
						}
						// Merge rules if needed
						if(isset($this->parsed[$block][$new_selector])){
							$this->parsed[$block][$new_selector] = $this->merge_rules(
								$this->parsed[$block][$new_selector],
								$child_properties,
								$this->ignore_on_merge
							);
						}
						else{
							$this->parsed[$block][$new_selector] = $child_properties;
						}
					}
					unset($this->parsed[$block][$selector]['children']);
				}
			}
		}
	}


	/**
	 * apply_combinators
	 * Applies combinator property
	 * @return void
	 */
	public function apply_combinators(){
		foreach($this->parsed as $block => $css){
			foreach($this->parsed[$block] as $selector => $styles){
				if(isset($this->parsed[$block][$selector]['combinator'])){
					$combinator = $this->parsed[$block][$selector]['combinator'];
					$newkey = trim($combinator).' '.$selector;
					$this->parsed[$block][$newkey] = $this->parsed[$block][$selector];
					unset($this->parsed[$block][$selector]);
					unset($this->parsed[$block][$newkey]['combinator']);
				}
			}
		}
	}


	/**
	 * apply_flags
	 * Applies flags
	 * @return void
	 */
	public function apply_flags(){
		foreach($this->parsed as $block => $css){
			foreach($this->parsed[$block] as $selector => $styles){
				if(isset($this->parsed[$block][$selector]['flags'])){
					$flags = explode(',', $this->parsed[$block][$selector]['flags']);
					// Remove flag
					if(in_array('remove', $flags)){
						unset($this->parsed[$block][$selector]);
					}
					else{
						// TODO: Add more flags!
						// Last step: Remove flags property
						unset($this->parsed[$block][$selector]['flags']);
					}
				}
			}
		}
	}


	/**
	 * cleanup
	 * Deletes empty and cssp-only elements
	 * @return void
	 */
	public function cleanup(){
		// Remove @constants and @aliases blocks
		foreach($this->parsed as $block => $css){
			if(isset($this->parsed[$block]['@constants'])){
				unset($this->parsed[$block]['@constants']);
			}
			/*if(isset($this->parsed[$block]['@aliases'])){
				unset($this->parsed[$block]['@aliases']);
			}*/
			// Remove empty elements
			foreach($this->parsed[$block] as $selector => $styles){
				if(empty($styles)){
					unset($this->parsed[$block][$selector]);
				}
			}
		}
	}


}


?>