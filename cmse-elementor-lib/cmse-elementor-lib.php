<?php
/**
Plugin Name: CMSE Elementor Widgets Builder
Plugin URI: https://cmsenergizer.com/elementor-widget-builder
Description: A library to make it simple to create widgets for Elementor page builder
Version: 1.0.17
Author: CMSEnergizer.com
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
*/

defined('ABSPATH') || exit('cmsenergizer.com');

define('CMSEVERSION', '1.0.17');
define('CMSEPATH', WP_PLUGIN_DIR.'/cmse-elementor-lib');
define('CMSEINC', CMSEPATH.'/includes');
define('CMSETXTDB', CMSEINC.'/textdb');
define('CMSEURL', plugins_url().'/cmse-elementor-lib');
define('CMSE_ASSETURL', CMSEURL.'/assets');

require_once __DIR__.'/includes/class.cmse.php';
//load widgetloader class
require_once __DIR__.'/includes/widgetloader.php';

// load language 
cmse::lang();



/**
* getting ob_end_flush() error
* this action stops the flush
* need to resolve with proper method
*/
remove_action('shutdown', 'wp_ob_end_flush_all', 1);
add_action('shutdown', function() {
   while(@ob_end_flush());
});