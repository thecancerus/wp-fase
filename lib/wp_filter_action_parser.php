<?php
/**
 *	Expects array of arrays in this format:
 *		'dir' => $dir,
 *		'file' => $file,
 *		'fullpath' => $dir . '/' . $file,
 *
 */
class wp_filter_action_parser {

	private $file_list;
	private $parsed_files;
	private $verbose;
	private $tokens;
	private $strings_to_parse = array(
		'apply_filters', 
		'do_action', 
		'do_action_ref_array',
	);

	function __construct($file_list = null, $verbose = true) {
		$this->file_list = $file_list;
		$this->verbose = $verbose;
	}

	function parse_file_list() {
		if (is_array($this->file_list)) {
			foreach ($this->file_list as $file) {
				$this->parse_file($file);
			}
		}
	}

	function parse_file($file_name) {


		// Check if file exists
		if (!file_exists($file_name['fullpath'])) {
			if ($this->verbose) {
				echo "FILE NOT FOUND: " . $file_name['fullpath'] . "\n";
			} 
			
			// **** Add error processing here ****
			
			return;
		}

		// Read contents of file
		if ($this->verbose) {
			echo "PROCESSING: " . $file_name['fullpath'] . "\n";
		} 

		$file_contents = file_get_contents($file_name['fullpath']);
		$file_lines = preg_split ('/$\R?^/m', $file_contents);

		// Split file contents into PHP tokens
		$this->tokens = token_get_all($file_contents);

		// Iterate tokens
		foreach ($this->tokens as $key => $token) {
			if ($token[0] == T_STRING) {
				if (in_array($token[1], $this->strings_to_parse)) {
					echo "   " . $token[1] . ' on line ' . $token[2];
					echo " filter name: " . $this->get_filter_action_name($key);
					echo "\n";
				}
			}
			//$this->tokens[$key][0] = token_name($token[0]);
		}
		//print_r($this->tokens);
	}


	function get_filter_action_name($key) {
		while ($key <= count($this->tokens)) {
			if ($this->tokens[$key][0] != T_CONSTANT_ENCAPSED_STRING && $this->tokens[$key][0] != T_VARIABLE) {
				$key++;
			} else {
				return $this->tokens[$key][1];
			}
		}
	}


}