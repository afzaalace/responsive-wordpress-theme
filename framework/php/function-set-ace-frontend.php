<?php  if ( ! defined('ACE_FW')) exit('No direct script access allowed');
/**
 * This file holds various helper functions that are needed by the frameworks FRONTEND
 *
 * @author		Afzaal Ameer
 * @copyright	Copyright (c) Afzaal Ameer
 * @link		http://www.afzaalace.com
 * @since		Version 1.0
 * @package 	AceFramework
 */



if(!function_exists('ace_option'))
{
	/**
	* This function serves as shortcut for ace_get_option and is used to retrieve options saved within the database with the first key set to "ace" which is the majority of all options
	* Please note that while the get_ace_option returns the result, this function echos it by default. if you want to retrieve an option and store the variable please use get_ace_option or set $echo to false
	* 
	* basically the function is called like this: ace_option('portfolio');
	* That would retrieve the following var saved in the global $ace superobject: $ace->options['ace']['portfolio']
	* If you want to set a default value that is returned in case there was no array match you need to use this scheme:
	*
	* ace_option( 'portfolio', "my default");
	*
	* @param string $key accepts a comma separated string with keys
	* @param string $default return value in case we got no result
	* @param bool $echo echo the result or not, default is to false
	* @param bool $decode decode the result or not, default is to false
	* @return string $result: the saved result. if no result was saved or the key doesnt exist returns an empty string
	*/
	function ace_option($key, $default = "", $echo = true, $decode = true)
	{	
		$result = ace_get_option($key, $default, false, $decode);
		
		if(!$echo) return $result; //if we dont want to echo the output end script here
		
		echo $result;
	}
}



if(!function_exists('ace_get_option'))
{
	/**
	* This function serves as shortcut to retrieve options saved within the database by the option pages of the ace framework
	* 
	* basically the function is called like this: ace_get_option('portfolio');
	* That would retrieve the following var saved in the global $ace superobject: $ace->options['ace']['portfolio']
	* If you want to set a default value that is returned in case there was no array match you need to use this scheme:
	*
	* ace_get_option('portfolio', "my default"); or
	* ace_get_option(array('ace','portfolio'), "my default"); or
	*
	* @param string $key accepts a comma separated string with keys
	* @param string $default return value in case we got no result
	* @param bool $echo echo the result or not, default is to false
	* @param bool $decode decode the result or not, default is to false
	* @return string $result: the saved result. if no result was saved or the key doesnt exist returns an empty string
	*/
	function ace_get_option($key, $default = "", $echo = false, $decode = true)
	{
		global $ace;
		$result = $ace->options;
		
		if(is_array($key)) 
		{ 
			$result = $result[$key[0]];
		}
		else
		{
			$result = $result['ace'];
		}
		
		if(isset($result[$key]))
		{	
			$result = $result[$key];
		}
		else
		{
			$result = $default;
		}
		
		
		if($decode) { $result = ace_deep_decode($result); }
		if($result == "") { $result = $default; }
		if($echo) echo $result;
		
		return $result;
	}
}



if(!function_exists('ace_get_the_ID'))
{
	/**
	* This function is similiar to the wordpress function get_the_ID, but other than the wordpress function this functions takes into account
	* if we will display a different post later on, a post that differs from the one we queried in the first place. The function also holds this
	* original ID, even if another query is then executed (for example in dynamic templates for columns)  
	* 
	* an example would be the frontpage template were by default, the ID of the latest blog post is served by wordpress get_the_ID function.
	* ace_get_the_ID would return the same blog post ID if the blog is really displayed on the frontpage. if a static page is displayed the
	* function will display the ID of the static page, even if the page is not yet queried
	*
	* @return int $ID: the "real" ID of the post/page we are currently viewing
	*/
	function ace_get_the_ID()
	{
		global $ace_config;
		$ID = false;
		
		if(!isset($ace_config['real_ID']))
		{
			if(!empty($ace_config['new_query']['page_id'])) 
			{ 
				$ID = $ace_config['new_query']['page_id']; 
			}
			else
			{
				$ID = @get_the_ID();
			}
			
			$ace_config['real_ID'] = $ID;
		}
		else
		{
			$ID = $ace_config['real_ID'];
		}
		return $ID;
	}
	
	add_action('wp_head', 'ace_get_the_ID');
}


if(!function_exists('ace_is_overview'))
{
	/**
	* This function checks if the page we are going to render is a page with a single entry or a multi entry page (blog or archive for example)
	*
	* @return bool $result true or false
	*/
	
	function ace_is_overview()
	{
		$result = true;
	
		if (is_singular())
		{ 
			$result = false;
		} 
		
		if(is_front_page() && ace_get_option('frontpage') == ace_get_the_ID())
		{
			$result = false;
		}
		
		return $result;
	}
}

if(!function_exists('ace_is_dynamic_template'))
{
	/**
	* This function checks if the page we are going to render is using a dynamic template
	*
	* @return bool $result true or false
	*/
	
	function ace_is_dynamic_template($id = false, $dependency = false)
	{
		$result = false;
		if(!$id) $id = ace_get_the_ID();
		
		if($dependency)
		{
			if(ace_post_meta($id, $dependency[0]) != $dependency[1])
			{
				return false;
			}
		}
		
		if(ace_post_meta($id, 'dynamic_templates')) 
		{
			$result = true; 
		}
		
		return $result;
	}
}



if(!function_exists('ace_post_meta'))
{
	/**
	* This function retrieves the custom field values for a given post and saves it to the global ace config array
	* If a subkey was set the subkey is returned, otherwise the array is saved to the global config array
	* The function also hooks into the post loop and is automatically called for each post
	*/
	function ace_post_meta($post_id = '', $subkey = false)
	{
		//if the user only passed a string and no id the string will be used as subkey
		if(!$subkey && $post_id != "" && !is_numeric($post_id) && !is_object($post_id))
		{
			$subkey = $post_id;
			$post_id = "";
		}
		
		global $ace, $ace_config;
		$key = '_ace_elements_'.$ace->option_prefix;
		//$key = '_ace_elements_theme_compatibility_mode';
		$values = "";
		
		//if post id is on object the function was called via hook. If thats the case reset the meta array
		if(is_object($post_id) && isset($post_id->ID)) 
		{ 
			$post_id = $post_id->ID;
		}
		
		
		if(!$post_id) 
		{ 
			$post_id = get_the_ID();
		}
		
		if(!is_numeric($post_id)) return;
		
		
		$ace_config['meta'] = ace_deep_decode(get_post_meta($post_id, $key, true));
		$ace_config['meta'] = apply_filters('ace_post_meta_filter', $ace_config['meta'], $post_id);
		
		if($subkey && isset($ace_config['meta'][$subkey]))
		{
			$meta = $ace_config['meta'][$subkey];
		}
		else if($subkey)
		{
			$meta = false;
		}
		else
		{
			$meta = $ace_config['meta'];
		}
		
		return $meta;
	}
	
	add_action('the_post', 'ace_post_meta');
}




if(!function_exists('ace_get_modified_option'))
{
	/**
	* This function returns an option that was set in the backend. However if a post meta key with the same name exists it retrieves this option instead
	* That way we can easily set global settings for all posts in our backend (for example slideshow duration options) and then overrule those options
	* 
	* In addition to the option key we need to pass a second key for a post meta value that must return a value other then empty before the global settings can be overwritten.
	* (example: should ths post use overwritten options? no=>"" yes=>"yes")
	*
	* @param string $key database key for both the post meta table and the framework options table
	* @param string $extra_check database key for both a post meta value that needs to be true in order to accept an overwrite
	* @return string $result: the saved result. if no result was saved or the key doesnt exist returns an empty string
	*/

	function ace_get_modified_option($key, $extra_check = false)
	{	
		global $post;
		
		//if we need to do an extra check get the post meta value for that key
		if($extra_check && isset($post->ID))
		{
			$extra_check = get_post_meta($post->ID, $extra_check, true);
			if($extra_check)
			{
				//add underline to the post meta value since we always hide those values
				$result = get_post_meta($post->ID, '_'.$key, true);
				return $result;
			}
		}
		
		$result = ace_get_option($key);
		return $result;
		
	}
}





if(!function_exists('ace_set_follow'))
{
	/**
	 * prevents duplicate content by setting archive pages to nofollow 
	 * @return string the robots meta tag set to index follow or noindex follow
	 */
	function ace_set_follow()
	{
		if ((is_single() || is_page() || is_home() ) && ( !is_paged() )) 
		{
			return '<meta name="robots" content="index, follow" />' . "\n";
		} 
		else 
		{
			return '<meta name="robots" content="noindex, follow" />' . "\n";
		}
	}
}





if(!function_exists('ace_logo'))
{
	/**
	 * return the logo of the theme. if a logo was uploaded and set at the backend options panel display it
	 * otherwise display the logo file linked in the css file for the .bg-logo class
	 * @return string the logo + url
	 */
	function ace_logo($use_image = "")
	{
		if($logo = ace_get_option('logo'))
		{
			 $logo = "<img src=".$logo." alt='' />";
			 $logo = "<h1 class='logo'><a href='".home_url('/')."'>".$logo."</a></h1>";
		}
		else
		{
			$logo = get_bloginfo('name');
			if($use_image) $logo = "<img src=".$use_image." alt='' title='$logo'/>";
			$logo = "<h1 class='logo bg-logo'><a href='".home_url('/')."'>".$logo."</a></h1>";
		}
	
		return $logo;
	}
}



if(!function_exists('ace_image_by_id'))
{
	/**
	 * Fetches an image based on its id and returns the string image with title and alt tag
	 * @return string image url
	 */
	function ace_image_by_id($thumbnail_id, $size = array('width'=>800,'height'=>800), $output = 'image')
	{	
		if(!is_numeric($thumbnail_id)) {return false; }
		
		if(is_array($size)) 
		{
			$size[0] = $size['width'];
			$size[1] = $size['height'];
		}

		// get the image with appropriate size by checking the attachment images
		$image_src = wp_get_attachment_image_src($thumbnail_id, $size);
		
		//if output is set to url return the url now and stop executing, otherwise build the whole img string with attributes
		if ($output == 'url') return $image_src[0];
	
		//get the saved image metadata:
		$attachment = get_post($thumbnail_id);
		
		if(is_object($attachment))
		{
			$image_description = $attachment->post_excerpt == "" ? $attachment->post_content : $attachment->post_excerpt;
			$image_description = trim(strip_tags($image_description));
			$image_title = trim(strip_tags($attachment->post_title));
			
			return "<img src='".$image_src[0]."' title='".$image_title."' alt='".$image_description."' />";
		}
	}
}


if(!function_exists('ace_html5_video_embed'))
{
	/**
	 * Creates HTML 5 output and also prepares flash fallback for a video of choice
	 * @return string HTML5 video element
	 */
	function ace_html5_video_embed($path, $image = "", $types = array('webm' => 'type="video/webm"', 'mp4' => 'type="video/mp4"', 'ogv' => 'type="video/ogg"'))
	{	
		preg_match("!^(.+?)(?:\.([^.]+))?$!", $path, $path_split);
		
		$output = "";
		if(isset($path_split[1]))
		{
			if(!$image && @file_get_contents($path_split[1].'.jpg',0,NULL,0,1))
			{
				$image = 'poster="'.$path_split[1].'.jpg"'; //poster image isnt accepted by the player currently, waiting for bugfix
			}
			
			$uid = 'player_'.get_the_ID().'_'.mt_rand().'_'.mt_rand();
		
			$output .= '<video class="ace_video" '.$image.' controls id="'.$uid.'">';

			foreach ($types as $key => $type)
			{
				if($path_split[2] == $key || @file_get_contents($path_split[1].'.'.$key,0,NULL,0,1)) 
				{  
					$output .= '	<source src="'.$path_split[1].'.'.$key.'" '.$type.' />';
				}
			}

			$output .= '</video>';
		}
		return $output;
	}
}






if(!function_exists('ace_get_link'))
{
	/**
	* Fetches a url based on values set in the backend
	* @param array $option_array array that at least needs to contain the linking method and depending on that, the appropriate 2nd id value
	* @param string $keyprefix option set key that must be in front of every element key
	* @param string $inside if inside is passed it will be wrapped inside <a> tags with the href set to the previously returned link url
	* @param string $post_id if the function is called outside of the loop we might want to retrieve the permalink of a different post with this id
	* @return string url (with image inside <a> tag if the image string was passed)
	*/
	function ace_get_link($option_array, $keyprefix, $inside = false, $post_id = false)
	{	
		if(empty($option_array[$keyprefix.'link'])) $option_array[$keyprefix.'link'] = "";
		
		//check which value the link array has (possible are empty, lightbox, page, post, cat, url) and create the according link
		switch($option_array[$keyprefix.'link'])
		{
			case "lightbox": 
				$url = ace_image_by_id($option_array[$keyprefix.'image'], array('width'=>8000,'height'=>8000), 'url');
			break;
			
			case "cat": 
				$url = get_category_link($option_array[$keyprefix.'link_cat']);
			break;
			
			case "page": 
				$url = get_page_link($option_array[$keyprefix.'link_page']);
			break;			
			
			case "self": 
				if(!is_singular() || $post_id != ace_get_the_ID() || !isset($option_array[$keyprefix.'image']))
				{
					$url = get_permalink($post_id);
				}
				else
				{
					$url = ace_image_by_id($option_array[$keyprefix.'image'], array('width'=>8000,'height'=>8000), 'url');
				}
			break;
			
			case "url": 
				$url = $option_array[$keyprefix.'link_url'];
			break;
			
			case "video": 
				$video_url = $option_array[$keyprefix.'link_video'];
				
				
				if(ace_backend_is_file($video_url, 'html5video'))
				{
					$output = ace_html5_video_embed($video_url);
					$class = "html5video";
				}
				else
				{
					global $wp_embed;
					$output = $wp_embed->run_shortcode("[embed]".$video_url."[/embed]");
					$class  = "embeded_video";
				}
				
				$output = "<div class='slideshow_video $class'>".$output."</div>";
				return $inside . $output;
				
			break;
		
			default: 
				$url = $inside;
			break;
		}
		
		if(!$inside || $url == $inside)
		{
			return $url;
		}
		else
		{
			return "<a href='".$url."'>".$inside."</a>";
		}
	}
}




if(!function_exists('ace_pagination'))
{
	/**
	* Displays a page pagination if more posts are available than can be displayed on one page
	* @param string $pages pass the number of pages instead of letting the script check the gobal paged var
	* @return string $output returns the pagination html code
	*/
	function ace_pagination($pages = '')
	{
		global $paged;
		
		if(get_query_var('paged')) {
		     $paged = get_query_var('paged');
		} elseif(get_query_var('page')) {
		     $paged = get_query_var('page');
		} else {
		     $paged = 1;
		}
		
		$output = "";
		$prev = $paged - 1;							
		$next = $paged + 1;	
		$range = 2; // only edit this if you want to show more page-links
		$showitems = ($range * 2)+1;
		
		if($pages == '')
		{	
			global $wp_query;
			$pages = $wp_query->max_num_pages;
			if(!$pages)
			{
				$pages = 1;
			}
		}
		
		$method = "get_pagenum_link";
		if(is_single())
		{
			$method = "ace_post_pagination_link";
		}
		
		
		
		if(1 != $pages)
		{
			$output .= "<div class='pagination'>";
			$output .= "<span class='pagination-meta'>".sprintf(__("Page %d of %d", 'ace_framework'), $paged, $pages)."</span>";
			$output .= ($paged > 2 && $paged > $range+1 && $showitems < $pages)? "<a href='".$method(1)."'>&laquo;</a>":"";
			$output .= ($paged > 1 && $showitems < $pages)? "<a href='".$method($prev)."'>&lsaquo;</a>":"";
			
				
			for ($i=1; $i <= $pages; $i++)
			{
				if (1 != $pages &&( !($i >= $paged+$range+1 || $i <= $paged-$range-1) || $pages <= $showitems ))
				{
					$output .= ($paged == $i)? "<span class='current'>".$i."</span>":"<a href='".$method($i)."' class='inactive' >".$i."</a>"; 
				}
			}
			
			$output .= ($paged < $pages && $showitems < $pages) ? "<a href='".$method($next)."'>&rsaquo;</a>" :"";
			$output .= ($paged < $pages-1 &&  $paged+$range-1 < $pages && $showitems < $pages) ? "<a href='".$method($pages)."'>&raquo;</a>":"";
			$output .= "</div>\n";
		}
			
		return $output;
	}
	
	function ace_post_pagination_link($link)
	{
		$url =  preg_replace('!">$!','',_wp_link_page($link));
		$url =  preg_replace('!^<a href="!','',$url);
		return $url;
	}
}




if(!function_exists('ace_check_custom_widget'))
{
	/**
	 *  checks which page we are viewing and if the page got a custom widget
	 */

	function ace_check_custom_widget($area, $return = 'title')
	{	
		$special_id_string = "";
		
		if($area == 'page')
		{
			$id_array = ace_get_option('widget_pages');
			

		}
		else if($area == 'cat')
		{
			$id_array = ace_get_option('widget_categories');
		}
		else if($area == 'dynamic_template')
		{
			global $ace;
			$dynamic_widgets = array();
			
			foreach($ace->options as $option_parent)
			{
				foreach ($option_parent as $element_data)
				{
					if(isset($element_data[0]) && is_array($element_data) && in_array('widget', $element_data[0]))
					{
						for($i = 1; $i <= $element_data[0]['dynamic_column_count']; $i++)
						{
							if($element_data[0]['dynamic_column_content_'.$i] == 'widget')
							{
								$dynamic_widgets[] =  $element_data[0]['dynamic_column_content_'.$i.'_widget'];
							}
						}
					}
				}
			}

			return $dynamic_widgets;
		}

		//first build the id string
		if(is_array($id_array))
		{
			foreach ($id_array as $special)
			{
				if(isset($special['widget_'.$area]) && $special['widget_'.$area] != "")
				{
					$special_id_string .= $special['widget_'.$area].",";
				}
			}
		}
		
		//if we got a valid string remove the last comma
		$special_id_string = trim($special_id_string,',');
		
		
		$clean_id_array = explode(',',$special_id_string);
		
		//if we dont want the title just return the id array
		if($return != 'title') return $clean_id_array;

		
		if(is_page($clean_id_array))
		{	
			return get_the_title();
		}
		else if(is_category($clean_id_array))
		{
			return single_cat_title( "", false );
		}
		
	}
}



if(!function_exists('ace_which_archive'))
{
	/**
	 *  checks which archive we are viewing and returns the archive string
	 */

	function ace_which_archive()
	{	
		$output = "";
		
		if ( is_category() ) 
		{ 		
			$output = __('Archive for category: ','ace_framework').single_cat_title('',false);
		} 
		elseif (is_day()) 
		{
			$output = __('Archive for date: ','ace_framework').get_the_time('F jS, Y');
		} 
		elseif (is_month())
		{ 
			$output = __('Archive for month: ','ace_framework').get_the_time('F, Y'); 
		} 
		elseif (is_year()) 
		{ 
			$output = __('Archive for year: ','ace_framework').get_the_time('Y'); 
		} 
		elseif (is_search()) 
		{
			global $wp_query;
			$search_result_count = !empty($wp_query->found_posts) ? $wp_query->found_posts ." s": "S";
			
			$output = __($search_result_count.'earch results for: ','ace_framework').$_GET['s']; 
		} 
		elseif (is_author()) 
		{ 
			$curauth = (get_query_var('author_name')) ? get_user_by('slug', get_query_var('author_name')) : get_userdata(get_query_var('author'));
			$output = __('Author Archive ','ace_framework');
			
			if(isset($curauth->nickname)) $output .= __('for:','ace_framework')." ".$curauth->nickname;

		} 
		elseif (is_tag()) 
		{
			$output = __('Tag Archive for: ','ace_framework').single_tag_title('',false); 
		} 
		elseif(is_tax())
		{
			$term = get_term_by( 'slug', get_query_var( 'term' ), get_query_var( 'taxonomy' ) );
			$output = __('Archive for: ','ace_framework').$term->name;
		}
		else
		{
			$output = __('Archives ','ace_framework');
		}
		
		if (isset($_GET['paged']) && !empty($_GET['paged'])) 
		{
			$output .= " (".__('Page ','ace_framework').$_GET['paged'].")";
		}
		
		return $output;
	}
}


if(!function_exists('ace_remove_more_jump_link'))
{
	/**
	 *  Removes the jump link from the read more tag
	 */

	function ace_remove_more_jump_link($link) 
	{ 
		$offset = strpos($link, '#more-');
		if ($offset) 
		{
			$end = strpos($link, '"',$offset);
		}
		if ($end) 
		{
			$link = substr_replace($link, '', $offset, $end-$offset);
		}
		return $link;
	}
}

if(!function_exists('ace_excerpt'))
{
	/**
	 *  Returns a post excerpt. depending on the order parameter the funciton will try to retrieve the excerpt from a different source
	 */

	function ace_excerpt($length = 250, $more_text = false, $order = array('more-tag','excerpt')) 
	{ 
		$excerpt = "";
		if($more_text === false) $more_text = __('Read more', 'ace_framework');
		
		foreach($order as $method)
		{
			if(!$excerpt)
			{
				switch ($method) 
				{ 
					case 'more-tag': 
						global $more;
						$more = 0;
						$content = get_the_content($more_text);
						$pos = strpos($content, 'class="more-link"'); 
						
						if($pos !== false)
						{
							$excerpt = $content;
						}
					
					break;
					
					case 'excerpt' : 
						
						$post = get_post(get_the_ID());
						if($post->post_excerpt)
						{
							$excerpt = get_the_excerpt();
						}
						else
						{
							$excerpt = preg_replace("!\[.+?\]!", "", get_the_excerpt());
							//	$excerpt = preg_replace("!\[.+?\]!", "", $post->post_content);
							$excerpt = ace_backend_truncate($excerpt, $length," ");
						}
						
						$excerpt = preg_replace("!\s\[...\]$!", '...', $excerpt);
						
					break;
				}
			}
		}
		
		if($excerpt)
		{
			$excerpt = apply_filters('the_content', $excerpt);
			$excerpt = str_replace(']]>', ']]&gt;', $excerpt);
		}
		return $excerpt;
	}
}

if(!function_exists('ace_get_browser'))
{
	function ace_get_browser($returnValue = 'class', $lowercase = false)
	{
		if(empty($_SERVER['HTTP_USER_AGENT'])) return false;
		
	    $u_agent = $_SERVER['HTTP_USER_AGENT'];
	    $bname = 'Unknown';
	    $platform = 'Unknown';
	    $version= "";
	
	    //First get the platform?
	    if (preg_match('!linux!i', $u_agent)) {
	        $platform = 'linux';
	    }
	    elseif (preg_match('!macintosh|mac os x!i', $u_agent)) {
	        $platform = 'mac';
	    }
	    elseif (preg_match('!windows|win32!i', $u_agent)) {
	        $platform = 'windows';
	    }
	   
	    // Next get the name of the useragent yes seperately and for good reason
	    if(preg_match('!MSIE!i',$u_agent) && !preg_match('!Opera!i',$u_agent))
	    {
	        $bname = 'Internet Explorer';
	        $ub = "MSIE";
	    }
	    elseif(preg_match('!Firefox!i',$u_agent))
	    {
	        $bname = 'Mozilla Firefox';
	        $ub = "Firefox";
	    }
	    elseif(preg_match('!Chrome!i',$u_agent))
	    {
	        $bname = 'Google Chrome';
	        $ub = "Chrome";
	    }
	    elseif(preg_match('!Safari!i',$u_agent))
	    {
	        $bname = 'Apple Safari';
	        $ub = "Safari";
	    }
	    elseif(preg_match('!Opera!i',$u_agent))
	    {
	        $bname = 'Opera';
	        $ub = "Opera";
	    }
	    elseif(preg_match('!Netscape!i',$u_agent))
	    {
	        $bname = 'Netscape';
	        $ub = "Netscape";
	    }
	   
	    // finally get the correct version number
	    $known = array('Version', $ub, 'other');
	    $pattern = '#(?<browser>' . join('|', $known) .
	    ')[/ ]+(?<version>[0-9.|a-zA-Z.]*)#';
	    if (!@preg_match_all($pattern, $u_agent, $matches)) {
	        // we have no matching number just continue
	    }
	   
	    // see how many we have
	    $i = count($matches['browser']);
	    if ($i != 1) {
	        //we will have two since we are not using 'other' argument yet
	        //see if version is before or after the name
	        if (strripos($u_agent,"Version") < strripos($u_agent,$ub)){
	            $version= $matches['version'][0];
	        }
	        else {
	            $version= $matches['version'][1];
	        }
	    }
	    else {
	        $version= $matches['version'][0];
	    }
	   
	    // check if we have a number
	    if ($version==null || $version=="") {$version="?";}
	    
	    $mainVersion = $version;
	    if (strpos($version, '.') !== false)
	    {
	    	$mainVersion = explode('.',$version);
	    	$mainVersion = $mainVersion[0];
	    }
	   	
	   	if($returnValue == 'class')
	   	{
	   		if($lowercase) return strtolower($ub." ".$ub.$mainVersion);
	   	
	   		return $ub." ".$ub.$mainVersion;
	   	}
	   	else
	   	{
		    return array(
		        'userAgent' => $u_agent,
		        'name'      => $bname,
		        'version'   => $version,
		        'platform'  => $platform,
		        'pattern'   => $pattern
		    );
	    }
	} 
}


if(!function_exists('ace_favicon'))
{
	function ace_favicon($url = "")
	{
		$icon_link = "";
		if($url)
		{
			$type = "image/x-icon";
			if(strpos($url,'.png' )) $type = "image/png";
			if(strpos($url,'.gif' )) $type = "image/gif";
		
			$icon_link = '<link rel="icon" href="'.$url.'" type="'.$type.'">';
		}
		
		return $icon_link;
	}
}