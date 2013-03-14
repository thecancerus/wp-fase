<?php 

// This file is mainly to verify that the extractor will find actions and filters of varying syntaxes.

$defaults = apply_filters('single_quote_name_no_spaces', $defaults);
$defaults = apply_filters("double_quote_name_no_spaces", $defaults);
$defaults = apply_filters($string_no_spaces, $defaults);
$defaults = apply_filters ( 'single_quote_name_with_spaces', $defaults);
$defaults = apply_filters ( "double_quote_name_with_spaces", $defaults);
$defaults = apply_filters ( $string_with_spaces, $defaults);

$defaults = do_action('single_quote_name_no_spaces');
$defaults = do_action("double_quote_name_no_spaces");
$defaults = do_action($string_no_spaces);
$defaults = do_action ( 'single_quote_name_with_spaces' );
$defaults = do_action ( "double_quote_name_with_spaces" );
$defaults = do_action ( $string_with_spaces );
