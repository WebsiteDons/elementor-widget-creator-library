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
	// and title bar of the widget when in edit view
	'widgetprefix'	=>	'CMSE',
	/* 
	keywords are used to find widgets via Elementor's editor search
	 separate each word by comma
	 set keywords in each widget's xml and separate each word by comma
	 example: <widget icon="eicon-edit" cat="basic" keywords="slideshow,kenburns,woocommerce">
	 a setting here is purely for default if preferred
	*/
	'keywords'		=> '',
	
	// widgets panel section ID, icon and name. 
	// Icon is default applied to each widget in case none is set via the xml file
	'panelid'		=>	'cmse-widgetcat',
	'panelcat'		=>	'CMSE Widgets',
	'panelicon'		=>	'fa fa-plug',
	
	// text domain for the language ID
	'textdomain'	=>	'plugin-name',
	
	// paths
	'widgetpath'	=>	WP_PLUGIN_DIR.'/cmse-elementor-lib/widgets/',
	'customcontrol'	=>	WP_PLUGIN_DIR.'/cmse-elementor-lib/includes/controls',
	
	// if custom separator shapes are in use set path to elementor's separator folder
	// and the path to your source where the shapes are stored
	'copydir'		=>	WP_PLUGIN_DIR.'/cmse-elementor-lib/assets/svg',
	'shapesdir'		=>	WP_PLUGIN_DIR.'/elementor/assets/shapes',
	'cssfileurl'	=>	get_bloginfo('url').'/wp-content/plugins/cmse-elementor-lib/assets/elementor-editor.css',
	'cssid'			=>	'cmse-elementor-editor'
	];
	
	return $const->$query;
};
