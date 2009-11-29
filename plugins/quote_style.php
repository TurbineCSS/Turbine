<?php


	/**
	 * Automatic quotes for the language of your choice
	 * Usage: div.foo q { quote-style:language[-country][-alt]-level; }
	 * Example: div.foo[lang=de-de] q { quote-style:german-alt-1; }
	 * Example: div.foo[lang=en-us] q q { quote-style:english-us-2; }
	 * 
	 * @param mixed $parsed
	 * @return void
	 */
	function quote_style($parsed){
		// List of quotes (Source: http://de.wikipedia.org/wiki/Anf%C3%BChrungszeichen#Kodierung)
		$quotes = array(
			'german' => array('201E', '201C', '201A', '2018'),
			'german-alt' => array('00BB', '00AB', '203A', '2039'),
			'swiss' => array('00AB', '00BB', '2039', '203A'),
			'english-uk' => array('2018', '2019', '201C', '201D'),
			'english-us' => array('201C', '201D', '2018', '2019')
		);
		// Main loop
		foreach($parsed as $block => $css){
			$block_count = count($css);
			$block_keys = array_keys($css);
			$insert = 0;
			foreach($parsed[$block] as $selector => $styles){
				// Apply quotes
				if(isset($parsed[$block][$selector]['quote-style'])){
					$value = $parsed[$block][$selector]['quote-style'];
					if(isset($quotes[$value])){
						$parsed[$block][$selector]['quotes'] = '"\\'.$quotes[$value][0].'" "\\'.$quotes[$value][1].'" "\\'.$quotes[$value][2].'" "\\'.$quotes[$value][3].'"';
					}
					// Remove quote-style property and unset quotes property
					unset($parsed[$block][$selector]['quote-style']);
				}
			}
		}
	}


?>