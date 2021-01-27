<?php
/**
 * Shortcode to create an inline Read more button
 * [spoiler title="Read More" initial_state="collapsed" id=""][/spoiler]
 * When you click more, you stay on the same page. 
 * Not useful for blog posts in an archive, but useful for a page
 * full of poems for example
 * Inspired by Sergey Kuzmich http://kuzmi.ch
 * This shortcode uses jQuery to open and close the spoiler
 * There is no styling, do your own or you can contact me and I might help
 * Naomi Blindeman: naomi@blindeman.com
 * For more shortcodes see https://github.com/Blindeman/wordpress-shortcodes
 */



add_shortcode ( 'spoiler', 'bw_spoiler_shortcode' );
function bw_spoiler_shortcode( $atts, $content ) {
	$atts = shortcode_atts( array (
		'title' => 'Read More',
		'initial_state' => 'collapsed',
		'id' => ''
	), $atts, 'spoiler' );

	$title = esc_attr( $atts['title'] );
	$head_class = ( esc_attr( $atts['initial_state'] ) == 'collapsed' ) ? ' collapsed' : ' expanded';
	$sID = esc_attr( $atts['id'] );
	if( $sID == '' ){ $sID = uniqid( 'bw', FALSE ); }
	if( $head_class == "collapsed" ){ $bAriaEx = FALSE; } else { $bAriaEx = TRUE; }

	$sOut  = '<div class="spoiler-wrap">
		<div id="' . $sID . '" class="spoiler-body">' . 
			do_shortcode( $content ) . 
		'</div><!-- spoiler-body -->
			<button type="button" class="spoiler-head ' . $head_class . '" aria-controls="' . $sID . '" aria-expanded="' . $bAriaEx . '">' . 
			$title . 
		'	</button><!-- spoiler-head -->
		</div><!-- spoiler-wrap -->';
	
	global $bSpoiler;
	$bSpoiler = TRUE;
	return $sOut;
}

add_action( 'wp_footer', 'bw_add_spoiler_script' );
function bw_add_spoiler_script() {
	global $bSpoiler;
	if( !$bSpoiler ){
		return;
	} else { ?>
		<script id="inline-spoiler-js">
				if( jQuery(".spoiler-head").hasClass("collapsed") ){
					jQuery(".spoiler-body").hide();
				}
				jQuery(".spoiler-head").on('click', function(e){
					$this = jQuery(this);
					if( $this.hasClass("expanded") ) {
						$this.prev().slideUp();
						$this.removeClass("expanded");
						$this.addClass("collapsed");
						$this.text( $this.text().replace( 'Less', 'More' ) );
						$this.attr("aria-expanded", "FALSE");
					} else {
						$this.prev().slideDown();
						$this.removeClass("collapsed");
						$this.addClass("expanded");
						$this.text( $this.text().replace( 'More', 'Less' ) );
						$this.attr("aria-expanded", "TRUE");
					}
				});
		</script><?php
	} 
}