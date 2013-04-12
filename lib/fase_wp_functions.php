<?php

function show($message) {
	global $verbose;
	if ($verbose == true) {
		if (is_array($message) || is_object($message)) {
			print_r($message);
		} else {
			echo $message . "\n";
		}
	}
}
