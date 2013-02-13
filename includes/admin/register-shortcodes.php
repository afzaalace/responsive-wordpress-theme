<?php
/**
 * COLUMNS
 * Shortcode which creates columns for better content separation 
 *
 * @param array $atts array of attributes
 * @param string $content text within enclosing form of shortcode element 
 * @param string $shortcodename the shortcode found, when == callback name
 * @return string $output returns the modified html string 
 */
 

if(!function_exists('ace_sc_column'))
{
	function ace_sc_column($atts, $content = "", $shortcodename = "")
	{	
		$first = '';
		if (isset($atts[0]) && trim($atts[0]) == 'first')  $first = 'first';
	

		$output  = '<div class="'.$shortcodename.' '.$first.' flex_column">';
		$output .=  wpautop( ace_remove_autop($content) );
		$output .= '</div>';
			
		return $output;
	}

	add_shortcode('one_third'	, 'ace_sc_column');
	add_shortcode('two_third'	, 'ace_sc_column');
	add_shortcode('one_fourth'	, 'ace_sc_column');
	add_shortcode('three_fourth', 'ace_sc_column');
	add_shortcode('one_half'	, 'ace_sc_column');
	add_shortcode('one_fifth'	, 'ace_sc_column');
	add_shortcode('two_fifth'	, 'ace_sc_column');
	add_shortcode('three_fifth'	, 'ace_sc_column');
	add_shortcode('four_fifth'	, 'ace_sc_column');
}



/**
 * HORIZONTAL RULERS
 * Creates a horizontal ruler that provides whitespace for the layout and helps with content separation
 *
 * @param array $atts array of attributes
 * @param string $content text within enclosing form of shortcode element 
 * @param string $shortcodename the shortcode found, when == callback name
 * @return string $output returns the modified html string 
 */

if(!function_exists('ace_sc_hr'))
{
	function ace_sc_hr($atts, $content = "", $shortcodename = "")
	{	
		$top = $toplink = false;
		if (isset($atts[0]) && trim($atts[0]) == 'top')  $top = 'top';
		if($top == 'top') $toplink = '<a href="#top" class="scrollTop">top</a>';
		
		if($shortcodename != "hr_invisible")
		{
			$output = ace_flag($toplink);
		}
		else
		{
			$output  = '<div class="'.$shortcodename.'">';
			$output .= '</div>';	
		}
		
		return $output;
	}

	add_shortcode('hr', 'ace_sc_hr');
	add_shortcode('hr_invisible', 'ace_sc_hr');
}





/**
 * DROPCAPS
 * Empahize the first character of a paragraph or string with the dropcaps shortcode
 *
 * @param array $atts array of attributes
 * @param string $content text within enclosing form of shortcode element 
 * @param string $shortcodename the shortcode found, when == callback name
 * @return string $output returns the modified html string 
 */

if(!function_exists('ace_sc_dropcaps'))
{
	function ace_sc_dropcaps($atts, $content = "", $shortcodename = "")
	{	
		//this is a fix that solves the false paragraph removal by wordpress if the dropcaps shortcode is used at the beginning of the content of single posts/pages
		global $post, $ace_add_p;
		
		$add_p = "";
		if(isset($post->post_content) && strpos($post->post_content, '[dropcap') === 0 && $ace_add_p == false && is_singular())
		{
			$add_p = "<p>";
			$ace_add_p = true;
		}
		
		//this is the actual shortcode
		$output  = $add_p.'<span class="'.$shortcodename.'">';
		$output .= $content;
		$output .= '</span>';	
		
	
		return $output;
	}
	
	add_shortcode('dropcap1', 'ace_sc_dropcaps');
	add_shortcode('dropcap2', 'ace_sc_dropcaps');
	add_shortcode('dropcap3', 'ace_sc_dropcaps');
}





/**
 * SLIDER AND SLIDE
 * Those 2 shortcodes createa small slideshows that switch content with the help of javascript
 *
 * @param array $atts array of attributes
 * @param string $content text within enclosing form of shortcode element 
 * @param string $shortcodename the shortcode found, when == callback name
 * @return string $output returns the modified html string 
 */

if(!function_exists('ace_sc_content_slider'))
{
	function ace_sc_content_slider($atts, $content = "", $shortcodename = "")
	{	
		//the user can enter a single attribute: a single int that tells the script if to autoslide and how long to show the slide
		$autoslide = 'autoslide_false';
		if(!empty($atts[0])) $autoslide = 'autoslide_true autoslidedelay__'.$atts[0];
	
		$output  = "";
		$output .=	"<div class='content_slider ".$autoslide."'>";
		$output .=   ace_remove_autop($content);
		$output .=	"</div>";
		
		return $output;
	}
	
	add_shortcode('slideshow', 'ace_sc_content_slider');

}

if(!function_exists('ace_sc_content_slide'))
{
	function ace_sc_content_slide($atts, $content = "", $shortcodename = "")
	{
		$output  = "";
		
		$output .=	"\n<div class='single_slide'>\n";
		if(!empty($atts['title'])) $output .=	"<h3>".$atts['title']."</h3>\n";
		$output .=  wpautop( ace_remove_autop($content) );
		$output .=	"</div>\n";
		
		return $output;

	}
	add_shortcode('slide', 'ace_sc_content_slide');

}



/**
 * Toggle container and toggle
 * Those 2 shortcodes create toggles that can be clicked to open or hide
 *
 * @param array $atts array of attributes
 * @param string $content text within enclosing form of shortcode element 
 * @param string $shortcodename the shortcode found, when == callback name
 * @return string $output returns the modified html string 
 */
if(!function_exists('ace_sc_toggle'))
{
	function ace_sc_toggle($atts, $content=null, $shortcodename ="")
	{	

		extract(shortcode_atts(array('title' => 'Click here'), $atts));
	
		$output  = '<div class="toggler extralight-border">'.$title.'<span class="toggle_icon extralight-border">';
		$output .= '<span class="vert_icon extralight-border"></span><span class="hor_icon extralight-border"></span></span></div>'."\n";
		$output .= '<div class="toggle_wrap">'."\n";
		$output .= '<div class="toggle_content">'."\n";
		$output .= wpautop(ace_remove_autop($content))."\n";
		$output .= '</div>'."\n";
		$output .= '</div>'."\n";
	
		return $output;
	}
	
	add_shortcode('toggle', 'ace_sc_toggle');
}



if(!function_exists('ace_sc_toggle_container'))
{
	function ace_sc_toggle_container($atts, $content=null, $shortcodename ="")
	{	
		extract(shortcode_atts(array('keep_open' => 'false', 'initial_open'=>''), $atts));

		$addClass = '';
		if($keep_open == 'false') $addClass = 'toggle_close_all ';
		if(is_numeric($initial_open)) $addClass .= 'toggle_initial_open  toggle_initial_open__'.$initial_open;
	
		$output  = '<div class="togglecontainer '.$addClass.'">'."\n";
	 	$output .= ace_remove_autop($content)."\n";
		$output .= '</div>'."\n";
		
		return $output;
	}
	add_shortcode('toggle_container', 'ace_sc_toggle_container');
}





/**
 * Tab container and tab
 * Those 2 shortcodes create clickeable content tabs
 *
 * @param array $atts array of attributes
 * @param string $content text within enclosing form of shortcode element 
 * @param string $shortcodename the shortcode found, when == callback name
 * @return string $output returns the modified html string 
 */
if(!function_exists('ace_sc_tabs'))
{
	function ace_sc_tabs($atts, $content=null, $shortcodename ="")
	{	
		extract(shortcode_atts(array('initial_open'=>1), $atts));
		
		if(!is_numeric($initial_open)) $initial_open = 1;
		
		$addClass = 'tab_initial_open  tab_initial_open__'.$initial_open;
	
		$output  = '<div class="tabcontainer '.$addClass.'">'."\n";
	 	$output .= ace_remove_autop($content)."\n";
		$output .= '</div>'."\n";
		
		return $output;
	}
	
	add_shortcode('tab_container', 'ace_sc_tabs');
}


if(!function_exists('ace_sc_tab_single'))
{
	function ace_sc_tab_single($atts, $content=null, $shortcodename ="")
	{		
		extract(shortcode_atts(array('title' => 'Click here'), $atts));
	
		$output  = '<div class="tab">'.$title.'</div>'."\n";
		$output .= '<div class="tab_content">'."\n";
		$output .= wpautop(ace_remove_autop($content))."\n";
		$output .= '</div>'."\n";
	
		return $output;
	}
	
	add_shortcode('tab', 'ace_sc_tab_single');
}







/**
 * QUOTES
 * This shortcode creates blockquote elements with different styles
 *
 * @param array $atts array of attributes
 * @param string $content text within enclosing form of shortcode element 
 * @param string $shortcodename the shortcode found, when == callback name
 * @return string $output returns the modified html string 
 */


if(!function_exists('ace_sc_quote'))
{
	function ace_sc_quote($atts, $content=null, $shortcodename ="")
	{	
		extract(shortcode_atts(array(	'style' => '', 'float' => ''), $atts));
		
		if($float) $float = ' pullquote_'.$float;
		if($style) $style = ' pullquote_'.$style;
		
		// add blockquotes to the content
		$output  = '<blockquote class="pullquote'.$style.$float.'">';
		$output .= '<div class="inner_quote">';
		$output .= wpautop( ace_remove_autop( $content ) );
		$output .= '</div>';
		$output .= '</blockquote>';
		
		return $output;
	}
	
	add_shortcode('quote', 'ace_sc_quote');
}



/**
 * WIDGET
 * This shortcode creates a widget shortcode that creates widgets within the content area
 *
 * @param array $atts array of attributes
 * @return string $output returns the modified html string 
 */

function ace_sc_widget($atts) {
    
    global $wp_widget_factory;
    
    extract(shortcode_atts(array(
        'widget_name' => FALSE,
        'widget_class_name' => FALSE
    ), $atts));
   
   	
   	foreach($atts as $key=>$value)
   	{
   		$instance[$key] = $value;
   	}
   
   
    
    $id = $widget_class_name;
    
    $widget_name = esc_html($widget_name);
    
    if (!is_a($wp_widget_factory->widgets[$widget_name], 'WP_Widget')):
        $wp_class = 'WP_Widget_'.ucwords(strtolower($class));
        
        if (!is_a($wp_widget_factory->widgets[$wp_class], 'WP_Widget')):
            return '<p>'.sprintf(__("%s: Widget class not found. Make sure this widget exists and the class name is correct"),'<strong>'.$class.'</strong>').'</p>';
        else:
            $class = $wp_class;
        endif;
    endif;
    
    ob_start();
    the_widget($widget_name, $instance, array('widget_id'=>'arbitrary-instance-'.$id,
        'before_widget' => '<div class="widget '.$widget_class_name.'">',
        'after_widget' => '</div>',
        'before_title' => '<h3 class="widgettitle">',
        'after_title' => '</h3>'
    ));
    $output = ob_get_contents() ;
    ob_end_clean();
    return $output;
    
}
add_shortcode('widget','ace_sc_widget'); 







/**
 * IconBox
 * This shortcode creates a div with icon heading + text
 *
 * @param array $atts array of attributes
 * @param string $content text within enclosing form of shortcode element 
 * @param string $shortcodename the shortcode found, when == callback name
 * @return string $output returns the modified html string 
 */


if(!function_exists('ace_sc_icon_box'))
{
	function ace_sc_icon_box($atts, $content=null, $shortcodename ="")
	{	
		extract(shortcode_atts(array('title' => '', 'icon' => ''), $atts));
		
		//check wich link base we should use for the icon. by default we take the iconbox folder. if the user sets a path use that path
		if($icon != ""&& strpos('/', $icon) === false) $icon = ACE_BASE_URL . 'images/icons/iconbox/'.$icon;
		
		if($icon != "") $icon = "<img src='$icon' alt='' />";
		
		// add blockquotes to the content
		$output  = '<div class="iconbox">';
		$output .= '<span class="iconbox_icon">'.$icon.'</span>';
		$output .= '<div class="iconbox_content">';
		$output .= '<h3 class="iconbox_content_title">'.$title."</h3>";
		$output .= wpautop( ace_remove_autop( $content ) );
		$output .= '</div></div>';
		
		return $output;
	}
	
	add_shortcode('iconbox', 'ace_sc_icon_box');
	
	//iconbox helper that creates a javascript array for the backend dropdown with all available images:
	function ace_sc_icon_box_add_icons()
	{
		$files = ace_backend_load_scripts_by_folder( ACE_BASE."images/icons/iconbox" );
		$filestring = "";
		foreach($files as $file) { $filestring .= ',"'.$file.'"'; }
		$filestring = substr($filestring, 1);
	
		echo "\n <script type='text/javascript'>\n /* <![CDATA[ */  \n";
		echo "ace_framework_globals['iconbox_icons'] = [\n \t ".$filestring."\n \t]; \n /* ]]> */ \n ";
		echo "</script>\n \n ";
	}
	
	add_action('admin_print_scripts','ace_sc_icon_box_add_icons');
	
	
}



// the following functions are slightly modified versions of some woothemes shortcodes
// wanted to give some attribution here ;)
/**
 * Buttons
 * The button shortcode enables you to place fully styled buttons on the page with a simple shortcode
 *
 * @param array $atts array of attributes
 * @param string $content text within enclosing form of shortcode element 
 * @return string $output returns the modified html string 
 * 
 *
 *	Optional arguments:
 *	 - size: small, large
 *	 - style: info, alert, tick, download, note
 *	 - color: red, green, black, grey OR custom hex color (e.g #000000)
 *	 - border: border color (e.g. red or #000000)
 *	 - text: black (for light color background on button) 
 *	 - class: custom class
 *	 - link: button link (e.g http://www.google.de)
 *	 - window: true/false
 *	 
 */
 
if(!function_exists('ace_sc_button'))
{
	function ace_sc_button($atts, $content = null) {
	   	extract(shortcode_atts(array(	'size' => '',
	   									'style' => '',
	   									'color' => '',   									
	   									'border' => '',   									
	   									'text' => '',   									
	   									'class' => '',
	   									'link' => '#',
	   									'window' => ''), $atts));
	
	   	
	   	// Set custom background and border color
	   	$color_output = '';
	   	if ( $color ) {
	   	
	   		if ( 	$color == "red" OR 
	   			 	$color == "orange" OR
	   			 	$color == "green" OR
	   			 	$color == "blue" OR
	   			 	$color == "black" OR
	   			 	$color == "grey" OR
	   			 	$color == "aqua" OR
	   			 	$color == "teal" OR
	   			 	$color == "purple" OR
	   			 	$color == "pink" OR
	   			 	$color == "silver"
	   			 	 ) {
		   		$class .= " ".$color;
	   		
	   		} else {
			   	if ( $border ) 
			   		$border_out = $border;
			   	else
			   		$border_out = $color;
			   		
		   		$color_output = 'style="background-color:'.$color.';border-color:'.$border_out.'"';
		   		
		   		// add custom class
		   		$class .= " custom";
	   		}
	   	}
	
		$class_output = '';
	
		// Set text color
		if ( $text ) $class_output .= ' '.$text;
	
		// Set class
		if ( $class ) $class_output .= ' '.$class;
	
		// Set Size
		if ( $size ) $class_output .= ' '.$size;
		
		// Open in new window?	
		if ( $window ) $window = 'target="_blank" ';
		
	   	
	   	$output = '<a '.$window.'href="'.$link.'" class="ace-button '.$class_output.'" '.$color_output.'><span class="ace-'.$style.'">' . ace_remove_autop($content) . '</span></a>';
	   	return $output;
	}
	add_shortcode('button', 'ace_sc_button');
}



/**
 * ICON LINKS
 * The button shortcode enables you to place fully styled buttons on the page with a simple shortcode
 *
 * @param array $atts array of attributes
 * @param string $content text within enclosing form of shortcode element 
 * @return string $output returns the modified html string 
 * 
 *
 * Optional arguments:
 *  - style: download, note, tick, info, alert
 *  - url: the url for your link 
 *  - icon: add an url to a custom icon
 */
if (!function_exists("ace_sc_ilink")) 
{
	function ace_sc_ilink($atts, $content = null) 
	{
	   	extract(shortcode_atts(array( 'style' => 'info', 'url' => '', 'icon' => ''), $atts));  
	   	
	   	$custom_icon = '';
	   	if ( $icon ) $custom_icon = 'style="background:url('.$icon.') no-repeat left 40%;"'; 
	
	   return '<span class="ace-ilink"><a class="'.$style.'" href="'.$url.'" '.$custom_icon.'>' . ace_remove_autop($content) . '</a></span>';
	}
	
	add_shortcode('ilink', 'ace_sc_ilink');
}



/**
 * INFO BOXES
 * The button shortcode enables you to place fully styled buttons on the page with a simple shortcode
 *
 * @param array $atts array of attributes
 * @param string $content text within enclosing form of shortcode element 
 * @return string $output returns the modified html string 
 * 
 *
 * Optional arguments:
 *  - type: info, alert, tick, download, note
 *  - size: medium, large
 *  - style: rounded
 *  - border: none, full
 *  - icon: none OR full URL to a custom icon 
*/
if (!function_exists("ace_sc_box")) 
{
	function ace_sc_box($atts, $content = null) 
	{
	   extract(shortcode_atts(array(	'type' => 'normal',
	   									'size' => '',
	   									'style' => '',
	   									'border' => '',
	   									'icon' => ''), $atts)); 
	   	
	   	$custom = $custom_class = '';								
	  	
	  	if ( $icon == "none") {$custom = ' style="background-image:none;"'; $custom_class = 'custom_icon_none'; }
	   	else if ( $icon )  { $custom = ' style="background-image:url('.$icon.');"'; $custom_class = 'custom_icon';}
	   		
	   										
	   	return '<div class="ace-box '.$type.' '.$size.' '.$custom_class.' '.$style.' '.$border.'"><span class="ace-innerbox" '.$custom.'>' . ace_remove_autop($content) . '</span></div>';
	}
	add_shortcode('box', 'ace_sc_box');
}


/**
 * Removes wordpress autop and invalid nesting of p tags, as well as br tags
 *
 * @param string $content html content by the wordpress editor
 * @return string $content
 */
 
if (!function_exists("ace_remove_autop")) 
{
	function ace_remove_autop($content) 
	{ 
		$content = do_shortcode( shortcode_unautop( $content ) ); 
		$content = preg_replace('#^<\/p>|^<br\s?\/?>|<p>$|<p>\s*(&nbsp;)?\s*<\/p>#', '', $content);
		return $content;
	}
}



// Enable shortcodes in widget areas
add_filter('widget_text', 'do_shortcode');

