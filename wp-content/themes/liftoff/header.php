<?php
/**
 * The Header for our theme.
 *
 * Displays all of the <head> section and everything up till main content
 */
?><!DOCTYPE html>
<!--[if IE 7]><html class="ie ie7" <?php language_attributes(); ?>><![endif]-->
<!--[if IE 8]><html class="ie ie8" <?php language_attributes(); ?>><![endif]-->
<!--[if !(IE 7) | !(IE 8)  ]><!-->
<html <?php language_attributes(); ?>>
<!--<![endif]-->
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>" />
<meta name="viewport" content="width=device-width" />
<title><?php wp_title( '|', true, 'right' ); ?></title>
<link rel="profile" href="http://gmpg.org/xfn/11" />
<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>" />
<?php // Loads HTML5 JavaScript file to add support for HTML5 elements in older IE versions. ?>
<!--[if lt IE 9]>
<script src="<?php echo get_template_directory_uri(); ?>/js/html5.js" type="text/javascript"></script>
<![endif]-->

<?php wp_head(); ?>

<script type="text/javascript">
	<?php

	# -----------------------------------------------------------------------------
	# Custom JavaScript from Theme Options
	# -----------------------------------------------------------------------------

	echo stripslashes(htmlspecialchars_decode(get_options_data('content-options', 'custom-js'))); 

	?>
</script>

</head>

<body <?php body_class(); ?>>
<div id="page" class="hfeed site">
	<header id="masthead" role="banner">
		<div class="site-header page-width">
			<hgroup class="header-title"><?php

				# -----------------------------------------------------------------------------
				# Site Title and Logo
				# -----------------------------------------------------------------------------

				$logo_image   = get_options_data('design-settings', 'logo');
				$logo_title   = get_options_data('design-settings', 'logo-title'); 
				$logo_tagline = get_options_data('design-settings', 'logo-tagline'); 
				$logo_link    = get_options_data('design-settings', 'logo-link');

				// Set defaults
				$logo_title   = ($logo_title) ? $logo_title : get_bloginfo('name'); 
				$logo_link    = ($logo_link) ? $logo_link : home_url('/'); 

				if (!$logo_image) {

					// Text logo
					// ----------------------------------------------------------------------------
					echo '<h1 class="site-title"><a href="'. esc_url( $logo_link ) .'" title="'. esc_attr( $logo_title ) .'" rel="home">'. $logo_title .'</a></h1>';
					if ($logo_tagline == 'true') {
						// Tagline
						echo '<h2 class="site-description">'. bloginfo( 'description' ) .'</h2>';
					}

				} else {

					// Image logo
					// ----------------------------------------------------------------------------
					echo '<h1 class="site-title"><a href="'. esc_url( $logo_link ) .'" title="'. esc_attr( $logo_title ) .'" rel="home"><img src="'. $logo_image .'" alt="'. esc_attr( $logo_title ) .'"></a></h1>';
				}
			?>
			</hgroup>

			<nav id="site-navigation" class="main-navigation" role="navigation">
				<h3 class="menu-toggle"><?php _e( 'Menu', 'liftoff' ); ?></h3>
				<a class="assistive-text" href="#content" title="<?php esc_attr_e( 'Skip to content', 'liftoff' ); ?>"><?php _e( 'Skip to content', 'liftoff' ); ?></a>
				<?php wp_nav_menu( array( 'theme_location' => 'primary', 'menu_class' => 'nav-menu' ) ); ?>
			</nav><!-- #site-navigation -->
			<div class="clear"></div>
		</div>
		<div class="entry-content page-width">
			<?php 
			
			// Header Content
			// ----------------------------------------------------------------------------
			echo wpautop(stripslashes(htmlspecialchars_decode(get_options_data('content-options', 'header-content')))); 

			?>
		</div>
		<div class="clear"></div>
	</header><!-- #masthead -->

	<div id="main" class="wrapper">
		<?php 

		// If using the Layout Manager extension
		// ----------------------------------------------------------------------------
		do_action('output_layout','start'); 

		?>