<?php
if($handle = opendir('parser')){
	while(false !== ($file = readdir($handle))){
		if($file != '.' && $file != '..''){
			include($file);
		}
	}
	closedir($handle);
}
?>