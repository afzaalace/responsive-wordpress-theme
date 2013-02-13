<?php


function ace_woocommerce_enabled()
{
	if (defined("WOOCOMMERCE_VERSION")) { return true; }
	return false;
}


global $ace_config;

//product thumbnails 
$ace_config['imgSize']['shop_thumbnail'] 	= array('width'=>80, 'height'=>80);
$ace_config['imgSize']['shop_catalog'] 	= array('width'=>450, 'height'=>355);
$ace_config['imgSize']['shop_single'] 		= array('width'=>450, 'height'=>355);
ace_backend_add_thumbnail_size($ace_config);

include('admin-options.php');
include('admin-import.php');



//check if the plugin is enabled, otherwise stop the script
if(!ace_woocommerce_enabled()) { return false; }


//register my own styles, remove wootheme stylesheet
if(!is_admin()){
	add_action('init', 'ace_woocommerce_register_assets');
}

function ace_woocommerce_register_assets()
{
	wp_enqueue_style( 'ace-woocommerce-css', ACE_BASE_URL.'config-woocommerce/woocommerce-mod.css');
	wp_enqueue_script( 'ace-woocommerce-js', ACE_BASE_URL.'config-woocommerce/woocommerce-mod.js', array('jquery'), 1, true);
}

define('WOOCOMMERCE_USE_CSS', false);






######################################################################
# config
######################################################################

//add ace_framework config defaults

$ace_config['shop_overview_column']  = get_option('ace_woocommerce_column_count');  // columns for the overview page
$ace_config['shop_overview_products']= get_option('ace_woocommerce_product_count'); // products for the overview page

$ace_config['shop_single_column'] 	 = 4;			// columns for related products and upsells
$ace_config['shop_single_column_items'] 	 = 4;	// number of items for related products and upsells
$ace_config['shop_overview_excerpt'] = false;		// display excerpt

if(!$ace_config['shop_overview_column']) $ace_config['shop_overview_column'] = 3;


######################################################################
# Create the correct template html structure
######################################################################

//remove woo defaults
remove_action( 'woocommerce_sidebar', 'woocommerce_get_sidebar', 10);
remove_action( 'woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10);
remove_action( 'woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end', 10);
remove_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_template_loop_product_thumbnail', 10);
remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10);
remove_action( 'woocommerce_before_main_content', 'woocommerce_breadcrumb', 20, 0);
remove_action( 'woocommerce_pagination', 'woocommerce_catalog_ordering', 20 );
remove_action( 'woocommerce_pagination', 'woocommerce_pagination', 10 );
//single page removes
remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_product_data_tabs', 10);
remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_related_products', 20);
remove_action( 'woocommerce_after_single_product', 'woocommerce_upsell_display');
remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_price', 10, 2);
remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_excerpt', 20, 2);

remove_action( 'woocommerce_product_tabs', 'woocommerce_product_description_tab', 10 );
remove_action( 'woocommerce_product_thumbnails', 'woocommerce_show_product_thumbnails', 20 );
remove_action( 'woocommerce_before_single_product_summary', 'woocommerce_show_product_images', 20);
remove_action( 'woocommerce_before_single_product', array($woocommerce, 'show_messages'), 10);
remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30, 2 );



//add theme actions && filter
add_action( 'woocommerce_before_main_content', 'ace_woocommerce_before_main_content', 10);
add_action( 'woocommerce_after_main_content', 'ace_woocommerce_after_main_content', 10);
add_action( 'woocommerce_before_shop_loop', 'ace_woocommerce_before_shop_loop', 1);
add_action( 'woocommerce_after_shop_loop', 'ace_woocommerce_after_shop_loop', 10);
add_action( 'woocommerce_before_shop_loop_item', 'ace_woocommerce_thumbnail', 10);
add_action( 'woocommerce_after_shop_loop_item_title', 'ace_woocommerce_overview_excerpt', 10);
add_filter( 'loop_shop_columns', 'ace_woocommerce_loop_columns');
add_filter( 'loop_shop_per_page', 'ace_woocommerce_product_count' );


//single page adds
add_action( 'woocommerce_before_single_product', 'check_ace_title', 1);
add_action( 'woocommerce_single_product_summary', array($woocommerce, 'show_messages'), 10);
add_action( 'woocommerce_single_product_summary', 'ace_woocommerce_template_single_excerpt', 10, 2);
add_action( 'woocommerce_single_product_summary', 'woocommerce_output_product_data_tabs', 50);
add_action( 'woocommerce_single_product_summary', 'ace_woocommerce_output_related_products', 60);
add_action( 'woocommerce_single_product_summary', 'ace_woocommerce_output_upsells', 70);
add_action( 'woocommerce_before_single_product_summary', 'ace_woocommerceproduct_prev_image', 1,  2);
add_action( 'woocommerce_before_single_product_summary', 'ace_add_summary_div', 2);
add_action( 'woocommerce_after_single_product_summary',  'ace_close_summary_div', 1000);
add_action( 'ace_add_to_cart', 'woocommerce_template_single_add_to_cart', 30, 2 );

//add_action( 'woocommerce_product_thumbnails', 'ace_woocommerceproduct_prev_image_after', 1000 );

add_filter( 'single_product_small_thumbnail_size', 'ace_woocommerce_thumb_size');
add_filter( 'ace_sidebar_menu_filter', 'ace_woocommerce_sidebar_filter');



######################################################################
# FUNCTIONS
######################################################################




#
# create the shop navigation with account links, as well as cart and checkout
#

function ace_woocommerce_template_single_excerpt($post, $_product)
{
	
	if(is_singular() && ace_post_meta('hero'))
	{
		echo "<div class='hero-text entry-content'>";
		the_excerpt();
		echo "<span class='seperator extralight-border'></span>";
		echo "</div>";
	}
	
	echo "<div class='summary-main-content entry-content'>";
	the_content();
	echo "</div>";
}


function check_ace_title()
{
	global $ace_config;
	if(empty($ace_config['slide_output'])) 
	{
		add_action( 'woocommerce_before_single_product', 'ace_title', 2, 2);
	}
}


function ace_add_summary_div()
{
	global $ace_config;
	$units = "eight";
	//if(!empty($ace_config['slide_output'])) $units = "nine";

	echo "<div class='$units units'>";
}

function ace_close_summary_div()
{
	echo "</div>";
}



function ace_shop_nav($args)
{
	$output = "";
	$url = ace_collect_shop_urls();
	
	$output .= "<ul>";
	
	if( is_user_logged_in() )
	{
		$output .= "<li class='account_overview_link'><a href='".$url['account_overview']."'>".__('My Account', 'ace_framework')."</a>";
			$output .= "<ul>";
			$output .= "<li class='account_change_pw_link'><a href='".$url['account_change_pw']."'>".__('Change Password', 'ace_framework')."</a></li>";
			$output .= "<li class='account_edit_adress_link'><a href='".$url['account_edit_adress']."'>".__('Edit Address', 'ace_framework')."</a></li>";
			$output .= "<li class='account_view_order_link'><a href='".$url['account_view_order']."'>".__('View Order', 'ace_framework')."</a></li>";
			$output .= "<li class='account_logout_link'><a href='".$url['logout']."'>".__('Log Out', 'ace_framework')."</a></li>";
			$output .= "</ul>";
		$output .= "</li>";
	}
	else
	{
		if(get_option('users_can_register')) 
		{
			$output .= "<li class='register_link'><a href='".$url['register']."'>".__('Register', 'ace_framework')."</a></li>";
		}
		
		$output .= "<li class='login_link'><a href='".$url['account_overview']."'>".__('Log In', 'ace_framework')."</a></li>";
	}
	
	$output .= "<li class='shopping_cart_link'><a href='".$url['cart']."'>".__('Cart', 'ace_framework')."</a></li>";
	$output .= "<li class='checkout_link'><a href='".$url['checkout']."'>".__('Checkout', 'ace_framework')."</a></li>";
	$output .= "</ul>";
	
	if($args['echo'] == true) 
	{
		echo $output;
	}
	else
	{
		return $output;
	} 
}


#
# helper function that collects all the necessary urls for the shop navigation
#

function ace_collect_shop_urls()
{
	global $woocommerce;
	
	$url['cart']				= $woocommerce->cart->get_cart_url();
	$url['checkout']			= $woocommerce->cart->get_checkout_url();
	$url['account_overview'] 	= get_permalink(get_option('woocommerce_myaccount_page_id'));
	$url['account_edit_adress']	= get_permalink(get_option('woocommerce_edit_address_page_id'));
	$url['account_view_order']	= get_permalink(get_option('woocommerce_view_order_page_id'));
	$url['account_change_pw'] 	= get_permalink(get_option('woocommerce_change_password_page_id'));
	$url['logout'] 				= wp_logout_url(home_url('/'));
	$url['register'] 			= site_url('wp-login.php?action=register', 'login');

	return $url;
}




#
# check which page is displayed and if the sidebar menu should be prevented
#
function ace_woocommerce_sidebar_filter($menu)
{
	$id = ace_get_the_ID();
	if(is_cart() || is_checkout() || get_option('woocommerce_thanks_page_id') == $id){$menu = "";}
	return $menu;
}

#
# single page thumbnail and preview image modifications
#
function ace_woocommerceproduct_prev_image($post, $_product)
{
	global $ace_config;

	$extraClass = ace_post_meta('zoom_lightbox');
	$units = "four";
	//if(!empty($ace_config['slide_output'])) $units = "three";
	
	echo "<div class='alpha $units units prev_image_container ".$extraClass."'>";
	
	if(empty($ace_config['slide_output']))
	{	
		$slider = new ace_slideshow(get_the_ID());
		$slider -> setImageSize('portfolio');
		echo $slider->display();
		echo $slider->slideshow_thumbs();
	}
	else
	{
		ace_add_to_cart($post, $_product );
		echo "<h1 class='post-title portfolio-single-post-title'>".get_the_title()."</h1>";
	}
	
	//price	
	if(is_object($_product) && is_singular())  
	{
		echo "<div class='price_container'>";
		woocommerce_template_single_price($post, $_product);
		echo "</div>";
	}
	
	
	if(!empty($slider)) { 
		ace_add_to_cart($post, $_product );
	}
	

	$ace_config['currently_viewing'] = 'shop_single';
	$ace_config['sidebar_class'] = 'alpha';

	get_sidebar();
	wp_reset_query();

	echo "</div>";
}


function ace_add_to_cart($post, $_product )
{
	echo "<div class='ace_cart ace_cart_".$_product->product_type."'>";
	do_action( 'ace_add_to_cart', $post, $_product );
	echo "</div>";
}


function ace_woocommerce_thumb_size()
{
	return 'shop_single';
}


#
# creates the ace framework container arround the shop pages
#
function ace_woocommerce_before_main_content()
{
	global $ace_config;
	
	ace_template_set_page_layout('page_layout');
	if(!isset($ace_config['shop_overview_column'])) $ace_config['shop_overview_column'] = "auto";
	if(is_shop() && $new = ace_post_meta( get_option('woocommerce_shop_page_id'), 'page_layout')) $ace_config['layout'] = $new;
	
	echo "<div id='main' class='container_wrap ".$ace_config['layout']." template-shop shop_columns_".$ace_config['shop_overview_column']."'>";
		echo "<div class='container'>";
		
		if(!is_singular()) 
		{
			$ace_config['overview'] = true;
			ace_woocommerce_advanced_title();
		}
}

#
# creates the title + description for overview pages
#
function ace_woocommerce_advanced_title()
{
	global $wp_query;
	$titleClass 	= "";
	$image		 	= "";
	if(isset($wp_query->query_vars['taxonomy']))
	{
		$term 			= get_term_by( 'slug', get_query_var($wp_query->query_vars['taxonomy']), $wp_query->query_vars['taxonomy']);
		$attachment_id 	= get_woocommerce_term_meta($term->term_id, 'thumbnail_id');
		if(!empty($term->description)) $titleClass .= "title_container_description ";
	}
	
	if(!empty($attachment_id))
	{
		$titleClass .= "title_container_image ";
		$image		= wp_get_attachment_image( $attachment_id, 'thumbnail', false, array('class'=>'category_thumb'));
	}

	echo "<div class='extralight-border title_container shop_title_container $titleClass'>";
	//echo ace_breadcrumbs();
	woocommerce_catalog_ordering();
	echo $image;
}



#
# creates the ace framework content container arround the shop loop
#
function ace_woocommerce_before_shop_loop()
{	

			global $ace_config;
			
			if(isset($ace_config['dynamic_template'])) return;
			
			ob_start();
			if (!empty($ace_config['overview'])) echo "</div>"; // end title_container
			echo "<div class='template-shop content ".$ace_config['content_class']." units'>";
			$content = ob_get_clean();
			echo $content;
			ob_start();
}

#
# closes the ace framework content container arround the shop loop
#
function ace_woocommerce_after_shop_loop()
{
			global $ace_config;
			if(isset($ace_config['dynamic_template'])) return;
			if(isset($ace_config['overview'] )) echo ace_pagination();
			echo "</div>"; //end content
}





#
# closes the ace framework container arround the shop pages
#
function ace_woocommerce_after_main_content()
{	
	global $ace_config;
	$ace_config['currently_viewing'] = "shop";
			
			//reset all previous queries
			wp_reset_query();
			
			//get the sidebar
			if(!is_singular())
			get_sidebar();
			
		echo "</div>"; // end container
	echo "</div>"; // end tempate-shop content
}




#
# creates the post image for each post
#
function ace_woocommerce_thumbnail($asdf)
{
	//circumvent the missing post and product parameter in the loop_shop template
	global $post;
	$_product = &new woocommerce_product( $post->ID );
	//$rating = $_product->get_rating_html(); //rating is removed for now since the current implementation requires wordpress to do 2 queries for each post which is not that cool on overview pages
	ob_start();
	woocommerce_template_loop_add_to_cart($post, $_product);
	$link = ob_get_clean();
	$extraClass  = empty($link) ? "single_button" :  "" ;
	
	echo "<div class='thumbnail_container'>";
	echo "<div class='thumbnail_container_inner'>";
		echo get_the_post_thumbnail( get_the_ID(), 'shop_catalog' );
		echo $link;
		echo "<a class='button show_details_button $extraClass' href='".get_permalink($post->ID)."'>".__('Show Details','ace_framework')."</a>";
		if(!empty($rating)) echo "<span class='rating_container'>".$rating."</span>";
		
		echo "</div>";
	echo "</div>";
}

#
# echo the excerpt
#
function ace_woocommerce_overview_excerpt()
{
	global $ace_config;

	if(!empty($ace_config['shop_overview_excerpt']))
	{
		echo "<div class='product_excerpt'>";
		the_excerpt();
		echo "</div>";
	}
}




#
# shopping cart dropdown in the main menu
#

function ace_woocommerce_cart_dropdown()
{
	global $woocommerce;
	$cart_subtotal = $woocommerce->cart->get_cart_subtotal();
	$link = $woocommerce->cart->get_cart_url();
	
	ob_start();
    the_widget('WooCommerce_Widget_Cart', '', array('widget_id'=>'cart-dropdown',
        'before_widget' => '',
        'after_widget' => '',
        'before_title' => '<span class="hidden">',
        'after_title' => '</span>'
    ));
    $widget = ob_get_clean();
	
	$output = "";
	$output .= "<ul class = 'cart_dropdown' data-success='".__('Product added', 'ace_framework')."'><li class='cart_dropdown_first'>";
	$output .= "<a class='cart_dropdown_link' href='".$link."'>".__('Cart', 'ace_framework')."</a><span class='cart_subtotal'>".$cart_subtotal."</span>";
	$output .= "<div class='dropdown_widget dropdown_widget_cart'>";
	$output .= $widget;
	$output .= "</div>";
	$output .= "</li></ul>";
	
	return $output;
}


#
# modify shop overview column count
#
function ace_woocommerce_loop_columns() 
{
	global $ace_config;
	return $ace_config['shop_overview_column'];
}


#
# modify shop overview product count
#

function ace_woocommerce_product_count() 
{
	global $ace_config;
	return $ace_config['shop_overview_products'];
}



#
# display upsells and related products
#
function ace_woocommerce_output_related_products()
{	
	global $ace_config;
	
	echo "<div class='product_column product_column_".$ace_config['shop_single_column']."'>";
	ob_start();
	woocommerce_related_products($ace_config['shop_single_column_items'],$ace_config['shop_single_column']); // 4 products, 4 columns
	$content = ob_get_clean();
	if($content)
	{
		echo ace_flag(__('Related Products', 'ace_framework'));
		echo $content;
	}


	echo "</div>";
}

function ace_woocommerce_output_upsells() 
{
	global $ace_config;

	echo "<div class='product_column product_column_".$ace_config['shop_single_column']."'>";
	ob_start();
	woocommerce_upsell_display($ace_config['shop_single_column_items'],$ace_config['shop_single_column']); // 4 products, 4 columns
	$content = ob_get_clean();
	if($content)
	{
		echo ace_flag(__('You may also like', 'ace_framework'));
		echo $content;
	}
	echo "</div>";
}




add_filter('ace_style_filter', 'ace_wooceommerce_colors');
/* add some color modifications to the forum table items */
function ace_wooceommerce_colors($config)
{
	require_once( 'woocommerce-mod-css-dynamic.php');			// register the plugins we need
	return $config;
}