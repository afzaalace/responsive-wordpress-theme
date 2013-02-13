<?php


function ace_bbpress_enabled()
{
	if (class_exists( 'bbPress' )) { return true; }
	return false;
}

//check if the plugin is enabled, otherwise stop the script
if(!ace_bbpress_enabled()) { return false; }


global $ace_config;


//register my own styles
if(!is_admin()){ add_action('bbp_enqueue_scripts', 'ace_bbpress_register_assets',15); }


function ace_bbpress_register_assets()
{
	//bbp_theme_compat_enqueue_css	
	wp_enqueue_style( 'ace-bbpress', ACE_BASE_URL.'config-bbpress/bbpress-mod.css');
	wp_dequeue_style( 'bbpress-style' );
	
}




//remove forum and single topic summaries at the top of the page
add_filter('bbp_get_single_forum_description', 'ace_bbpress_filter_form_message',10,2 );
add_filter('bbp_get_single_topic_description', 'ace_bbpress_filter_form_message',10,2 );



add_filter('ace_style_filter', 'ace_bbpress_forum_colors');
/* add some color modifications to the forum table items */
function ace_bbpress_forum_colors($config)
{
	global $ace_config;

	$config[] = array(
		'elements'	=>'#top .bbp-forum-title, #top .bbp-topic-title a',
		'key'		=>'color',
		'value'		=> $ace_config['colorRules']['body_font']
		);
	
	$config[] = array(
		'elements'	=>'#top .template-forum-wrap div, .bbp-pagination-links, .bbp-pagination-links span, .bbp-pagination-links a, #top .bbp-topic-pagination a, #bbp-your-profile span, img.avatar, table ul',
		'key'		=>'border-color',
		'value'		=> $ace_config['colorRules']['border']
		);	
		
		
	$config[] = array(
		'elements'	=>'#top .bbp-pagination, #top .bbp-topic-pagination a, #entry-author-info #author-description, div.bbp-breadcrumb a, .bbp_widget_login .bbp-lostpass-link:after',
		'key'		=>'color',
		'value'		=> $ace_config['colorRules']['meta_color']
		);
	
	$config[] = array(
		'elements'	=>'.bbp-admin-links',
		'key'		=>'color',
		'value'		=> $ace_config['colorRules']['border']
		);	
		
	return $config;
}


function ace_bbpress_filter_form_message( $retstr, $args )
{
	return false;
}