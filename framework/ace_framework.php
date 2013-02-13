<?php
/**
 * ACE Framework
 *
 * A flexible Wordpress Framework
 *
 * This file includes the superobject class and loads the parameters neccessary for the backend pages.
 * A new $ace superobject is then created that holds all data necessary for either front or backend, depending what page you are browsing
 *
 * @author		Afzaal Ameer
 * @copyright	Copyright (c) Afzaal Ameer
 * @link		http://www.afzaalace.com
 * @since		Version 1.0
 * @package 	AceFramework
 * @version 	Responsive-Edition

*/ 

 
/**
 *  Config File
 *  Load the autoconfig file that will set some 
 *  constants based on the installation type (plugin or theme)
 * 
 */
 
 require( 'php/inc-autoconfig.php' );



/**
 *  Superobject Class
 *  Load the super object class, but only if it hasn't been
 *  already loaded by an ace plugin with newer version
 * 
 */
 
if( ! defined('ACE_PLUGIN_FW') || ! defined('ACE_THEME_FW') || ( version_compare(ACE_THEME_FW, ACE_PLUGIN_FW, '>=') ) )
{ 
	require( ACE_PHP.'class-superobject.php' );
}


/**
 *  Include Backend default Function set
 *  Loads the autoincluder function to be able to retrieve the 
 *  predefined page options and to be able to include
 *  files based on option arrays
 * 
 */
 
require( ACE_PHP.'function-set-ace-backend.php' );



/*
 * ------------------------------------------------------
 *  Load the options array with manually passed functions
 *  in functions.php for theme or plugin specific scripts
 * ------------------------------------------------------
 */
 
 if(isset($ace_autoload) && is_array($ace_autoload)) ace_backend_load_scripts_by_option($ace_autoload);


/**
 * ------------------------------------------------------
 *  create a new superobject, pass the options name that
 *  should be used to save and retrieve database entries
 * ------------------------------------------------------
 */
 
 $ace = new ace_superobject($ace_base_data);


// ------------------------------------------------------------------------

