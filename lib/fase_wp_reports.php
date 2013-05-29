<?php
/**
 * Stub
 */
class fase_wp_reports {

	protected $output = '';
	private $parser;

	//public function __construct($parser) {
	//	$this->parser = $parser;
	//}

	public function get_output() {
		$this->assemble();
		return $this->output;
	}

}