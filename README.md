#FASE-WP

##(Filter and Action Syntax Extractor for WordPress)

This package is designed to traverse and arbitrary folder and find all instances of WordPress `apply_filters` `do_action` and `do_action_ref_array` with HTML, JSON or plain text output. Output is directed to the console and shoudl be redirected for capturing. 

This project is very much "alpha" and still in very early development.

**Current Version:** 0.14 (2014-01-11)

## Invoking

> php extract.php [options]

### Options

Some options, such as the directory, are required. The following options are available:

	-d 	path to the directory to traverse. REQUIRED

	-f 	File Types, comma separated (no spaces). Defaults to 'php,inc'.

	-t 	Output Type. HTML, JSON or text. Defaults to HTML. Invalid goes to default.

	-v 	Verbose mode, no parameters - turns on verbose reporting

## Immediate TODO

- Add proper processing and output formatting for `add_action` and `add_filter`

## Planned Features

- Split output into multiple files
	- broken down by actions, filters, files
- Speficiy a single file for output on the command line
- Override CSS styling for HTML 
