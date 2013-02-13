<?php
/**
 * This file first checks if the framework is used as a theme or plugin framework and sets values accordingly
 *
 * @author		Afzaal Ameer
 * @copyright	Copyright (c) Afzaal Ameer
 * @link		http://www.afzaalace.com
 * @since		Version 1.0
 * @package 	AceFramework
 */

/**
 * check if file is a plugin or theme based on its location, then set constant and globals for further use within the framework
 * @todo create plugin version of framework and prevent interfering with theme version
 */
 


/**
* ACE_BASE contains the root server path of the framework that is loaded
*/
if( ! defined('ACE_BASE' ) ) 	 { 	define( 'ACE_BASE', TEMPLATEPATH.'/' ); }



/**
* ACE_BASE_URL contains the http url of the framework that is loaded
*/
if( ! defined('ACE_BASE_URL' ) ){	define( 'ACE_BASE_URL', get_bloginfo('template_url') . '/'); }



$ace_base_data = get_theme_data( ACE_BASE . '/style.css' );
$ace_base_data['prefix'] = $ace_base_data['Title'];



/**
* THEMENAME contains the Name of the currently loaded theme
*/
if( ! defined('THEMENAME' ) ) { define( 'THEMENAME', $ace_base_data['Title'] ); }



if( ! defined('ACE_FW' ) )
{	
	//define path constants
	
	/**
	* ACE_FW contains the server path of the framework folder
	*/
	define( 'ACE_FW', ACE_BASE . 'framework/' ); 
	
	
	/**
	* ACE_PHP contains the server path of the frameworks php folder
	*/
	define( 'ACE_PHP', ACE_FW . 'php/' );
	
	
	/**
	* ACE_JS contains the server path of the frameworks javascript folder
	*/
	define( 'ACE_JS', ACE_FW . 'js/' );
	
	
	/**
	* ACE_CSS contains the server path of the frameworks css folder
	*/ 
	define( 'ACE_CSS', ACE_FW . 'css/' );
	
	
	/**
	* ACE_OPTIONS contains the server path of the theme_option_pages folder
	*/ 
	define( 'ACE_OPTIONS', ACE_BASE . 'theme_option_pages' ); 
	
	
	
	
	//define url constants
	
	/**
	* ACE_FW_URL contains the url of the framework folder
	*/ 
	define( 'ACE_FW_URL', ACE_BASE_URL . 'framework/' );
	
	/**
	* ACE_IMG_URL contains the url of the frameworks images folder
	*/ 
	define( 'ACE_IMG_URL', ACE_FW_URL . 'images/' ); 
	
	
	/**
	* ACE_PHP_URL contains the url of the frameworks php folder
	*/ 
	define( 'ACE_PHP_URL', ACE_FW_URL . 'php/' );
	
	
	/**
	* ACE_JS_URL contains the url of the frameworks javascript folder
	*/ 
	define( 'ACE_JS_URL', ACE_FW_URL . 'js/' ); 
	
	
	/**
	* ACE_CSS_URL contains the url of the frameworks css folder
	*/ 
	define( 'ACE_CSS_URL', ACE_FW_URL . 'css/' ); 
	
	
	/**
	* ACE_OPTIONS contains the url of the theme_option_pages folder
	*/ 
	define( 'ACE_OPTIONS_URL', ACE_BASE_URL . 'theme_option_pages' ); 
}



//file includes

/**
* This file holds a function set for commonly used operations done by the frameworks frontend
*/
require( ACE_PHP.'function-set-ace-frontend.php' );

/**
* This file holds the class that improves the menu with megamenu capabilities
*/
require( ACE_PHP.'class-megamenu.php' );

/**
* This file holds the function that creates the shortcodes within the backend
*/
require( ACE_PHP.'ace_shortcodes/shortcodes.php' );

/**
* This file holds the class that creates various styles for the frontend that are set within the backend
*/
require( ACE_PHP.'class-style-generator.php' );

/**
* This file holds the class that creates forms based on option arrays
*/
require( ACE_PHP.'class-form-generator.php' );

/**
* This file holds the class that creates several framework specific widgets
*/
require( ACE_PHP.'class-framework-widgets.php' );

/**
* This file holds the class that creates several framework specific widgets
*/
require( ACE_PHP.'class-breadcrumb.php' );


if(is_admin())
{

	// Load script that are needed for the backend

	/**
	* This file holds a function set for ajax operations done by the framework
	*/
	require( ACE_PHP.'function-set-ace-ajax.php' );
	
	/**
	* The adminpage class creates the option page menu items
	*/
	require( ACE_PHP.'class-adminpages.php' );
	
	/**
	* The metabox class creates meta boxes for single posts, pages and other custom post types
	*/
	require( ACE_PHP.'class-metabox.php' );
	
	/**
	* The htmlhelper class is needed to render the options defined in the config files
	*/
	require( ACE_PHP.'class-htmlhelper.php' );
		
	/**
	* This file improves the media uploader so it can be used within the framework
	*/
	require( ACE_PHP.'class-media.php' );
	
	/**
	* This file loads the option set class to create new backend options on the fly
	*/
	require( ACE_PHP.'class-database-option-sets.php' );
	
		/**
	* This file loads the option set class to create new backend options on the fly
	*/
	require( ACE_PHP.'wordpress-importer/ace-export-class.php' );
	
	

}




















