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
 * Simple JSON output.
 */
class output_json extends fase_wp_reports {

	public function __construct($parser) {
		$this->parser = $parser;
	}

	function assemble() {
		print_r(json_encode($this->parser));
		return;
	}
}