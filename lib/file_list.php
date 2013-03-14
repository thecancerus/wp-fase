<?php
/**
 * Utility class to traverse a folder and all subfolders to get a list of files
 * matching the provided extentions
 */

class file_list {

	private $dir;
	private $file_extensions;
	private $file_list;


	function __construct($dir = null, $file_extensions = null, $verbose = true) {
		$this->dir = $dir;
		$this->file_extensions = $file_extensions;	
		$this->verbose = $verbose;
	}

	function get_file_list($dir = null, $count = 0) {
		if ($dir === null) {
			$dir = $this->dir;
		}
		$files = scandir($dir);
		foreach($files as $file) {
			if($file != '.' && $file != '..') {

				if (in_array(pathinfo($dir.'/'.$file, PATHINFO_EXTENSION), $this->file_extensions)) {
					if ($this->verbose == true) { echo "."; }
					$this->file_list[] = array(
						'dir' => $dir,
						'file' => $file,
						'fullpath' => $dir . '/' . $file,
					);
				}
				if(is_dir($dir.'/'.$file)) $this->get_file_list($dir.'/'.$file, $count + 1);
			}
		}
		if ($this->verbose == true && $count == 0) {
			echo "\n";
		}
	}

	function get_files() {
		if (empty($this->file_list)) {
			$this->get_file_list();
		}
		return $this->file_list;
	}

}