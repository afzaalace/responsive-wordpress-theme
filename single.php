<?php 
global $ace_config;

	ace_get_template();

	/*
	 * get_header is a basic wordpress function, used to retrieve the header.php file in your theme directory.
	 */	
	 get_header();
	 
	?>

		<!-- ####### MAIN CONTAINER ####### -->
		<div class='container_wrap <?php echo $ace_config['layout']; ?>' id='main'>
		
			<div class='container template-blog template-single-blog'>
				
				
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
				
				
				<div class='content units <?php echo $ace_config['content_class']; ?>'>
				
				<?php
				/* Run the loop to output the posts.
				* If you want to overload this in a child theme then include a file
				* called loop-index.php and that will be used instead.
				*
				*/
				
					get_template_part( 'includes/loop', 'index' );
					
					?>
					<div class='post_nav'>
						<div class='previous_post_link_align'>
							<?php previous_post_link('<span class="previous_post_link">&larr; %link </span><span class="post_link_text">'.__('(previous entry)'))."</span>"; ?>
						</div>
						<div class='next_post_link_align'>
							<?php next_post_link('<span class="next_post_link"><span class="post_link_text">'.__('(next entry)').'</span> %link &rarr;</span>'); ?>
						</div>
					</div> <!-- end navigation -->
					<?php
					//show related posts based on tags if there are any
					get_template_part( 'includes/related-posts');
					
					//wordpress function that loads the comments template "comments.php"
					comments_template( '/includes/comments.php'); 
				
				?>
				
				
				<!--end content-->
				</div>
				
				<?php 
				$ace_config['currently_viewing'] = "blog";
				//get the sidebar
				get_sidebar();
				
				?>
				
			</div><!--end container-->

	</div>
	<!-- ####### END MAIN CONTAINER ####### -->


<?php get_footer(); ?>