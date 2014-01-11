<?php
/**
 * Plain text output
 */
class output_text extends fase_wp_reports {

	public function __construct($parser) {
		$this->parser = $parser;
	}

	function assemble() {

		ksort($this->parser->processed_finds);
		foreach ($this->parser->processed_finds as $type => $finds) {
			$this->output .= "TYPE: $type\n\n";

			ksort ($finds);
			foreach ($finds as $find_name => $instances) {
				$this->output .= "\n** NAME: $find_name\n\n";

				foreach ($instances as $instance) {
					$this->output .= "CALLED IN: '" . $instance['file']['fullpath'] . "' (" . $instance['token'][2] . ")\n";

					if (isset($instance['value_modified'])) {
						$this->output .= "VALUE MODIFIED: ". $instance['value_modified'] . "\n";
					}

					if (count($instance['optional_vars']) > 0) {
						$this->output .= "PARAMETERS\n";
						foreach ($instance['optional_vars'] as $var_name) {
							$this->output .= " - " . $var_name . "\n";
						}
					}

					if (isset($instance['data']['docblock'])) {
						
						$docblock = $instance['data']['docblock'];

						// Get rid of actual comment markup
						$docblock = str_replace('/**', '', $docblock);
						$docblock = str_replace('*/', '', $docblock);
						$docblock = preg_replace( '/^\s*\*/m', '', $docblock);

						$this->output .= "\nDOCBLOCK:\n";
						$this->output .= $docblock;
						$this->output .= "\n";
					}
				}
			}
		}

		ksort($this->parser->processed_files);
		foreach ($this->parser->processed_files as $file => $lines) {
			$this->output .= "** FILE: $file\n";

			ksort ($lines);
			foreach ($lines as $line_number => $instance) {
				$this->output .= "\n$line_number: " . $instance['type'] . ": '" . $instance['hook'] . "'\n";

				if (isset($instance['value_modified'])) {
					$this->output .= "VALUE MODIFIED ". htmlspecialchars($instance['value_modified']) . "\n";
				}

				if (count($instance['optional_vars']) > 0) {
					$this->output .= "PARAMETERS\n";
					foreach ($instance['optional_vars'] as $var_name) {
						$this->output .= " - " . htmlspecialchars($var_name) . "\n";
					}
				}

				if (isset($instance['data']['docblock'])) {
					
					$docblock = $instance['data']['docblock'];

					// Get rid of actual comment markup
					$docblock = str_replace('/**', '', $docblock);
					$docblock = str_replace('*/', '', $docblock);
					$docblock = preg_replace( '/^\s*\*/m', '', $docblock);

					$this->output .= "\nDOCBLOCK:\n";
					$this->output .= $docblock;
					$this->output .= "\n";
				}
			}
		}
	}

}