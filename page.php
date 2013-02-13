<?php 
global $ace_config;

	/*
	 * check which page template should be applied: 
	 * cecks for dynamic pages as well as for portfolio, fullwidth, blog, contact and any other possibility :)
	 * Be aware that if a match was found another template wil be included and the code bellow will not be executed
 	 * located at the bottom of includes/helper-templates.php
	 */
	 ace_get_template();


	/*
	 * get_header is a basic wordpress function, used to retrieve the header.php file in your theme directory.
	 */	
	 get_header();
 	 
	?>
		
		<!-- ####### MAIN CONTAINER ####### -->
		<div class='container_wrap <?php echo $ace_config['layout']; ?>' id='main'>
		
			<div class='container'>

				<?php 
					if(empty($ace_config['slide_output'])) 
					{
						ace_title(); 
					}
					else
					{
						echo "<h1>".get_the_title(ace_get_the_id())."</h1>";
					}
				?>
				<div class='template-page content <?php echo $ace_config['content_class']; ?> units'>

				<?php
				/* Run the loop to output the posts.
				* If you want to overload this in a child theme then include a file
				* called loop-page.php and that will be used instead.
				*/
				$ace_config['size'] = 'page';
				get_template_part( 'includes/loop', 'page' );
				?>
				
				
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