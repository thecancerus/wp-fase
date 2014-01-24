# WP-FASE CHANGE LOG

## V0.17 
- add copyright and license (Apache License 2.0) information
- change `__autoload()` to `spl_autoload_regsiter()` call
- change FASE-WP references to WP-FASE
- add comment to HTML header and a bit of cleanup in output
- add comment header to plain text output

## V0.16
- Code cleanup and dead code removal
- Documentation update
- add support for text removal from front of file path names by default
- add '-r' parameter for command line to override default text removal in file paths
- replaced magic numbers from PHP tokenizer with meaningful constants in action parser

## V0.15
- add_action properly assembled and output in HTML and text
- add_filter properly assembled and output in HTML and text
- add `<a>` tag references to CSS
- add names and href links within the HTML document
- add header links at top of HTML document for easy access

## V0.14
- HTML output functional and formatted for output by hook and file
- plain text output functional for output by hook and file
- initial support for "add_action"
- initial support for "add_filter"
- add .gitignore file with "file.html" ignored

## V0.13
- Flesh out most of HTML output
- Add CSS styling to HTML output
- Add JSON output
- Add plain text output
- Add -t CLI parameter to determine type of output

## V0.12
- Abstract reporting into classes (with some stubs)
- Change to autoload to support report classes
- Add file reporting to HTML output
- Add options list support (with defaults) to action parser
- Add do_action line to test1

## V0.11 
- Rename files and classes. 
- Add simple autoloader. 
- Abstract extractor code into its own class.

## V0.1
- Still undergoing heavy initial development.