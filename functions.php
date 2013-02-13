<?php
##################################################################
# ACE FRAMEWORK by afzaalace

# this include calls a file that automatically includes all 
# the files within the folder framework and therefore makes 
# all functions and classes available for later use
						
require_once( 'framework/ace_framework.php' );

##################################################################

global $ace_config;

$ace_config['imgSize']['widget'] 			 	= array('width'=>36,  'height'=>36 );						// small preview pics eg sidebar news
$ace_config['imgSize']['post-format-image'] 	= array('width'=>630, 'height'=>999, 'crop'=>false);		// big images for post format image and gallery posts
$ace_config['imgSize']['fullsize'] 		 	= array('width'=>930, 'height'=>930, 'crop'=>false);		// big images for lightbox and portfolio single entries
$ace_config['imgSize']['featured'] 		 	= array('width'=>930, 'height'=>340);						// images for fullsize pages and fullsize slider
$ace_config['imgSize']['portfolio'] 		 	= array('width'=>450, 'height'=>335);						// images for portfolio entries (2,3,4 column)

//dynamic columns
$ace_config['imgSize']['dynamic_1'] 		 	= array('width'=>446, 'height'=>151);						// images for 2/4 (aka 1/2) dynamic portfolio columns when using 3 columns
$ace_config['imgSize']['dynamic_2'] 		 	= array('width'=>609, 'height'=>210);						// images for 2/3 dynamic portfolio columns
$ace_config['imgSize']['dynamic_3'] 		 	= array('width'=>688, 'height'=>151);						// images for 3/4 dynamic portfolio columns



ace_backend_add_thumbnail_size($ace_config);






##################################################################
# Frontend Stuff necessary for the theme:
##################################################################


$lang = TEMPLATEPATH . '/lang';
load_theme_textdomain('ace_framework', $lang);


/* Register frontend javascripts: */
if(!is_admin()){
	add_action('init', 'ace_frontend_js');
}

function ace_frontend_js()
{
	wp_register_script( 'ace-default', ACE_BASE_URL.'js/ace.js', array('jquery','ace-html5-video'), 1, false );
	wp_register_script( 'ace-prettyPhoto',  ACE_BASE_URL.'js/prettyPhoto/js/jquery.prettyPhoto.js', 'jquery', "3.0.1", true);
	wp_register_script( 'ace-html5-video',  ACE_BASE_URL.'js/projekktor/projekktor.min.js', 'jquery', "1", true);
	wp_register_script( 'adaptace-slider',  ACE_BASE_URL.'js/adaptace.js', 'jquery', "1.0.0", true);
}



/* Activate native wordpress navigation menu and register a menu location */
add_theme_support('nav_menus');
$ace_config['nav_menus'] = array('ace' => 'Main Menu', 'ace2'=> 'Sub Menu');
foreach($ace_config['nav_menus'] as $key => $value){ register_nav_menu($key, THEMENAME.' '.$value); }


//adds the plugin initalization scripts that add styles and functions
require_once( 'config-woocommerce/config.php' ); 	//woocommerce shop plugin		
require_once( 'config-bbpress/config.php' );		//bbpress forum plugin

//load some frontend functions in folder include:

require_once( 'includes/admin/register-portfolio.php' );		// register custom post types for portfolio entries
require_once( 'includes/admin/register-widget-area.php' );		// register sidebar widgets for the sidebar and footer
require_once( 'css/dynamic-css.php' );							// register the styles for dynamic frontend styling
require_once( 'includes/admin/register-shortcodes.php' );		// register wordpress shortcodes
require_once( 'includes/loop-comments.php' );					// necessary to display the comments properly
require_once( 'includes/helper-slideshow.php' ); 				// holds the class that generates the 2d & 3d slideshows, as well as feature images
require_once( 'includes/helper-templates.php' ); 				// holds some helper functions necessary for dynamic templates
require_once( 'includes/admin/compat.php' );					// compatibility functions for 3rd party plugins
require_once( 'includes/admin/register-plugins.php');			// register the plugins we need







//activate framework widgets
register_widget( 'ace_tweetbox');
register_widget( 'ace_newsbox' );
register_widget( 'ace_portfoliobox' );
register_widget( 'ace_socialcount' );
register_widget( 'ace_combo_widget' );
register_widget( 'ace_partner_widget' );




//add post format options
add_theme_support( 'post-formats', array('link', 'quote', 'gallery' ) );  






######################################################################
# CUSTOM THEME FUNCTIONS
######################################################################


//call functions for the theme
add_filter('the_content_more_link', 'ace_remove_more_jump_link');
add_post_type_support('page', 'excerpt');


//allow mp4, webm and ogv file uploads
add_filter('upload_mimes','ace_upload_mimes');
function ace_upload_mimes($mimes){ return array_merge($mimes, array ('mp4' => 'video/mp4', 'ogv' => 'video/ogg', 'webm' => 'video/webm')); }


//change default thumbnail size on theme activation
add_action('ace_backend_theme_activation', 'ace_set_thumb_size');
function ace_set_thumb_size() {update_option( 'thumbnail_size_h', 80 ); update_option( 'thumbnail_size_w', 80 );}

//remove post thumbnails from page and posts
add_theme_support( 'post-thumbnails' );
add_action('posts_selection', 'ace_remove_post_thumbnails');
add_action('init', 'ace_remove_post_thumbnails');
function ace_remove_post_thumbnails() 
{
	global $post_type;
	$remove_when = array('post','page','portfolio','product');
	
	if(is_admin())
	{
		foreach($remove_when as $remove)
		{
			if($post_type == $remove || (isset($_GET['post_type']) && $_GET['post_type'] == $remove)) { remove_theme_support( 'post-thumbnails' ); };
		}
	}
}





/*advanced title + breadcrumb function*/
function ace_title($title = "", $product = "", $meta = true)
{
	if(is_object($title)) $title = $title->post_title;
	if(!$title) $title = get_the_title(ace_get_the_id());
	
	$extraClass = "";
	if(!$meta) $extraClass = "no_padding_title";
	
	echo "<div class='$extraClass title_container extralight-border'>";
	echo '<h1 class="page-title meta-color">'.$title.'</h1>';
	echo "<div class='title_meta'>";
	
	/*
	*	display the theme search form
	*   the tempalte file that is called is searchform.php in case you want to edit it
	*/
	if($meta === true)
	{
		get_search_form(); 
	 
		
		echo '<ul class="social_bookmarks">';
			if($dribbble = ace_get_option('dribbble')) echo "<li class='dribbble'><a href='http://dribbble.com/".$dribbble."'>".__('Follow us on dribbble', 'ace_framework')."</a></li>";
			if($twitter = ace_get_option('twitter')) echo "<li class='twitter'><a href='http://twitter.com/".$twitter."'>".__('Follow us on Twitter', 'ace_framework')."</a></li>";
			if($facebook = ace_get_option('facebook')) echo "<li class='facebook'><a href='".$facebook."'>".__('Join our Facebook Group', 'ace_framework')."</a></li>";
			echo '	<li class="rss"><a href="'.ace_get_option('feedburner',get_bloginfo('rss2_url')).'">RSS</a></li>';
				
		echo '</ul>';
	}
	else if(function_exists($meta))
	{
		$meta();
	}
	
	echo "</div>";
	echo "</div>";
}



/*crates the hr separator with the colored flag by using divs without images. Therefore complete color customization from the backend is possible */
function ace_flag($text = "", $class = "")
{
	$output  = "";
	$output .= '<div class="hr hr_flag '.$class.'">';
	
	if($text !== false)
	{
		$output .= '		<div class="primary-background flag">';
		$output .= '    		<span class="flag-text on-primary-color">'.$text.'</span>';
		$output .= '    		<span class="flag-diamond site-background"></span>';
		$output .= '    		<span class="mini-seperator extralight-border"></span>';
		$output .= '    	</div>';
	}
	
	$output .= '    	<span class="hr-seperator extralight-border"></span>';
	$output .= '    	<span class="primary-background seperator-addon"></span>';
	$output .= '</div>';
	
	return $output;
}




function ace_banner($position)
{
	$extraClass = "";
	$output = "";
	$cookieHash = "";
	$bannerText = ace_get_option('banner');
	$cookieName = THEMENAME.'_ace_banner';
	$bannerHash = md5($bannerText);
	if($position) $extraClass = 'relative_pos';
	
	if(!empty($_COOKIE[$cookieName])) $cookieHash = $_COOKIE[$cookieName];
	
	$output .= "<div class='$extraClass container_wrap info_text_header' id='info_text_header' data-hash='$bannerHash' data-cookiename='$cookieName'><div class='container no_css_transition'>";
	if(trim($bannerText) != "" && $bannerHash != $cookieHash)
	{
		$output .= "<div class='infotext'>$bannerText <a class='close_info_text rounded' href='#close'>".__('close','ace_framework')."</a></div>";
	}
	
	$output .= "</div></div>";
	echo $output;
}

function ace_shop_banner()
{
	
	$pos = false;
	$output = "";
	$sub = $cart = $menu = "";
	if(ace_woocommerce_enabled()) 
	{
		$sub = $cart = ace_woocommerce_cart_dropdown(); 
	}
	
	$sub .= "<div class='sub_menu'>";
	$args = array('theme_location'=>'ace2', 'fallback_cb' => '', 'echo' => 0);
	if(ace_woocommerce_enabled()) $args['fallback_cb'] ='ace_shop_nav';
	$menu = wp_nav_menu($args);
	$sub .=  $menu;
	$sub .= "</div>";
	
	$output .= "<div class='container_wrap info_text_header' id='shop_header'><div class='container'>";
	if($cart || $menu) 
	{
		$output .= $sub;
	}
	else
	{
		$pos = true;
	}
	$output .= "</div></div>";
	echo $output;
	
	return $pos;
	
}




//set post excerpt to be visible on theme acivation in user backend
add_action('ace_backend_theme_activation', 'ace_show_menu_description');
function ace_show_menu_description()
{
	global $current_user;
    get_currentuserinfo();
	$old_meta_data = $meta_data = get_user_meta($current_user->ID, 'metaboxhidden_page', true);
	
	if(is_array($meta_data) && isset($meta_data[0]))
	{
		$key = array_search('postexcerpt', $meta_data);
		
		if($key !== false)
		{	
			unset($meta_data[$key]);
			update_user_meta( $current_user->ID, 'metaboxhidden_page', $meta_data, $old_meta_data );
		}
	}	
	else
	{
			update_user_meta( $current_user->ID, 'metaboxhidden_page', array('postcustom', 'commentstatusdiv', 'commentsdiv', 'slugdiv', 'authordiv', 'revisionsdiv') );
	}
}





//import the dynamic frontpage template on theme installation

add_action('ace_backend_theme_activation', 'ace_default_dynamics');
function ace_default_dynamics() 
{
	global $ace;
	$firstInstall = get_option($ace->option_prefix.'_dyn_amic_elements');
	
	if(empty($firstInstall))
	{
		$custom_export = "dynamic_elements";
		require_once ACE_PHP . 'inc-ace-importer.php';
	}
}



