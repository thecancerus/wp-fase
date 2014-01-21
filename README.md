#FASE-WP

##(Filter and Action Syntax Extractor for WordPress)

This package is designed to traverse and arbitrary folder and find all instances of WordPress `add_action`, `add_filter`, `apply_filters`, `do_action`, and `do_action_ref_array` with HTML, JSON or plain text output. Other output format filters can be programmed and are easily added.

Output is directed to the console and should be redirected for capturing. Future versions will be more flexible in output.

This project is very much "alpha" and still in very early development.

**Current Version:** 0.16 (2014-01-21)

## Invoking

> php extract.php [options]

### Options

Some options, such as the directory, are required. The following options are available:

	-d 	path to the directory to traverse. REQUIRED

	-r  text to remove from file paths. Defaults to value given in '-d' if not specified.

	-f 	File Types, comma separated (no spaces). Defaults to 'php,inc'.

	-t 	Output Type. HTML, JSON or text. Defaults to HTML. Invalid values go to default.

	-v 	Verbose mode, no parameters - turns on verbose reporting

## Immediate TODO

- Override CSS styling for HTML 

## Planned Features

- Split output into multiple files
	- broken down by actions, filters, files
- Specify a folder for multiple-file output on the command line
- Specify a single file for output on the command line
- HTML output filter with JavaScript / Ajax navigation

