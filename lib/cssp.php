<?php


/**
 * CSSP - CSS Preprocessor
 * Constants and inheritance for CSS
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
		if($query){
			#$start = microtime(true);
			// Cache: Has file already been parsed?
			$incache = false;
			// Cache: Where to store parsed files
			$cachedir = str_replace('\\','/',dirname(__FILE__)).'/cssp_cache';
			// Cache: Check if cache-directory has been created
			if(!is_dir($cachedir)) mkdir($cachedir,0777);
			$cachefile = preg_replace('/[^0-9A-Za-z\-\._]/','',str_replace(array('\\','/'),'.',$query));
			// Cache: Check if a cached version of the file already exists
			if(file_exists($cachedir.'/'.$cachefile) && filemtime($cachedir.'/'.$cachefile) >= filemtime($file)) $incache = true;
			
			if(!$incache)
			{
				#echo "/* nocache */\r\n";
				$this->load_file($query);
				$this->parse();
				$this->apply_children();
				$this->apply_inheritance();
				$this->apply_constants();
				$this->apply_flags();
				$this->cleanup();
				
				// Cache: Write parsed content to file
				file_put_contents($cachedir.'/'.$cachefile,serialize($this->parsed));
			}
			else 
			{
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
				$this->apply_block_constant($this->parsed['css']['@constants'], $block);
			}
		}
		// Apply constants for @media blocks
		foreach($this->parsed as $block => $css){
			if(isset($this->parsed[$block]['@constants'])){
				$this->apply_block_constant($this->parsed[$block]['@constants'], $block);
			}
		}
	}


	/**
	 * Applies a set of constants to a specific block of css
	 * @param array $constants Array of constants
	 * @param string $block Block key to apply the constants to
	 * @return void
	 */
	protected function apply_block_constant($constants, $block){
		foreach($constants as $constant => $value){
			foreach($this->parsed[$block] as $selector => $styles){
				foreach($styles as $css_property => $css_value){
					$this->parsed[$block][$selector][$css_property] = str_replace('$'.$constant, $value, $css_value);
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
			}
		}
	}


	/***
	 * merge_rules
	 * Merges possible conflicting css rules.
	 * Overloads the parsers native merge_rules method to make exclusion of certain properties possible
	 * @param mixed $old The OLD rules (overridden by the new rules)
	 * @param mixed $new The NEW rules (override the old rules)
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
	 * mask_unsafe_chars
	 * Overloads the parsers native mask_unsafe_chars method to allow child notation
	 * @param string $str css with unmasked chars
	 * @return string css with masked chars
	 */
	public function mask_unsafe_chars($str){
		foreach($this->unsafe_chars as $mask => $unsafe_char){
			$masked_unsafe_char = str_replace(array('*', '/'), array('\*', '\/'), $unsafe_char);
			$patterns[] = '/content(.*:.*(\'|").*)('.$masked_unsafe_char.')(.*(\'|"))/';
			$replacements[] = 'content$1'.$mask.'$4';
		}
		$str = preg_replace($patterns, $replacements, $str);
		// A regex pattern for fining children, defined in such a way that it always matches the currently lowest one
		$pattern = '/(.*)(children\s*?:\s*?\{)(.*?)(\};)(.*)/ms';
		// Using a loop to path our way from the lowest level to the highest until no child is left
		while(preg_match($pattern,$str,$matches) == 1) 
		{
			$originalstring = $matches[1].$matches[2].$matches[3].$matches[4].$matches[5];
			$replacementstring = $matches[1].$this->parse_children($matches[3]).$matches[5];
			$str = str_replace($originalstring,$replacementstring,$str);
		}
		return $str;
	}

	/**
	 * parse_children
	 * Parses children, stores them serialized in the "children" property
	 * @return string $child_css A property-value-pair
	 */
	public static function parse_children($str){
		// print_r($matches);
		$childParser = new Cssp();
		$childParser->load_string($str);
		$childParser->parse();
		$childParser->apply_children();
		$childParser->apply_inheritance();
		$childParser->apply_constants();
		$childParser->apply_flags();
		$childParser->cleanup();
		// Return serialized children
		// FIXME: base64 encoding is a temporary workaround to prevent the curly braces in the serialization from confusing the parser
		$child_css = base64_encode(serialize($childParser->parsed['css']));
		$child_css = 'children: '.$child_css;
		//Debugging output added by Schepp on 25.11.2009
		#print_r($childParser->parsed['css']);
		#echo "\r\n-------------------------------------\r\n";
		return $child_css;
	}


	/**
	 * apply_children
	 * Applies children
	 * @return void
	 */
	public function apply_children(){
		foreach($this->parsed as $block => $css){
			foreach($this->parsed[$block] as $selector => $styles){
				if(isset($styles['children']) && !empty($styles['children']) && trim($styles['children']) != ''){
					// Unserialize children
					$children = unserialize(base64_decode($styles['children']));
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
				#elseif(isset($styles['children']) && (empty($styles['children']) || trim($styles['children']) != '')) unset($this->parsed[$block][$selector]['children']);
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
		// Remove @constants blocks
		foreach($this->parsed as $block => $css){
			if(isset($this->parsed[$block]['@constants'])){
				unset($this->parsed[$block]['@constants']);
			}
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