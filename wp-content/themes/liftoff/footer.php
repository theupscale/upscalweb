<?php
/**
 * The template for displaying the footer.
 *
 * Contains footer content and the closing of the
 * #main and #page div elements.
 */
?>
	<?php 

	// If using the Layout Manager extension
	// ----------------------------------------------------------------------------
	do_action('output_layout','end');

	?>

	</div><!-- #main .wrapper -->

	<footer id="footer" role="contentinfo">
		<div class="top-border clear"></div>
		<div class="footer-content entry-content page-width">
			<?php 
			
			// Footer Content
			// ----------------------------------------------------------------------------

			$footer_content = get_options_data('content-options', 'footer-content');

			if (empty($footer_content)) {
				echo '<p style="text-align:center">Built with the <a href="http://runwaywp.com" rel="nofollow">Runway WordPress framework</a> and powered by <a href="http://wordpress.org" rel="nofollow">WordPress</a><br>Copyright &copy; '. date('Y') .'. Created by the team at <a href="http://para.llel.us" rel="nofollow">Parallelus</a>.</p>';
			} else {
				echo wpautop(stripslashes(htmlspecialchars_decode( $footer_content )));
			}

			?>
		</div><!-- .site-info -->
	</footer><!-- #colophon -->
</div><!-- #page -->

<?php wp_footer(); ?>
</body>
</html>