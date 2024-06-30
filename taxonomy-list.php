<?php
/**
 * Shortcode to create a dropdown for initial filtering of posts. [show_tax_list tax="category" title="" excl="" style="list"]
 * tax is the name of the taxonomy (category, post_tag, etc)
 * title is the title of the list
 * excl is any terms (categories, tags) you wish to exclude. Provide the ids in a list seperated by spaces (33 2 4)
 * Standard styling is an unordered list, the other option is a string seperated by line breaks
 * Naomi Blindeman: naomi@blindeman.com
 * For more shortcodes see https://github.com/Blindeman/wordpress-shortcodes
*/

add_shortcode( 'show_tax_list', 'bw_tax_list' );
function bw_tax_list ( $atts = [] ) {
	
	$aParam = shortcode_atts( array(
		'tax' => 'category',
		'title' => '',
		'excl' => '',
		'style' => 'list'
	), $atts, 'show_tax_list' );
	
	if( strpos( $aParam["excl"], "," ) !== FALSE ){
		$aExcl = explode( ",", $aParam["excl"] );
		foreach( $aExcl as $key => $value ){
			//to turn each value into an integer, in case it isn't
			$aExcl[$key] = intval($aExcl[$value]);
		}
	} else {
		$aExcl = "";
	}
	
	$args = array(
		'title_li' => esc_html( $aParam["title"] ),
		'show_count' => 0,
		'echo' => 0,
		'taxonomy' => sanitize_title( $aParam["tax"], "category" ),
		'exclude' => $aExcl,
		'style' => sanitize_title( $aParam["style"], 'list' ),
	);
	
	$sSelect = wp_list_categories( $args );
	//$sReplace = "<select$1 onchange='return this.form.submit()'>";
	//$sSelect  = preg_replace( '#<select([^>]*)>#', $sReplace, $sSelect );
	//$sForm = '<form id="tax-select" class="tax-select" action="' . esc_url( home_url( '/' ) ) . '" method="get">' . $sSelect . "</form>";
	//$sNoScript = '<noscript><input type="submit" value="Filter" /></noscript>';

	//$out = $sForm . $sNoScript;

	//$out = $sForm . $sNoScript;
	if( $aParam["style"] === "list" ){
		$out = '<ul class="bwtaxnav">' . $sSelect . '</ul>';
	} else {
		$out = '<div class="bwtaxnav">' . $sSelect . '</div>';
	}

	return $out;
}
