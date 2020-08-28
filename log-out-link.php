<?php
/*
 * Create a link on the front-end to log out of the site and be redirected to the home-page
*/

add_shortcode( 'taf_logout', function () {

	$out = "<a href=\"" . wp_logout_url( home_url() ) . "\">Log out</a>";
	
	if( is_user_logged_in() ){
		return $out;
	}
} );
