<?php


	// Simple selectors (two simple selectors)
	$input = '#foo
    color:red
#bar
    background:blue';
	$output = 'a:1:{s:6:"global";a:4:{s:7:"@import";a:0:{}s:10:"@font-face";a:0:{}s:4:"#foo";a:1:{s:5:"color";s:3:"red";}s:4:"#bar";a:1:{s:10:"background";s:4:"blue";}}}';
	test($input, $output, 'parsed', 'Simple selectors');

		// Same selectors
	$input = '#foo
    color:red
#foo
    background:blue';
	$output = 'a:1:{s:6:"global";a:3:{s:7:"@import";a:0:{}s:10:"@font-face";a:0:{}s:4:"#foo";a:2:{s:5:"color";s:3:"red";s:10:"background";s:4:"blue";}}}';
	test($input, $output, 'parsed', 'Same selectors');

	// Nested selectors 1 (two nested selectors)
	$input = '#foo
    color:red
    #bar
        background:blue';
	$output = 'a:1:{s:6:"global";a:4:{s:7:"@import";a:0:{}s:10:"@font-face";a:0:{}s:4:"#foo";a:1:{s:5:"color";s:3:"red";}s:9:"#foo #bar";a:1:{s:10:"background";s:4:"blue";}}}';
	test($input, $output, 'parsed', 'Nested selectors 1');


	// Nested selectors 2 (three nested selectors)
	$input = '#foo
    color:red
    #bar
        background:blue
        #blubb
            font-weight:italic';
	$output = 'a:1:{s:6:"global";a:5:{s:7:"@import";a:0:{}s:10:"@font-face";a:0:{}s:4:"#foo";a:1:{s:5:"color";s:3:"red";}s:9:"#foo #bar";a:1:{s:10:"background";s:4:"blue";}s:16:"#foo #bar #blubb";a:1:{s:11:"font-weight";s:6:"italic";}}}';
	test($input, $output, 'parsed', 'Nested selectors 2');


	// Nested selectors 3 (two selectors nested into one)
	$input = '#foo
    color:red
    #bar
        background:blue
    #blubb
        font-weight:italic';
	$output = 'a:1:{s:6:"global";a:5:{s:7:"@import";a:0:{}s:10:"@font-face";a:0:{}s:4:"#foo";a:1:{s:5:"color";s:3:"red";}s:9:"#foo #bar";a:1:{s:10:"background";s:4:"blue";}s:11:"#foo #blubb";a:1:{s:11:"font-weight";s:6:"italic";}}}';
	test($input, $output, 'parsed', 'Simple nested selectors 3');


	// Nested selectors 4 (four selectors nested in different levels)
	$input = '#foo
    color:red
    #bar
        background:blue
        #blubb
            font-weight:italic
    #alpha
        color:yellow';
	$output = 'a:1:{s:6:"global";a:6:{s:7:"@import";a:0:{}s:10:"@font-face";a:0:{}s:4:"#foo";a:1:{s:5:"color";s:3:"red";}s:9:"#foo #bar";a:1:{s:10:"background";s:4:"blue";}s:16:"#foo #bar #blubb";a:1:{s:11:"font-weight";s:6:"italic";}s:11:"#foo #alpha";a:1:{s:5:"color";s:6:"yellow";}}}';
	test($input, $output, 'parsed', 'Nested selectors 3');


	// Combined selectors 1 (two combined selectors)
	$input = '#foo, #bar
    color:red
    #alpha, #beta
        background:blue';
	$output = 'a:1:{s:6:"global";a:4:{s:7:"@import";a:0:{}s:10:"@font-face";a:0:{}s:10:"#foo, #bar";a:1:{s:5:"color";s:3:"red";}s:48:"#foo #alpha, #foo #beta, #bar #alpha, #bar #beta";a:1:{s:10:"background";s:4:"blue";}}}';
	test($input, $output, 'parsed', 'Combined selectors 1');


	// Combining selectors and nesting pseudo classes
	$input = '#foo
    color:red
    #bar, :hover
        background:blue';
	$output = 'a:1:{s:6:"global";a:4:{s:7:"@import";a:0:{}s:10:"@font-face";a:0:{}s:4:"#foo";a:1:{s:5:"color";s:3:"red";}s:21:"#foo #bar, #foo:hover";a:1:{s:10:"background";s:4:"blue";}}}';
	test($input, $output, 'parsed', 'Combining selectors and nesting pseudo classes');


?>