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
 */

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
