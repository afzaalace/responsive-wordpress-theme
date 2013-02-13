<?php 
	global $ace_config, $more;

	/*
	 * get_header is a basic wordpress function, used to retrieve the header.php file in your theme directory.
	 */	
	 get_header();
 		
	?>


		<!-- ####### MAIN CONTAINER ####### -->
		<div class='container_wrap <?php echo $ace_config['layout']; ?>' id='main'>
		
			<div class='container template-blog '>	
				
				<?php echo ace_title(ace_which_archive()); ?>
				
				<div class='content <?php echo $ace_config['content_class']; ?> units'>
				<?php
				
				/* Run the loop to output the posts.
				* If you want to overload this in a child theme then include a file
				* called loop-index.php and that will be used instead.
				*/
				
				
				$more = 0;
				get_template_part( 'includes/loop', 'index' );
				?>
				
				
				<!--end content-->
				</div>
				
				<?php 

				//get the sidebar
				$ace_config['currently_viewing'] = 'blog';
				get_sidebar();
				
				?>
				
			</div><!--end container-->

	</div>
	<!-- ####### END MAIN CONTAINER ####### -->


<?php get_footer(); ?>