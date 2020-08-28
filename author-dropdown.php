<?php
/*
 * Shortcode to create a dropdown of authors with links to an archive of their posts. [show_author_dropdown excl=""]
 * excl is any author you wish to exclude. Provide the ids in a space seperated list (33 2 4)
*/

add_shortcode( 'show_author_dropdown', 'bw_author_dropdown' );
function bw_author_dropdown ( $atts = [] ) {
	
	$aParam = shortcode_atts( array(
		'excl' => '',
	), $atts, 'show_user_dropdown' );
	
	if( strpos( $aParam["excl"], " " ) !== FALSE ){
		$aExcl = explode( " ", $aParam["excl"] );
		foreach( $aExcl as $key => $value ){
			//to turn each value into an integer, in case it isn't
			$aExcl[$key] = $aExcl[$value] + 0;
		}
		$sExcl = implode( ",", $aExcl);
	} else {
		$sExcl = "";
	}
	
	$args = array(
		'exclude' => $sExcl,
		'fields' => array( 'display_name', 'ID' ),
		'role' => 'author',
	);
	
	$oListAuthors = get_users( array( 'role' => 'author', 'fields' => array( 'display_name', 'ID' ) ) );
	$aListAuthors = array();
	$aListAuthors[] .= "<option value=\"-1\">Gallery</option>";
	foreach( $oListAuthors as $user ){
		$aListAuthors[] .= "<option class=\"level-0\" value=\"" . get_author_posts_url( $user->ID ) . "\">" . esc_html( $user->display_name ) . "</option>";
	}
	$sListAuthors = implode( "", $aListAuthors );
	$sSelect = '<select name="gallery" id="user" class="postform" onchange="return this.form.submit()">' . $sListAuthors . '</select>';
	
	$sScript = "<script>/* <![CDATA[ */ 
	(function() { 
		var dropdown = document.getElementById( \"user\" );
		function onSelectChange() {
			if ( dropdown.options[ dropdown.selectedIndex ].value !== '' ) {
				document.location.href = this.options[ this.selectedIndex ].value;
			}
		}
		dropdown.onchange = onSelectChange;
	})();
	/* ]]&gt; */</script>";

	$out = $sSelect . $sScript;

	return $out;
}
