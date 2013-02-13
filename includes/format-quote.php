
<?php global $ace_config; ?>


		<div class='post-entry'>
		
			<?php 
				$slider = new ace_slideshow(get_the_ID());
				echo $slider->display();
			?>

			<!--meta info-->
	        <div class="three units alpha blog-meta meta-color blog-meta-<?php echo get_post_format(); ?>">
	        	
	        	<div class='post-format primary-background flag'>
	        		<span class='post-format-icon post-format-icon-<?php echo get_post_format(); ?>'></span>
	        		<span class='flag-diamond site-background'></span>
	        	</div>
	        	
	        	<div class='blog-inner-meta extralight-border'>
	        	
					<span class='post-meta-infos'>
					
						<span class='date-container-mod minor-meta meta-color'><?php echo get_the_date(); ?></span>	
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
						
						echo '<span class="blog-author minor-meta">'.__('by ','ace_framework');
						the_author_posts_link(); 
						echo '</span><span class="text-sep">/</span>';
						
						
						echo '<span class="blog-permalink minor-meta">';
						echo "<a href='".get_permalink()."'>".__('#permalink','ace_framework')."</a>";
						echo '</span>';
						
						?>
					
					</span>	
					
				</div>	
				
			</div><!--end meta info-->	
			

			<div class="six units entry-content">	
			
			
			<blockquote class='first-quote'>
					<?php the_title(); ?>
			</blockquote>
			
				<?php 
				
				if(is_singular() && ace_post_meta('hero'))
				{
					echo "<div class='hero-text entry-content'>";
					the_excerpt();
					echo "<span class='seperator extralight-border'></span>";
					echo "</div>";
				}
				
				echo "<div class='quote-content'>";
				the_content(__('Read more  &rarr;','ace_framework'));  
				echo "</div>";
				
				if(has_tag() && is_single())
				{	
					echo '<span class="blog-tags">';
					echo the_tags('<strong>'.__('Tags: ','ace_framework').'</strong><span>'); 
					echo '</span></span>';
				}	
				?>	
								
			</div>	
			

		</div><!--end post-entry-->