<?php
/**
 * HTML output of raw data
 */
class output_html extends fase_wp_reports {

	public function __construct($parser) {
		//parent::__construct($parser);
		$this->parser = $parser;
	}

	function assemble() {

		$this->output .= "<html>\n<head>\n\t<title>FASE-WP output</title>";

		$this->output .= $this->addCSS();

		$this->output .= "\n</head>\n<body>\n";

		ksort($this->parser->processed_finds);
		foreach ($this->parser->processed_finds as $type => $finds) {
			$this->output .= "\t<h1>$type</h1>\n\n";

			ksort ($finds);
			foreach ($finds as $find_name => $instances) {
				$this->output .= "\t<h2>$find_name</h2>\n\n";

				foreach ($instances as $instance) {
					$this->output .= "\t\t\t<h3>Called in '" . htmlspecialchars($instance['file']['fullpath']) . "' (" . htmlspecialchars($instance['token'][2]) . ")</h3>\n";

					$this->output .= "\t\t\t<blockquote>\n";
					if (isset($instance['value_modified'])) {
						$this->output .= "\t\t\t<p><b>Value Modified:</b> ". htmlspecialchars($instance['value_modified']) . "</p>\n";
					}

					if (count($instance['optional_vars']) > 0) {
						$this->output .= "\t\t\t<p><b>Parameters:</b>\n\t\t\t<ul>\n";
						foreach ($instance['optional_vars'] as $var_name) {
							$this->output .= "\t\t\t\t<li>" . htmlspecialchars($var_name) . "</li>\n";
						}
						$this->output .= "\t\t\t</ul>\n";
					}

					if (isset($instance['data']['docblock'])) {
						
						$docblock = $instance['data']['docblock'];

						// Get rid of actual comment markup
						$docblock = str_replace('/**', '', $docblock);
						$docblock = str_replace('*/', '', $docblock);
						$docblock = preg_replace( '/^\s*\*/m', '', $docblock);

						$this->output .= "\n<pre>\n";
						$this->output .= htmlspecialchars($docblock);
						$this->output .= "\n</pre>\n";
					}
					$this->output .= "\t\t\t</blockquote>\n";


				//	$this->output .= "<pre>";
				//	$this->output .= print_r($instance, true);
				//	$this->output .= "</pre>";
				}
				//$this->output .= "\t\t</td></tr>\n";
				//$this->output .= "\t</table><br>\n\n";
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

	/**
	 * Add basic CSS to the output
	 *
	 * TODO: add ability to override with a CSS file.
	 */ 
	protected function addCSS() {

		$output = '<style>

			/* http://meyerweb.com/eric/tools/css/reset/ 
			   v2.0 | 20110126
			   License: none (public domain)
			*/

			html, body, div, span, applet, object, iframe,
			h1, h2, h3, h4, h5, h6, p, blockquote, pre,
			a, abbr, acronym, address, big, cite, code,
			del, dfn, em, img, ins, kbd, q, s, samp,
			small, strike, strong, sub, sup, tt, var,
			b, u, i, center,
			dl, dt, dd, ol, ul, li,
			fieldset, form, label, legend,
			table, caption, tbody, tfoot, thead, tr, th, td,
			article, aside, canvas, details, embed, 
			figure, figcaption, footer, header, hgroup, 
			menu, nav, output, ruby, section, summary,
			time, mark, audio, video {
				margin: 0;
				padding: 0;
				border: 0;
				font-size: 100%;
				font: inherit;
				font-family: "Source Sans Pro", Arial, Helvetica, sans-serif;
				vertical-align: baseline;
				text-align: left;
				color: #111;
			}
			/* HTML5 display-role reset for older browsers */
			article, aside, details, figcaption, figure, 
			footer, header, hgroup, menu, nav, section {
				display: block;
			}
			body {
				line-height: 1;
				margin: 1.2em;
				background: #f8f8f8;
			}
			ol, ul {
				list-style: square;
			}
			blockquote, q {
				quotes: none;
			}
			blockquote:before, blockquote:after,
			q:before, q:after {
				content: "";
				content: none;
			}
			table {
				border-collapse: collapse;
				border-spacing: 0;
			}
			
			h1, h2, h3, h4, h5, h6 { font-weight: bold; margin-bottom: .75em; }
			h1 { font-size: 150%; }
			h2 { font-size: 125%; margin-top: 2em; }
			h3 { font-size: 115%; margin-left: 2.5em; }
			h4 { font-size: 110%; }
			h5, h6 { font-size: 100%; }

			pre { font-family: "Source Code Pro", Concolas, "Courier New", Courier, monospace; }

			p, pre, ul, ol { margin-bottom: 1em; }

			pre { 
				background: #e0e0e0; 
				border: 1px solid #111;
				padding: 0.8em;
				
			}

			li { 
				margin-left: 2.5em;
				margin-bottom: 0.5em;
			}

			b { font-weight: bold; }

			blockquote { margin-left: 5em; }

		</style>';

		return $output;
	}

}