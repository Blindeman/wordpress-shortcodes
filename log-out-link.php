<?php
/**
 * Create a link on the front-end to log out of the site and be redirected to the home-page
 * There is no styling, do your own or you can contact me and I might help
 * Naomi Blindeman: naomi@blindeman.com
 * For more shortcodes see https://github.com/Blindeman/wordpress-shortcodes
*/

add_shortcode( 'taf_logout', function () {

	$out = "<a href=\"" . wp_logout_url( home_url() ) . "\">Log out</a>";
	
	if( is_user_logged_in() ){
		return $out;
	}
} );
