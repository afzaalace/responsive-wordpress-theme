<?php 

	global $ace_config, $more;

	$ace_config['new_query'] = array( "paged" => get_query_var( 'paged' ), "posts_per_page"=>get_option('posts_per_page') ) ;

	/*
	 * get_header is a basic wordpress function, used to retrieve the header.php file in your theme directory.
	 */	
	 get_header();
 		
 	ace_template_set_page_layout('blog_layout');
	?>


		<!-- ####### MAIN CONTAINER ####### -->
		<div class='container_wrap <?php echo $ace_config['layout']; ?>' id='main'>
		
			<div class='container template-blog '>	
				
				<?php 
					
					$title  = __('Blog - Latest News', 'ace_framework'); //default blog title
					$t_link = "<a href='".home_url('/')."'>".$title."</a>";
					
					if(ace_get_option('frontpage') && $new = ace_get_option('blogpage')) 
					{ 
						$title 	= get_the_title($new); //if the blog is attached to a page use this title
						$t_link = "<a href='".get_permalink($new)."'>".$title."</a>"; 
					}
					
					ace_title($t_link); 
					
				 ?>
				
				
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
				wp_reset_query();
				//get the sidebar
				$ace_config['currently_viewing'] = 'blog';
				get_sidebar();
				
				?>
				
			</div><!--end container-->

	</div>
	<!-- ####### END MAIN CONTAINER ####### -->


<?php get_footer(); ?>