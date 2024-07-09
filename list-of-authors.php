<?php
/**
 * Shortcode to create a list of users [list_all_authors excl="" link=yes role="author,administrator"]
 * excl is any author you wish to exclude. Provide the ids in a space separated list (33 2 4)
 * link is whether to link to the author post pages. Options are yes or no
 * There is no styling, do your own or you can contact me and I might help
 * The author image I'm using has been added through ACF
 * Naomi Blindeman: naomi@blindeman.com
 * For more shortcodes see https://github.com/Blindeman/wordpress-shortcodes
*/

add_shortcode( 'list_all_authors', 'bw_author_list' );
function bw_author_list ( $atts = [] ) {
	
	$aParam = shortcode_atts( array(
		'role' => 'author',
		'excl' => '',
		'link' => 'yes'
	), $atts, 'list_all_authors' );
	
	//Find anyone that needs to be excluded
	if( strpos( $aParam["excl"], "," ) !== FALSE ){
		//If it's a comma-separated list
		$aExcl = explode( ",", $aParam["excl"] );
		foreach( $aExcl as $key => $value ){
			//to turn each value into an integer, in case it isn't
			$aExcl[$key] = $value + 0;
		}
	} elseif( strpos( $aParam["excl"], "," ) === FALSE && $aParam["excl"] !== "" ){
		//if it is just one user
		//to turn the value into an integer, in case it isn't
		$aExcl = array( $aParam["excl"] + 0 );
	} else {
		//if there is no one to exclude
		$sExcl = "";
	}
	
	//Find the role or roles to include
	if( $aParam["role"] != "" ){
		if( strpos( $aParam["role"], "," ) !== FALSE ){
			$aRole = explode( ",", $aParam["role"]);
			foreach( $aRole as $key => $value ){
				$aRole[$key] = esc_html( $value );
			}
		} else {
			$aRole = array( esc_html( $aParam["role"] ) );
		}
	} else {
		$aRole = array( "author" );
	}
	
	$args = array(
		'role__in' => $aRole,
		'fields' => array( 'display_name', 'ID' ),
		'exclude' => $aExcl,
	);
	
	//$oListAuthors = get_users( array( 'role__in' => 'author', 'fields' => array( 'display_name', 'ID' ) ) );
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
		$image_id = get_field( 'portret', $sTheUser );
		$aListAuthors[] .= '<li class="contributor">' . $sLink1 . wp_get_attachment_image( $image_id, 'medium' ) . '<span class="contributor-link">' . esc_html( $user->display_name ) . "</span>" . $sLink2 . "</li>";
	}
	$sListAuthors = implode( "", $aListAuthors );
	$sSelect = '<ul class="contributors">' . $sListAuthors . '</ul>';

	$out = $sSelect;
	//$out = implode( ", ", $aExcl ) . implode( ", ", $aRole );

	return $out;
}
