<?php

function show($message) {
	global $verbose;
	if ($verbose == true) {
		echo $message . "\n";
	}
}
