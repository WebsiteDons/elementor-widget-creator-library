<?php

defined('ABSPATH') || exit('CMSEnergizer.com');

/*
Edit the array values as needed. paths, ID, names etc
DO NOT change the key names else all will break
Format: keyname => value
*/

global $cmse;

$cmse = function($query)
{
	$const = (object)[
	// set the prefered prefix which displays on the widget button
	// can be empty. ideal if widgets will be mixed in with another category
	// and title bar of the widget when in edit view
	'widgetprefix'	=>	'CMSE',
	
	// widgets panel section ID, icon and name
	'panelid'	=>	'cmse-widgetcat',
	'panelcat'	=>	'CMSE Widgets',
	'panelicon'	=>	'fa fa-plug', // a default icon
	
	// text domain for the language ID
	'textdomain'	=>	'plugin-name',
	
	// paths
	'widgetpath'	=>	WP_CONTENT_DIR.'/elementor-widgets/widgets/',
	// optional to locate any custom controls 
	'customcontrol'	=>	WP_CONTENT_DIR.'/elementor-widgets/controls', 
	
	// if custom separator shapes are in use set path to elementor's separator folder
	// and the path to your source where the shapes are stored
	'copydir'	=>	WP_CONTENT_DIR.'/elementor-widgets/svg',
	'shapesdir'	=>	WP_PLUGIN_DIR.'/elementor/assets/shapes',
	
	// not a requirement but will serve to style or adjust widget elements in the editor
	'cssfileurl'	=>	get_bloginfo('url').'/wp-content/elementor-widgets/elementor-alt.css',
	'cssid'		=>	'elementor-alt'
	];
	
	return $const->$query;
}; 
