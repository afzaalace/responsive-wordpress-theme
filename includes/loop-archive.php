<?php 
global $ace_config;
if(isset($ace_config['new_query'])) { query_posts($ace_config['new_query']); }

// check if we got posts to display:
if (have_posts()) :

	while (have_posts()) : the_post();	
	
?>

		<div class='post-entry'>
			

			<!--meta info-->
	        <div class="blog-meta grid3">
	        	
	        	<?php
				//force to display small inline content slider on archive pages. Single pages and posts are allowed to display the 3d slider
				$force_small_slider = true;
				if(is_singular()) $force_small_slider = false;
				
				$slider = new ace_slideshow(get_the_ID());
	 	 		echo $slider->display_small('portfolio', $force_small_slider);
				?>
	        	
				<span class='post-date-comment-container'>
					<span class='date-container'><strong><?php the_time('d') ?> <?php the_time('M') ?></strong><span><?php the_time('Y') ?></span></span>
					<span class='comment-container'><?php comments_popup_link("<strong>0</strong> ".__('Comments','ace_framework'), "<strong>1</strong> ".__('Comment' ,'ace_framework'),
																			  "<strong>%</strong> ".__('Comments','ace_framework'),'comments-link',
																			  "<strong></strong> ".__('Comments<br/>Off','ace_framework')
																			  ); ?>
					</span>
					
					
				</span>	

			</div><!--end meta info-->
			
			<div class="entry-content">	
				
				<h1 class='post-title'>
					<a href="<?php echo get_permalink() ?>" rel="bookmark" title="<?php _e('Permanent Link:','ace_framework')?> <?php the_title(); ?>"><?php the_title(); ?></a>
				</h1>
				<?php
					$cats = get_the_category();
					
					echo '<span class="minor-meta-wrap">';
					if(!empty($cats))
					{
						echo '<span class="blog-categories minor-meta">';
						echo '<strong>Categories: </strong><span>';
						the_category(', ');
						echo '</span></span>';
					}
					
					if(has_tag())
					{	
						echo '<span class="blog-tags minor-meta">';
						echo the_tags('<strong>'.__('Tags: ','ace_framework').'</strong><span>'); 
						echo '</span></span>';
					}	
						echo '<span class="blog-author minor-meta">';
						echo '<strong>Author: </strong><span>';
						the_author_posts_link(); 
						echo '</span></span>';
					
					echo '</span>';
			
				
				the_content(__('Read more  &rarr;','ace_framework'));  ?>	
								
			</div>							
		
		
		</div><!--end post-entry-->
		
		
<?php 
	endwhile;		
	else: 
?>	
	
	<div class="entry">
		<h1 class='post-title'><?php _e('Nothing Found', 'ace_framework'); ?></h1>
		<p><?php _e('Sorry, no posts matched your criteria', 'ace_framework'); ?></p>
	</div>
<?php

	endif;
	
	if(!isset($ace_config['remove_pagination'] ))
		echo ace_pagination();	
?>