<?php

$ua_tests = array(

	// Internet Explorer bestiary
	'IE9 on Win7' => array(
		'ua' => 'Mozilla/5.0 (compatible; MSIE 9.0; Windows NT 6.1; Trident/5.0)',
		'test' => array(
			'name' => 'msie',
			'version' => '9',
			'engine' => 'msie',
			'engineversion' => '9',
			'platform' => 'windows',
			'platformversion' => '6.1',
			'platformtype' => 'desktop'
		)
	),
	'IE9 on Vista' => array(
		'ua' => 'Mozilla/5.0 (compatible; MSIE 9.0; Windows NT 6.0; Trident/5.0)',
		'test' => array(
			'name' => 'msie',
			'version' => '9',
			'engine' => 'msie',
			'engineversion' => '9',
			'platform' => 'windows',
			'platformversion' => '6.0',
			'platformtype' => 'desktop'
		)
	),
	'IE8 on Win7' => array(
		'ua' => 'Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.1; WOW64; Trident/4.0; SLCC2; .NET CLR 2.0.50727; .NET CLR 3.5.30729; .NET CLR 3.0.30729; Media Center PC 6.0; MS-RTC LM 8)',
		'test' => array(
			'name' => 'msie',
			'version' => '8',
			'engine' => 'msie',
			'engineversion' => '8',
			'platform' => 'windows',
			'platformversion' => '6.1',
			'platformtype' => 'desktop'
		)
	),
	'IE8 on Vista' => array(
		'ua' => 'Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.0; Trident/4.0)',
		'test' => array(
			'name' => 'msie',
			'version' => '8',
			'engine' => 'msie',
			'engineversion' => '8',
			'platform' => 'windows',
			'platformversion' => '6.0',
			'platformtype' => 'desktop'
		)
	),
	'IE8 on WinXP' => array(
		'ua' => 'Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 5.1; Trident/4.0; InfoPath.1; .NET CLR 2.0.50727; .NET CLR 3.0.04506.30; .NET CLR 3.0.04506.648; yie8)',
		'test' => array(
			'name' => 'msie',
			'version' => '8',
			'engine' => 'msie',
			'engineversion' => '8',
			'platform' => 'windows',
			'platformversion' => '5.1',
			'platformtype' => 'desktop'
		)
	),
	'IE7 on Win7' => array(
		'ua' => 'Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 6.1; SLCC2; .NET CLR 2.0.50727; .NET CLR 3.5.30729; .NET CLR 3.0.30729; Media Center PC 6.0)',
		'test' => array(
			'name' => 'msie',
			'version' => '7',
			'engine' => 'msie',
			'engineversion' => '7',
			'platform' => 'windows',
			'platformversion' => '6.1',
			'platformtype' => 'desktop'
		)
	),
	'IE7 (IE Tester) on Win7' => array(
		'ua' => 'Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 6.1; WOW64; SLCC2; .NET CLR 2.0.50727; .NET CLR 3.5.30729; .NET CLR 3.0.30729; Media Center PC 6.0)',
		'test' => array(
			'name' => 'msie',
			'version' => '7',
			'engine' => 'msie',
			'engineversion' => '7',
			'platform' => 'windows',
			'platformversion' => '6.1',
			'platformtype' => 'desktop'
		)
	),
	'IE7 on Vista' => array(
		'ua' => 'Mozilla/5.0 (compatible; MSIE 7.0; Windows NT 6.0; WOW64; SLCC1; .NET CLR 2.0.50727; Media Center PC 5.0; c .NET CLR 3.0.04506; .NET CLR 3.5.30707; InfoPath.1; el-GR)',
		'test' => array(
			'name' => 'msie',
			'version' => '7',
			'engine' => 'msie',
			'engineversion' => '7',
			'platform' => 'windows',
			'platformversion' => '6.0',
			'platformtype' => 'desktop'
		)
	),
	'IE7 on WinXP' => array(
		'ua' => 'Mozilla/4.0 (Windows; MSIE 7.0; Windows NT 5.1; SV1; .NET CLR 2.0.50727)',
		'test' => array(
			'name' => 'msie',
			'version' => '7',
			'engine' => 'msie',
			'engineversion' => '7',
			'platform' => 'windows',
			'platformversion' => '5.1',
			'platformtype' => 'desktop'
		)
	),
	'IE6 (IE Tester) on Win 7' => array(
		'ua' => 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 6.1; WOW64; SLCC2; .NET CLR 2.0.50727; .NET CLR 3.5.30729; .NET CLR 3.0.30729; Media Center PC 6.0)',
		'test' => array(
			'name' => 'msie',
			'version' => '6',
			'engine' => 'msie',
			'engineversion' => '6',
			'platform' => 'windows',
			'platformversion' => '6.1',
			'platformtype' => 'desktop'
		)
	),
	'IE6 on WinXP' => array(
		'ua' => 'Mozilla/5.0 (compatible; MSIE 6.0; Windows NT 5.1)',
		'test' => array(
			'name' => 'msie',
			'version' => '6',
			'engine' => 'msie',
			'engineversion' => '6',
			'platform' => 'windows',
			'platformversion' => '5.1',
			'platformtype' => 'desktop'
		)
	),

	// Safari
	'Safari 3.1 on Win7' => array(
	),
	'Safari 3.1 on Vista' => array(
	),
	'Safari 3.1 on WinXP' => array(
	),
	'Safari 3.1 on OS X' => array(
	),
	'Safari 4 on Win7' => array(
		'ua' => 'Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US) AppleWebKit/532+ (KHTML, like Gecko) Version/4.0.2 Safari/530.19.1',
		'test' => array(
			'name' => 'safari',
			'version' => '4.02',
			'engine' => 'webkit',
			'engineversion' => '532',
			'platform' => 'windows',
			'platformversion' => '6.1',
			'platformtype' => 'desktop'
		)
	),
	'Safari 4 on Vista' => array(
		'ua' => 'Mozilla/5.0 (Windows; U; Windows NT 6.0; pl-PL) AppleWebKit/530.19.2 (KHTML, like Gecko) Version/4.0.2 Safari/530.19.1',
		'test' => array(
			'name' => 'safari',
			'version' => '4.02',
			'engine' => 'webkit',
			'engineversion' => '530.192',
			'platform' => 'windows',
			'platformversion' => '6.0',
			'platformtype' => 'desktop'
		)
	),
	'Safari 4 on WinXP' => array(
		'ua' => 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US) AppleWebKit/530.19.2 (KHTML, like Gecko) Version/4.0.2 Safari/530.19.1',
		'test' => array(
			'name' => 'safari',
			'version' => '4.02',
			'engine' => 'webkit',
			'engineversion' => '530.192',
			'platform' => 'windows',
			'platformversion' => '5.1',
			'platformtype' => 'desktop'
		)
	),
	'Safari 4 on OS X' => array(
		'ua' => 'Mozilla/5.0 (Macintosh; U; PPC Mac OS X 10_5_7; en-us) AppleWebKit/530.19.2 (KHTML, like Gecko) Version/4.0.2 Safari/530.19',
		'test' => array(
			'name' => 'safari',
			'version' => '4.02',
			'engine' => 'webkit',
			'engineversion' => '530.192',
			'platform' => 'macintosh',
			'platformversion' => '0',
			'platformtype' => 'desktop'
		)
	),


	// Gecko/Mozilla
	'Firefox 3.5 on Win7' => array(
	),
	'Firefox 3.5 on Vista' => array(
	),
	'Firefox 3.5 on WinXP' => array(
	),
	'Firefox 3.5 on OS X' => array(
	),
	'Firefox 3.5 on Linux' => array(
	),
	'Firefox 3.6 on Win7' => array(
		'ua' => 'Mozilla/5.0 (Windows; U; Windows NT 6.1; lt; rv:1.9.2) Gecko/20100115 Firefox/3.6',
		'test' => array(
			'name' => 'gecko',
			'version' => '1.923',
			'engine' => 'gecko',
			'engineversion' => '1.92',
			'platform' => 'windows',
			'platformversion' => '6.1',
			'platformtype' => 'desktop'
		)
	),
	'Firefox 3.6 on Vista' => array(
		'ua' => 'Mozilla/5.0 (Windows; U; Windows NT 6.0; ru; rv:1.9.2) Gecko/20100115 Firefox/3.6',
		'test' => array(
			'name' => 'gecko',
			'version' => '1.92',
			'engine' => 'gecko',
			'engineversion' => '1.92',
			'platform' => 'windows',
			'platformversion' => '6.0',
			'platformtype' => 'desktop'
		)
	),
	'Firefox 3.6 on WinXP' => array(
		'ua' => 'Mozilla/5.0 (Windows; U; Windows NT 5.1; pt-BR; rv:1.9.2) Gecko/20100115 Firefox/3.6',
		'test' => array(
			'name' => 'gecko',
			'version' => '1.92',
			'engine' => 'gecko',
			'engineversion' => '1.92',
			'platform' => 'windows',
			'platformversion' => '5.1',
			'platformtype' => 'desktop'
		)
	),
	'Firefox 3.6 on OS X' => array(
		'ua' => 'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10.6; en-US; rv:1.9.2) Gecko/20091218 Firefox 3.6',
		'test' => array(
			'name' => 'firefox',
			'version' => '1.92',
			'engine' => 'gecko',
			'engineversion' => '1.92',
			'platform' => 'macintosh',
			'platformversion' => '0',
			'platformtype' => 'desktop'
		)
	),
	'Firefox 3.6 on Linux' => array(
		'ua' => 'Mozilla/5.0 (X11; U; Linux x86_64; en-US; rv:1.9.2) Gecko/20100222 Ubuntu/10.04 (lucid) Firefox/3.6',
		'test' => array(
			'name' => 'gecko',
			'version' => '1.92',
			'engine' => 'gecko',
			'engineversion' => '1.92',
			'platform' => 'linux',
			'platformversion' => '0',
			'platformtype' => 'desktop'
		)
	),


	// Strange Gecko/Mozilla pre-releases and spin-offs
	'Firefox 3.6.x pre on Linux' => array(
		'ua' => 'Mozilla/5.0 (X11; U; Linux x86_64; de-DE; rv:1.9.2.3pre) Gecko/20100326 Ubuntu/9.10 (karmic) Namoroka/3.6.3pre',
		'test' => array(
			'name' => 'gecko',
			'version' => '1.923',
			'engine' => 'gecko',
			'engineversion' => '1.923',
			'platform' => 'linux',
			'platformversion' => '0',
			'platformtype' => 'desktop'
		)
	),
	'Firefox 3.7a pre on Linux' => array(
		'ua' => 'Mozilla/5.0 (X11; U; Linux x86_64; de-DE; rv:1.9.3a3pre) Gecko/20100304 Ubuntu/9.10 (karmic) Minefield/3.7a3pre',
		'test' => array(
			'name' => 'gecko',
			'version' => '1.93',
			'engine' => 'gecko',
			'engineversion' => '1.93',
			'platform' => 'linux',
			'platformversion' => '0',
			'platformtype' => 'desktop'
		)
	),
	'Songbird 1.4 on Linux' => array(
		'ua' => 'Mozilla/5.0 (X11; U; Linux x86_64; de; rv:1.9.0.14) Gecko/2009091418 Songbird/1.4.3 (20091223030122)',
		'test' => array(
			'name' => 'gecko',
			'version' => '1.9014',
			'engine' => 'gecko',
			'engineversion' => '1.9014',
			'platform' => 'linux',
			'platformversion' => '0',
			'platformtype' => 'desktop'
		)
	),
	'Flock 2.5.6 on WinXP' => array(
		'ua' => 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.9.1.7) Gecko/20091221 Firefox/3.5.7 Flock/2.5.6 (.NET CLR 3.5.30729)',
		'test' => array(
			'name' => 'gecko',
			'version' => '1.9014',
			'engine' => 'gecko',
			'engineversion' => '1.917',
			'platform' => 'windows',
			'platformversion' => '5.1',
			'platformtype' => 'desktop'
		)
	),


	// Opera
	'Opera < 10 on Win7' => array(
	),
	'Opera < 10 on Vista' => array(
	),
	'Opera < 10 on WinXP' => array(
	),
	'Opera < 10 on OS X' => array(
	),
	'Opera < 10 on Linux' => array(
		'ua' => 'Opera/9.60 (X11; Linux i686; U; en) Presto/2.1.1',
		'test' => array(
			'name' => 'opera',
			'version' => '9.60',
			'engine' => 'opera',
			'engineversion' => '9.60',
			'platform' => 'linux',
			'platformversion' => '0',
			'platformtype' => 'desktop'
		)
	),
	'Opera >= 10 on Win7' => array(
	),
	'Opera >= 10 on Vista' => array(
	),
	'Opera >= 10 on WinXP' => array(
	),
	'Opera >= 10 on OS X' => array(
		'ua' => 'Opera/9.80 (Macintosh; Intel Mac OS X; U; en) Presto/2.2.15 Version/10.00',
		'test' => array(
			'name' => 'opera',
			'version' => '10.00',
			'engine' => 'opera',
			'engineversion' => '10.00',
			'platform' => 'macintosh',
			'platformversion' => '0',
			'platformtype' => 'desktop'
		)
	),
	'Opera >= 10 on Linux' => array(
		'ua' => 'Opera/9.80 (X11; Linux x86_64; U; de) Presto/2.2.15 Version/10.10',
		'test' => array(
			'name' => 'opera',
			'version' => '10.10',
			'engine' => 'opera',
			'engineversion' => '10.10',
			'platform' => 'linux',
			'platformversion' => '0',
			'platformtype' => 'desktop'
		)
	),


	// Chrome
	'Chrome 4 on Win 7' => array(
		'ua' => 'Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US) AppleWebKit/532.5 (KHTML, like Gecko) Chrome/4.1.249.1025 Safari/532.5',
		'test' => array(
			'name' => 'chrome',
			'version' => '4.12491025',
			'engine' => 'webkit',
			'engineversion' => '532.5',
			'platform' => 'windows',
			'platformversion' => '6.1',
			'platformtype' => 'desktop'
		)
	),
	'Chrome 4 on Vista' => array(
		'ua' => 'Mozilla/5.0 (Windows; U; Windows NT 6.0; en-US) AppleWebKit/532.3 (KHTML, like Gecko) Chrome/4.0.224.2 Safari/532.3',
		'test' => array(
			'name' => 'chrome',
			'version' => '4.02242',
			'engine' => 'webkit',
			'engineversion' => '532.3',
			'platform' => 'windows',
			'platformversion' => '6.0',
			'platformtype' => 'desktop'
		)
	),
	'Chrome 4 on WinXP' => array(
		'ua' => 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US) AppleWebKit/532.8 (KHTML, like Gecko) Chrome/4.0.288.1 Safari/532.8',
		'test' => array(
			'name' => 'chrome',
			'version' => '4.02881',
			'engine' => 'webkit',
			'engineversion' => '532.8',
			'platform' => 'windows',
			'platformversion' => '5.1',
			'platformtype' => 'desktop'
		)
	),
	'Chrome 4 on OS X' => array(
		'ua' => 'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_5_8; en-US) AppleWebKit/532.8 (KHTML, like Gecko) Chrome/4.0.302.2 Safari/532.8',
		'test' => array(
			'name' => 'chrome',
			'version' => '4.03022',
			'engine' => 'webkit',
			'engineversion' => '532.8',
			'platform' => 'macintosh',
			'platformversion' => '0',
			'platformtype' => 'desktop'
		)
	),
	'Chrome 4 on Linux' => array(
		'ua' => 'Mozilla/5.0 (X11; U; Slackware Linux x86_64; en-US) AppleWebKit/532.5 (KHTML, like Gecko) Chrome/4.0.249.30 Safari/532.5',
		'test' => array(
			'name' => 'chrome',
			'version' => '4.02493',
			'engine' => 'webkit',
			'engineversion' => '532.5',
			'platform' => 'linux',
			'platformversion' => '0',
			'platformtype' => 'desktop'
		)
	),
	'Chrome 5 on Win 7' => array(
		'ua' => 'Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US) AppleWebKit/533.3 (KHTML, like Gecko) Chrome/5.0.354.0 Safari/533.3',
		'test' => array(
			'name' => 'chrome',
			'version' => '5.0354',
			'engine' => 'webkit',
			'engineversion' => '533.3',
			'platform' => 'windows',
			'platformversion' => '6.1',
			'platformtype' => 'desktop'
		)
	),
	'Chrome 5 on Vista' => array(
		'ua' => 'Mozilla/5.0 (Windows; U; Windows NT 6.0; en-US) AppleWebKit/533.2 (KHTML, like Gecko) Chrome/5.0.342.5 Safari/533.2',
		'test' => array(
			'name' => 'chrome',
			'version' => '5.03425',
			'engine' => 'webkit',
			'engineversion' => '533.2',
			'platform' => 'windows',
			'platformversion' => '6.0',
			'platformtype' => 'desktop'
		)
	),
	'Chrome 5 on WinXP' => array(
		'ua' => 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US) AppleWebKit/533.3 (KHTML, like Gecko) Chrome/5.0.355.0 Safari/533.3',
		'test' => array(
			'name' => 'chrome',
			'version' => '5.0355',
			'engine' => 'webkit',
			'engineversion' => '533.3',
			'platform' => 'windows',
			'platformversion' => '5.1',
			'platformtype' => 'desktop'
		)
	),
	'Chrome 5 on OS X' => array(
		'ua' => 'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_2; en-US) AppleWebKit/533.2 (KHTML, like Gecko) Chrome/5.0.343.0 Safari/533.2',
		'test' => array(
			'name' => 'chrome',
			'version' => '5.0343',
			'engine' => 'webkit',
			'engineversion' => '533.2',
			'platform' => 'macintosh',
			'platformversion' => '0',
			'platformtype' => 'desktop'
		)
	),
	'Chrome 5 on Linux' => array(
		'ua' => 'Mozilla/5.0 (X11; U; Linux x86_64; en-US) AppleWebKit/532.9 (KHTML, like Gecko) Chrome/5.0.307.11 Safari/532.9',
		'test' => array(
			'name' => 'chrome',
			'version' => '5.030711',
			'engine' => 'webkit',
			'engineversion' => '532.9',
			'platform' => 'linux',
			'platformversion' => '0',
			'platformtype' => 'desktop'
		)
	),


	// The more esoteric browsers
	'iCab >= 4' => array(
		'ua' => 'iCab/4.5 (Macintosh; U; Mac OS X Leopard 10.5.7)',
		'test' => array(
			'name' => 'iCab',
			'version' => '4.5',
			'engine' => 'webkit',
			'engineversion' => false,
			'platform' => 'macintosh',
			'platformversion' => '0',
			'platformtype' => 'desktop'
		)
	),
	'iCab < 4' => array(
		'ua' => 'Mozilla/5.0 (compatible; iCab 3.0.5; Macintosh; U; PPC Mac OS)',
		'test' => array(
			'name' => 'iCab',
			'version' => '3.05',
			'engine' => 'iCab',
			'engineversion' => '3.05',
			'platform' => 'macintosh',
			'platformversion' => '0',
			'platformtype' => 'desktop'
		)
	),
	'Epiphany >= 2.28' => array(
		'ua' => 'Mozilla/5.0 (X11; U; Linux x86_64; nl-nl) AppleWebKit/531.2+ (KHTML, like Gecko) Safari/531.2+ Epiphany/2.29.91',
		'test' => array(
			'name' => 'epiphany',
			'version' => '2.2991',
			'engine' => 'webkit',
			'engineversion' => '531.2',
			'platform' => 'Linux',
			'platformversion' => '0',
			'platformtype' => 'desktop'
		)
	),
	'Epiphany <= 2.22' => array(
		'ua' => 'Mozilla/5.0 (X11; U; Linux x86_64; en; rv:1.9.0.8) Gecko/20080528 Fedora/2.24.3-4.fc10 Epiphany/2.22 Firefox/3.0',
		'test' => array(
			'name' => 'epiphany',
			'version' => '2.22',
			'engine' => 'gecko',
			'engineversion' => '1.908',
			'platform' => 'linux',
			'platformversion' => '0',
			'platformtype' => 'desktop'
		)
	),

	// Tablets
	'iPad' => array(
		'ua' => 'Mozilla/5.0 (iPad; U; CPU OS 3_2 like Mac OS X; en-us) AppleWebKit/531.21.10 (KHTML, like Gecko) Version/4.0.4 Mobile/7B334b Safari/531.21.10',
		'test' => array(
			'name' => 'safari',
			'version' => '4.04',
			'engine' => 'webkit',
			'engineversion' => '531.211',
			'platform' => 'macintosh',
			'platformversion' => '0',
			'platformtype' => 'desktop'
		)
	),

	// Mobile browsers
	'iPhone OS 3.0' => array(
		'ua' => 'Mozilla/5.0 (iPhone; U; CPU like Mac OS X; en) AppleWebKit/420+ (KHTML, like Gecko) Version/3.0 Mobile/1A543a Safari/419.3',
		'test' => array(
			'name' => 'safari',
			'version' => '3',
			'engine' => 'webkit',
			'engineversion' => '420',
			'platform' => 'macintosh',
			'platformversion' => '0',
			'platformtype' => 'mobile'
		)
	),
	'iPhone OS 3.1.2' => array(
		'ua' => 'Mozilla/5.0 (iPhone; U; CPU iPhone OS 3_1_2 like Mac OS X; en-us) AppleWebKit/528.18 (KHTML, like Gecko) Version/4.0 Mobile/7D11 Safari/528.16',
		'test' => array(
			'name' => 'safari',
			'version' => '4',
			'engine' => 'webkit',
			'engineversion' => '528.18',
			'platform' => 'macintosh',
			'platformversion' => '0',
			'platformtype' => 'mobile'
		)
	),
	'Opera Mini' => array(
		'ua' => 'Opera/9.50 (J2ME/MIDP; Opera Mini/4.0.10031/298; U; en)',
		'test' => array(
			'name' => 'opera mini',
			'version' => '4.010031',
			'engine' => 'opera',
			'engineversion' => '9.5',
			'platform' => false,
			'platformversion' => false,
			'platformtype' => 'mobile'
		)
	),
	'Blackberry 9XXX' => array(
		'ua' => 'BlackBerry9700/5.0.0.351 Profile/MIDP-2.1 Configuration/CLDC-1.1 VendorID/123',
		'test' => array(
			'name' => 'blackberry',
			'version' => false,
			'engine' => false,
			'engineversion' => false,
			'platform' => 'blackberry',
			'platformversion' => false,
			'platformtype' => 'mobile'
		)
	),
	'Blackberry 88XX' => array(
		'ua' => 'BlackBerry8330/4.3.0 Profile/MIDP-2.0 Configuration/CLDC-1.1 VendorID/105',
		'test' => array(
			'name' => 'blackberry',
			'version' => false,
			'engine' => false,
			'engineversion' => false,
			'platform' => 'blackberry',
			'platformversion' => false,
			'platformtype' => 'mobile'
		)
	),
	'Blackberry 87XX' => array(
		'ua' => 'BlackBerry8703e/4.1.0 Profile/MIDP-2.0 Configuration/CLDC-1.1 VendorID/105',
		'test' => array(
			'name' => 'blackberry',
			'version' => false,
			'engine' => false,
			'engineversion' => false,
			'platform' => 'blackberry',
			'platformversion' => false,
			'platformtype' => 'mobile'
		)
	),
	'Blackberry 81XX' => array(
		'ua' => 'BlackBerry8130/4.5.0.89 Profile/MIDP-2.0 Configuration/CLDC-1.1 VendorID/106',
		'test' => array(
			'name' => 'blackberry',
			'version' => false,
			'engine' => false,
			'engineversion' => false,
			'platform' => 'blackberry',
			'platformversion' => false,
			'platformtype' => 'mobile'
		)
	),
	'Android 2.1' => array(
		'ua' => 'Mozilla/5.0 (Linux; U; Android 2.1; en-us; Nexus One Build/ERD62) AppleWebKit/530.17 (KHTML, like Gecko) Version/4.0 Mobile Safari/530.17',
		'test' => array(
			'name' => 'android',
			'version' => '4.0',
			'engine' => 'webkit',
			'engineversion' => '530.17',
			'platform' => 'linux',
			'platformversion' => '0',
			'platformtype' => 'mobile'
		)
	),
	'HP' => array(
		'ua' => array(
			'Mozilla/4.0 (compatible; MSIE 4.01; Windows CE; PPC; 240x320; HP iPAQ h6300)',
			'Mozilla/4.0 (compatible; MSIE 4.01; Windows CE; PPC; 240x320)'
		),
		'test' => array(
			'name' => 'msie',
			'version' => '4.01',
			'engine' => 'msie',
			'engineversion' => '4.01',
			'platform' => 'windows ce',
			'platformversion' => '0',
			'platformtype' => 'mobile'
		)
	),
	'HTC + IE' => array(
		'ua' => array(
			'Mozilla/4.0 (compatible; MSIE 6.0; Windows CE; IEMobile 8.12; MSIEMobile 6.0) USCCHTC6875',
			'Modzilla/4.0 (compatible; MSIE 6.0; Windows CE; IEMobile 7.11) 480x640; XV6850; Window Mobile 6.1 Professional',
			'HTC-P4600/1.2 Mozilla/4.0 (compatible; MSIE 6.0; Windows CE; IEMobile 7.11) UP.Link/6.3.1.17.0',
			'Mozilla/4.0 (compatible; MSIE 6.0; Windows CE; IEMobile 7.11) '
		),
		'test' => array(
			'name' => 'msie',
			'version' => '6',
			'engine' => 'msie',
			'engineversion' => '6',
			'platform' => 'windows ce',
			'platformversion' => '0',
			'platformtype' => 'mobile'
		)
	),
	'HTC Touch PRO/PRO2 + Opera' => array(
		'ua' => array(
			'HTC-ST7377/1.59.502.3 (67150) Opera/9.50 (Windows NT 5.1; U; en) UP.Link/6.3.1.17.0',
			'htc_touch_pro2_t7373 opera/9.50 (windows nt 5.1; u; de)'
		),
		'test' => array(
			'name' => 'opera',
			'version' => '9.5',
			'engine' => 'opera',
			'engineversion' => '9.5',
			'platform' => 'windows ce',
			'platformversion' => '0',
			'platformtype' => 'mobile'
		)
	),
	'Kindle' => array(
		'ua' => 'Mozilla/4.0 (compatible; Linux 2.6.22) NetFront/3.4 Kindle/2.0 (screen 600x800)',
		'test' => array(
			'name' => 'Kindle',
			'version' => '2.0',
			'engine' => false,
			'engineversion' => false,
			'platform' => 'linux',
			'platformversion' => '0',
			'platformtype' => 'mobile'
		)
	)


	// TV browsers


);

?>

<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<title>Browser sniffing test</title>
	</head>
	<body>
		<h1>Test results</h1>
		<p>
			<a href="http://www.useragentstring.com/pages/useragentstring.php">Add more tests!</a>
		</p>
		<table border="1">
			<tr>
				<th rowspan="2">Subject</th>
				<th colspan="7">Results</th>
				<th rowspan="2">UA-String</th>
			</tr>
			<tr>
				<th>Name</th>
				<th>Version</th>
				<th>Engine</th>
				<th>Version</th>
				<th>OS</th>
				<th>Version</th>
				<th>Type</th>
			</tr>

<?php

include('../lib/base.php');
include('../lib/browser.php');

$properties = array('name', 'version', 'engine', 'engineversion', 'platform', 'platformversion', 'platformtype');

foreach($ua_tests as $title => $test){
	if(!isset($test['ua'])){
		$test['ua'] = '';
	}
	$uas = (is_array($test['ua'])) ? $test['ua'] : array($test['ua']);
	echo '<tr>';
	echo '<th rowspan="'.count($uas).'" id="'.preg_replace('/[^(\x20-\x7F)\x0A]*/','', $title).'"><a href="#'.preg_replace('/[^(\x20-\x7F)\x0A]*/','', $title).'">'.$title.'</a></th>';
	foreach($uas as $ua){
		if($ua != $uas[0]){
			echo '<tr>';
		}
		$browser = new Browser($ua);
		foreach($properties as $property){
			if(!isset($test['test'][$property]) || $test['test'][$property] == ''){
				if(empty($browser->$property)){
					$message = 'Not tested, not found';
				}
				else{
					$message = 'Not tested, found <b>'.$browser->$property.'</b>';
				}
				echo '<td bgcolor="#EEEEEE">'.$message.'</td>';
			}
			elseif(strtolower($test['test'][$property]) == strtolower($browser->$property)){
				echo '<td bgcolor="#00FF00">Pass ('.$browser->$property.')</td>';
			}
			else{
				echo '<td bgcolor="#FF0000">Fail (Expected <b>'.$test['test'][$property].'</b>, found <b>'.$browser->$property.'</b>)</td>';
			}
		}
		echo '<td>'.$ua.'</td>';
		echo '</tr>';
	}
}

?>

		</table>
	</body>
</html>