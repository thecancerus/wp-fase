#FASE-WP

##(Filter and Action Syntax Extractor for WordPress)

This package is designed to traverse and arbitrary folder and find all instances of WordPress apply_filters and do_action with output into a simple HTML file. It is still in very early development.

## Invoking

> php extract.php [options]

### Options

Some options, such as the directory, are required. The following options are available:

	-d 	path to the directory to traverse. REQUIRED

	-f 	File Types, comma seaparated (no spaces). Defaults to 'php,inc'.

	-v 	Verbose mode, no parameters - turns on verbose reporting