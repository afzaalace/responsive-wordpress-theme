<?php 
global $ace_config;
if(isset($ace_config['new_query'])) { query_posts($ace_config['new_query']); }

// check if we got posts to display:
if (have_posts()) :

	while (have_posts()) : the_post();	
	$slider = new ace_slideshow(get_the_ID());
	$image_size = "page";
?>

		<div class='post-entry'>
			
			<?php 
				$slider = new ace_slideshow(get_the_ID());
				if($slider) echo $slider->display();
			?>

			
			<div class="entry-content">	
				
				<?php 
				
				if(is_singular() && ace_post_meta('hero'))
				{
					echo "<div class='hero-text entry-content'>";
					the_excerpt();
					echo "<span class='seperator extralight-border'></span>";
					echo "</div>";
				}
				
				
				//echo "<h1 class='post-title'>".get_the_title()."</h1>";
				//display the actual post content
				the_content(__('Read more  &rarr;','ace_framework')); 
				
				//check if this is the contact form page, if so display the form
                $contact_page_id = ace_get_option('email_page');
                
                //wpml prepared
                if (function_exists('icl_object_id'))
                {
                    $contact_page_id = icl_object_id($contact_page_id, 'page', true);
                }
                
				if(isset($post->ID) && $contact_page_id == $post->ID) get_template_part( 'includes/contact-form' );
			
				 ?>	
								
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