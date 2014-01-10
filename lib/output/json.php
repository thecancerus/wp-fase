<?php
/**
 * SImple JSON output.
 */
class output_json extends fase_wp_reports {
	public function __construct($parser) {
		//parent::__construct($parser);
		$this->parser = $parser;
	}

	function assemble() {
		print_r(json_encode($this->parser));
		return;
	}
}