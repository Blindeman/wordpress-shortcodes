<?php
/**
 * Shortcode to create a dropdown for initial filtering of posts. [show_tax_dropdown tax="category" text="Choose..." excl=""]
 * tax is the name of the taxonomy (category, post_tag, etc)
 * text is the first words available in the dropdown
 * excl is any terms (categories, tags) you wish to exclude. Provide the ids in a list seperated by spaces (33 2 4)
 * There is no styling, do your own or you can contact me and I might help
 * Naomi Blindeman: naomi@blindeman.com
*/

add_shortcode( 'show_tax_dropdown', 'sgu_tax_dropdown' );
function sgu_tax_dropdown ( $atts = [] ) {
	
	$aParam = shortcode_atts( array(
		'tax' => 'category',
		'text' => 'Choose...',
		'excl' => '',
		'name' => 'cat',
		'value' => 'term_id'
	), $atts, 'show_tax_dropdown' );
	
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
		'show_option_none' => esc_html( $aParam["text"] ),
		'orderby' => "name",
		'show_count' => 1,
		'echo' => 0,
		'taxonomy' => sanitize_title( $aParam["tax"], "category" ),
		'exclude' => $aExcl,
		'value_field' => sanitize_title( $aParam["value"], "term_id" ),
		'name' => sanitize_title( $aParam["name"], 'cat' ),
	);
	
	$sSelect = wp_dropdown_categories( $args );
	$sReplace = "<select$1 onchange='return this.form.submit()'>";
	$sSelect  = preg_replace( '#<select([^>]*)>#', $sReplace, $sSelect );
	$sForm = '<form id="tax-select" class="tax-select" action="' . esc_url( home_url( '/' ) ) . '" method="get">' . $sSelect . "</form>";
	$sNoScript = '<noscript><input type="submit" value="Filter" /></noscript>';

	$out = $sForm . $sNoScript;

	return $out;
}
