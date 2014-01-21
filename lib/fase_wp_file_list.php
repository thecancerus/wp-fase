<?php
/**
 * Utility class to traverse a folder and all subfolders to get a list of files
 * matching the provided extentions
 */
class fase_wp_file_list {

	private $dir;
	private $file_extensions;
	private $file_list;
	private $remove_text; // text to be removed from full paths for "friendly_name"

	function __construct($dir = null, $file_extensions = null, $verbose = true, $remove_text = '') {
		$this->dir = $dir;
		$this->file_extensions = $file_extensions;	
		$this->verbose = $verbose;
		$this->remove_text = $remove_text;
	}

	function get_file_list($dir = null, $count = 0) {
		if ($dir === null) {
			$dir = $this->dir;
		}
		$files = scandir($dir);
		foreach($files as $file) {
			if($file != '.' && $file != '..') {

				if (in_array(pathinfo($dir.'/'.$file, PATHINFO_EXTENSION), $this->file_extensions)) {
					//show("."); 
					$fullpath = $dir . '/' . $file;
					$replace_count = 1;
					$friendly_name = str_replace($this->remove_text, '', $fullpath, $replace_count);
					if (substr($friendly_name, 0, 1) == '/') {
						$friendly_name = substr($friendly_name, 1);
					}
					$this->file_list[] = array(
						'dir' => $dir,
						'file' => $file,
						'fullpath' => $fullpath,
						'friendly_name' => $friendly_name,
					);
				}
				if(is_dir($dir.'/'.$file)) $this->get_file_list($dir.'/'.$file, $count + 1);
			}
		}
	}

	function get_files() {
		if (empty($this->file_list)) {
			$this->get_file_list();
		}
		return $this->file_list;
	}

}