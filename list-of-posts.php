<?php
/*
 * [list_posts posttype="post" howmany="5" class="bw-post-list" entryheader="h2" date="no" featuredimage="yes" imagesize="post-thumbnail" summary="summary" cat="" author="no" orderby="date" order="DESC"]
*/

add_shortcode( 'list_posts', 'bw_list_posts_shortcode' );
function bw_list_posts_shortcode( $atts ) {

	// Attributes
	$atts = shortcode_atts(
		array(
			'posttype' => 'post',
			'howmany' => 5,
			'class' => 'bw-post-list',
            'entryheader' => 'h2',
            'date' => 'no',
            'featuredimage' => 'yes',
            'imagesize' => 'post-thumbnail',
			'summary' => 'summary',
			'cat' => '',
			'author' => 'no',
			'orderby' => 'date',
			'order' => 'DESC'
		),
		$atts,
		'list_posts'
    );

    if( strpos( $atts['posttype'], ',') !== FALSE ){
        //if there is more than one post_type
        $aPosttype = explode( ',', $atts['posttype'] );
        //pushing it through sanitize_title() for the sake of cheap sanitation
        //with a fallback of post
        foreach( $aPosttype as $key => $value ){
            $aPosttype[$key] = sanitize_title( $value, 'post' );
        }
    } else {
        //if there is only one post_type
        //pushing it through sanitize_title() for the sake of cheap sanitation
        //with a fallback of post
        $aPosttype = array( sanitize_title( $atts['posttype'], 'post') );
	}

	if( strpos( $atts['cat'], ',') !== FALSE ){
		//if there is more than one category id
		$aCategory = explode( ',', $atts['cat'] );
		//making sure they're all integers
		foreach( $aCategory as $key => $value ){
			$aCategory[$key] = intval( $value );
		}
	} elseif( $atts['cat'] !== '' ) {
		//if there is only one post_type
		//make sure it's an integer
		$aCategory = array( intval( $atts['cat'] ) );
	} else {
		$aCategory = '';
	}

	//set up an array with all possible values of orderby
	$aOrderby = array( 'none', 'ID', 'author', 'title', 'name', 'type', 'date', 'modified', 'parent', 'rand', 'comment_count', 'menu_order' );
	//check wether the given value is in the array
	if( in_array( $atts['orderby'], $aOrderby ) ){
		$sOrderby = $atts['orderby'];
	} else {
		$sOrderby = 'date';
	}
	
	if( $atts['order'] == 'ASC'){
		$sOrder = $atts['order'];
	} else {
		$sOrder = 'DESC';
	}
    
    // WP_Query arguments
    $args = array(
        'post_type' => $aPosttype,
		'posts_per_page' => intval( $atts['howmany'] ),
		'cat' => $aCategory,
		'orderby' => $sOrderby,
		'order' => $sOrder
    );

    // The Query
    $bw_query = new WP_Query( $args );

    // The Loop
    if ( $bw_query->have_posts() ) {
        $bw_post_list = "<div class='" . sanitize_html_class( $atts['class'], 'bw-post-list' ) . "'>";
        while ( $bw_query->have_posts() ) {
            $bw_query->the_post();
            //get the classes for posts to give more control
            $sPostClasses = implode( ' ', get_post_class() );
            //put together the html for the posts
            $bw_post_list .= "<article class='" . $sPostClasses . "'>" . 
                "<header>" . 
                    "<" . sanitize_html_class( $atts['entryheader'], 'h2' ) . " class=\"entry-title\">" . 
                        "<a href=\"" . get_the_permalink() . "\" title=\"" . get_the_title() . "\" rel=\"bookmark\">" . 
                            get_the_title() . 
                        "</a>" . 
                    "</" . sanitize_html_class( $atts['entryheader'], 'h2' ) . ">" . 
					( $atts['date'] === "yes" ? "<div class=\"entry-meta\"><span class=\"entry-date\">" . get_the_time( get_option( 'date_format' ) ) . "</span></div>" : "" ) .
					( $atts['author'] === "yes" ? "<span class=\"author vcard\">" . get_the_author_posts_link() . "</span>" : "") . 
                    ( current_user_can( 'edit_pages' ) ? "<a href=\"" . get_edit_post_link() . "\" class=\"post-edit-link\">" . __( 'Edit', 'bw-adding-posts-through-shortcode' ) . "</a>" : "" ) . 
                "</header>" . 
                ( $atts['featuredimage'] === "yes" ? "<a href=\"" . get_the_permalink() . "\" title=\"" . get_the_title() . "\" class=\"thumbnail-link\">" . 
                    get_the_post_thumbnail( null, sanitize_title( $atts['imagesize'], 'post-thumbnail' ) ) . 
                "</a>" : "" ) . 
                ( $atts['summary'] === "summary" ? "<div class=\"entry-summary\">" . 
                    get_the_excerpt() . 
                "</div>" : "" ) .
                ( $atts['summary'] === "content" ? "<div class=\"entry-content\">" . 
                    get_the_content() . 
                "</div>" : "" ) .
            "</article>";
        }
        $bw_post_list .= "</div>";
    } else {
        //put together the html for no posts found
        $bw_post_list = "<div class='" . sanitize_html_class( $atts['class'], 'bw-post-list' ) . "'><article class='nopost'>" . __( 'There are no posts to display.', 'bw-adding-posts-through-shortcode') . "</article></div>";
    }

    // Restore original Post Data
    wp_reset_postdata();

	return $bw_post_list;

}
