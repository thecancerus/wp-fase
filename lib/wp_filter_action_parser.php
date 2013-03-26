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
			show("FILE NOT FOUND: " . $file_name['fullpath']);
			
			// **** Add error processing here ****
			
			return;
		}

		// Read contents of file
		//show("PROCESSING: " . $file_name['fullpath']);
	
		$file_contents = file_get_contents($file_name['fullpath']);
		$file_lines = preg_split ('/$\R?^/m', $file_contents);

		// Split file contents into PHP tokens
		$this->tokens = token_get_all($file_contents);

//print_r($this->tokens);return;

		// Iterate tokens
		foreach ($this->tokens as $key => $token) {
			if (is_array($token) && ($token[0] == T_STRING)) {
				if (in_array($token[1], $this->strings_to_parse)) {

					// TODO: check for function calls defining the string_to_parse
					if ($this->is_function_call($key) == false) {
						$parameters = $this->process_find($key);
						show("\n" . $file_name['fullpath']);
						show($token[1] . ' on line ' . $token[2] . " filter name: " . $parameters[0] );
					}
				}
			}
			//$this->tokens[$key][0] = token_name($token[0]);
		}
		echo "-------------------------------------\n";
		//print_r($this->tokens);
	}


	function process_find($key) {
		echo "-------------------------------------\n";

		// Search back for docblock
		$docblock = $this->process_raw_docblock($key);

		// Search forward for parameters
		$parameters = array();

		// gather array - find first "("
		while ($key < count($this->tokens)) {
			if ($this->tokens[$key] == '(') {
				$key++;
				break;
			}
			$key++;
		}
		$paren_count = 1;
		while ($key < count($this->tokens)) {

			if ($this->tokens[$key] == '(') {
				$paren_count++;
			}
			if ($this->tokens[$key] == ')') {
				$paren_count--;
			}

			// Add information until ")" count is zero
			if ($paren_count > 0) {
				$processed = $this->process_raw_parameters($key);
				// Add the parameter string
				$parameters[] = $processed[0];
				// Add the offset
				$key += $processed[1];
			}
			// End of logical line...
			if ($this->tokens[$key] == ';' || $this->tokens[$key] == ')') {
				break;
			}
			$key++;
		}
		print_r($docblock . "\n");
		print_r($parameters);
		return $parameters;
	}

	function process_raw_parameters($key) {
		$paren_count = 0;
		$parameter = '';
		$offset = 0;
		while ($key < count($this->tokens) && ($paren_count >= 0) ) {
			if ($this->tokens[$key] == ';'|| ($this->tokens[$key] == ')' && $paren_count == 0)) {
				break;
			}
			if ($this->tokens[$key] == '(') {
				$paren_count++;
			}
			if ($this->tokens[$key] == ')') {
				$paren_count--;
			}

			// Break on commas unless they're inside a paren
			if (($this->tokens[$key] != ',' || $paren_count > 0) ) {
				// Add anything that's a non-whitespace token
				if (is_array($this->tokens[$key]) && ($this->tokens[$key][0] !== T_WHITESPACE)) {
					$parameter .= $this->tokens[$key][1];
				} 
				else if (is_string($this->tokens[$key]) && (($this->tokens[$key] != ')' || $paren_count >= 0))) {
					$parameter .= $this->tokens[$key];
				}
			}
			else {
				break;
			}
			$key++;
			$offset++;
		}
		return array($parameter, $offset);
	}

	function process_raw_docblock ($key) {
		$docblock = null;

		while ($key > 0) {
			// Detect closing curle braces or ; which indicate code that's not related
			if ($this->tokens[$key] == ';' || $this->tokens[$key] == '}') {
				break;
			}

			if (is_array($this->tokens[$key]) && $this->tokens[$key][0] == T_DOC_COMMENT) {
				return $this->tokens[$key][1];
			}


			$key--;
		}


		return $docblock;

	}

	function is_function_call($key) {

		// Ignore  whitespace
		while ($key > 0) {
			if (is_array($this->tokens[$key]) && ($this->tokens[$key][0] == T_WHITESPACE)) {
				$key--;
				continue;
			}
			if (is_array($this->tokens[$key]) && ($this->tokens[$key][0] == T_FUNCTION)) {
				return true;
			} 
			if (! is_array($this->tokens[$key])) {
				return false;
			}
			$key--;
		}

	}

}