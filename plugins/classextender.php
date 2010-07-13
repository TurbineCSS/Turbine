<?php


	/**
	 * Class-Extender for container
	 * 
	 * Usage: Too complicated, see docs
	 * Example: 
	 * Status: Alpha
	 * Version: 0.9
	 * 
	 * @param mixed &$parsed
	 * @return void
	 */
	function classextender(&$parsed){
		global $cssp;
		// Main loop
		foreach($parsed as $block => $css){
			foreach($parsed[$block] as $selector => $styles) {
				// Define the new (extended) selector
				$extended_selector = null;

				// Looking for &.class, &#id or &:selector
				if (preg_match('@(\&.|&#|&\:)@',$selector)) {
					// Remove the &
					$extended_selector = preg_replace('@( \&)@','',$selector);
				// Looking for auto generated selector i.e. "a(:link, :visited)"
				} elseif (preg_match_all('@(.*?)\((\:.*?)\)@',$selector,$matches)) {
					$exploded_selectors = explode(',',$matches[2][0]);
					foreach ($exploded_selectors as $key => $value) {
						$extended_selector .= preg_replace('@\((\:.*?)\)@',trim($value),$selector).", ";
					}
				// Looking for auto generated selector i.e. "div.foo(1-3)"
				} elseif (preg_match_all('@(.*?)\((\d{1,})-(\d{1,})\)@',$selector,$matches)) {
				  // Check if starting value is smaller than ending value - "div.foo(3-1)" i.e. will be ignored
					if ($matches[2][0] < $matches[3][0]) {
						for ($i=$matches[2][0]; $i <= $matches[3][0]; $i++) {
							$extended_selector .= preg_replace('@\((\d{1,})-(\d{1,})\)@',$i,$selector).", ";
						}
					}
				}

				// Insert the new selector if present
				if (isset($extended_selector)) {
					// Remove ', ' at the end of the new selector if present
					$extended_selector = preg_replace('@(, )$@', '', $extended_selector);
					$changed = array();
					$changed[$extended_selector] = $styles;
					$cssp->insert($changed,'global',$selector);
					// Remove old selector
					unset($parsed[$block][$selector]);
				}
			}
		}
	}


	/**
	 * Register the plugin
	 */
	$cssp->register_plugin('before_compile', 0, 'classextender');


?>