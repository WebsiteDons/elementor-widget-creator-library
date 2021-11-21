<?php

defined('ABSPATH') || exit('CMSEnergizer.com');

class cmse 
{
	function __construct() {
	}
	
	public static function debug($var) {
		$out = print_r($var, true);
		echo '<pre class="debug">'.$out.'</pre>';
	}

	public static function _tx($string, $print=false, $notice=null)
	{
		$start= $end='';
		if( !empty($notice) ) {
			$start = '<div class="notice notice-'.$notice.' is-dismissible" style="margin: 0;padding:8px;">';
			$end = '</div>';
		}
		if( !defined($string) ) {
			$text = esc_html((string)$string);
		}else{
			$text = constant($string);
		}
		
		if( $print ) {
			echo $start.$text.$end;
		}else{
			return $start.$text.$end;
		}
	}

	public static function lang() 
	{
		$lang = parse_ini_file(CMSEPATH.'/languages/en_GB.ini');
		if( file_exists(CMSEPATH.'/languages/override/override.ini') ) {
			$override = parse_ini_file(CMSEPATH.'/languages/override/override.ini');
			if( is_array($override) )
				$lang = array_merge($lang,$override);
		}
		
		foreach($lang as $key => $val) {
			if( !defined($key) ) 
			define(strtoupper($key), $val);
		}
	}
	
	public static function form()
	{
		
	}
	
	public static function createFolder($dir,$overwrite=false) 
	{
		$dir = wp_strip_all_tags($dir);
		if( file_exists($dir) && is_dir($dir) && !$overwrite ) {
			return;
		}else{
			mkdir($dir);
		}
	}
	
	## File Copy
	public static function copyFile($src, $dest, $recurse=false, $overwrite=true) 
	{
		if( !$overwrite ) {
			if( file_exists($dest) )
			return;
		}
		
		if( $recurse ) {
			foreach(self::filelist($src,null,true) as $copy) 
			{
				$fname = explode('/',$copy);
				$fname = end($fname);
				$dirname = explode('/',$dest);
				$dirname = end($dirname);
				
				if( 'display.php' === $fname ) {
				}else{
					$fname = str_replace(substr($fname,0,-4), 'widget_'.$dirname, $fname);
				}
				
				copy($copy, $dest.'/'.$fname);
			}
		}else{
			copy($src, $dest);
		}
	}
	
	## File Scanner
	public static function filelist($path, $filter=null, $getpath=false)
	{
		$files = new \DirectoryIterator($path);
		$filelist=[];
		foreach($files as $file) 
		{
			if( $file->isFile() && !$file->isDot() ) 
			{
				// include only files in $filter 
				// methods: 'css' or 'css|txt' or starting with '^cat' or ending with '$er'
				if( !empty($filter) && !preg_match(chr(1).$filter.chr(1), $file) ) {
					continue;
				}

				$filelist[] = ($getpath == true ? $file->getPath().'/'.$file->getFilename() : $file->getFilename());
			}
		}

		return $filelist;
	}
	
	## Folder Scanner
	public static function folderlist($path, $getpath=false)
	{
		$topdir = new \DirectoryIterator($path);
		$dirs=[];
		foreach($topdir as $dir) {
			if( $dir->isDir() && !$dir->isDot() ) {
				$dirs[] = ($getpath == true ? $dir->getPath().'/'.$dir->getFilename() : $dir->getFilename());
			}
		}

		return $dirs;
	}
	
	public static function redirect($url, $delay=0, $method = 302) {
		if( !headers_sent() ) {
			return header('Location: '.$url, true, $method);
			exit();
		}else{
			echo '<meta http-equiv="refresh" content="'.(int)$delay.'; URL='.$url.'">';
		}
	}
	
	public static function printOption($key) {
		self::debug(get_option($key));
	}
	
	public static function txtDB($datafile, $query, $default=null)
	{
		if( !file_exists(CMSETXTDB.'/'.$datafile.'.json') ) {
			return;
		}
		
		$data = file_get_contents(CMSETXTDB.'/'.$datafile.'.json');
		if( empty($data) ) {
			return;
		}
		
		$data = json_decode($data);
		if( is_object($data) ) {
			$val = (isset($data->$query) ? $data->$query:$default);
		}else{
			$val = '';
		}
		
		return $val;
	}
	
	public static function widgetEdit($wname) 
	{
		$datafile = simplexml_load_file(CMSEPATH.'/widgets/'.$wname.'/widget_'.$wname.'.xml');
		$data = $datafile->attributes();
		
		$val = (object)[
		'icon' => (string)$data->icon,
		'cat' => (string)$data->cat,
		'keywords' => (string)$data->keywords
		];
		
		return $val;
	}
}


add_action('admin_menu', function()
{
	global $menu, $submenu;
	
	add_menu_page('','CMSE Widgets','manage_options','cmse-create-elementor-widget',function() {
		include 'form.html.php';
	},'dashicons-welcome-widgets-menus',99); 
});
// remove feed value
update_option('elementor_remote_info_feed_data', []);