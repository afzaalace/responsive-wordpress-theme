<?php 
/*
* The Loop for single portfolio Items. Works in conjunction with the file single-portfolio.php
*/



global $ace_config;
if(isset($ace_config['new_query'])) { query_posts($ace_config['new_query']); }

// check if we got posts to display:
if (have_posts()) :

	while (have_posts()) : the_post();	
	$slider = new ace_slideshow(get_the_ID());
	$slider->setImageSize('fullsize');
	$slideHtml = $slider->display();
?>

		<div class='post-entry'>
			
			
			<?php 
								
			//call the function that displays featured images and slideshows within posts
			
 	 		
 	 		if($slideHtml)
 	 		{
 	 			echo "<div class='container_wrap' id='slideshow_big'><div class='container'>".$slideHtml."</div></div>";	
 	 			$ace_config['slider_first_post_active'] = true;
 	 		}
			?>

			
			<div class="entry-content">	
			
				<div class="three units alpha blog-meta meta-color">
		        	<?php echo "<h1 class='post-title portfolio-single-post-title'>".get_the_title()."</h1>"; ?>
		        	
		        	<div class='portfolio-inner-meta extralight-border'>
		        	
						<span class='post-meta-infos'>
							
							<?php
							
							$portfolio_cats = get_the_term_list(  get_the_ID(), 'portfolio_entries', '', ', ','');
							
							if($portfolio_cats && !is_object($portfolio_cats))
							{
								echo '<span class="portfolio-categories minor-meta">'.__('in ','ace_framework');
								echo $portfolio_cats;
								echo ' </span><span class="text-sep">/</span> ';
							}
							
							
							/*
							echo '<span class="blog-author minor-meta">'.__('by ','ace_framework');
							the_author_posts_link(); 
							echo '</span>';
							*/
							
							?>
						</span>	
						
						
						
					</div>	
					
				</div><!--end meta info-->	
				
				
				<?php 
				
				echo "<div class='nine units'>";
				
				if(ace_post_meta('hero'))
				{
					echo "<div class='hero-text entry-content'>";
					the_excerpt();
					echo "<span class='seperator extralight-border'></span>";
					echo "</div>";
				}
				
				//display the actual post content
				the_content(__('Read more  &rarr;','ace_framework')); 
				echo ace_flag(false);
			 ?>	
				
				
				<div class='post_nav'>
						<div class='previous_post_link_align'>
							<?php previous_post_link('<span class="previous_post_link">&larr; %link </span><span class="post_link_text">'.__('(previous entry)'))."</span>"; ?>
						</div>
						<div class='next_post_link_align'>
							<?php next_post_link('<span class="next_post_link"><span class="post_link_text">'.__('(next entry)').'</span> %link &rarr;</span>'); ?>
						</div>
					</div> <!-- end navigation -->
			
				</div>	<!-- end nine units -->	
									
			</div>							
		
		
		</div><!--end post-entry-->
		
		
<?php 
	endwhile;		
	else: 
?>	
	
	<div class="entry">
		<h1 class='post-title'><?php _e('Nothing Found', 'ace_framework'); ?></h1>
		<?php get_template_part('includes/error404'); ?>
	</div>
<?php

	endif;
?>