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
			$this->output .= "<h1>$file</h1>\n\n";

			ksort ($lines);
			foreach ($lines as $line_number => $data) {
				$this->output .= "<h2>$file: $line_number</h2>\n\n";
				$this->output .= "" . '<table border="1" cellspacing="0" cellpadding="3" width="100%">' . "\n";
				$this->output .= "<tr><td valign='top'>\n";
				//foreach ($data as $instance) {
					$this->output .= "<pre>";
					$this->output .= print_r($data, true);
					$this->output .= "</pre>";

					//$this->output .= 

					//$this->output .= "<p>{$instance['file']['fullpath']} ({$instance['token'][2]})</p>\n";
				//}
				$this->output .= "</td></tr>\n";
				$this->output .= "</table><br>\n\n";
			}
		}

		$this->output .= "\n</body>\n</html>\n";

	}

}