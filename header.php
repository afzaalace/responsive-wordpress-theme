<?php $style = ace_get_option('boxed','stretched'); ?>

<!DOCTYPE html>
<html <?php language_attributes(); ?> class="<?php echo ace_get_browser('class', true); echo " html_$style";?> ">
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>" />
<?php 
	global $ace_config;

	/*
	 * outputs a rel=follow or nofollow tag to circumvent google duplicate content for archives
	 * located in framework/php/function-set-ace-frontend.php
	 */
	 if (function_exists('ace_set_follow')) { echo ace_set_follow(); }
	 
	 
	 /*
	 * outputs a favicon if defined
	 */
	 if (function_exists('ace_favicon'))    { echo ace_favicon(ace_get_option('favicon')); }
	 
?>


<!-- page title, displayed in your browser bar -->
<title><?php bloginfo('name'); ?> | <?php is_home() ? bloginfo('description') : wp_title(''); ?></title>


<!-- add feeds, pingback and stuff-->
<link rel="profile" href="http://gmpg.org/xfn/11" />
<link rel="alternate" type="application/rss+xml" title="<?php echo get_bloginfo('name'); ?> RSS2 Feed" href="<?php ace_option('feedburner',get_bloginfo('rss2_url')); ?>" />
<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>" />


<!-- add css stylesheets -->	
<link rel="stylesheet" type="text/css" media="all" href="<?php bloginfo( 'stylesheet_url' ); ?>" />
<link rel="stylesheet" href="<?php echo get_bloginfo('template_url'); ?>/css/grid.css" type="text/css" media="screen"/>
<link rel="stylesheet" href="<?php echo get_bloginfo('template_url'); ?>/css/base.css" type="text/css" media="screen"/>
<link rel="stylesheet" href="<?php echo get_bloginfo('template_url'); ?>/css/layout.css" type="text/css" media="screen"/>
<link rel="stylesheet" href="<?php echo get_bloginfo('template_url'); ?>/css/slideshow.css" type="text/css" media="screen"/>
<link rel="stylesheet" href="<?php echo get_bloginfo('template_url'); ?>/css/shortcodes.css" type="text/css" media="screen"/>



<link rel="stylesheet" href="<?php echo get_bloginfo('template_url'); ?>/js/prettyPhoto/css/prettyPhoto.css" type="text/css" media="screen"/>
<link rel="stylesheet" href="<?php echo get_bloginfo('template_url'); ?>/js/projekktor/theme/style.css" type="text/css" media="screen"/>


<!-- mobile setting -->
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">

<?php

	/* add javascript */
	wp_enqueue_script( 'jquery' );
	wp_enqueue_script( 'ace-default' );
	wp_enqueue_script( 'ace-prettyPhoto' );
	wp_enqueue_script( 'ace-html5-video' );
	wp_enqueue_script( 'adaptace-slider' );


	/* We add some JavaScript to pages with the comment form
	 * to support sites with threaded comments (when in use).
	 */
	if ( is_singular() && get_option( 'thread_comments' ) ) { wp_enqueue_script( 'comment-reply' ); }
	
?>

<!-- plugin and theme output with wp_head() -->
<?php 

	/* Always have wp_head() just before the closing </head>
	 * tag of your theme, or you will break many plugins, which
	 * generally use this hook to add elements to <head> such
	 * as styles, scripts, and meta tags.
	 */
	 
	wp_head();
?>


<link rel="stylesheet" href="<?php echo get_bloginfo('template_url'); ?>/css/custom.css" type="text/css" media="screen"/>

</head>



<?php 
/*
 * prepare big slideshow if available
 * If we are displaying a dynamic template the slideshow might already be set
 * therefore we dont need to call it here
 */

if(!ace_special_dynamic_template())
{
	ace_template_set_page_layout();
	if(isset($post))
	{
		$slider = new ace_slideshow(ace_get_the_ID());
		$ace_config['slide_output'] =  $slider->display_big();
	}
}


?>


<body id="top" <?php body_class($style); ?>>

	<div id='wrap_all'>	
	
			<?php 	
					$position = ace_shop_banner();
					ace_banner($position);   // ace_banner functions located in functions.php - creates the notification at the top of the site as well as the shopping cart 
			?>
				  
			<!-- ####### HEAD CONTAINER ####### -->
						
				<div class='container_wrap' id='header'>
				
						<div class='container'>

						<?php  
						/*
						*	display the theme logo by checking if the default css defined logo was overwritten in the backend.
						*   the function is located at framework/php/function-set-ace-frontend-functions.php in case you need to edit the output
						*/
						echo ace_logo(ACE_BASE_URL.'images/layout/logo.png');
						
						/*
						*	display the main navigation menu
						*   check if a description for submenu items was added and change the menu class accordingly
						*   modify the output in your wordpress admin backend at appearance->menus
						*/
						echo "<div class='main_menu' data-selectname='".__('Select a page','ace_framework')."'>";
						$args = array('theme_location'=>'ace', 'fallback_cb' => 'ace_fallback_menu', 'max_columns'=>4);
						wp_nav_menu($args); 
						echo "</div>";
						
						?>
	
						</div><!-- end container-->
				
				</div><!-- end container_wrap-->
			
			<!-- ####### END HEAD CONTAINER ####### -->
			
			<?php 
			//display slideshow big if one is available	
			if(!empty($ace_config['slide_output'])) echo "<div class='container_wrap' id='slideshow_big'><div class='container'>".$ace_config['slide_output']."</div></div>";	
			?>

			