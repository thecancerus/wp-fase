<?php
/**
 * Stub
 */
class output_html extends fase_wp_reports {

	public function __construct($parser) {
		//parent::__construct($parser);
		$this->parser = $parser;
	}

	function assemble() {

		$this->output .= "<html>\n<head>\n\t<title>FASE-WP output</title>\n<body>\n";

		ksort($this->parser->processed_finds);
		foreach ($this->parser->processed_finds as $type => $finds) {
			$this->output .= "\t<h1>$type</h1>\n\n";

			ksort ($finds);
			foreach ($finds as $find_name => $instances) {
				$this->output .= "\t<h2>$find_name</h2>\n\n";
				$this->output .= "\t" . '<table border="1" cellspacing="0" cellpadding="3" width="100%">' . "\n";
				$this->output .= "\t\t<tr><td valign='top'>\n";
				foreach ($instances as $instance) {
					$this->output .= "\t\t\t<p>{$instance['file']['fullpath']} ({$instance['token'][2]})</p>\n";
					$this->output .= "<pre>";
					$this->output .= print_r($instance, true);
					$this->output .= "</pre>";
				}
				$this->output .= "\t\t</td></tr>\n";
				$this->output .= "\t</table><br>\n\n";
			}
		}

		ksort($this->parser->processed_files);
		foreach ($this->parser->processed_files as $file => $lines) {
			$this->output .= "\t<h1>$file</h1>\n\n";

			ksort ($lines);
			foreach ($lines as $line_number => $data) {
				$this->output .= "\t<h2>$file: $line_number</h2>\n\n";
				$this->output .= "\t" . '<table border="1" cellspacing="0" cellpadding="3" width="100%">' . "\n";
				$this->output .= "\t\t<tr><td valign='top'>\n";
				//foreach ($data as $instance) {
					$this->output .= "<pre>";
					$this->output .= print_r($data, true);
					$this->output .= "</pre>";

					//$this->output .= 

					//$this->output .= "\t\t\t<p>{$instance['file']['fullpath']} ({$instance['token'][2]})</p>\n";
				//}
				$this->output .= "\t\t</td></tr>\n";
				$this->output .= "\t</table><br>\n\n";
			}
		}

		$this->output .= "\n</body>\n</html>\n";

	}

}