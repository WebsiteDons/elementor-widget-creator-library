<?php

defined('ABSPATH') || exit('CMSEnergizer.com');

require_once __DIR__.'/constants.php';

// load shpaes class
use \Elementor\Shapes;
use \Elementor\Core\Schemes\Color;
use \Elementor\Core\Schemes\Typography;
use \Elementor\Utils;
use \Elementor\Controls_Stack;
use \Elementor\Plugin;


final class Cmse_Elementor_Widgets 
{
	const VERSION = CMSEVERSION;
	const MINIMUM_ELEMENTOR_VERSION = '2.0.0';
	const MINIMUM_PHP_VERSION = '7.0';

	private static $_instance = null;

	public static function instance()
	{
		if( is_null(self::$_instance) ) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}

	public function __construct()
	{
		add_action('plugins_loaded', function()
		{
			if( $this->is_compatible() )
			{
				add_action('elementor/init', function()
				{
					// Load And Register Widgets Detected
					add_action('elementor/widgets/widgets_registered', function() 
					{
						// load global constants variable
						global $cmse;
						
						$mgr = \Elementor\Plugin::instance()->widgets_manager;

						foreach($this->folderlist($cmse('widgetpath')) as $w) 
						{
							//make widget pilot file if not exists
							if( !file_exists($cmse('widgetpath').$w.'/widget_'.$w.'.php') ) {
								$getfile = file_get_contents(__DIR__.'/builder/widget.php');
								$getfile = str_replace('widgetname', 'widget_'.$w, $getfile);
								file_put_contents($cmse('widgetpath').$w.'/widget_'.$w.'.php', $getfile);
							}
							// make XML file if not exists
							if( !file_exists($cmse('widgetpath').$w.'/widget_'.$w.'.xml') ) {
								$getfile = file_get_contents(__DIR__.'/builder/widget.xml');
								$getfile = str_replace('basic', $cmse('panelid'), $getfile);
								file_put_contents($cmse('widgetpath').$w.'/widget_'.$w.'.xml', $getfile);
							}
							// make display file if not exists
							if( !file_exists($cmse('widgetpath').$w.'/display.php') ) {
								$getfile = file_get_contents(__DIR__.'/builder/display.php');
								$getfile = str_replace('widgetname', $cmse('panelid'), $getfile);
								file_put_contents($cmse('widgetpath').$w.'/display.php', $getfile);
							}
							
							require_once($cmse('widgetpath').$w.'/widget_'.$w.'.php');
							$class = '\Cmse_widget_'.$w.'_Widget';
							$mgr->register_widget_type(new $class);
						}
					});
					
					// Add Widget category to the editor slide panels to separate custom widgets
					add_action('elementor/elements/categories_registered', function($cat) 
					{
						global $cmse;
						
						$cat->add_category($cmse('panelid'),[
						'title'=>$cmse('panelcat'),
						'icon'=>$cmse('panelicon'),
						'active'=>false
						]);
					});
					
					
					/* add section separator custom shapes
					upload svg files to elementor/assets/shapes
					see elementor/includes/shapes.php
					*/
					add_filter('elementor/shapes/additional_shapes', function($separator)
					{
						global $cmse;
						$sep=[];
						$elemshapes = WP_PLUGIN_DIR.'/elementor/assets/shapes/';
						
						if( file_exists($cmse('shapesdir')) )
						{
							$shapes = $this->filelist($cmse('shapesdir').'/','svg');
							
							if( !empty($shapes) ) 
							{
								foreach($shapes as $shape)
								{
									/*
									retain a directory somewhere outside of the Elementor directory where the custom 
									shapes will be held for copy. This is due to the method of folder delete when 
									WordPress updates a plugin. All files are deleted
									*/
									if( !file_exists($elemshapes.'/'.$shape) )
										$this->copyFile($cmse('shapesdir').'/'.$shape, $elemshapes.'/'.$shape);

									$shape = str_replace('.svg','',$shape);
									// set selector controls options
									$sep[$shape] = [
									'title'=>ucfirst($shape),
									'has_flip'=>true,
									'has_negative' => true
									];
								}
							}
						}

						$separator = $sep;

						return $separator;
					}); // EOF filter elementor/shapes/additional_shapes


					// add css when in elementor editor for various adjustments if needed
					add_action('elementor/editor/after_enqueue_styles', function() {
						global $cmse;
						wp_register_style($cmse('cssid'), $cmse('cssfileurl'),[], self::VERSION);
						wp_enqueue_style($cmse('cssid'));
					});

				}); // EOF action elementor/init
			} // EOF is_compatible() check
		}); // EOF action plugins_loaded
	} // EOF __construct()



	## Define Elementor core controls with minimal type ID
	// just for simplification
	protected static function ctr()
	{
		$e = new \Elementor\Controls_Manager;
		$v = (object)[
		'tab'=>$e::TAB_CONTENT,
		'tabstyle'=>$e::TAB_STYLE,
		'tabadv'=>$e::TAB_ADVANCED,
		'text'=>$e::TEXT,
		'textarea'=>$e::TEXTAREA,
		'rich'=>$e::WYSIWYG,
		'list'=>$e::SELECT,
		'list2'=>$e::SELECT2,
		'img'=>$e::MEDIA,
		'date'=>$e::DATE_TIME,
		'radio'=>$e::SWITCHER,
		'url'=>$e::URL,
		'num'=>$e::NUMBER,
		'repeat'=>$e::REPEATER,
		'color'=>$e::COLOR,
		'hidden'=>$e::HIDDEN,
		'quad'=>$e::DIMENSIONS,
		'code'=>$e::CODE,
		'font'=>$e::FONT,
		'icon'=>$e::ICONS,
		'anim'=>$e::ANIMATION,
		'animhover'=>$e::HOVER_ANIMATION,
		'slider'=>$e::SLIDER,
		'choose'=>$e::CHOOSE,
		'readme'=>$e::RAW_HTML,
		'gal'=>$e::GALLERY,
		'hr'=>$e::DIVIDER,
		// control groups
		'bg'=>\Elementor\Group_Control_Background::get_type(),
		'border'=>\Elementor\Group_Control_Border::get_type(),
		'shadow'=>\Elementor\Group_Control_Box_Shadow::get_type(),
		'shadowtxt'=>\Elementor\Group_Control_Text_Shadow::get_type(),
		'typo'=>\Elementor\Group_Control_Typography::get_type(),
		'cssfilter'=>\Elementor\Group_Control_Css_Filter::get_type(),
		'imgsize'=>\Elementor\Group_Control_Image_Size::get_type(),
		'rpt' => new \Elementor\Repeater()
		];

		return $v;
	}
	
	

	## Load Fields From XML Markup
	public static function fields($xml,$obj)
	{
		global $cmse;
		$formfile = simplexml_load_file($cmse('widgetpath').'/'.str_replace('widget_','',$xml).'/'.$xml.'.xml');
		$form = $formfile->attributes();
		$icon = (string)$form->icon;
		$cat = (string)$form->cat;
		$keywords = (!empty($form->keywords) ? (string)$form->keywords : $cmse('keywords'));
	
		$f = self::ctr();
		
		foreach($formfile->fieldset as $fieldset)
		{
			$fset = $fieldset->attributes();
			$fsetlbl = (string)$fset->label;
			$fsetid = (string)$fset->id;
			$tabtype = (string)$fset->tab;
			$fsetnote = (isset($fset->note) ? '<div class="tabnote">'.(string)$fset->note.'</div>':null);
	
			@$obj->start_controls_section($fsetid,['label'=>$fsetlbl,'tab'=>$f->$tabtype]);
			if( !empty($fsetnote) ) 
			{
				$obj->add_control($fsetid.'_note',['type'=>$f->readme,'raw'=>$fsetnote],['overwrite'=>true]);
				add_action('elementor/editor/footer', function() use($fsetid) 
				{
					echo '
					<style>
					.elementor-control-'.$fsetid.'_note {
						padding:0 !important; 
						padding-bottom: 8px !important;
					}
					.elementor-control-'.$fsetid.' {
						padding-bottom: 0 !important;
					}
					</style>';
				});
			}

			$i=null;
			foreach($fieldset->field as $field) 
			{
				$i++;
				$att = $field->attributes();
				$fv = self::fieldval($att);
				$type = (string)$att->type;
				$gtype = $fv->gtype;
				
				
				## Do Controls
				// group controls
				if( $type == 'controlgroup' ) 
				{
					self::controlGroup($fv,$type,$gtype,$obj,$f);
				}
				else
				// horizontal line
				if( $type == 'hr' ) {
					self::controlSep($fv,$obj,$f,$i);
				}
				else
				// output HTML or plain text 
				if( $type == 'readme' ) 
				{
					self::controlReadme($fv,$obj,$f,$i);
				}
				else
				// repeat fields
				if( $type == 'repeat' ) 
				{
					$rpt = new \Elementor\Repeater();
					self::repeat($field->repeat, $rpt, $f);
					
					$titlefield = (isset($att->titlefield) ? '{{{'.(string)$att->titlefield.'}}}':null);
					$default = [];
					
					$obj->add_control($fv->name, [
					'type'=>$f->repeat,
					'fields'=>$rpt->get_controls(),
					'title_field'=>$titlefield,
					'show_label'=>$fv->showlabel,
					'label'=>$fv->lbl,
					'prevent_empty'=>false
					],['overwrite'=>true]);
				}
				else
				// standard controls
				{
					self::controlStandard($fv,$type,$gtype,$obj,$f);
				}
			}
			
			$obj->end_controls_section();
		}
		
		return ['icon'=>$icon,'category'=>$cat,'keywords'=>$keywords];
	}
	
	
	
	// Repeater fields process
	protected static function repeat($fields,$obj, $f)
	{
		$i=null;
		foreach($fields as $atts) 
		{
			$i++;
			$att = $atts->attributes();
			$fv = self::fieldval($att);
			$type = (string)$att->type;
			$gtype = $fv->gtype;
			
			if( $type == 'controlgroup' ) 
			{
				self::controlGroup($fv,$type,$gtype,$obj,$f);
			}
			else
			if( $type == 'hr' ) {
				self::controlSep($fv,$obj,$f,$i);
			}
			else
			// output HTML or plain text 
			if( $type == 'readme' ) 
			{
				self::controlReadme($fv,$obj,$f,$i);
			}
			else
			{
				self::controlStandard($fv,$type,$gtype,$obj,$f);
			}
		}
	}

	
	// standard controls printer
	protected static function controlStandard($fv, $type, $gtype, $obj, $f) 
	{
		$obj->add_control(
		$fv->name,
		self::args($fv, $type, $gtype, $obj, $f),
		['overwrite'=>true]
		);
	}
	
	// group controls printer
	protected static function controlGroup($fv, $type, $gtype, $obj, $f) 
	{
		@$obj->add_group_control(
		$f->$gtype,
		self::args($fv, $type, $gtype, $obj, $f),
		['overwrite'=>true]
		);
	}
	
	// print readme field
	protected static function controlReadme($fv, $obj, $f, $i) 
	{
		$obj->add_control('readme-'.(isset($fv->name) ? (string)$fv->name:$i),[
		'type'=>$f->readme,
		'raw'=>(string)$fv->note,
		'content_classes'=>(isset($fv->class) ? (string)$fv->class:null),
		'label'=>(isset($fv->label) ? (string)$fv->label:null)
		],['overwrite'=>true]);
	}
	
	// separator with title
	protected static function controlSep($fv, $obj, $f, $i)
	{
		if( !empty($fv->title) ) 
		{
			$html = '
			<div class="hrblock">
			<h5>
			<span class="line"><hr /></span>
			<span>'.$fv->title.'</span>
			<span class="line"><hr /></span>
			</h5>
			</div>
			';
		}else{
			$html = '<hr />';
		}
		
		$obj->add_control('hr-'.$i,[
		'type'=>$f->readme,
		'raw'=>$html
		],['overwrite'=>true]);
	}
	
	
	// define controls attributes array 
	protected static function args($fv, $type, $gtype, $obj, $f)
	{
		$args = [
		'type'=>$f->$type,
		'options'=>$fv->options,
		'default'=>$fv->def,
		'placeholder'=>$fv->hint,
		'label'=>$fv->lbl,
		'description'=>$fv->note,
		'selectors'=>$fv->selectors,
		'selector'=>$fv->selector,
		'separator'=>$fv->separator,
		'picker_options'=>$fv->config,
		'return_value'=>$fv->valueformat,
		'label_block'=>$fv->lblblock,
		'label_on'=>$fv->labelon,
		'label_off'=>$fv->labeloff,
		'conditions'=>$fv->condition,
		'rows'=>$fv->rows,
		'show_label'=>$fv->showlabel,
		'show_external'=>$fv->extlink,
		'size_units'=>$fv->unit,
		'types' =>$fv->gtypes,
		'isLinked'=>$fv->linkunits
		];
		
		return $args;
	}
	
	// values processor
	protected static function fieldVal($att)
	{
		$def=null;
		if( isset($att->default) ) 
		{
			if( strstr((string)$att->default,'{') ) 
			{
				$def = self::stringToArray((string)$att->default);
			}else{
				$def = (string)$att->default;
			}
		}
		
		$options='';
		if( !empty($att->options) || ($att->type == 'choose' && empty($att->options)) ) 
		{
			$options = self::options((string)$att->options,(string)$att->type);
		}
		
		//selectors
		$selectors=null;$selector=null;
		if( isset($att->selectors) ) {
			if( strstr((string)$att->selectors,':') ) {
				$selectors = self::stringToArray((string)$att->selectors);
			}else{
				$selector = (string)$att->selectors;
			}
		}
		
		//configs
		$config=[];
		if( !empty($fv->configs) ) {
			$parts = explode(',',$fv->configs);
			foreach($parts as $part) {
				list($configindex, $configval) = explode('|',$part);
				$config[$configindex] = $configval;
			}
		}
		
		// condition handler
		$condition=null;
		if( isset($att->condition) )
		{
			$condition = self::condition((string)$att->condition);
		}
		
		// array of field types that default to label_block
		$blockdef = ['url','code','rich','textarea','text','img','icon','gal','quad'];
			
		$vals = (object)[
		'type'=> (string)$att->type,
		'name'=> (string)$att->name,
		'lbl'=> (isset($att->label) ? (string)$att->label:null),
		'hint'=> (isset($att->hint) ? (string)$att->hint:null),
		'gtype'=> (isset($att->gtype) ? (string)$att->gtype:null),
		'gtypes'=> (isset($att->gtypes) ? explode(',',(string)$att->gtypes):null),
		'note'=> (isset($att->note) ? (string)$att->note:null),
		'separator'=> (isset($att->line) ? (string)$att->line:null),
		'configs'=> (isset($att->configs) ? (string)$att->configs:null),
		'valueformat'=> (isset($att->valueformat) ? (string)$att->valueformat:1),
		'lblblock'=> (isset($att->labelblock) || in_array($att->type,$blockdef) ? true:null),
		'labelon'=> (isset($att->labelon) ? (string)$att->labelon:'Yes'),
		'labeloff'=> (isset($att->labeloff) ? (string)$att->labeloff:'No'),
		'rows'=> (isset($att->rows) ? (string)$att->rows:null),
		'showlabel'=> (isset($att->showlabel) ? false:true),
		'extlink'=> (isset($att->extlink) ? false:true),
		'options'=>$options,
		'selectors'=>$selectors,
		'selector'=>$selector,
		'config'=>$config,
		'def'=>$def,
		'condition'=>$condition,
		'unit'=>(isset($att->unit) ? explode(',',(string)$att->unit):null),
		'linkunits'=>(isset($att->linkunits) ? true:false),
		'title'=>(!empty($att->title) ? (string)$att->title:null)
		];
		
		return $vals;
	}
	
	// string condition="{'texton':'1'}"
	// converted array([texton]=>1)
	protected static function stringToArray($val) 
	{
		$arr = str_replace('\'','"',$val);
		$value = (array)json_decode($arr);
		
		return $value;
	}
	
	protected static function condition($condition)
	{
		$arr = self::stringToArray($condition);
		$field = array_keys($arr);
		$check = array_values($arr);
		
		$cond=[];$conditions=[];
		if( isset($check[0]) && isset($field[0]) ) 
		{
			$checks = explode('|',(string)$check[0]);
			foreach($checks as $chk) {
				$cond[] = ['name'=>$field[0],'value'=>$chk];
			}
		}
		
		$conditions['terms'][] = ['relation'=>'or','terms'=>$cond];
		
		return $conditions;
	}
	
	protected static function options($vals) 
	{
		$arr = self::stringToArray($vals);
		$options=[];$grp=[]; $out=null;
		foreach($arr as $k=>$v) 
		{
			// if array is compiled by external function
			if( strstr($vals,'::') ) {
				if( count($arr) > 1 ) {
					$grp[] = call_user_func($k,$v);
				}else{
					$options = call_user_func($k,$v);
				}
			}else
			// when alignment choose is set
			if( strstr($vals, '|') ) {
				list($title,$icon) = explode('|',$v);
				$options[$k] = ['title'=>$title,'icon'=>$icon];
			}
			else{
				$options[$k] = $v;
			}
		}
		
		if( !empty($grp) ) {
			$out = array_merge(...$grp);
		}else{
			$out = $options;
		}
		
		return $out;
	}
	
	
	

	## File Scanner
	protected function filelist($path, $filter=null, $getpath=false)
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
	protected function folderlist($path, $getpath=false)
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
	
	## File Copy
	protected function copyFile($src, $dest, $overwrite=true) 
	{
		if( $overwrite == false ) {
			if( file_exists($dest) )
			return;
		}
		
		copy($src, $dest);
	}
	
	
	## Notices and compatibility check
	public function is_compatible() 
	{
		// Check if Elementor installed and activated
		if( !did_action('elementor/loaded') ) 
		{
			add_action('admin_notices', function() 
			{
				if( isset($_GET['activate']) ) 
					unset($_GET['activate']);

				$message = sprintf('%1$s requires %2$s .',
				'<strong>This MU-PLUGIN extension</strong>','<strong><a href="'.admin_url().'plugin-install.php?s=elementor&tab=search&type=term">Elementor plugin installed and activated</a></strong>'
				);

				printf('<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', $message);
			});

			return false;
		}

		// Check for required Elementor version
		if( !version_compare(ELEMENTOR_VERSION, self::MINIMUM_ELEMENTOR_VERSION, '>=') ) 
		{
			add_action('admin_notices', function() 
			{
				if( isset($_GET['activate']) ) 
					unset($_GET['activate']);

				$message = sprintf(
				'"%1$s" requires "%2$s" version %3$s or greater.',
				'<strong>Elementor Test Extension</strong>',
				'<strong>Elementor</strong>',
				self::MINIMUM_ELEMENTOR_VERSION
				);

				printf('<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', $message);
			});

			return false;
		}

		// Check for required PHP version
		if( version_compare(PHP_VERSION, self::MINIMUM_PHP_VERSION, '<') ) 
		{
			add_action('admin_notices', function()
			{
				if( isset($_GET['activate']) ) 
					unset($_GET['activate']);

				$message = sprintf(
				'"%1$s" requires "%2$s" version %3$s or greater.',
				'<strong>Elementor Test Extension</strong>',
				'<strong>PHP</strong>',
				self::MINIMUM_PHP_VERSION
				);

				printf('<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', $message);
			});

			return false;
		}

		return true;
	}

}

// initialize
Cmse_Elementor_Widgets::instance();
