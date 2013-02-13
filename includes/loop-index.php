<?php 
global $ace_config;
if(isset($ace_config['new_query'])) { query_posts($ace_config['new_query']); }

// check if we got posts to display:
if (have_posts()) :

	while (have_posts()) : the_post();	
	if(empty($ace_config['layout'])) $ace_config['layout'] = "sidebar_right";
	
		//retrieve the post format that the user selected for this post
		if(!get_post_format()) 
		{
			get_template_part('includes/format', 'standard');
		} 
		else 
		{
			get_template_part('includes/format', get_post_format());
		}
			
	endwhile;		
	else: 
?>	
	
	<div class="entry">
		<h1 class='post-title'><?php _e('Nothing Found', 'ace_framework'); ?></h1>
		<p><?php _e('Sorry, no posts matched your criteria', 'ace_framework'); ?></p>
	</div>
	
<?php

	endif;
	
	if(!isset($ace_config['remove_pagination'] )) echo ace_pagination();	
?>