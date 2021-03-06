<?php  if ( ! defined('ACE_FW')) exit('No direct script access allowed');
/**
 * This file holds various helper functions that are needed by the frameworks BACKEND
 *
 * @author		Afzaal Ameer
 * @copyright	Copyright (c) Afzaal Ameer
 * @link		http://www.afzaalace.com
 * @since		Version 1.0
 * @package 	AceFramework
 */



/**
 * load files from a multidemensional array
 *
 * @param array $scripts_to_load the array to pass
 */
if(!function_exists('ace_backend_load_scripts_by_option'))
{
	function ace_backend_load_scripts_by_option( $scripts_to_load )
	{
		foreach ( $scripts_to_load as $path => $includes )
		{	
			if( $includes )
			{
				foreach ( $includes as $include )
				{
					switch( $path )
					{
					case 'php':
					include_once( ACE_PHP.$include.'.php' );
					break;
					}
				}
			}
		}
	}
}



/**
 * load all php files in one folder, if the folder contains files with different file extensions return the filenames as array
 * 
 * @param string $folder path to the folder that should be loaded
 * @return array $files files the folder contains that are no php files
 */
if(!function_exists('ace_backend_load_scripts_by_folder'))
{
	function ace_backend_load_scripts_by_folder( $folder )
	{	
		$files = array();
		
		// Open a known directory, and proceed to read its contents
		if ( is_dir( $folder ) ) 
		{
		    if ( $dh = opendir( $folder ) ) 
		    {
		        while ( ( $file = readdir( $dh ) ) !== false) 
		        {
		        	if('.' != $file && '..' != $file)
		        	{	
		        		$pathinfo = pathinfo($folder ."/". $file);
		        		
		        		if( isset($pathinfo['extension']) && $pathinfo['extension']  == 'php' )
		        		{
		        			include_once( $folder ."/". $file );
		        		}
		        		else
		        		{
		        			$files[] = $file;
		        		}
		        	}
		        }
		        closedir($dh);
		    }
		}
		
		return $files;
	}
}
 
 
 
 
 
if(!function_exists('ace_backend_safe_string'))
{
	/**
	* Create a lower case version of a string without spaces so we can use that string for database settings
	* 
	* @param string $string to convert
	* @return string the converted string
	*/
	function ace_backend_safe_string( $string , $replace = "_")
	{
		$string = strtolower($string);
	
		$trans = array(
					'&\#\d+?;'				=> '',
					'&\S+?;'				=> '',
					'\s+'					=> $replace,
					'ä'						=> 'ae',
					'ö'						=> 'oe',
					'ü'						=> 'ue',
					'Ä'						=> 'Ae',
					'Ö'						=> 'Oe',
					'Ü'						=> 'Ue',
					'ß'						=> 'ss',
					'[^a-z0-9\-\._]'		=> '',
					$replace.'+'			=> $replace,
					$replace.'$'			=> $replace,
					'^'.$replace			=> $replace,
					'\.+$'					=> ''
				  );

		$string = strip_tags($string);

		foreach ($trans as $key => $val)
		{
			$string = preg_replace("#".$key."#i", $val, $string);
		}
		
		return stripslashes($string);
	}
}

if(!function_exists('ace_backend_check_by_regex'))
{
	/**
	* Checks a string based on a passed regex and returns true or false
	* 
	* @param string $string to check
	* @param string $regex to check
	* @return string the converted string
	*/
	function ace_backend_check_by_regex( $string , $regex)
	{
		if(!$regex) return false;
		if($regex == 'safe_data') $regex = '^[a-zA-Z0-9\s-_]+$';
		if($regex == 'email')	  $regex = '^\w[\w|\.|\-]+@\w[\w|\.|\-]+\.[a-zA-Z]{2,4}$';
		if($regex == 'url')	  	  $regex = '^(ftp|http|https):\/\/(\w+:{0,1}\w*@)?(\S+)(:[0-9]+)?(\/|\/([\w\#!:.?+=&%@!\-\/]))?$';
		
		if(preg_match('#'.$regex.'#', $string))
		{
			return true;
		}
		
		return false;
	}
}



if(!function_exists('ace_backend_is_file'))
{

	/**
	* Checks if a file is an image, text, video or if the file extension matches one of the exensions in a given array
	* 
	* @param string $passedNeedle the file name
	* @param string | array $haystack to match against. can be either array or a keyword: image, text, videoService
	* @return bool returns true oder false
	*/
	function ace_backend_is_file($passedNeedle, $haystack)
	{	
		
		// get file extension
		$needle = substr($passedNeedle, strrpos($passedNeedle, '.') + 1);
		
		//check if file or url was passed
		//if its a url 
		
		if(strlen($needle) > 4)
		{
			if(!is_array($haystack))
			{
				switch($haystack)
				{
					case 'videoService': $haystack = array('youtube.com/','vimeo.com/'); break;
				}
			}
			
			if(is_array($haystack))
			{
				foreach ($haystack as $regex)
				{
					if(preg_match("!".$regex."!", $passedNeedle)) return true;
				}
			}	
		}
		else // if its a file
		{
			//predefined arrays
			if(!is_array($haystack))
			{
				switch($haystack)
				{
					case 'image':
						$haystack = array('png','gif','jpeg','jpg','pdf','tif');
						
					break;
					
					case 'text':
						$haystack = array('doc','docx','rtf','ttf','txt','odp');
					break;
					
					case 'html5video':
						$haystack = array('ogv','webm','mp4');
					break;
				}
			}
			
			//match extension against array
			if(is_array($haystack))
			{
				if (in_array($needle,$haystack))
				{
					return true;
				}
			}
		}
		
		return false;
	}
}



if(!function_exists('ace_backend_get_hex_from_rgb'))
{
	/**
	 *  converts an rgb string into a hex value and returns the string
	 *  @param string $r red
	 *  @param string $g green
	 *  @param string $B blue
	 *  @return string returns the converted string
	 */
 	function ace_backend_get_hex_from_rgb($r=FALSE, $g=FALSE, $b=FALSE) {
		$x = 255;
		$y = 0;
		
		$r = (is_int($r) && $r >= $y && $r <= $x) ? $r : 0;
		$g = (is_int($g) && $g >= $y && $g <= $x) ? $g : 0;
		$b = (is_int($b) && $b >= $y && $b <= $x) ? $b : 0;
		
		
		return sprintf('#%02X%02X%02X', $r, $g, $b);
	}
}


if(!function_exists('ace_backend_calculate_similar_color'))
{
	/**
	 *  calculates a darker or lighter color variation of a color
	 *  @param string $color hex color code
	 *  @param string $shade darker or lighter
	 *  @param int $amount how much darker or lighter
	 *  @return string returns the converted string
	 */
 	function ace_backend_calculate_similar_color($color, $shade, $amount) 
 	{
 		//remove # from the begiining if available and make sure that it gets appended again at the end if it was found
 		$newcolor = "";
 		$prepend = "";
 		if(strpos($color,'#') !== false) 
 		{ 
 			$prepend = "#";
 			$color = substr($color, 1, strlen($color)); 
 		}
 		
 		//iterate over each character and increment or decrement it based on the passed settings
 		$nr = 0;
		while (isset($color[$nr])) 
		{
			$char = strtolower($color[$nr]);
			
			for($i = $amount; $i > 0; $i--)
			{
				if($shade == 'lighter')
				{
					switch($char)
					{
						case 9: $char = 'a'; break;
						case 'f': $char = 'f'; break;
						default: $char++;
					}
				}
				else if($shade == 'darker')
				{
					switch($char)
					{
						case 'a': $char = '9'; break;
						case '0': $char = '0'; break;
						default: $char = chr(ord($char) - 1 );
					}
				}
			}
			$nr ++;
			$newcolor.= $char;
		}
 		
		$newcolor = $prepend.$newcolor;
		return $newcolor;
	}
}

if(!function_exists('ace_backend_hex_to_rgb_array'))
{
	/**
	 *  converts an hex string into an rgb array
	 *  @param string $color hex color code
	 *  @return array $color 
	 */
	function ace_backend_hex_to_rgb_array($color) 
	{
		if(strpos($color,'#') !== false) 
		{ 
			$color = substr($color, 1, strlen($color)); 
		}
		
		$color = str_split($color, 2);
		foreach($color as $key => $c) $color[$key] = hexdec($c);
		
		return $color;
	}
}

if(!function_exists('ace_backend_merge_colors'))
{
	/**
	 *  merges to colors
	 *  @param string $color1 hex color code
	 *  @param string $color2 hex color code
	 *  @return new color 
	 */
	function ace_backend_merge_colors($color1, $color2) 
	{
		if(empty($color1)) return $color2;
		if(empty($color2)) return $color1;
	
		$prepend = array("", "");
		$colors  = array(ace_backend_hex_to_rgb_array($color1), ace_backend_hex_to_rgb_array($color2));
		
		$final = array();
		foreach($colors[0] as $key => $color)
		{
			$final[$key] = (int) ceil(($colors[0][$key] + $colors[1][$key]) / 2);
		}
	
		return ace_backend_get_hex_from_rgb($final[0], $final[1], $final[2]);
	
	}
}


function ace_backend_counter_color($color) 
{
	$color = ace_backend_hex_to_rgb_array($color);
	
	foreach($color as $key => $value)
	{
		$color[$key] = (int) (255 - $value);
	}
	
	return ace_backend_get_hex_from_rgb($color[0], $color[1], $color[2]); 
}


if(!function_exists('ace_backend_add_thumbnail_size'))
{
	/**
	 *  creates wordpress image thumb sizes for the theme
	 *  @param array $ace_config arraw with image sizes 
	 */

	function ace_backend_add_thumbnail_size($ace_config)
	{	
		if (function_exists('add_theme_support')) 
		{ 
			foreach ($ace_config['imgSize'] as $sizeName => $size)
			{
				if($sizeName == 'base')
				{
					set_post_thumbnail_size($ace_config['imgSize'][$sizeName]['width'], $ace_config[$sizeName]['height'], true);
				}
				else
				{	
					if(!isset($ace_config['imgSize'][$sizeName]['crop'])) $ace_config['imgSize'][$sizeName]['crop'] = true;
				
					add_image_size(	 
						$sizeName,
						$ace_config['imgSize'][$sizeName]['width'], 
						$ace_config['imgSize'][$sizeName]['height'], 
						$ace_config['imgSize'][$sizeName]['crop']);
				}
			}
		}
	}
}


if(!function_exists('ace_flush_rewrites'))
{
	
	/**
	 *  This function checks if the user has saved the options page by checking the ace_rewrite_flush option
	 *  if thats the case it flushes the rewrite rules so permalink changes work properly
	 */

	function ace_flush_rewrites()
	{
		if(get_option('ace_rewrite_flush'))
		{	
			global $wp_rewrite;
			$wp_rewrite->flush_rules();
			delete_option('ace_rewrite_flush');
		}
		
	}
	
	add_action('wp_loaded', 'ace_flush_rewrites');
}







if(!function_exists('ace_backend_theme_activation'))
{
	/**
	 *  This function gets executed if the theme just got activated. It resets the global frontpage setting
	 *  and then redirects the user to the ace framework main options page
	 */
	function ace_backend_theme_activation()
	{
		global $pagenow;
		if ( is_admin() && 'themes.php' == $pagenow && isset( $_GET['activated'] ) ) 
		{
			#set frontpage to display_posts
			update_option('show_on_front', 'posts');
			
			#provide hook so themes can execute theme specific functions on activation
			do_action('ace_backend_theme_activation');
			
			#redirect to options page
			header( 'Location: '.admin_url().'admin.php?page=ace' ) ;
		}
	}
	
	add_action('admin_init','ace_backend_theme_activation');
}





if(!function_exists('ace_backend_truncate'))
{
	/**
	 *  This function shortens a string
	 */
	function ace_backend_truncate($string, $limit, $break=".", $pad="...") 
	{
		if(strlen($string) <= $limit) return $string;
		
		if(false !== ($breakpoint = strpos($string, $break, $limit))) 
		{ 
			if($breakpoint < strlen($string) - 1) 
			{ 
				$string = substr($string, 0, $breakpoint) . $pad; 
			} 
		} 
		return $string; 
	}
}


if(!function_exists('ace_deep_decode'))
{
	/**
	 *  This function performs deep decoding on an array of elements
	 */
	function ace_deep_decode($elements) 
	{
		if(is_array($elements) || is_object($elements))
		{
			foreach($elements as $key=>$element)
			{
				$elements[$key] = ace_deep_decode($element);
			}
		}
		else
		{
			$elements = html_entity_decode($elements, ENT_QUOTES, get_bloginfo('charset')); 
		}
		
		return $elements; 
	}
}



if(!function_exists('ace_backend_get_dynamic_templates'))
{
	/**
	 *  This function gets dynamic templates created at the template generator
	 */
	function ace_backend_get_dynamic_templates() 
	{
		$templates = array();
		global $ace;
		
		if(is_array($ace->option_pages))
		{
			foreach($ace->option_pages as $page)
			{
				if(array_key_exists('sortable', $page))
				{	
					$templates[$page['title']] = $page['slug'];
				}
			}
		}
		
		return $templates; 
	}
}




if(!function_exists('ace_backend_get_post_page_cat_name_by_id'))
{
	//export helper
	function ace_backend_get_post_page_cat_name_by_id($id, $type, $taxonomy = false)
	{	
		switch ($type)
		{
			case 'page':
			case 'post':	
				$the_post = get_post($id);
				if(isset($the_post->post_title)) return $the_post->post_title;
			break;
			
			case 'cat':	
				$return = array();
				$ids = explode(',',$id);
				foreach($ids as $cat_id)
				{	
					if($cat_id)
					{
						if(!$taxonomy) $taxonomy = 'category';
						$cat = get_term( $cat_id, $taxonomy );

						if($cat) $return[] = $cat->name;
					}
				}
			if(!empty($return)) return $return;
			
				

			break;
		}
	}
}



// ADMIN MENU
if(!function_exists('ace_backend_admin_bar_menu'))
{
	add_action('admin_bar_menu', 'ace_backend_admin_bar_menu', 99);
	function ace_backend_admin_bar_menu() {
	
	
	global $ace, $wp_admin_bar;
	if(empty($ace->option_pages)) return;
	
		$urlBase = admin_url( 'admin.php' );
		foreach($ace->option_pages as $ace_page)
		{
			$menu = array(
				'id' => $ace_page['slug'],
				'title' => $ace_page['title'],
				'href' => $urlBase."?page=".$ace_page['slug'],
				'meta' => array('target' => 'blank')
			);
			
			if($ace_page['slug'] != $ace_page['parent']  )
			{
				 $menu['parent'] = $ace_page['parent'];
				 $menu['href'] = $urlBase."?page=".$ace_page['parent']."#goto_".$ace_page['slug'];
			}
			
			if(is_admin()) $menu['meta'] = array('onclick' => 'self.location.replace(encodeURI("'.$menu['href'].'")); window.location.reload(true);  ');
		
			$wp_admin_bar->add_menu($menu);
		}
	}
}



