<?php
/*
 * Shortcode to create a list of users [list_all_authors excl="" link=yes]
 * excl is any author you wish to exclude. Provide the ids in a space separated list (33 2 4)
 * link is whether to link to the author post pages. Options are yes or no
*/

add_shortcode( 'list_all_authors', 'bw_author_list' );
function bw_author_list ( $atts = [] ) {
	
	$aParam = shortcode_atts( array(
		'excl' => '',
		'link' => 'yes'
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
	
	//$oListAuthors = get_users( array( 'role' => 'author', 'fields' => array( 'display_name', 'ID' ) ) );
	$oListAuthors = get_users( $args );
	$aListAuthors = array();
	foreach( $oListAuthors as $user ){
		$sTheUser = 'user_'.$user->ID;
		if( $aParam["link"] == 'no' ){
			$sLink1 = "" ;
			$sLink2 = "";
		} else {
			$sLink1 = "<a href=\"" . get_author_posts_url( $user->ID ) . "\">";
			$sLink2 = "</a>";
		}
		$image_id = get_field( 'profile_picture', $sTheUser );
		$aListAuthors[] .= "<li class=\"gallery-profile\">" . $sLink1 . wp_get_attachment_image( $image_id, 'archive-square' ) . "<span>" . esc_html( $user->display_name ) . "</span>" . $sLink2 . "</li>";
	}
	$sListAuthors = implode( "", $aListAuthors );
	$sSelect = '<ul class="all-galleries">' . $sListAuthors . '</ul>';

	$out = $sSelect;

	return $out;
}

?>
