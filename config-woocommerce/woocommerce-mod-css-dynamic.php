<?php
global $ace_config;

	$config[] = array(
		'elements'	=>'#top .cart_dropdown .dropdown_widget li a',
		'key'		=>'color',
		'value'		=> $ace_config['colorRules']['heading_color']
		);
			
	$config[] = array(
		'elements'	=>'.woocommerce_tabs .tabs a, .product_meta, .quantity input.qty, .cart_dropdown .dropdown_widget, #top .sub_menu li ul a, .ace_select_fake_val, address, .product>a .product_excerpt, .term_description',
		'key'		=>'color',
		'value'		=> $ace_config['colorRules']['meta_color']
		);
	
	$config[] = array(
		'elements'	=>'.quantity input, .cart_dropdown .dropdown_widget li, .cart_dropdown .dropdown_widget div, .cart_dropdown .dropdown_widget img, #top .sub_menu ul ul li, .thumbnails_container li, .thumbnails_container li span, #top .comment-text, div #reviews li .avatar, div .widget_price_filter .ui-slider-horizontal .ui-slider-range, .entry-content .ace_select_unify, .addresses.col2-set .col-1, .addresses.col2-set .col-2, .payment_methods, .payment_methods li, #payment, .ace_cart, form.login',
		'key'		=>'border-color',
		'value'		=> $ace_config['colorRules']['border']
		);	
		
	$config[] = array(
		'elements'	=>'#top div.product .woocommerce_tabs ul.tabs li.active a, .quantity input.qty, .cart_dropdown .dropdown_widget,  #top .sub_menu li ul a, #payment',
		'key'		=>'background-color',
		'value'		=> $ace_config['colorRules']['content_bg']
		);	
		
	$config[] = array(
		'elements'	=>'.woocommerce_tabs ul.tabs, .activeslideThumb, #payment li',
		'key'		=>'background-color',
		'value'		=> $ace_config['colorRules']['bg_highlight']
		);	
		
	$config[] = array(
		'elements'	=>'.summary div',
		'key'		=>'border-color',
		'value'		=> $ace_config['colorRules']['bg_highlight']
		);		
		
	$config[] = array(
		'elements'	=>'#top .sub_menu li li a:hover, div .widget_price_filter .ui-slider-horizontal .ui-slider-range, div .widget_price_filter .price_slider_wrapper .ui-widget-content, .ace_cart',
		'key'		=>'background-color',
		'value'		=> $ace_config['colorRules']['bg_highlight']
		);	
		
	$config[] = array(
		'elements'	=>'div .quantity input.plus, div .quantity input.minus, #shop_header a:hover, #top .widget_layered_nav ul li.chosen a, #top .widget_layered_nav ul li.chosen small',
		'key'		=>'color',
		'value'		=> $ace_config['colorRules']['content_bg']
		);	
	
	$config[] = array(
		'elements'	=>'div .quantity input.plus, div .quantity input.minus, div .widget_layered_nav ul li.chosen, div .widget_price_filter .price_slider_wrapper .price_slider .ui-slider-handle, #top a.remove',
		'key'		=>'background-color',
		'value'		=> $ace_config['colorRules']['primary']
		);	
		
		
