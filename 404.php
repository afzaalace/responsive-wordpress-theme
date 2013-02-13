<?php 
global $ace_config;

	/*
	 * get_header is a basic wordpress function, used to retrieve the header.php file in your theme directory.
	 */	
	 get_header();

	?>
		
		<!-- ####### MAIN CONTAINER ####### -->
		<div class='container_wrap <?php echo $ace_config['layout']; ?>' id='main'>
						
			<div class='container'>
			
				<?php echo ace_title(__('Error 404 - page not found', 'ace_framework')); ?>

				<div class='template-page content <?php echo $ace_config['content_class']; ?> units'>
				
					<div class="entry entry-content" id='search-fail'>
					<?php get_template_part('includes/error404'); ?>
				</div>
				
				
				<!--end content-->
				</div>
				
				<?php 

				//get the sidebar
				$ace_config['currently_viewing'] = 'page';
				get_sidebar();
				
				?>
				
			</div><!--end container-->

	</div>
	<!-- ####### END MAIN CONTAINER ####### -->


<?php get_footer(); ?>