<?php
/**
 * @package fase-wp
 *
 * This file is part of the Capsule Theme for WordPress
 * https://github.com/crowdfavorite/fase-wp
 *
 * Copyright (c) 2013-2014 Crowd Favorite, Ltd. All rights reserved.
 * http://crowdfavorite.com
 *
 * **********************************************************************
 * This program is distributed in the hope that it will be useful, but
 * WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. 
 * **********************************************************************
 *
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
