<?php
/**
 * This one is a bit more involved [filter_gallery tax="" imgsize="medium" howmany="-1"]
 * It will output a list of all the media files in your media library
 * that are IN A TAXONOMY(!!!) and create a list of buttons
 * with which to filter that gallery by terms from that taxonomy
 * Don't want an image to show? Don't give it a term in that taxonomy
 * How to create a taxonomy for your media files?
 * There are plugins in the WordPress repository that will do this for free or for money
 * I like creating my own taxonomy in CPT UI and applying it to media
 * This shortcode uses jQuery for the filtering
 * There is no styling, do your own or you can contact me and I might help
 * Naomi Blindeman: naomi@blindeman.com
 * For more shortcodes see https://github.com/Blindeman/wordpress-shortcodes
 */

add_shortcode( 'filter_gallery', 'bw_filter_gallery_shortcode');
function bw_filter_gallery_shortcode( $atts ){
	//Shortcode Attributes / Options
	$atts = shortcode_atts(
		array(
			'tax' => '',
			'howmany' => -1,
			'imgsize' => 'medium'
		), $atts, 'filter_gallery' );
	
	//If the tax parameter is empty, give an error, else process the content
	if( $atts['tax'] === '' ){
		$out = "<p class=\"alert error\">This shortcode needs the name of the taxonomy associated with media.</p>";
	} else {
		//Using sanitize_title for cheap sanitation 
		$sTaxonomy = sanitize_title( $atts['tax'] );
		//Check that this taxonomy exists, if so process the request, else give an error
		if( taxonomy_exists( $sTaxonomy ) ){
			$oAllTerms = get_terms( array( 'taxonomy' => $sTaxonomy ) );
			$aTermIds = array();
			
			$out = "<div class=\"button-group gallery-filter\">
				<button class=\"filter button active\" value=\"fgimage\">All</button>";
				foreach( $oAllTerms as $oAterm ){
					$out .= "<button class=\"filter button\" value=\"" . $oAterm->slug . "\">" . $oAterm->name . "</button>";
					$aTermIds[] = $oAterm->term_id;
				}
			$out .= "</div>";
							
			//WP_Query Arguments
			$args = array(
				'post_status' => 'any',
				'post_type'   => 'attachment',
				'post_mime_type' => 'image',
				'posts_per_page' => intval( $atts['howmany'] ),
				'tax_query' => array(
					array(
						'taxonomy' => $sTaxonomy,
						'terms' => $aTermIds
					)
				)
			);

			//The Query
			$bw_query = new WP_Query( $args );

			//The Loop
			if( $bw_query->have_posts() ){
				$out .= "<ul class=\"filterable-gallery\">";
				while( $bw_query->have_posts() ){
					$bw_query->the_post();
					$oTermies = get_the_terms( null, $sTaxonomy );
					$aTermlist = array();
					foreach( $oTermies as $oTermie ){
						$aTermlist[] = $oTermie->slug;
					}
					$sTermlist = implode( " ", $aTermlist);
					$out .= "<li class=\"fgimage " . $sTermlist . "\">" . wp_get_attachment_link( null, sanitize_title( $atts['imgsize'], 'medium' ), false ) . "</li>";
				}
				$out .= "</ul>";
				global $bFilter;
				$bFilter = TRUE;
			} else {
				$out = "<p>There are no images to show.</p>";
			}

			//Restore original Post Data
			wp_reset_postdata();
			
		} else {
			$out = "<p class=\"alert error\">This taxonomy does not exist.</p>";
		}
	}
	
	return $out;
}

add_action( 'wp_footer', 'bw_add_gallery_filter_script' );
function bw_add_gallery_filter_script(){
	global $bFilter;
	//If the shortcode isn't present, don't add the js, else do
	if( !$bFilter ){
		return;
	} else { 
		//Script to make the buttons actually filter ?>
		<script id="filterable-gallery-js">
			jQuery(document).ready(function(){
				jQuery(".filter.button").on('click', function(){
					var btnValue = jQuery(this).prop("value");
					jQuery(".filterable-gallery").fadeTo("fast", 0.01);
					jQuery(".fgimage").not("." + btnValue).fadeOut();
					setTimeout(function(){
						jQuery("." + btnValue).fadeIn();
						jQuery(".filterable-gallery").fadeTo("fast", 1);
					}, 400);
					jQuery(this).addClass("active").siblings().removeClass("active");
				});
			});
		</script><?php
	}
}
