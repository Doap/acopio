<?php

	$hash = $_GET['hash'];
	$output = @file_get_contents('http://gravatar.com/' . $hash . '.json?callback=Gravatar.fetch_profile_callback');
	
	if($output != '') {
		@header("Content-type: text/javascript;charset=utf-8");
		echo $output;
	}
	else {
		@header("HTTP/1.1 404 Not Found");
		@header("Status: 404 Not Found");
		echo 'Gravatar.fetch_profile_callback("User not found");';
	}
				
?>
