<?php
/**
 * Shortcode to create an inline Read more button
 * [list_child_pages]
 * List child pages for the current page if there are any 
 * but will show the same list on one of those child pages
 * There is no styling, do your own or you can contact me and I might help
 * Naomi Blindeman: naomi@blindeman.com
 * For more shortcodes see https://github.com/Blindeman/wordpress-shortcodes
 */



add_shortcode( 'list_child_pages', 'bw_list_child_pages' );
function bw_list_child_pages(){
	global $post;
	if( $post->post_parent ){
		$aAncestors = array_reverse( get_post_ancestors( $post->ID ) );
		$iID = $aAncestors[0];
	} else {
		$iID = $post->ID;
	}
	
	$aArgs = array(
		'child_of' => $iID,
		'title_li' => '',
		'echo' => 0
	);
	$sListChildPages = '<nav class="child-pages-list"><ul class="secondary-menu">' . wp_list_pages( $aArgs ) . "</ul></nav>";
	
	if ( is_page() && $post->post_parent ){
		$sChildpages = wp_list_pages( 'sort_column=menu_order&title_li=&child_of=' . $post->post_parent . '&echo=0' );
	} else {
		$sChildpages = wp_list_pages( 'sort_column=menu_order&title_li=&child_of=' . $post->ID . '&echo=0' );
	}
	
	if( $sChildpages ){
		return $sListChildPages;
	}
}
