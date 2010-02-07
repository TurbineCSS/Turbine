<?php
if(isset($_GET['cache']))
{
	$mhtmlfile = '../../lib/cssp_cache/'.preg_replace('/[^a-z0-9]/i','',preg_replace('/\!img[a-z0-9]+$/i','',$_GET['cache'])).'_mhtml.txt';
	$etag = md5($mhtmlfile.filemtime($mhtmlfile));

	if(@$_SERVER['HTTP_IF_NONE_MATCH'] === $etag) 
	{
		header('HTTP/1.1 304 Not Modified');
		exit();
	}
	
	header("Cache-Control: no-cache, must-revalidate");
	header("Expires: ".gmdate('D, d M Y H:i:s')." GMT");
	header("Content-type: text/plain"); 
	header("ETag: ".$etag);
	
	echo file_get_contents($mhtmlfile);
}
?>