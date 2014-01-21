<?php

/**
 * Functional extractor class
 */

class fase_wp_extractor {

	function __construct() {
		// Verify we're being called from CLI
		if (php_sapi_name() != 'cli') {
			die ("must be called from CLI");
		}
	}

	function extract() {
		// Parse and assign options
		$options_short = ''
			.'d:'	// Direcotry to traverse
			.'f:'	// File Types, comma seaparated. Defaults to 'php, inc'
			.'t:'	// Output file type (html, json, text) - defualt is html
			.'v'	// Verbose
			;
		$options = getopt($options_short);

		// Process $options['d']
		// Chop trailing / from directory
		if (isset($options['d']) && substr($options['d'], -1) == '/') {
			$options['d'] = substr($options['d'], 0 , -1);
		}

		// Set text to remove from file paths if not specified
		if (!isset($options['r'])) {
			$options['r'] = $options['d'];
		}

		// Make sure the directory exists and is specified
		if (isset($options['d']) && !is_dir($options['d'])) {
			die("ERROR: invalid directory specified\n");
		}
		if (!isset($options['d'])) {
			die("ERROR: no directory specified\n");
		}

		// Not used yet.
		if (!isset($options['o'])) {
			$output_file = 'fase-wp.txt';
		}

		// Define the type of output.
		if (!isset($options['t'])) {
			$options['t'] = 'html';
		}
		switch ($options['t']) {
			case 'json':
				$options['t'] = 'json';
			break;

			case 'text':
				$options['t'] = 'text';
			break;

			case 'html':
			default:
				$options['t'] = 'html';
			break;
		}

		// Process "verbosity"
		$verbose = (isset($options['v'])) ? true : false;

		// Process $options['f'] - get file types into an array with sensible defaults
		if (! isset($options['f'])) {
			show("using default file types");
			$options['f'] = array('php','inc');
		} 
		else {
			$options['f'] = explode(',', $options['f']);
		}

		// Messages
		show("Looking in file types: " . implode(', ', $options['f']));
		show("Traversing: " . $options['d']);

		$files = new fase_wp_file_list($options['d'], $options['f'], $verbose, $options['r']);
		$file_list = $files->get_files();

		//$file_list[] = array('dir' => 'fakedir', 'file' => 'fakefile.txt', 'fullpath' => 'fakedir/fakefile.txt');
			
		//var_dump($file_list);

		$parser = new fase_wp_filter_action_parser(
			$file_list, 
			array(
				'format' => $options['t'],
			)
		);
		$parser->parse_file_list();
	}
}