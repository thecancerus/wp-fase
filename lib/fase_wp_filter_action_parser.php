<?php
/**
 *	Expects array of arrays in this format:
 *		'dir' => $dir,
 *		'file' => $file,
 *		'fullpath' => $dir . '/' . $file,
 */
class fase_wp_filter_action_parser {

	private $file_list;
	private $parsed_files;
	private $tokens;
	private $strings_to_parse = array(
		'apply_filters', 
		'do_action', 
		'do_action_ref_array',
	);
	private $options;

	public $processed_files; // Holder for files that were found, separated by file.
	public $processed_finds; // Holder for actouns/filters that were found, separated by action/filter type.

	// Do a little setup
	function __construct($file_list = null, $options = array()) {
		$this->file_list = $file_list;
		
		$defaults = array(
			'verbose' => true,
			'format' => 'html'
		);
		$this->options = array_merge($defaults, $options);
	}

	/**
	 * Iterate files, parse them, trigger reports
	 */
	function parse_file_list() {
		if (is_array($this->file_list)) {
			foreach ($this->file_list as $file) {
				$this->parse_file($file);
			}
		}
		$this->assemble_reports($this->options['format']);
	}

	/**
	 * Pull out gathered information and output reports.
	 */ 
	function assemble_reports($type = 'html') {

		$name = 'output_' . $type ;
		$report = new $name($this);

		echo $report->get_output();

	}

	/**
	 * Parse an indvidual file
	 */
	function parse_file($file_name) {
		// Check if file exists
		if (!file_exists($file_name['fullpath'])) {
			show("FILE NOT FOUND: " . $file_name['fullpath']);
			// TODO: error processing here
			return;
		}

		// Read contents of file
		show("PROCESSING: " . $file_name['fullpath']);
	
		$file_contents = file_get_contents($file_name['fullpath']);
		$file_lines = preg_split ('/$\R?^/m', $file_contents);

		// Split file contents into PHP tokens
		$this->tokens = token_get_all($file_contents);

		// Iterate processed tokens
		foreach ($this->tokens as $key => $token) {
			if (is_array($token) && ($token[0] == T_STRING)) {
				if (in_array($token[1], $this->strings_to_parse)) {
					if ($this->is_function_definition($key) == false) {
						$find = $this->process_raw_find($key);
						$processor = 'processor_'.$token[1];
						if (method_exists($this, $processor)) {
							$this->$processor($token, $find, $file_name);
						}
						else {
							echo "ERROR: PROCESSOR $processor DOES NOT EXIST.\n";
						}
					}
				}
			}
		}
	}

	/**
	 * If a match is found, process that match
	 */
	function process_raw_find($key) {
		// What did we find?
		$find_type = $this->tokens[$key][1];

		// Search back for docblock
		$docblock = $this->process_raw_docblock($key);

		// Gather parameters
		$parameters = array();

		// find next "("
		while ($key < count($this->tokens)) {
			if ($this->tokens[$key] == '(') {
				$key++;
				break;
			}
			$key++;
		}
		$paren_count = 1;
		// The closing parenthesis is the delimiter, but they might be nested...
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
			// End of logical line or function call...
			if ($this->tokens[$key] == ';' || $this->tokens[$key] == ')') {
				break;
			}
			$key++;
		}
		return array('parameters' => $parameters, 'docblock' => $docblock);
	}

	/**
	 * Find parameters by key, noting that parentheses can be nested
	 */
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


	/**
	 * Find docblock info based on the key, return raw contents
	 */
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


	/**
	 * See if the given key is a function definition
	 */
	function is_function_definition($key) {
		while ($key > 0) {
			// Ignore  whitespace
			if (is_array($this->tokens[$key]) && ($this->tokens[$key][0] == T_WHITESPACE)) {
				$key--;
				continue;
			}
			// Is it T_FUNCTION? Yes, then we've got a function definition
			if (is_array($this->tokens[$key]) && ($this->tokens[$key][0] == T_FUNCTION)) {
				return true;
			} 
			// Anything else it's not a function definition
			if (! is_array($this->tokens[$key])) {
				return false;
			}
			$key--;
		}
		return false;
	}


	/**
	 * Convert quoted strings with no "$" in them to non-quoted strings
	 * If there is a "$" present, convert all single quotes to double quotes for consistency
	 */
	function normalize_tag_names($tag) {

		if (strpos($tag, '$') === false) {
			$tag = str_replace(array('"', "'"), '', $tag);
		} else {
			$tag = str_replace(array("'"), '"', $tag);
		}
		return $tag;
	}


	// http://codex.wordpress.org/Function_Reference/apply_filters
	function processor_apply_filters($token, $find, $file_name) {

		$tag = $this->normalize_tag_names(array_shift($find['parameters']));
		$value = array_shift($find['parameters']);
		$vars = $find['parameters'];

		$this->processed_files[$file_name['fullpath']][$token[2]] = array(
			'token' => $token,
			'type' => 'apply_filters',
			'hook' => $tag,
			'value_modified' => $value,
			'optional_vars' => $vars,
			'data' => $find,
		);

		$this->processed_finds['apply_filters'][$tag][] = array(
			'token' => $token,
			'file' => $file_name,
			'hook' => $tag,
			'value_modified' => $value,
			'optional_vars' => $vars,
			'data' => $find,
		);

		//$message = "- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - \n";
		//$message .= $file_name['fullpath'] . ' on line ' . $token[2] . "\n";
		//$message .= "apply_filter\n";
		//$message .= "  Hook: $tag\n";
		//$message .= "  Value to modify: $value\n";
		//if (count($vars) > 0) {
		//	foreach ($vars as $var) {
		//		$message .= "    - optional var: " . $var;
		//		$message .= "\n";
		//	}
		//}
		//echo $message;

		return;
	}

	// http://codex.wordpress.org/Function_Reference/do_action
	function processor_do_action($token, $find, $file_name) {
		$tag = $this->normalize_tag_names(array_shift($find['parameters']));
		//$value = array_shift($find['parameters']);
		$vars = $find['parameters'];

		$this->processed_files[$file_name['fullpath']][$token[2]] = array(
			'token' => $token,
			'type' => 'do_action',
			'hook' => $tag,
			'optional_vars' => $vars,
			'data' => $find,
		);

		$this->processed_finds['do_action'][$tag][] = array(
			'token' => $token,
			'file' => $file_name,
			'hook' => $tag,
			'optional_vars' => $vars,
			'data' => $find,
		);

		//$message = "- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - \n";
		//$message .= $file_name['fullpath'] . ' on line ' . $token[2] . "\n";
		//$message .= "do_action\n";
		//$message .= "  Hook: $tag\n";
		//if (count($vars) > 0) {
		//	foreach ($vars as $var) {
		//		$message .= "    - argument: " . $var;
		//		$message .= "\n";
		//	}
		//}
		//echo $message;

		return;
	}

	// http://codex.wordpress.org/Function_Reference/do_action_ref_array
	function processor_do_action_ref_array($token, $find, $file_name) {
		$tag = $this->normalize_tag_names(array_shift($find['parameters']));
		$arguments = $find['parameters'];

		$this->processed_files[$file_name['fullpath']][$token[2]] = array(
			'token' => $token,
			'type' => 'do_action_ref_array',
			'hook' => $tag,
			'optional_vars' => $arguments,
			'data' => $find,
		);

		$this->processed_finds['do_action_ref_array'][$tag][] = array(
			'token' => $token,
			'file' => $file_name,
			'hook' => $tag,
			'optional_vars' => $arguments,
			'data' => $find,
		);

		//$message = "- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - \n";
		//$message .= $file_name['fullpath'] . ' on line ' . $token[2] . "\n";
		//$message .= "do_action_ref_array\n";
		//$message .= "  Hook: $tag\n";
		//$message .= "  Argument array:\n";
		//if (count($arguments) > 0) {
		//	foreach ($arguments as $var) {
		//		$message .= "    - array: " . $var;
		//		$message .= "\n";
		//	}
		//}
		//echo $message;

		return;
	}

}