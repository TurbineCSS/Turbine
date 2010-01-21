<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>CSS to CSSP Converter</title>
	<style type="text/css">
		html, body, table { margin:0; padding:0; height:100%; }
		textarea { display:block; }
	</style>
</head>
<body>

<?php
	require('parser.php');
	require('converter.php');
?>

<form action="index.php" method="post">
<table>
	<tr>
		<td valign="top">
			<h2>CSS</h2>
<textarea cols="120" rows="50" name="css">
<?php if(isset($_POST['css'])): echo stripslashes($_POST['css']); else: ?>
/**
 * Screen-Styles
 * 
 * EnthÃ¤lt alle Styles, die alle Desktop-Browser betreffen. Bugfixes
 * fÃ¼r die Internet Explorer befinden sich in den entsprechenden Sektionen.
 * 
 * @section screen
 * @media screen, projection
 * 
 */
@media screen, projection {


	/**
	 * Allgemeines
	 * @subsection screen-basics
	 */
	body { color:#261D1D; background:#F0EFE1; font-family:Verdana, Arial, sans-serif; }
	strong, b { font-weight:bold; }
	em, i { font-style:italic; }
		strong em, em strong { font-weight:bold; font-style:italic; }
	q { font-style:italic; }
		q:before { content:"\201E"; }
		q:after { content:"\201C"; }
			q q:before { content:"\201A"; }
			q q:after { content:"\2018"; }
	q em, blockquote p em { font-style:normal; }
	del, strike { text-decoration:line-through; }
	abbr { border-bottom:1px dotted #3B2A2A; cursor:help; }


	/**
	 * Layout
	 * @subsection screen-layout
	 */
	#wrapper { background:url(img/wrapperbg.png) top center repeat-y; }
	#wrapperhead { background:url(img/header.png) top center no-repeat; }
	#wrapperfoot { background:url(img/footer.png) bottom center no-repeat; }
	#header { width:960px; height:288px; position:relative; margin:0 auto 16px auto; }
	#content { width:960px; margin:0 auto; }
	#main { width:592px; float:left; }
	#side { width:288px; float:right; }
	#footer { clear:both; width:960px; margin:0 auto; }
	div.postcontainer { background:#FFF url(img/section.png) center bottom no-repeat; margin:0 16px; padding:16px 0 64px 0; }
	div.section { background:#FFF url(img/section.png) center bottom no-repeat; height:48px; margin:8px 0; }
	div.cols { background:url(img/cols.png) top center repeat-y; margin:16px 0; }
		div.cols div.col1 { width:256px; float:left; margin:0; }
		div.cols div.col2 { width:256px; float:left; margin:0 80px; }
		div.cols div.col3 { width:256px; float:right; margin:0; }
	div.cols2 { background:url(img/cols2.png) top center repeat-y; margin:16px 0; }
		div.cols2 div.col1 { width:420px; float:left; margin:0; }
		div.cols2 div.col2 { width:420px; float:right; margin:0; }
	div.tools { background:url(img/cols.png) 280px 0 repeat-y; }
	div.tool1 { width:240px; float:left; }
	div.tool2 { width:240px; float:right; }


	/**
	 * Headline
	 * @subsection screen-headline
	 */
	h1 a { display:block; height:100px; width:384px; position:relative; top:120px; left:0; }
		h1 a:link, h1 a:visited, h1 a:active, h1 a:hover { border:none; background:none; }
		h1 a span { width:0; height:0; display:block; overflow:hidden; }


	/**
	 * Breadcrumbs
	 * @subsection screen-breadcrumbs
	 */
	#breadcrumb { width:850px; position:absolute; top:32px; left:24px; }
		#breadcrumb p { font-size:11px; line-height:2.0; margin:16px 0 0 0; }


	/**
	 * Suche im Header
	 * @subsection screen-headersearch
	 */
	#headersearch { color:#FFF; position:absolute; top:140px; right:0; width:288px; padding:8px 16px; }
		#headersearch h2 { font-family:Verdana, Arial, sans-serif; font-size:12px; font-weight:bold; line-height:1; background:none; text-align:left; margin:0 0 -8px 0; padding:0; }
	#suchbox { font-size:12px; width:64%; float:left; }
	#suchsubmit { font-size:12px; font-weight:bold; background:#912121; color:#EEE; border:1px solid #000; width:28%; float:right; }
		#suchsubmit:hover { background:#801A1A; color:#FFF; }
		#suchsubmit:focus { background:#801A1A; color:#FFF; padding:10px 4px 6px 4px; }



	/**
	 * Textgestaltung
	 * @subsection screen-text
	 */
	p, ol, ul, dl { font-size:14px; line-height:1.8; margin:16px 0; }
		ol li, ul li { margin:8px 0; padding-left:4px; }
	ol { list-style-type:decimal; margin-left:48px; padding:0; }
	ul { margin-left:16px; padding:0; }
		ul li { background:url(img/bullet.png) 8px 9px no-repeat; padding-left:24px; }
		ul.iconlist, ul.iconlist li { background:none; margin-left:0; padding-left:0; }
	h2, h3 { font-weight:normal; font-family:"Times New Roman", Times, serif; text-align:center; margin:16px 0; }
		h2 { background:url(img/headline.png) center bottom no-repeat; font-size:30px; line-height:1.25; margin:20px 0; padding:0 0 20px 0; }
			#side h2, h3 { font-size:26px; margin:16px 0; }
		h3 { line-height:1.25; margin:32px 0 16px 0; padding:0; }
			#side h3 { font-size:21px; }
		h4 { font-size:14px; font-weight:bold; text-align:left; }
	blockquote { border-left:8px solid #EDE9E6; font-style:italic; margin:8px 0 8px 16px; padding-left:32px; }
	pre { font-size:14px; color:#0B1E45; font-family:monospace; line-height:1.5em; overflow:auto; padding:18px 0; }
	code { color:#0B1E45; background:#F1F3F5; font-family:monospace; padding:1px; }
		pre code { background:none; padding:0; }
	dl dt { font-weight:bold; margin:4px 0; }
	dl dd { margin:4px 0 4px 24px; }
	table { font-size:14px; line-height:1.8; margin:16px 0; }
		table th, table td { text-align:left; border:1px solid #EDE9E6; padding:4px 8px; } 
		table th { font-weight:bold; color:#FFF; background:#902020; }


	/**
	 * FAQ
	 * @subsection screen-faq
	 */
	dl.faq dt { float:none; width:100%; font-weight:bold; margin-bottom:0; }
	dl.faq dd { float:none; width:100%; margin-bottom:24px; }


	/**
	 * Links
	 * @subsection screen-links
	 */
	a { text-decoration:none; border-bottom:1px solid #E0DAD5; padding:1px 0; }
		a:link { color:#941919; border-color:#E0DAD5; }
		a:visited { color:#942323; border-color:#E0DAD5; border-bottom-style:dotted; }
		a:hover { color:#000; border-color:#FFF; }
		a:active { color:#FFF; background:#942323; border-color:#942323; }
		a:focus { outline:1px dotted #942323; }
			a[href^="http://"]:after, a[href^="https://"]:after { content:"\00A0â†’"; }
				a:link:after { color:#ABA391; border-bottom:3px solid #FFF; }
				a:visited:after { color:#B0A999; border-bottom:3px solid #FFF; }
				a:hover:after { color:#941919; border-bottom:none; }
				a:active:after { color:#FFF; border-bottom:none; }
			a[href^="http://www.peterkroener.de"]:after, a[href^="http://dev.peterkroener.de"]:after { content:""; border-bottom:none; }
	a.toggled { border-bottom:1px solid transparent; color:#000; }


	/**
	 * Navi
	 * @subsection screen-navi
	 */
	#navi { position:absolute; width:960px; bottom:-2px; margin:0; padding:0; }
		#navi li { background:none; margin:0; padding:0; }
			#navi li a { border:none; background:none; width:120px; height:48px; display:block; position:relative; float:left; }
				#navi li a span { cursor:pointer; font-family:"Times New Roman", Times, serif; font-size:20px; position:absolute; top:8px; left:18px; }
					#navi li a:link span, #navi li a:visited span { color:#C89090; }
					#navi li a:hover span, #navi li a:active span, #navi li a:focus span { color:#FFF; background:none; }
			#navi li.active a { background:url(img/tab.png) top left no-repeat; }
				#navi li.active a:link span, #navi li.active a:visited span { color:#000; }
				#navi li.active a:hover span, #navi li.active a:active span, #navi li.active a:focus span { color:#912121; background:none; }


	/**
	 * Formulare
	 * @subsection screen-forms
	 */
	label { font-size:12px; display:block; clear:both; }
		label span { color:#801A1A; font-weight:bold; }
	span.tiplink { float:right; cursor:help; }
		span.tiplink span { font-size:11px; color:#261D1D; border-bottom:1px dotted #261D1D; }
	input, textarea, select, button { font-size:13px; line-height:1.5; font-family:Verdana, Arial, sans-serif; background:#FAF9F7; border:1px solid #DBD7CE; width:98%; padding:8px 4px; }
		input:hover, textarea:hover, select:hover { background:#FFF; border-color:#B3B0AB; }
		input:focus, textarea:focus, select:focus { background:#FFF; border-color:#000; }
		input[type = submit], input[type = button], button { cursor:pointer; }
		input[type = checkbox], input[type = radio] { cursor:pointer; border:none; padding:0; }
	p.subscribe-to-comments label { display:inline; margin:0 0 0 8px; }
	fieldset .fl, fieldset .fr { width:50%; }
	#semi_live_preview_button { font-weight:bold; background:#292826; color:#EEE; border:1px solid #000; }
		#semi_live_preview_button:hover { background:#1A1918; color:#FFF; }
		#semi_live_preview_button:focus { background:#1A1918; color:#FFF; padding:10px 4px 6px 4px; }
	#submit { font-weight:bold; background:#912121; color:#EEE; border:1px solid #000; }
		#submit:hover { background:#801A1A; color:#FFF; }
		#submit:focus { background:#801A1A; color:#FFF; padding:10px 4px 6px 4px; }
	#semi_live_preview_headline { font-size:14px; font-family:Verdana, Arial, sans-serif; font-weight:bold; text-align:left; }


	/**
	 * Tooltip
	 * @subsection screen-tooltip
	 */
	div.tooltip { background:#FFF; border:1px solid #DBD7CE; width:400px; }
		div.tooltip div.tip-text p { font-size:11px; margin:12px; }


	/**
	 * Tagcloud
	 * @subsection screen-tagcloud
	 */
	#themen { padding-bottom:16px; }
	#tagcontrol { font-size:11px; margin:16px 0; }
		#tagcontrol img.icon { top:4px; }
	div.postcontainer p.catcloud { line-height:2.5; }
		div.postcontainer p.catcloud a { margin:2px; }
	#catlist li { float:left; width:45%; }


	/**
	 * Wolfgang-SchÃ¤uble-Gimmick
	 * @subsection screen-wolle
	 */
	#wolle { background:url(img/wolle.png) bottom center no-repeat; position:relative; padding:10px 20px 160px 20px; }
		#wolle p { font-size:12px; }
		#wolle-top { background:url(img/wolle.png) top center no-repeat; position:absolute; top:0; left:0; width:256px; height:16px; }


	/**
	 * Propaganda-Box
	 * @subsection screen-propaganda
	 */
	#propaganda { text-align:center; }
		#propaganda a { border:none; }
			#propaganda a:after { content:''; }


	/**
	 * Footer
	 * @subsection screen-footer
	 */
	#footer { margin:0 auto; padding:48px 0 128px 0; }
		#footer p { font-size:11px; font-family:Verdana, Arial, sans-serif; text-align:center; margin:0 auto; }


	/**
	 * Referenzen
	 * @subsection screen-referenzen
	 */
	a.referenz { border:2px solid #D4CFC3; height:202px; display:block; padding:1px; }
		a.referenz:link, a.referenz:visited { background:#FFF; border-color:#D4CFC3; }
		a.referenz:hover, a.referenz:active { background:#FFF; border-color:#942323; }
		a.referenz:after { content:""; }
	span.referenz { border:1px dotted #D4CFC3; display:block; padding:2px; }
		a.referenz img, span.referenz img { background:url(img/refbg.png) center center no-repeat; border:1px solid #F8F6F4; display:block; }
		a.referenz canvas, span.referenz canvas { background:url(pics/canvas.png) center center no-repeat; border:1px solid #F8F6F4; display:block; }
	ul.refshort { margin-left:0; }


	/**
	 * Mini-Navigation
	 * @subsection screen-mininav
	 */
	#mininav { position:fixed; margin:80px 0 0 1024px; }
		#mininav li { background:none; margin:12px 0; padding:0; }
		#mininav a:link, #mininav a:visited, #mininav a:hover, #mininav a:active { background:url(img/icons/mininavi.png); border:none; display:block; height:16px; width:16px; padding:0; }
			#mininav a span { display:block; height:0; width:0; overflow:hidden; }
	#mininav #mini-start { background-position:0 0; }
	#mininav #mini-up { background-position:0 -16px; }
	#mininav #mini-mobile { background-position:0 -32px; }
	#mininav #mini-newsfeed { background-position:0 -48px; }
	#mininav #mini-commfeed { background-position:0 -64px; }
	#mininav #mini-contact { background-position:0 -80px; }
	#mininav #mini-key { background-position:0 -96px; }


	/**
	 * Semi Live Preview (fÃ¼r die Anti-JS-Fraktion alles am Start verstecken)
	 * @subsection screen-semilivepreview
	 */
	#semi_live_preview_button { display:none; }
	#semi_live_preview_headline { display:none; clear:both; }
	#semi_live_preview { display:none; padding-bottom:8px; }
		#semi_live_preview pre { width:540px; } /* Verhindert das Expandieren des Preview-Divs bei normalen Code-BlÃ¶cken */
		#semi_live_preview blockquote pre { width:480px; } /* Verhindert das Expandieren des Preview-Divs bei normalen Code-BlÃ¶cken in Blockquotes */
		html>/**/body #semi_live_preview_wrapper > div, x:-moz-any-link, x:default { overflow:hidden !important; padding-bottom:20px; } /* Verhindert Flackern beim Einfahren der Preview im Firefox */


	/**
	 * Styles fÃ¼r die Startseite
	 * @subsection screen-index
	 */
	img.floatbild { border:1px solid #EDE9E6; float:right; display:block; height:160px; width:296px; margin:0 0 0 64px; }



	/**
	 * Riesige Box
	 * @subsection screen-giantbox
	 */
	.bigbox { background:url(img/bigbox.png) top left repeat-y; position:relative; clear:both; padding:24px 32px; margin:12px 0; }
		.bigbox .head { background:url(img/bigbox-head-foot.png) top left no-repeat; width:928px; height:28px; position:absolute; top:0; left:0; }
		.bigbox .foot { background:url(img/bigbox-head-foot.png) bottom left no-repeat; width:928px; height:16px; position:absolute; bottom:0; left:0; }


	/**
	 * GroÃŸe Box
	 * @subsection screen-bigbox
	 */
	.box { background:url(img/box.png) top left repeat-y; position:relative; clear:both; padding:24px 32px; margin:12px 0; }
		.box .head { background:url(img/box-head-foot.png) top left no-repeat; width:560px; height:28px; position:absolute; top:0; left:0; }
		.box .foot { background:url(img/box-head-foot.png) bottom left no-repeat; width:560px; height:16px; position:absolute; bottom:0; left:0; }


	/**
	 * Kleine Box
	 * @subsection screen-smallbox
	 */
	.box-klein { background:url(img/boxklein.png); position:relative; width:216px; padding:24px 32px; }
		.box-klein .head { background:url(img/boxklein-head-foot.png) top left no-repeat; width:280px; height:28px; position:absolute; top:0; left:0; }
		.box-klein .foot { background:url(img/boxklein-head-foot.png) bottom left no-repeat; width:280px; height:16px; position:absolute; bottom:0; left:0; }
	.bild-links { float:left; margin:8px 24px 8px 0; }
	.bild-rechts { float:right; margin:8px 0 8px 16px; }


	/**
	 * Bildbox
	 * @subsection screen-bildbox
	 */
	.bild, .bild p { text-align:center; font-size:11px; line-height:2.0; }
		.bild a  { border:none; background:none; }
		.bild a img { display:block; border:2px solid #D4CFC3; margin:0 auto; padding:2px; }
			.bild a:link img, .bild a:visited img { background:#FFF; border-color:#D4CFC3; }
			.bild a:hover img, .bild a:active img { background:#FFF; border-color:#942323; }
	.bild .half { width:240px; }


	/**
	 * Kommentare
	 * @subsection screen-comments
	 */
	div.comment { position:relative; }
		div.comment span.num { color:#F5F2EB; font-size:180px; font-family:"Georgia", "Times New Roman", Times, serif; position:absolute; top:0; right:40px; }
		div.comment div.text { position:relative; z-index:1; }
		div.comment h4 { font-size:20px; line-height:1.75; font-family:"Times New Roman", Times, serif; font-weight:normal; position:relative; z-index:1337; margin:16px 0 4px 0; padding-left:80px; }
		div.comment p.datetime { font-size:11px; padding-left:80px; margin-top:0; }
		div.comment img.avatar { border:1px solid #F2EEE6; position:absolute; top:0; left:0; padding:3px; }
	p.quote_comments { font-size:11px; text-align:right; }


	/**
	 * Blogpost-Style
	 * @subsection screen-blogpoststyle
	 */
	div.postheader { position:relative; padding:32px 0 0 0; }
	div.postheader p { background:#FFF url(img/postheader.png) center center no-repeat; font-size:11px; font-family:Verdana, Arial, sans-serif; width:100%; position:absolute; top:8px; left:0; margin:8px 0; }
		div.postheader p img.icon { top:4px; }
	div.postfoot { margin:16px 0; }
		div.postfoot p { font-size:11px; line-height:2.5; text-align:center; margin:8px; }
	#postnav { text-align:center; color:#666; }


	/**
	 * Meldungen
	 * @subsection screen-messages
	 */
	#fixfooter { width:100%; position:fixed; bottom:0; }
	#meldungen { width:1024px; position:relative; margin:0 auto; }
	p.meldung { font-size:11px; cursor:pointer; border:1px solid #000; background:#902020 url(img/icons/cross_circle_frame.png) 5% 50% no-repeat; color:#FFF; width:208px; position:absolute; top:-128px; right:48px; padding:8px 8px 8px 40px; }


	/**
	 * Suchergebnis-Highlighter
	 * @subsection screen-search-highlight
	*/
	.searchresult b { background:#FCFFAD; }
	.searchfoot { margin-top:-12px; margin-bottom:48px; }
	.searchresult a:after, .searchfoot a:after { content:''; }
	.searchresult a:hover b, .searchresult a:active b, .searchresult a:focus b { background:none; }


	/*
	 * Dieser-Post-Ist-Alt-Meldung
	 * @subsection screen-oldpost
	 */
	div.oldpost { background:#fff286; border:2px solid #3B2A2A; }
		div.oldpost p { background:url(img/icons/clock.png) center left no-repeat; font-size:11px; padding:0 16px 0 44px; margin-left:20px; }


	/*
	 * Generische Warnung
	 * @subsection screen-warning
	 */
	div.warning { background:#fff286; border:2px solid #3B2A2A; }
		div.warning h2 { background:none; padding-bottom:0; }
		div.warning p { font-size:11px; padding:0 16px; }
		#side div.warning {  margin-top:16px; }


	/**
	 * Code-Highlighting
	 * @subsection screen-highlight
	 */
	pre.pepeLighter { color: #261D1D; font-family: monospace; font-size: 14px; line-height: 1.5em; overflow: auto; }
	pre.pepeLighter { padding-left: 0px; padding-right: 0px; padding-top: 12px; padding-bottom: 12px; }
	pre.pepeLighter span { font-size: 14px; color: #261D1D; line-height: 1.5em; }
	pre.pepeLighter .de1 { color: #CF6A4C; font-weight: bold; }
	pre.pepeLighter .de2 { color: #CF6A4C; font-weight: bold; }
	pre.pepeLighter .kw1 { color: #000; font-weight: bold; }
	pre.pepeLighter .kw2 { color: #000; font-weight: bold; }
	pre.pepeLighter .kw3 { color: #562699; }
	pre.pepeLighter .kw4 { color: #990073; font-weight: bold; }
	pre.pepeLighter .co1 { color: #3D993D; font-style: italic; }
	pre.pepeLighter .co2 { color: #3D993D; font-style: italic; }
	pre.pepeLighter .st0 { color: #A61111; }
	pre.pepeLighter .st1 { color: #A61111; }
	pre.pepeLighter .st2 { color: #A61111; }
	pre.pepeLighter .nu0 { color: #195FA6; }
	pre.pepeLighter .me0 { color: #195FA6; }
	pre.pepeLighter .br0 { color: #000; }
	pre.pepeLighter .sy0 { color: #000; }
	pre.pepeLighter .es0 { color: #000; }
	pre.pepeLighter .re0 { color: #A60053; }


	/**
	 * Slimbox
	 * @subsection slimbox
	 */
	#lbOverlay { position:fixed; z-index:9999; left:0; top:0; width:100%; height:100%; background-color:#000; cursor:pointer; }
	#lbCenter, #lbBottomContainer { position:absolute; z-index:9999; overflow:hidden; background-color:#FFF; }
	.lbLoading { background:#FFF url(img/loading.gif) no-repeat center; }
	#lbImage { position:absolute; left:0; top:0; border:10px solid #FFF; background-repeat:no-repeat; }
	#lbPrevLink, #lbNextLink { border:none; display:block; position:absolute; top:0; width:50%; outline:none; }
	#lbPrevLink { left:0; }
	#lbPrevLink:hover { background:transparent url(img/prevlabel.png) no-repeat 0 15%; }
	#lbNextLink { right:0; }
	#lbNextLink:hover { background:transparent url(img/nextlabel.png) no-repeat 100% 15%; }
	#lbBottom { font-family: Verdana, Arial, sans-serif; font-size:12px; line-height:1.8; color:#261D1D; text-align:left; border:10px solid #FFF; border-top-style:none; }
	#lbCloseLink { display:block; float:right; width:92px; height:28px; background:transparent url(img/closelabel.png) no-repeat bottom left; margin:12px 4px; outline:none; border:none; }
		#lbCloseLink:hover { background-position:bottom right; }
	#lbCaption, #lbNumber { margin-right:80px; }
	#lbCaption { padding-top:8px; font-weight:bold; }


	/**
	 * Sonstige Klassen
	 * @subsection screen-classes
	 */
	a.more-link, a.back-link { font-weight:bold; font-size:16px; float:right; }
		a.more-link:before, a.back-link:before { content:"â†’\00A0"; border:8px solid #FFF; color:#000; background:#FFF; }
	.small { font-size:11px; line-height:2.0; }
	img.icon { margin-right:2px; position:relative; top:3px; }
	.clearfix:after { content:"."; display:block; width:100%; clear:both; height:0; visibility:hidden; }
	.clear { display:block; float:none; width:100%; clear:both; }
	.error { color:#911616; border-color:#911616; }
	.huge { font-size:2em; }
	.center { text-align:center; }
	.right { text-align:right; }
	.fl { float:left; }
	.fr { float:right; }


	/**
	 * Besonderheiten fÃ¼r's Wiki
	 * @subsection screen-wiki
	 */


		/*
		 * Ãœberschriften
		 * @subsubsection screen-wiki-headlines
		 */
		.wiki #content h1, .wiki #content h2 { font-weight:normal; font-family:"Times New Roman", Times, serif; text-align:center; margin:16px 0; }
			.wiki #content h1 { background:url(img/headline.png) center bottom no-repeat; font-size:30px; line-height:1.25; margin:20px 0; padding:0 0 20px 0; }
				.wiki #content h1 a, .wiki #content h2 a, .wiki #content h3 a { color:inherit; display:inline; position:static; border:none; }
			.wiki #content h2 { font-size:26px; background:none; clear:both; margin:32px 0 16px 0; padding:0; }
			.wiki #content h3 { font-size:21px; font-weight:bold; text-align:left; }


		/*
		 * Dekoration und Diverses
		 * @subsubsection print-screen-decoration
		 */
		.wiki hr { display:none; }
		.wiki div.centeralign { text-align:left; }
		.wiki div.pageinfo { background:#FFF url(img/postheader.png) center center no-repeat; text-align:center; font-size:11px; font-family:Verdana, Arial, sans-serif; width:100%; margin:12px 0; }
			.wiki div.pageinfo img.icon { top:4px; }
		.wiki #breadcrumb span.bchead { font-weight:bold; }
		.wiki pre a:after { content:none; }


		/*
		 * Suche
		 * @subsubsection screen-wiki-search
		 */
		.wiki #wikisearch { color:#FFF; position:absolute; top:140px; right:0; width:288px; padding:8px 16px; }
			.wiki #wikisearch h2 { font-family:Verdana, Arial, sans-serif; font-size:12px; font-weight:bold; line-height:1; background:none; text-align:left; margin:0 0 -24px 0; padding:0; }
			.wiki #wikisearch input { margin-top:16px; }
			.wiki #wikisearch #qsearch__in { font-size:12px; width:64%; float:left; }
			.wiki #wikisearch input.button { font-size:12px; font-weight:bold; background:#912121; color:#EEE; border:1px solid #000; width:28%; float:right; }
				.wiki #wikisearch input.button:hover { background:#801A1A; color:#FFF; }
				.wiki #wikisearch input.button:focus { background:#801A1A; color:#FFF; padding:10px 4px 6px 4px; }
		.wiki blockquote div.no { font-size:14px; line-height:1.8; margin:16px 0; }


		/*
		 * Formulare allgemein
		 * @subsubsection screen-wiki-forms
		 */
		.wiki legend { display:none; }
		.wiki label { text-align:left; }
			.wiki label span { color:inherit; font-weight:normal; }
			.wiki label input { margin:4px 0; }


		/*
		 * Anmelde-Formular
		 * @subsubsection screen-wiki-login
		 */
		.wiki #dw__login input.button { width:50%; float:right; margin:6px 0; }
		.wiki #dw__login label.simple { width:50%; float:left; }
			.wiki #dw__login label.simple input { display:inline; width:auto; }


		/*
		 * Namespace-Index
		 * @subsubsection screen-wiki-index
		 */
		.wiki #alphaindex span.curid { font-style:italic; }


		/*
		 * Edit-Formular
		 * @subsubsection screen-wiki-search
		 */
		.wiki #tool__bar button.toolbutton { width:auto; margin:0 4px 4px 0; }
		.wiki #wiki__editbar .editButtons input { font-weight:bold; background:#292826; color:#EEE; border:1px solid #000; width:25%; float:right; margin:0 4px 4px 0; }
			.wiki #wiki__editbar .editButtons input:hover { background:#1A1918; color:#FFF; }
			.wiki #wiki__editbar .editButtons input:focus { background:#1A1918; color:#FFF; padding:10px 4px 6px 4px; }
		.wiki #wiki__editbar .editButtons #edbtn__save { background:#912121; color:#EEE; border:1px solid #000; float:left; }
			.wiki #wiki__editbar .editButtons #edbtn__save:hover { background:#801A1A; color:#FFF; }
			.wiki #wiki__editbar .editButtons #edbtn__save:focus { background:#801A1A; color:#FFF; padding:10px 4px 6px 4px; }
		.wiki #wiki__editbar .editButtons #edbtn__preview { float:left; }


		/**
		 * Inhaltsverzeichnis (Wird nicht angezeigt)
		 * @subsection screen-wiki-toc
		 */
		.wiki div.toc { background:url(img/boxklein-head-foot.png) bottom left no-repeat; width:280px; padding-bottom:16px; float:right; margin:80px 0 0 32px; }
			.wiki div.toc .tocheader { font-size:0.8em; font-weight:bold; background:url(img/boxklein-head-foot.png) top left no-repeat; width:280px; height:8px; padding:20px 20px 0 20px; overflow:visible; }
			.wiki div.toc ul.toc { background:url(img/boxklein.png); margin:0; padding:4px 16px; width:240px; }
				.wiki div.toc ul.toc .toc { background:none; padding-bottom:0; width:auto; }


		/**
		 * Buttons
		 * @subsection screen-wiki-buttons
		 */
		.wiki form.button, .wiki a.nolink { float:left; border:none; padding:0; }
		.wiki input.button, .wiki form.button input, .wiki a.nolink input { width:auto; font-weight:bold; background:#912121; color:#EEE; border:1px solid #000; margin:0 0 0 4px; }
			.wiki input.button, .wiki form.button input:hover, .wiki a.nolink input:hover { background:#801A1A; color:#FFF; }
			.wiki input.button, .wiki form.button input:focus, .wiki a.nolink input:focus { background:#801A1A; color:#FFF; padding:10px 4px 6px 4px; }


	/**
	 * IE7-Hacks
	 * @subsection screen-ie7
	 * @bugfix
	 * @affected ie7
	 */
	#ie7 .clearfix { display:inline-block; }
	#ie7 #mininav { display:none; }
	#ie7 #navi li { display:inline; }
	#ie7 div.box { zoom:1; }
	#ie7 div.code pre { overflow:scroll; }
	#ie7 div.bild p { padding-top:4px; }
	#ie7 #footer { padding:16px 0 128px 0; }
	#ie7 div.comment p.datetime { margin-top:-16px; }
	#ie7 div.comment img.avatar { top:28px; }
	#ie7 div.postfoot p { padding:8px 0 0 0; }
	#ie7 #postnav { zoom:1; }


	/**
	 * IE6-Hacks
	 * @subsection screen-ie6
	 * @bugfix
	 * @affected ie6
	 */
	#ie6 a { text-decoration:none; border-bottom:1px solid #E0DAD5; padding:1px 0; }
		#ie6 a:link { color:#941919; border-width:1px; border-color:#E0DAD5; }
			#ie6 a:visited { color:#942323; border-width:1px; border-color:#E0DAD5; border-bottom-style:dotted; }
			#ie6 a:hover { color:#000; border-width:1px; border-color:#FFF; }
			#ie6 a:active { color:#FFF; border-width:1px; background:#942323; border-color:#942323; }
			#ie6 a:focus { outline:1px dotted #942323; border-width:1px; }
	#ie6 .clearfix { display:inline-block; }
	#ie6 #mininav { display:none; }
	#ie6 h1 a { border:none !important; background:none; }
	#ie6 #navi { bottom:-2px; }
		#ie6 #navi li { display:inline; }
			#ie6 #navi li a { border:none; background:none; }
			#ie6 #navi li.active a { background:none; filter: progid:DXImageTransform.Microsoft.AlphaImageLoader(src='/wp-content/themes/pk/img/tab.png', sizingMethod='crop'); }
	#ie6 div.box { zoom:1; }
	#ie6 div.code { overflow:scroll; width:480px; }
		#ie6 div.code pre { overflow:visible; }
	#ie6 #footer { padding:16px 0 128px 0; }
	#ie6 div.comment { width:416px; }
	#ie6 div.comment p.datetime { margin-top:-16px; }
	#ie6 div.comment img.avatar { position:static; float:left; margin-top:-76px; }
		#ie6 div.comment span.num { right:24px; }
	#ie6 .bild a img { position:relative; z-index:1337; }
	#ie6 #subscribe { border:none; background:none; }
	#ie6 div.postheader { zoom:1; padding-top:64px; }
	#ie6 ul.iconlist li { margin-top:10px; margin-bottom:10px; }
	#ie6 blockquote div.highlight { width:440px; }
	#ie6 img.icon { display:none; } /* Sorry, aber da mache ich keinen PNG-Fix. Wer heute noch mit dem 6er surft, der kann mich mal gern haben. */
	#ie6 #wolle-top { left:-20px; }
	#ie6.wiki a.interwiki { background:none !important; }
	#ie6.wiki div.bigbox { zoom:1; }
	#ie6.wiki div.code { width:864px; }
	#ie6 div.oldpost p { background:none; padding:0 16px; margin-left:0; }


}




/**
 * Print-Styles
 * 
 * EnthÃ¤lt alle Print-Styles.
 * 
 * @section print
 * @media print
 * 
 */
@media print {


	/**
	 * Allgemeines fÃ¼r Print
	 * @subsection print-basics
	 */
	body { background:#FFF; color:#261D1D; font-family:Helvetica, Arial, sans-serif; text-align:left; }
	.clear { clear:both; }
	#navi, #mininav, #footer, #breadcrumb, #postnav, 
	.quote_comments, .sprunglink, .commenttips, img.floatbild, .noprint, div.postfoot, img.icon, 
	fieldset,
	.no { display:none; }


	/**
	 * Textgestaltung fÃ¼r Print
	 * @subsection print-text
	 */
	p, ol, ul, dl { font-size:11pt; line-height:1.75; margin:12pt 0; }
		ol li, ul li { margin:4pt 0; padding-left:4pt; }
	h1, h2, h3 { font-weight:normal; font-family:"Times New Roman", Times, serif; page-break-after:avoid; }
		h1 { font-size:29pt; text-align:right; margin:24pt 0 64pt 0; padding:0; }
			h1 a { border:none !important; }
			h1 span.dash { display:none; }
			h1 span.description { display:block; font-size:18pt; font-style:italic; margin:8pt 0 12px 0; padding:0; }
		h2, .wiki h1 { text-align:left; font-size:22pt; line-height:1.25; margin:12pt 0; padding:0; }
		h3, .wiki h2 { font-size:18pt; line-height:1.25; margin:12pt 0; padding:0; }
		h4, .wiki h3 { font-size:15pt; line-height:1.25; font-weight:bold; margin:12pt 0; }
	ol { list-style-type:decimal; margin:12px 0; padding:0 0 0 24pt; }
	ul { list-style-type:square; margin:12px 0; padding:0 0 0 24pt; }
	blockquote { clear:both; border-left:8pt solid #EDE9E6; font-style:italic; margin:12pt 0 12pt 16pt; padding-left:16pt; }
	pre { font-size:11pt; color:#0B1E45; font-family:monospace; line-height:1.5; padding:12pt 0; }
	code { color:#0B1E45; background:#F1F3F5; font-family:monospace; padding:1pt; }
		pre code { background:none; padding:0; }
	dl dt { float:left; width:25%; clear:both; margin:8pt 0; }
	dl dd { float:right; width:75%; margin:8pt 0; }
	h2, h3, h4, h5, h6, li, dt, dd { page-break-inside:never; }
	p, pre { widows:4; orphans:4; }


	/**
	 * Kommentare fÃ¼r Print
	 * @subsection print-comments
	 */
	div.comment { clear:both; }
		div.comment h4 { page-break-after:avoid; padding:24pt 0 0 50pt; }
		div.comment span.num { display:none; }
		div.comment p.datetime { font-size:9pt; page-break-after:avoid; padding-left:50pt; }
		div.comment img.avatar { page-break-after:avoid; float:left; border:0.5pt solid #E0DAD5; margin:-58pt 16pt 0 0; }


	/**
	 * Blogpost-Style fÃ¼r Print
	 * @subsection print-blogpoststyle
	 */
	div.postheader p { font-size:11pt; font-family:"Times New Roman", Times, serif; font-style:italic; }
		div.postheader p a { display:none; }


	/**
	 * Bilder fÃ¼r Print
	 * @subsection print-images
	 */
	#werkzeugkasten img { display:block; border:none; }
	div.bild a { border:none; }


	/**
	 * Links fÃ¼r Print
	 * @subsection print-links
	 */
	a, a:link, a:visited, a:hover, a:active, a:focus { color:#912121; text-decoration:none; border-bottom:0.5pt solid #E0DAD5; padding:0.5pt 0; }
		a[href]:link:after, a[href]:link:visited:after { content:" (Link auf <" attr(href) ">) "; font-size:9pt; color: #999; font-weight:normal !important; font-family:Helvetica, Arial, sans-serif; }
		a[href^="/"]:link:after, a[href^="/"]:visited:after { content:" (Link auf <http://www.peterkroener.de " attr(href) ">) "; font-size:9pt; color: #999; font-weight:normal !important; font-family:Helvetica, Arial, sans-serif; }


	/**
	 * Sonstige Klassen fÃ¼r Print
	 * @subsection print-classes
	 */
	div.postcontainer { margin:32pt 0; }


}<?php endif; ?>
</textarea>
		</td>
		<td align="center"><input type="submit" value="&nbsp;&rarr;&nbsp;"></input></td>
		<td valign="top">
			<h2>CSSP</h2>
<textarea cols="120" rows="50" name="cssp">
<?php if(isset($_POST['css'])){ CsspConverter::factory()->load_string(stripslashes($_POST['css']))->parse()->convert(); } ?>
</textarea>
		</td>
	</tr>
</table>
</form>

</body>
</html>