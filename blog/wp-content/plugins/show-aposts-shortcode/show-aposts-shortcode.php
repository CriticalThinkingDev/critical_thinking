<?php
/*
Plugin Name: Show All Posts Shortcode
Plugin URI: http://www.mokamedianyc.com/dev/show-aposts-shortcode/
Description: Provides a [show-aposts] <a href="http://codex.wordpress.org/Shortcode_API">shortcodes</a> for displaying posts or pages using the get_posts function.  The shortcode accepts most parameters that you can pass to the <a href="http://codex.wordpress.org/Template_Tags/wp_nav_menu">get_posts()</a> function.  To show posts or pages, add [show-aposts category="NN"] in the page or post body.
Author: Bob Matsuoka
Version: 1.0
Author URI: http://www.mokamedianyc.com
*/

function shortcode_show_aposts( $atts, $content, $tag ) {
	
	global $post;
	
	// Set defaults
	$defaults = array(
	 	'numberposts'     => 5,
	    'offset'          => 0,
	    'category'        => '',
	    'orderby'         => 'post_date',
	    'order'           => 'DESC',
	    'include'         => '',
	    'exclude'         => '',
	    'meta_key'        => '',
	    'meta_value'      => '',
	    'post_type'       => 'post',
	    'post_mime_type'  => '',
	    'post_parent'     => '',
	    'post_status'     => 'publish',
	);
	
	// Merge user provided atts with defaults
	$atts = shortcode_atts( $defaults, $atts );
	 
	// Get Posts
	$posts_to_show = get_posts( $atts );
	
	// Create output
	$out = '<div class="show-aposts"><ul>';
 
	foreach ($posts_to_show as $post_to_show) {
		$permalink = get_permalink( $post_to_show->ID );
		$out .= <<<HTML
		<li><a href ="{$permalink}" title="{$post_to_show->post_title}">{$post_to_show->post_title}</a> </li>
HTML;
	}
	
	$out .= '</ul></div>';

	return apply_filters( 'shortcode_show_aposts', $out, $atts, $content, $tag );
}

add_shortcode( 'show-aposts', 'shortcode_show_aposts' );

?>