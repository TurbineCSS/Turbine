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
	),
	'IE7 on Vista' => array(
	),
	'IE7 on WinXP' => array(
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


	// Gecko/Mozilla
	'Firefox 3.6 on Win7' => array(
		'ua' => '# Mozilla/5.0 (Windows; U; Windows NT 6.1; lt; rv:1.9.2) Gecko/20100115 Firefox/3.6',
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
			'version' => '1.923',
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
			'version' => '1.923',
			'engine' => 'gecko',
			'engineversion' => '1.92',
			'platform' => 'windows',
			'platformversion' => '5.1',
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
	'Chrome 5 on Win 7' => array(
		'ua' => 'Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US) AppleWebKit/533.3 (KHTML, like Gecko) Chrome/5.0.354.0 Safari/533.3',
		'test' => array(
			'name' => 'chrome',
			'version' => '5.0',
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
			'version' => '5.0',
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
			'version' => '5.0',
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
			'version' => '5.0',
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
			'version' => '5.0',
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
	)


	// Mobile browsers
	

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
	echo '<tr>';
	echo '<th id="'.preg_replace('/[^(\x20-\x7F)\x0A]*/','', $title).'"><a href="#'.preg_replace('/[^(\x20-\x7F)\x0A]*/','', $title).'">'.$title.'</a></th>';
	$browser = new Browser($test['ua']);
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
	echo '<td>'.$test['ua'].'</td>';
	echo '</tr>';
}

?>

		</table>
	</body>
</html>