<?php

/*This file holds ALL color information of the theme that is applied with the styling backend admin panel. It is recommended to not edit this file, instead create new styles in custom.css and overwrite the styles within this file*/


global $ace_config;

  $ace_config['colorRules']['body_font'] 	= $body_font		= ace_get_option('body_font');
  $ace_config['colorRules']['primary'] 	= $primary			= ace_get_option('primary');
  $ace_config['colorRules']['body_bg'] 	= $body_bg			= ace_get_option('bg_color');
  $ace_config['colorRules']['boxed_bg'] 	= $boxed_bg			= ace_get_option('bg_color_boxed');
  $ace_config['colorRules']['border'] 		= $border			= ace_get_option('border');
  $ace_config['colorRules']['highlight'] 	= $highlight		= ace_get_option('highlight');
  $ace_config['colorRules']['bg_highlight']= $bg_highlight		= ace_get_option('bg_highlight');
  $ace_config['colorRules']['socket'] 		= $socket			= ace_get_option('socket_bg'); 
  $ace_config['colorRules']['socket_font'] = $socket_font		= ace_get_option('socket_font'); 
  $ace_config['colorRules']['footer'] 		= $footer			= ace_get_option('footer_bg'); 
  $ace_config['colorRules']['footer_font'] = $footer_font		= ace_get_option('footer_font'); 
  $ace_config['colorRules']['footer_meta'] = $footer_meta  	= ace_backend_merge_colors($footer_font, $footer);
  $ace_config['colorRules']['footer_meta_2'] = $footer_meta_2  = ace_backend_merge_colors($footer_meta, $footer);
  $ace_config['colorRules']['footer_meta'] = $footer_meta  	= ace_backend_merge_colors($footer_font, $footer_meta); 
  $ace_config['colorRules']['bg_image'] 	= $bg_image			= ace_get_option('bg_image') == "custom" ? ace_get_option('bg_image_custom') : ace_get_option('bg_image');


if(ace_get_option('boxed') == 'boxed')
{
	$meta_color 	= ace_backend_merge_colors($body_font, $boxed_bg); // creates a new color from the background color and the default font color (results in a lighter color)
	$heading_color 	= ace_backend_merge_colors($body_font, ace_backend_counter_color($boxed_bg)); //calculates the inverse of the background color, then again creates a new color for headins (results in a stronger color)
	$content_bg 	= $boxed_bg;
}
else
{
	$meta_color 	= ace_backend_merge_colors($body_font, $body_bg); // creates a new color from the background color and the default font color (results in a lighter color)
	$heading_color 	= ace_backend_merge_colors($body_font, ace_backend_counter_color($body_bg)); //calculates the inverse of the background color, then again creates a new color for headins (results in a stronger color)
	$content_bg 	= $body_bg;
}

$ace_config['colorRules']['meta_color'] = $meta_color;
$ace_config['colorRules']['heading_color'] = $heading_color;
$ace_config['colorRules']['content_bg'] = $content_bg;











/*array that generates the colors. unfortunatley there is no easier way of grouping the items (or a more readable way)*/

$ace_config['style'] = array(

	//body font color
	array(
		'elements'	=>'#top .site-fontcolor, html, body, .blog-meta .post-meta-infos a, .blog-meta .post-meta-infos a span',
		'key'		=>'color',
		'value'		=> $body_font
		),
		
	//meta font color: creates a lighter version of the main color
	array(
		'elements'	=>'#top .meta-color, div .meta-color a, .main_menu ul li a, #top .comment-container a, #top .widget ul li a, .minitext, .form-allowed-tags, div .pagination, #comments span, .minitext, .commentmetadata a, .blog-tags, .blog-tags a, .title_container #s, .wp-caption, blockquote:before, blockquote:after, blockquote small, blockquote cite, .hero-text, .js_active .tab, .news-time, .contentSlideControlls a, #js_sort_items a, .text-sep, .template-search a.post-entry .news-excerpt, ul.borderlist>li, .post_nav, .post_nav a, .quote-content, #top .widget_nav_menu li, .tweet-time, #top .ace_parnter_empty, .ace_socialcount a span, td, #top th.nobg, caption, .page-title',
		'key'		=>'color',
		'value'		=> $meta_color
		),
		
	//heading font color: creates a stronger version of the main color
	array(
		'elements'	=>'strong, #top .main-color, .main_menu ul:first-child > li.current-menu-item > a, .main_menu ul:first-child > li.current_page_item > a,  #top blockquote p, #top .main_menu .menu li a:hover, h1, h2, h3, h4, h5, h6, .js_active .tab.active_tab, #top #wrap_all .current_page_item>a, .first-quote, div .callout',
		'key'		=>'color',
		'value'		=> $heading_color
		),
	
	//superlight color
	array(
		'elements'	=>'#top .search-result-counter',
		'key'		=>'color',
		'value'		=> $bg_highlight
		),

	######################################################################
	# Page background color
	######################################################################

	
	array(
		'elements'	=>'#top .site-background, html, body, .comment-reply-link, .main_menu .menu ul li, .title_container #searchsubmit:hover, .isotope .entry-content, .image_overlay_effect, .tagcloud a, .news-thumb, .tweet-thumb a, fieldset, pre',
		'key'		=>'background-color',
		'value'		=> $body_bg
		),
		
	array(
		'elements'	=>'tr:nth-child(even) td, tr:nth-child(even) th',
		'key'		=>'background-color',
		'value'		=> $body_bg
		),
		
	//font on elements with primary color, derived from background color	
	array(
		'elements'	=>'#top .on-primary-color, #top .on-primary-color a, .dropcap2, div .button, input[type="submit"], #submit, .info_text_header, .info_text_header a, .info_text_header a:hover, .contentSlideControlls a.activeItem, #top .related_posts .contentSlideControlls a.activeItem, .contentSlideControlls a:hover, #top .related_posts .contentSlideControlls a:hover, #top th, #top th a,  a.button:hover',
		'key'		=>'color',
		'value'		=> $body_bg
		),
	
	//body background color highlight
	array(
		'elements'	=>'#top .aside-background, div .gravatar img, .slideshow, #top .main_menu .menu li ul a:hover, .related_posts_default_image, div .numeric_controls a, .title_container #searchsubmit, .title_container #s, .tab_content.active_tab_content, .js_active #top  .active_tab, .toggler.activeTitle, .contentSlideControlls a',
		'key'		=>'background-color',
		'value'		=> $bg_highlight
		),
		
		array(
		'elements'	=>'tr:nth-child(odd) td, tr:nth-child(odd) th',
		'key'		=>'background-color',
		'value'		=> $bg_highlight
		),
	
	//boxed background variations
	array(
		'elements'	=>'.boxed #wrap_all, #top.boxed .site-background, .boxed .comment-reply-link, .boxed .main_menu .menu ul li, .boxed .title_container #searchsubmit:hover, .boxed .isotope .entry-content, .boxed .image_overlay_effect, .boxed .tagcloud a, .boxed .news-thumb, .boxed fieldset, .boxed pre',
		'key'		=>'background-color',
		'value'		=> $boxed_bg
		),
		
	array(
		'elements'	=>'.boxed tr:nth-child(even) td, .boxed tr:nth-child(even) th',
		'key'		=>'background-color',
		'value'		=> $boxed_bg
		),
	
	array(
		'elements'	=>'#top.boxed  .on-primary-color,  #top.boxed .on-primary-color a, .boxed .dropcap2, .boxed div .button,.boxed  input[type="submit"],.boxed  #submit, .boxed .info_text_header,.boxed  .info_text_header a,.boxed  .info_text_header a:hover, .boxed .contentSlideControlls a.activeItem, #top.boxed  .related_posts .contentSlideControlls a.activeItem, .boxed .contentSlideControlls a:hover, #top.boxed  .related_posts .contentSlideControlls a:hover, .boxed th, .boxed .tweet-thumb a, #top.boxed th, #top.boxed th a, .boxed a.button:hover',
		'key'		=>'color',
		'value'		=> $boxed_bg
		),




	######################################################################
	# primary color
	######################################################################
	
	//background color
	array(
		'elements'	=>'#top .primary-background, .dropcap2, div .button, input[type="submit"], #submit, .info_text_header, .numeric_controls a:hover, .numeric_controls .active_item, .contentSlideControlls a.activeItem, #top th, #top .related_posts .contentSlideControlls a.activeItem, #top .arrow_controls a, #main .content #searchsubmit:hover, .callout a',
		'key'		=>'background-color',
		'value'		=> $primary
		),
		
	//color
	array(
		'elements'	=>'#top .primary-color, a, #cancel-comment-reply-link, .blog-tags a:hover, .relThumb a:hover strong, .flex_column h1, .flex_column h2, .flex_column h3, .flex_column h4, .flex_column h5, .flex_column h6, #top #wrap_all .tweet-text a, #top #js_sort_items a.active_sort',
		'key'		=>'color',
		'value'		=> $primary
		),
		
	//border color
	array(
		'elements'	=>'#top .primary-border, div .main_menu ul:first-child > li.current-menu-item > a, div .main_menu ul:first-child > li.current_page_item > a, div .button, input[type="submit"], #submit, #top .main_menu .menu ul, .info_text_header',
		'key'		=>'border-color',
		'value'		=> $primary
		),


	//google webfonts
	array(
		'elements'	=> 'h1, h2, h3, h4, h5, h6, .hero-text, blockquote, legend, #top .slideshow_caption h1',
		'key'	=>	'google_webfont',
		'value'		=> ace_get_option('google_webfont')
		),
		
	######################################################################
	# border color
	######################################################################
	
	array(
		'elements'	=>'#top .extralight-border, div #header .container, div .pagination, #top .pagination span, div .pagination a, div .gravatar img, #top div .commentlist ul, div .children .children .says, div .commentlist>.comment, div .input-text, input[type="text"], input[type="password"], input[type="email"], textarea, select, #top .main_menu .menu li, pre, code, div .numeric_controls a, div .pullquote_boxed, div .news-thumb, div .tweet-thumb a, #top ul.borderlist>li, .post_nav, #top .wp-caption, .slideshow,  .widget a, .widget li, .widget span, .widget div, table, td, tr, th, #footer .container, #socket .container, #top fieldset',
		'key'		=>'border-color',
		'value'		=> $border
		),	
		
	
	######################################################################
	# highlight hover color
	######################################################################
	
	array(
		'elements'	=>'#top .highlight-background, div .button:hover, input[type="submit"]:hover, #submit:hover, .contentSlideControlls a:hover, #top .related_posts .contentSlideControlls a:hover, #top .caption-slideshow-button:hover, #top .arrow_controls a:hover, #main .content #searchsubmit',
		'key'		=>'background-color',
		'value'		=> $highlight
		),	
	
	array(
		'elements'	=>'a:hover, #top .widget ul li a:hover, #top .widget ul li .news-link:hover strong, #top #wrap_all .tweet-text a:hover, #js_sort_items a:hover',
		'key'		=>'color',
		'value'		=> $highlight
		),	
		
	array(
		'elements'	=>'#top .caption-slideshow-button:hover',
		'key'		=>'border-color',
		'value'		=> $highlight
		),
		
		
	######################################################################
	# footer
	######################################################################
	
	array(
		'elements'	=>'#footer',
		'key'		=>'background-color',
		'value'		=> $footer
		),	
		
		array(
		'elements'	=>'#top #wrap_all #footer a, #footer h1, #footer h2, #footer h3, #footer h4, #footer h5, #footer h6, #footer strong, #footer .tabcontainer span, #top #footer table, #top #footer table td, #top #footer table caption',
		'key'		=>'color',
		'value'		=> $footer_font
		),
		
		array(
		'elements'	=>'#footer, #footer div, #footer p, #footer span, #top #wrap_all #footer a:hover strong',
		'key'		=>'color',
		'value'		=> $footer_meta
		),
		
		array(
		'elements'	=>'#footer a, #footer div, #footer span, #footer li, #footer ul',
		'key'		=>'border-color',
		'value'		=> $footer_meta_2
		),
		
		array(
		'elements'	=>'#footer table, #footer td, #footer tr, #footer th #footer img',
		'key'		=>'border-color',
		'value'		=> $footer_meta
		),
		
		
		array(
		'elements'	=>'#top #footer .tagcloud a, #footer .tab_content.active_tab_content, .js_active #top #footer .active_tab, #footer .news-thumb, #footer .tweet-thumb a',
		'key'		=>'background-color',
		'value'		=> $footer_meta_2
		),
		
		array(
		'elements'	=>'#footer tr:nth-child(odd) td, #footer tr:nth-child(odd) th',
		'key'		=>'background-color',
		'value'		=> $footer_meta_2
		),
		
	
		

		
	######################################################################
	# socket
	######################################################################
	
	array(
		'elements'	=>'#socket, #socket a, html.html_stretched',
		'key'		=>'background-color',
		'value'		=> $socket
		),	
		
		array(
		'elements'	=>'#socket, #socket a',
		'key'		=>'color',
		'value'		=> $socket_font
		),	
		
	######################################################################
	# text selection
	######################################################################		
	
	array(
		'elements'	=>'::-moz-selection',
		'key'		=>'background-color',
		'value'		=> $primary
		),
		
		array(
		'elements'	=>'::-webkit-selection',
		'key'		=>'background-color',
		'value'		=> $primary
		),
		
		array(
		'elements'	=>'::selection',
		'key'		=>'background-color',
		'value'		=> $primary
		),
		
		array(
		'elements'	=>'::-moz-selection',
		'key'		=>'color',
		'value'		=> $content_bg
		),
		
		array(
		'elements'	=>'::-webkit-selection',
		'key'		=>'color',
		'value'		=> $content_bg
		),
		
		array(
		'elements'	=>'::selection',
		'key'		=>'color',
		'value'		=> $content_bg
		),
		
		array(
		'key'	=>	'direct_input',
		'value'		=> ace_get_option('quick_css')
		),
		
	
);



if($bg_image != '')
{

	if(ace_get_option('bg_image_repeat') != 'fullscreen')
	{
	$ace_config['style'][] = array(
		'elements'	=>'html.html_boxed, body',
		'key'		=>'backgroundImage',
		'value'		=> $bg_image
		);
		
	$ace_config['style'][] = array(
		'elements'	=>'html, body',
		'key'		=>'background-position',
		'value'		=> 'top '.ace_get_option('bg_image_position')
		);

		
	$ace_config['style'][] = array(
		'elements'	=>'html, body',
		'key'		=>'background-repeat',
		'value'		=> ace_get_option('bg_image_repeat')
		);
		
	$ace_config['style'][] = array(
		'elements'	=>'html, body',
		'key'		=>'background-attachment',
		'value'		=> ace_get_option('bg_image_attachment')
		);
	}
}




