<?php global $ace_config; ?>


		<div class='post-entry'>

			<h1 class='post-title offset-by-three'>
					<a href="<?php echo get_permalink() ?>" rel="bookmark" title="<?php _e('Permanent Link:','ace_framework')?> <?php the_title(); ?>"><?php the_title(); ?></a>
			</h1>
			
			<?php 
				$slider = new ace_slideshow(get_the_ID());
				if($slider) echo $slider->display();
			?>
			
			<!--meta info-->
	        <div class="three units alpha blog-meta meta-color">
	        	
	        	<div class='post-format primary-background flag'>
	        		<span class='post-format-icon post-format-icon-<?php echo get_post_format(); ?>'></span>
	        		<span class='flag-diamond site-background'></span>
	        	</div>
	        	
	        	<div class='blog-inner-meta extralight-border'>
	        	
					<span class='post-meta-infos'>
					
						
						<?php 
						if(comments_open() || get_comments_number())
						{
							echo "<span class='comment-container minor-meta'>";
							comments_popup_link(__('this entry has','ace_framework')." <span>0 ".__('Comments','ace_framework')."</span>", 
												__('this entry has','ace_framework')." <span>1 ".__('Comment' ,'ace_framework')."</span>",
												__('this entry has','ace_framework')." <span>% ".__('Comments','ace_framework')."</span>",'comments-link',
												__('Comments Off'  ,'ace_framework')); 	
							echo "</span><span class='text-sep'>/</span>";	 
						}
						
						
						?>
						 
	
						<?php
						$cats = get_the_category();
						
						if(!empty($cats))
						{
							echo '<span class="blog-categories minor-meta">'.__('in ','ace_framework');
							the_category(', ');
							echo ' </span><span class="text-sep">/</span> ';
						}
						
						$portfolio_cats = get_the_term_list(  get_the_ID(), 'portfolio_entries', '', ', ','');
						
						if($portfolio_cats && !is_object($portfolio_cats))
						{
							echo '<span class="blog-categories minor-meta">'.__('in ','ace_framework');
							echo $portfolio_cats;
							echo ' </span><span class="text-sep">/</span> ';
						}
						
						
						echo '<span class="blog-author minor-meta">'.__('by ','ace_framework');
						the_author_posts_link(); 
						echo '</span>';
						
						
						?>
					
					</span>	
					
				</div>	
				
			</div><!--end meta info-->	
			

			<div class="six units entry-content">	
			<span class='date-container minor-meta meta-color'><?php echo get_the_date(); ?></span>	
				<?php 
				
				if(is_singular() && ace_post_meta('hero'))
				{
					echo "<div class='hero-text entry-content'>";
					the_excerpt();
					echo "<span class='seperator extralight-border'></span>";
					echo "</div>";
				}
				
				the_content(__('Read more  &rarr;','ace_framework'));  
				
				if(has_tag() && is_single())
				{	
					echo '<span class="blog-tags">';
					echo the_tags('<strong>'.__('Tags: ','ace_framework').'</strong><span>'); 
					echo '</span></span>';
				}	
				?>	
								
			</div>	
			

		</div><!--end post-entry-->