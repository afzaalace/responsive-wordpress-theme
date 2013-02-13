<?php 
/*
* The Loop for portfolio overview pages. Works in conjunction with the file template-portfolio.php and taxonomy-portfolio_entries.php
*/



global $ace_config;
if(isset($ace_config['new_query'])) { query_posts($ace_config['new_query']); }

$loop_counter = 1;
// check if we got a page to display:
if (have_posts()) :
	
	$extraClass = 'first';
	$style = 'portfolio-entry-no-description';
	
	$grid = 'one_fourth';
	$image_size = 'portfolio';

	
	
	switch($ace_config['portfolio_columns'])
	{
		case "1": $grid = 'fullwidth';  $image_size = 'fullsize'; break;
		case "2": $grid = 'one_half';   break;
		case "3": $grid = 'one_third';  break;
		case "4": $grid = 'one_fourth'; break;
	}
	
	$ace_config['portfolio_columns_iteration'] = $ace_config['portfolio_columns'][0];
	if(!isset($ace_config['remove_portfolio_text'])) $style = 'portfolio-entry-description';
	
	
	$includeArray = "";
	if(isset($ace_config['new_query']['tax_query'][0]['terms'])) $includeArray = $ace_config['new_query']['tax_query'][0]['terms'];
	
	$args = array(
	
		'taxonomy'	=> 'portfolio_entries',
		'hide_empty'=> 0,
		'include'	=> $includeArray
	
	);

	$categories = get_categories($args);
	$container_id = "";
	
	
	if(!isset($ace_config['portfolio_sorting']) || $ace_config['portfolio_sorting'] == 'yes')
	{
		if(!empty($categories[0]))
		{
			$output = "<div id='js_sort_items' >";
	
			$hide = "hidden";
			if (isset($categories[1])){ $hide = ""; }
			
			$output .= "<div class='sort_by_cat $hide '>";
			$output .= "<a href='#' data-filter='all_sort' class='active_sort'>".__('All','ace_framework')."</a>";
			
			foreach($categories as $category)
			{
				$output .= "<span class='text-sep'>/</span><a href='#' data-filter='".$category->category_nicename."_sort'>".$category->cat_name."</a>";
				$container_id .= $category->term_id;
			}
			
			$output .= "</div>";
			
	
			$output .= "</div>";
			
			echo $output;
		}
	}
	

	echo "<div class='portfolio-sort-container'>";	
	//iterate over the posts
	while (have_posts()) : the_post();	
	
	//get the categories for each post and create a string that serves as classes so the javascript can sort by those classes
	$sort_classes = "";
	$item_categories = get_the_terms( $id, 'portfolio_entries' );

	if(is_object($item_categories) || is_array($item_categories))
	{
		foreach ($item_categories as $cat)
		{
			$sort_classes .= $cat->slug.'_sort ';
		}
	}
		

?>

		
		<div class='post-entry flex_column all_sort <?php echo $sort_classes.' '.$grid.' '.$extraClass.' '.$style; ?>'>
														
			<?php 
				$forceSmall = true;
				$slider = new ace_slideshow(get_the_ID());
				$slider -> setImageSize($image_size);
				echo $slider->display($forceSmall);
			
 	 		
 	 		//$showcaption
			
			if(!isset($ace_config['remove_portfolio_text']))
			{
				echo '<div class="post-content">';
				echo "<h1 class='post-title'>";
				echo "<a href='".get_permalink()."' rel='bookmark' title='".__('Permanent Link:','ace_framework')." ".get_the_title()."'>".get_the_title()."</a>";
				echo "</h1>";
				
				echo '<div class="entry-content">';
					 
				if($ace_config['portfolio_columns_iteration'] == 1)
				{
					echo '<span class="portfolio-categories minor-meta">';
					echo get_the_term_list(  get_the_ID(), 'portfolio_entries', '<strong>'.__('Categories','ace_framework').': </strong><span>', '</span><span class="text-sep">/</span><span>','</span>');
					echo '</span>';

				}
				
				the_excerpt(); 
				echo '<a class="more-link" href="'. get_permalink().'">'.__('Read more  &rarr;','ace_framework').'</a>';
				echo "</div>";
				echo "</div>";
				
			}
			else
			{
				$hr_class = 'hr hr_invisible';
			}
			 
		?>				        
		<!-- end post-entry-->
		</div>
	
	<?php 

	$loop_counter++;
	$extraClass = "";

	if($loop_counter > $ace_config['portfolio_columns_iteration'])
	{
		$loop_counter = 1;
		$extraClass = 'first';
	}


	endwhile;
	
	echo "</div>";	// end portfolio-sort-container

	
	
	if(!isset($ace_config['remove_pagination'] ))
	{
		echo "<div class='hr hr_invisible'></div>";
		echo ace_pagination();	
	}	
	echo "<!-- end -->"; //dont remove
	else: 
?>	
	
	<div class="entry">
		<h1 class='post-title'><?php _e('Nothing Found', 'ace_framework'); ?></h1>
		<p><?php _e('Sorry, no posts matched your criteria', 'ace_framework'); ?></p>
	</div>
<?php

	


	endif;
	
		
?>