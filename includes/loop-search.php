<?php 
global $ace_config;
if(isset($ace_config['new_query'])) { query_posts($ace_config['new_query']); }

// check if we got posts to display:
if (have_posts()) :
	$first = true;
	
	$counterclass = "";
	$counter = 1;
	$page = (get_query_var('paged')) ? get_query_var('paged') : 1;
	if($page > 1) $counter = (int) ($page - 1) * (int) get_query_var('posts_per_page');
	

	
	while (have_posts()) : the_post();	
	
		//check for preview images:
		$image = "";
		$slides = ace_post_meta('slideshow');
		
		if( is_array($slides) && !empty( $slides[0]['slideshow_image'] ) )
		{
			$image = ace_image_by_id($slides[0]['slideshow_image'], 'widget', 'image');
		}
		
		if(!$first) echo ace_flag(false);
		echo "<a class='post-entry news-content' title='".get_the_title()."' href='".get_permalink()."'>";
		echo "<span class='search-result-counter $counterclass'>$counter</span>";
		echo "<span class='news-link'>";
		if($image)
		{
			echo "<span class='news-thumb'>";
			echo $image;
			echo "</span>";
		}
		
		echo "<span class='news-excerpt'>";
		echo "<strong class='news-headline'>".get_the_title();
		echo "<span class='news-time'>".get_the_time("F j, Y, g:i a")."</span>";
		echo "</strong>";
		
		the_excerpt();
		echo "</span>";
		
		echo "</span>";
		
		echo "</a><!--end post-entry-->";
		$first = false;
		$counter++;
		if($counter >= 100) $counterclass = "nowidth";
	endwhile;		
	else: 
	
	
?>	
	
	<div class="entry entry-content" id='search-fail'>
		<p><strong><?php _e('Nothing Found', 'ace_framework'); ?></strong><br/>
		   <?php _e('Sorry, no posts matched your criteria. Try another search?', 'ace_framework'); ?>
		   <?php get_search_form(); ?>
	    </p>
		
		<div class='hr_invisible'></div>  
		
		<?php _e('You might want to consider some of our suggestions to get better results:', 'ace_framework'); ?></p>
		<ul>
			<li><?php _e('Check your spelling.', 'ace_framework'); ?></li>
			<li><?php _e('Try a similar keyword, for example: tablet instead of laptop.', 'ace_framework'); ?></li>
			<li><?php _e('Try using more than one keyword.', 'ace_framework'); ?></li>
		</ul>
		
		<div class='hr_invisible'></div>
		<h3 class=''><?php _e('Feel like browsing some posts instead?', 'ace_framework'); ?></h3>

<?php
the_widget('ace_combo_widget', 'error404widget', array('widget_id'=>'arbitrary-instance-'.$id,
        'before_widget' => '<div class="widget ace_combo_widget">',
        'after_widget' => '</div>',
        'before_title' => '<h3 class="widgettitle">',
        'after_title' => '</h3>'
    ));
	
	echo "</div>";

	endif;
	echo ace_pagination();	
?>