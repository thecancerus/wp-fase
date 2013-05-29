<?php
/** 
 * Bootstrap file
 *
 * - Load utility functions
 * - Set autolaoder
 * - Instantiate extractor and run
 */

require_once('lib/fase_wp_functions.php');

function __autoload($class_name) {
	if (strpos($class_name, 'output_') !== false) {
		$class_name = str_replace('output_', 'output/', $class_name);
	}
	include 'lib/'.$class_name . '.php';
}

$extractor = new fase_wp_extractor();
$extractor->extract();
