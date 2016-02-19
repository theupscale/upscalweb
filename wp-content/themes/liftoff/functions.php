<?php
/**
 *
 * Sets up the theme and provides some helper functions, which are used
 * in the theme as custom template tags. Others are attached to action and
 * filter hooks in WordPress to change core functionality.
 *
 */

/**
 * Sets up the content width value based on the theme's design and stylesheet.
 */
if ( ! isset( $content_width ) )
	$content_width = 700;

/**
 * Sets up theme defaults and registers the various WordPress features.
 *
 * @uses load_theme_textdomain() For translation/localization support.
 * @uses add_editor_style() To add a Visual Editor stylesheet.
 * @uses add_theme_support() To add support for post thumbnails, automatic feed links,
 * 	custom background, and post formats.
 * @uses register_nav_menu() To add support for navigation menus.
 * @uses set_post_thumbnail_size() To set a custom post thumbnail size.
 */
function liftoff_setup() {
	/*
	 * Makes theme available for translation.
	 *
	 * Translations can be added to the /languages/ directory.
	 * If you're building a theme based on Liftoff, use a find and replace
	 * to change 'liftoff' to the name of your theme in all the template files.
	 */
	load_theme_textdomain( 'liftoff', get_stylesheet_directory() . '/languages' );

	// This theme styles the visual editor with editor-style.css to match the theme style.
	add_editor_style();

	// Adds RSS feed links to <head> for posts and comments.
	add_theme_support( 'automatic-feed-links' );

	// This theme supports a variety of post formats.
	add_theme_support( 'post-formats', array( 'aside', 'image', 'link', 'quote', 'status' ) );

	// This theme uses wp_nav_menu() in one location.
	register_nav_menu( 'primary', __( 'Primary Menu', 'liftoff' ) );

	// This theme uses a custom image size for featured images, displayed on "standard" posts.
	add_theme_support( 'post-thumbnails' );
	set_post_thumbnail_size( 700, 9999 ); // Unlimited height, soft crop
}
add_action( 'after_setup_theme', 'liftoff_setup' );

/**
 * Enqueues scripts and styles for front-end.
 */
function liftoff_scripts_styles() {
	global $wp_styles;

	/*
	 * Adds JavaScript to pages with the comment form to support
	 * sites with threaded comments (when in use).
	 */
	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) )
		wp_enqueue_script( 'comment-reply' );

	/*
	 * Adds JavaScript for handling the navigation menu hide-and-show behavior.
	 */
	wp_enqueue_script( 'liftoff-navigation', get_stylesheet_directory_uri() . '/js/navigation.js', array(), '1.0', true );

	/*
	 * Loads our main stylesheet.
	 */
	wp_enqueue_style( 'liftoff-style', get_stylesheet_uri() );

	/*
	 * Loads the Internet Explorer specific stylesheet.
	 */
	wp_enqueue_style( 'liftoff-ie', get_stylesheet_directory_uri() . '/css/ie.css', array( 'liftoff-style' ), '20121010' );
	$wp_styles->add_data( 'liftoff-ie', 'conditional', 'lt IE 9' );
}
add_action( 'wp_enqueue_scripts', 'liftoff_scripts_styles' );

/**
 * Creates a nicely formatted and more specific title element text
 * for output in head of document, based on current view.
 *
 * @param string $title Default title text for current view.
 * @param string $sep Optional separator.
 * @return string Filtered title.
 */
function liftoff_wp_title( $title, $sep ) {
	global $paged, $page;

	if ( is_feed() )
		return $title;

	// Add the site name.
	$title .= get_bloginfo( 'name' );

	// Add the site description for the home/front page.
	$site_description = get_bloginfo( 'description', 'display' );
	if ( $site_description && ( is_home() || is_front_page() ) )
		$title = "$title $sep $site_description";

	// Add a page number if necessary.
	if( is_int($paged) && is_int($page) ){
		if ( $paged >= 2 || $page >= 2 ){
			$title = "$title $sep " . sprintf( __( 'Page %s', 'liftoff' ), max( $paged, $page ) );
		}
	}

	return $title;
}
add_filter( 'wp_title', 'liftoff_wp_title', 10, 2 );

/**
 * Makes our wp_nav_menu() fallback -- wp_page_menu() -- show a home link.
 */
function liftoff_page_menu_args( $args ) {
	if ( ! isset( $args['show_home'] ) )
		$args['show_home'] = true;
	return $args;
}
add_filter( 'wp_page_menu_args', 'liftoff_page_menu_args' );

/**
 * Registers our main widget area and the front page widget areas.
 */
function liftoff_widgets_init() {
	register_sidebar( array(
		'name' => __( 'Main Sidebar', 'liftoff' ),
		'id' => 'sidebar-1',
		'description' => __( 'Appears on posts and pages except the optional Front Page template, which has its own widgets', 'liftoff' ),
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget' => '</aside>',
		'before_title' => '<h3 class="widget-title">',
		'after_title' => '</h3>',
	) );

	register_sidebar( array(
		'name' => __( 'First Front Page Widget Area', 'liftoff' ),
		'id' => 'sidebar-2',
		'description' => __( 'Appears when using the optional Front Page template with a page set as Static Front Page', 'liftoff' ),
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget' => '</aside>',
		'before_title' => '<h3 class="widget-title">',
		'after_title' => '</h3>',
	) );

	register_sidebar( array(
		'name' => __( 'Second Front Page Widget Area', 'liftoff' ),
		'id' => 'sidebar-3',
		'description' => __( 'Appears when using the optional Front Page template with a page set as Static Front Page', 'liftoff' ),
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget' => '</aside>',
		'before_title' => '<h3 class="widget-title">',
		'after_title' => '</h3>',
	) );
}
add_action( 'widgets_init', 'liftoff_widgets_init' );

if ( ! function_exists( 'liftoff_content_nav' ) ) :
/**
 * Displays navigation to next/previous pages when applicable.
 */
function liftoff_content_nav( $html_id ) {
	global $wp_query;

	$html_id = esc_attr( $html_id );

	if ( $wp_query->max_num_pages > 1 ) : ?>
		<nav id="<?php echo $html_id; ?>" class="navigation" role="navigation">
			<h3 class="assistive-text"><?php _e( 'Post navigation', 'liftoff' ); ?></h3>
			<div class="nav-previous alignleft"><?php next_posts_link( __( '<span class="meta-nav">&larr;</span> Older posts', 'liftoff' ) ); ?></div>
			<div class="nav-next alignright"><?php previous_posts_link( __( 'Newer posts <span class="meta-nav">&rarr;</span>', 'liftoff' ) ); ?></div>
		</nav><!-- #<?php echo $html_id; ?> .navigation -->
	<?php endif;
}
endif;

if ( ! function_exists( 'liftoff_comment' ) ) :
/**
 * Template for comments and pingbacks.
 *
 * To override this walker in a child theme without modifying the comments template
 * simply create your own liftoff_comment(), and that function will be used instead.
 *
 * Used as a callback by wp_list_comments() for displaying the comments.
 */
function liftoff_comment( $comment, $args, $depth ) {
	$GLOBALS['comment'] = $comment;
	switch ( $comment->comment_type ) :
		case 'pingback' :
		case 'trackback' :
		// Display trackbacks differently than normal comments.
	?>
	<li <?php comment_class(); ?> id="comment-<?php comment_ID(); ?>">
		<p><?php _e( 'Pingback:', 'liftoff' ); ?> <?php comment_author_link(); ?> <?php edit_comment_link( __( '(Edit)', 'liftoff' ), '<span class="edit-link">', '</span>' ); ?></p>
	<?php
			break;
		default :
		// Proceed with normal comments.
		global $post;
	?>
	<li <?php comment_class(); ?> id="li-comment-<?php comment_ID(); ?>">
		<article id="comment-<?php comment_ID(); ?>" class="comment">
			<header class="comment-meta comment-author vcard">
				<?php
					echo get_avatar( $comment, 44 );
					printf( '<cite class="fn">%1$s %2$s</cite>',
						get_comment_author_link(),
						// If current post author is also comment author, make it known visually.
						( $comment->user_id === $post->post_author ) ? '<span> ' . __( 'Post author', 'liftoff' ) . '</span>' : ''
					);
					printf( '<a href="%1$s"><time datetime="%2$s">%3$s</time></a>',
						esc_url( get_comment_link( $comment->comment_ID ) ),
						get_comment_time( 'c' ),
						/* translators: 1: date, 2: time */
						sprintf( __( '%1$s at %2$s', 'liftoff' ), get_comment_date(), get_comment_time() )
					);
				?>
			</header><!-- .comment-meta -->

			<?php if ( '0' == $comment->comment_approved ) : ?>
				<p class="comment-awaiting-moderation"><?php _e( 'Your comment is awaiting moderation.', 'liftoff' ); ?></p>
			<?php endif; ?>

			<section class="comment-content comment">
				<?php comment_text(); ?>
				<?php edit_comment_link( __( 'Edit', 'liftoff' ), '<p class="edit-link">', '</p>' ); ?>
			</section><!-- .comment-content -->

			<div class="reply">
				<?php comment_reply_link( array_merge( $args, array( 'reply_text' => __( 'Reply', 'liftoff' ), 'after' => ' <span>&darr;</span>', 'depth' => $depth, 'max_depth' => $args['max_depth'] ) ) ); ?>
			</div><!-- .reply -->
		</article><!-- #comment-## -->
	<?php
		break;
	endswitch; // end comment_type check
}
endif;

if ( ! function_exists( 'liftoff_entry_date' ) ) :
/**
 * Prints HTML with meta information for current date.
 */
function liftoff_entry_date() {
	$date = sprintf( '<span title="%1$s" ><time class="entry-date" datetime="%2$s">%3$s</time></span>',
		esc_attr( get_the_time() ),
		esc_attr( get_the_date( 'c' ) ),
		esc_html( get_the_time(get_option('date_format')) )
	);		

	echo $date;
}
endif;

if ( ! function_exists( 'liftoff_entry_meta' ) ) :
/**
 * Prints HTML with meta information for current post: categories, tags, permalink, author.
 */
function liftoff_entry_meta() {
	// Translators: used between list items, there is a space after the comma.
	$categories_list = get_the_category_list( __( ', ', 'liftoff' ) );

	// Translators: used between list items, there is a space after the comma.
	$tag_list = get_the_tag_list( '', __( ', ', 'liftoff' ) );

	$author = sprintf( '<span class="author vcard"><a class="url fn n" href="%1$s" title="%2$s" rel="author">%3$s</a></span>',
		esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ),
		esc_attr( sprintf( __( 'View all posts by %s', 'liftoff' ), get_the_author() ) ),
		get_the_author()
	);

	// Translators: 1 is author, 2 is category, 3 is tag
	if ( $tag_list ) {
		$utility_text = __( '<span class="by-author">%1s | </span>Category: <span class="category-list">%2$s</span> | Tags: <span class="tag-list">%3$s</span>', 'liftoff' );
	} else {
		$utility_text = __( '<span class="by-author">%1s | </span>Category: <span class="category-list">%2$s</span>', 'liftoff' );
	}

	printf(
		$utility_text,
		$author,
		$categories_list,
		$tag_list
	);
}
endif;

/**
 * Extends the default WordPress body class to denote:
 * 1. Using a full-width layout, when no active widgets in the sidebar
 *    or full-width template.
 * 2. Front Page template: thumbnail in use and number of sidebars for
 *    widget areas.
 * 3. White or empty background color to change the layout and spacing.
 * 4. Custom fonts enabled.
 * 5. Single or multiple authors.
 *
 *
 * @param array Existing class values.
 * @return array Filtered class values.
 */
function liftoff_body_class( $classes ) {
	$background_color = get_background_color();

	if ( ! is_active_sidebar( 'sidebar-1' ) || is_page_template( 'page-templates/full-width.php' ) )
		$classes[] = 'full-width';

	if ( is_page_template( 'page-templates/front-page.php' ) ) {
		$classes[] = 'template-front-page';
		if ( has_post_thumbnail() )
			$classes[] = 'has-post-thumbnail';
		if ( is_active_sidebar( 'sidebar-2' ) && is_active_sidebar( 'sidebar-3' ) )
			$classes[] = 'two-sidebars';
	}

	if ( ! is_multi_author() )
		$classes[] = 'single-author';

	return $classes;
}
add_filter( 'body_class', 'liftoff_body_class' );

/**
 * Adjusts content_width value for full-width and single image attachment
 * templates, and when there are no active widgets in the sidebar.
 */
function liftoff_content_width() {
	if ( is_page_template( 'page-templates/full-width.php' ) || is_attachment() || ! is_active_sidebar( 'sidebar-1' ) ) {
		global $content_width;
		$content_width = 960;
	}
}
add_action( 'template_redirect', 'liftoff_content_width' );

/**
 * Add postMessage support for site title and description for the Theme Customizer.
 *
 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
 * @return void
 */
function liftoff_customize_register( $wp_customize ) {
	$wp_customize->get_setting( 'blogname' )->transport = 'postMessage';
	$wp_customize->get_setting( 'blogdescription' )->transport = 'postMessage';
}
add_action( 'customize_register', 'liftoff_customize_register' );



/**
 * Includes CSS styles set in theme options after default stylesheet
 */
if ( ! function_exists( 'liftoff_custom_css' ) ) :
function liftoff_custom_css() {

	$custom_css = '';




	# -----------------------------------------------------------------------------
	# Custom CSS Styles from Theme Options
	# -----------------------------------------------------------------------------


	// Text colors
	// ----------------------------------------------------------------------------

	$link_color      = get_options_data('design-settings', 'link-color');
	$link_hover      = get_options_data('design-settings', 'link-hover-color');
	$underline       = get_options_data('design-settings', 'underline-links');
	$underline_hover = get_options_data('design-settings', 'underline-links-hover');
	$link_styles     = '';
	$hover_styles    = '';

	// Font Styles
	$default_font    = get_liftoff_font_styles('font-default');
	$heading_font    = get_liftoff_font_styles('font-heading');

	if ($link_color) $link_styles .= 'color: '. $link_color .';';
	if ($underline == 'true') $link_styles .= 'text-decoration: underline;';
	if ($link_styles) {
		$custom_css .= 'a, .widget-area .widget a:hover, .comments-link a:hover, .entry-meta a:hover, .entry-header .entry-title a:hover, footer[role="contentinfo"] a { '. $link_styles .' }';
		$custom_css .= '@media screen and (min-width: 600px) { .main-navigation li ul li a:hover { background-color: '. $link_color .' } }';
	}
	if ($link_hover) $hover_styles .= 'color: '. $link_hover .';';
	if ($underline_hover == 'true') $hover_styles .= 'text-decoration: underline;';
	if ($hover_styles) {
		$custom_css .= 'a:hover, footer[role="contentinfo"] a:hover { '. $hover_styles .' }';
	}

	if ($default_font) $custom_css .= 'body { '. $default_font .' }';
	if ($heading_font) $custom_css .= 'h1, h2, h3, h4, h5, h6, .entry-header .entry-title a { '. $heading_font .' }';


	// Header styles (text color, backgrounds, etc.)
	// ----------------------------------------------------------------------------

	$header_color      = get_options_data('design-settings', 'header-text-color');
	$header_background = get_options_data('design-settings', 'header-background-color');
	$header_image      = get_options_data('design-settings', 'header-background-image');
	$header_pos_x      = get_options_data('design-settings', 'header-background-image-pos-x', 'left');
	$header_pos_y      = get_options_data('design-settings', 'header-background-image-pos-y', 'top');
	$header_repeat     = get_options_data('design-settings', 'header-background-image-repeat', 'no-repeat');
	$header_styles     = '';

	if ($header_color)  $header_styles .= 'color: '. $header_color .';';
	if ($header_background)  $header_styles .= 'background-color: '. $header_background .';';
	if ($header_image) {
		$header_styles .= 'background-image: url('. $header_image .');';
		if ($header_repeat == 'fixed') { 
			$header_pos = $header_repeat;
			$header_repeat = 'no-repeat'; 
		} else { 
			$header_pos = $header_pos_x .' '. $header_pos_y;
		}
		$header_styles .= 'background-position: '. $header_pos .';';
		$header_styles .= 'background-repeat: '. $header_repeat .';';
	}
	if ($header_styles) {
		$custom_css .= '#masthead { '. $header_styles .' }';
	}


	// Logo
	// ----------------------------------------------------------------------------

	if ($header_color) {
		$custom_css .= '.site-header h1 a, .site-header h2 a, .site-header h1 a:hover, .site-header h2 a:hover { color: '. $header_color .' }';
	}


	// Footer styles (text color, backgrounds, etc.)
	// ----------------------------------------------------------------------------

	$footer_color      = get_options_data('design-settings', 'footer-text-color');
	$footer_background = get_options_data('design-settings', 'footer-background-color');
	$footer_image      = get_options_data('design-settings', 'footer-background-image');
	$footer_pos_x      = get_options_data('design-settings', 'footer-background-image-pos-x');
	$footer_pos_y      = get_options_data('design-settings', 'footer-background-image-pos-y');
	$footer_repeat     = get_options_data('design-settings', 'footer-background-image-repeat');
	$footer_styles     = '';

	if ($footer_color) $footer_styles .= 'color: '. $footer_color .';';
	if ($footer_background) $footer_styles .= 'background-color: '. $footer_background .';';
	if ($footer_image) {
		$footer_styles .= 'background-image: url('. $footer_image .');';
		if ($footer_repeat == 'fixed') { 
			$footer_pos = $footer_repeat;
			$footer_repeat = 'no-repeat'; 
		} else { 
			$footer_pos = $footer_pos_x .' '. $footer_pos_y;
		}
		$footer_styles .= 'background-position: '. $footer_pos .';';
		$footer_styles .= 'background-repeat: '. $footer_repeat .';';
	}
	if ($footer_styles) {
		$custom_css .= '#footer { '. $footer_styles .' }';
	}

	$custom_css .= stripslashes(htmlspecialchars_decode(get_options_data('content-options', 'custom-css')));


	// Enqueue the CSS to load in header ( $main_stylesheet_handle, $css_data )
	// ----------------------------------------------------------------------------

	wp_add_inline_style( 'liftoff-style', $custom_css );

}
endif;

add_action( 'wp_enqueue_scripts', 'liftoff_custom_css' );


if ( ! function_exists( 'get_liftoff_font_styles' ) ) :
function get_liftoff_font_styles( $alias = false ) {

	if ($alias) {

		$font = array();
		$font_family = '';
		$font['family'] = get_options_data('design-settings', $alias);

		if ($font['family'] == 'google') {
			// Google font
			$font = get_options_data('design-settings', $alias.'-google'); // returns: array( 'family', 'style', 'weight', 'size', 'color' )

			// Load the font
			$googleFont = $font['family'] .':'. $font['weight'];
			if ($font['style'] == 'italic') {
				$googleFont .= ','.$font['weight'].'italic';
			}
			$google_family = str_replace(' ', '+', $googleFont); // make ready for query string	
			$protocol      = is_ssl() ? 'https' : 'http';
			$subsets       = 'latin,latin-ext';
			$query_args    = array( 'family' => $google_family, 'subset' => $subsets );
			wp_enqueue_style( 'google-font-'.sanitize_key($google_family), add_query_arg( $query_args, "$protocol://fonts.googleapis.com/css" ), array(), null );

		} else {
		
			// Standard font styles
			$font['style'] = get_options_data('design-settings', $alias.'-style');
			$font['size']  = get_options_data('design-settings', $alias.'-size');
			$font['color'] = get_options_data('design-settings', $alias.'-color');

			// set the font weight & style
			switch ($font['style']) {
				case 'bold':
					$font['style']  = 'normal';
					$font['weight'] = 'bold';
					break;
				case 'italic':
					$font['style']  = 'italic';
					$font['weight'] = 'normal';
					break;				
				case 'bold-italic':
					$font['style']  = 'italic';
					$font['weight'] = 'bold';
					break;	
				default:
					$font['weight'] = 'normal';
					break;
			}
		}

		// Error checking values
		if (empty($font['color']) || $font['color'] == '#') {
			$font['color'] = '#333';
		}
		if (is_numeric($font['size'])) {
			$font['size'] += 'px';
		}
		if ($font['family'] !== 'default') {
			$font_family = 'font-family: "'.$font['family'].'";';
		}

		// Create the CSS styles
		$css = $font_family.' font-style: '.$font['style'].'; font-weight: '.$font['weight'].'; font-size: '.$font['size'].'; color: '.$font['color'].';';

		return $css;

	} 

}
endif;
