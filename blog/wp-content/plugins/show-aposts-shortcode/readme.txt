=== Plugin Name ===
Contributors: Bob Matsuoka
Donate link:
Tags: shortcodes, menu, show menu
Requires at least: 3.1
Tested up to: 3.2
Stable tag: 1.0

Provides a [show-aposts] shortcode for displaying posts or pages within a post or page using the get_posts() function.

== Description ==

Provides a [show-aposts] <a href="http://codex.wordpress.org/Shortcode_API">shortcodes</a> for displaying posts or pages using the get_posts function.  The shortcode accepts most parameters that you can pass to the <a href="http://codex.wordpress.org/Template_Tags/wp_nav_menu">get_posts()</a> function.  To show posts or pages, add [show-aposts category="NN"] in the page or post body.


= Usage =

*Show posts with a category NN*

`[show-aposts category="NN"]`

= Please Note =

The default values are the same as for the [`get_posts()`](http://codex.wordpress.org/Template_Tags/get_posts). 
== Changelog ==

= 1.0 =

* First release.

== Installation ==

1. Download and unzip the most recent version of this plugin
2. Upload the show-menu-shortcode folder to /path-to-wordpress/wp-content/plugins/
3. Login to your WP Admin panel, click Plugins, and activate "Show Aposts Shortcode"