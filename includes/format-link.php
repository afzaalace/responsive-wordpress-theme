
<?php global $ace_config;  
			
//retrieve the link for the post
$link 		= "";
$title		= "";
$content 	= get_the_content();
$pattern1 	= '/^(([\w]+:)?\/\/)?(([\d\w]|%[a-fA-f\d]{2,2})+(:([\d\w]|%[a-fA-f\d]{2,2})+)?@)?([\d\w][-\d\w]{0,253}[\d\w]\.)+[\w]{2,4}(:[\d]+)?(\/([-+_~.\d\w]|%[a-fA-f\d]{2,2})*)*(\?(&amp;?([-+_~.\d\w]|%[a-fA-f\d]{2,2})=?)*)?(#([-+_~.\d\w]|%[a-fA-f\d]{2,2})*)?/';
$pattern2 	= "!^\<a.+?<\/a>!";

//if the url is at the begnning of the content extract it
preg_match($pattern1, $content, $link);
if(!empty($link[0])) 
{
	$link = $link[0];
	$title = "<a href='$link' rel='bookmark' title='".__('Link to: ','ace_framework').get_the_title()."' >".get_the_title()."</a>";
}
else
{
	preg_match($pattern2, $content, $link);
	if(!empty($link[0])) 
	{
		$link = $title = $link[0];
	}
}

if(!$title) $title = get_the_title();

?>
			
			



		<div class='post-entry'>
		
			<h1 class='post-title offset-by-three'>
					<?php echo "<span class='link-title'>".__("Link:",'ace_framework')."</span> ".$title; ?>
			</h1>
		
			<?php 
				$slider = new ace_slideshow(get_the_ID());
				echo $slider->display();
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
			<span class='date-container minor-meta meta-color'><?php echo get_the_date(); ?></span>	
				<?php 
				
				if(is_singular() && ace_post_meta('hero'))
				{
					echo "<div class='hero-text entry-content'>";
					the_excerpt();
					echo "<span class='seperator extralight-border'></span>";
					echo "</div>";
				}
				
				echo apply_filters('the_content', str_replace($link, "", get_the_content(__('Read more  &rarr;','ace_framework'))));  
				
				if(has_tag() && is_single())
				{	
					echo '<span class="blog-tags">';
					echo the_tags('<strong>'.__('Tags: ','ace_framework').'</strong><span>'); 
					echo '</span></span>';
				}	
				?>	
								
			</div>	
			

		</div><!--end post-entry-->