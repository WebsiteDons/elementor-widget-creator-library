# About This Class
**This is not a plugin for a general user** It is for basic developers who wish to quickly create widgets specifically for [Elementor](https://elementor.com) without writing complex Elementor form controls PHP coding.

This class which serves as a simplified method to easily and quickly create widgets for WordPress page builder plugin called Elementor. Simply create widget form controls using XML markup instead of writing complex PHP.

---

## Page Menu

* [MU-PLUGINS method](https://github.com/WebsiteDons/elementor-widget-creator-library#method-using-mu-plugins)
* [Distributable plugin method](https://github.com/WebsiteDons/elementor-widget-creator-library#creating-a-distributable-plugin)
* [Creating Widgets](https://github.com/WebsiteDons/elementor-widget-creator-library#creating-widgets)
* [Sample widget](https://github.com/WebsiteDons/elementor-widget-creator-library/tree/main/elementor-widgets/widgets/image)
* [XML Structure](https://github.com/WebsiteDons/elementor-widget-creator-library#xml-structure)
* [XML Attributes](https://github.com/WebsiteDons/elementor-widget-creator-library#xml-markup-attributes)
* [Field Types](https://github.com/WebsiteDons/elementor-widget-creator-library#field-types)
* [Auto widget generator](https://github.com/WebsiteDons/elementor-widget-creator-library/tree/main/copy)
* [Known errors](https://github.com/WebsiteDons/elementor-widget-creator-library#known-errors)

---



## Method using MU-PLUGINS
* Place file `widgetloader.php` and `constants.php` in `wp-content/mu-plugins` to autoload the class.
* Place directory `elementor-widgets` in `wp-content`

If widgets are created for distribution via installable plugin, `widgetloader.php` must be included in the distributed package, then load it via the plugin's pilot file. [See The Plugin Development Guide](https://github.com/WebsiteDons/elementor-widget-creator-library#creating-a-distributable-plugin)

That's it! All ready to use.

### Notes
If the directory paths will be changed from the default structure, edit `constants.php` and change the array values accordingly

```php
$const = (object)[
	// set the prefered prefix which displays on the widget button 
	// and title bar of the widget when in edit view
	'widgetprefix'	=>	'CMSE',
	
	// widgets panel section ID, icon and name
	'panelid'		=>	'cmse-widgetcat',
	'panelcat'		=>	'CMSE Widgets',
	'panelicon'		=>	'fa fa-plug',
	
	// text domain for the language ID
	'textdomain'	=>	'plugin-name',
	
	// paths
	'widgetpath'	=>	WP_CONTENT_DIR.'/elementor-widgets/widgets/',
	'customcontrol'	=>	WP_CONTENT_DIR.'/elementor-widgets/controls',
	
	// if custom separator shapes are in use set path to elementor's separator folder
	// and the path to your source where the shapes are stored
	'copydir'		=>	WP_CONTENT_DIR.'/elementor-widgets/svg',
	'shapesdir'		=>	WP_PLUGIN_DIR.'/elementor/assets/shapes',
	'cssfileurl'	=>	get_bloginfo('url').'/wp-content/elementor-widgets/elementor-alt.css',
	'cssid'			=>	'elementor-alt'
	];
```



## Creating Widgets

Add widgets to `wp-content/elementor-widgets/widgets` If a prefereed location is desired, be sure to set the path in the above noted constants. You only need to add a folder to the widgets folder and the library will create all the files. All you do next is edit the XML to add all the control fields desired and edit display.php to format the data in HTML and all supporting scripts (javascript / css).

Each widget folder will consist of 2 files with exact matching names of the folder and one display ie: 
* image
    * image.php
    * image.xml
    * display.php

 See the included demo files `elementor-widgets/widgets/image`



 ## XML Structure

```xml
<?xml version="1.0" encoding="utf-8"?>
<widget icon="eicon-image" cat="basic">
 
  <fieldset id="animage" tab="tab" note="This is text description of the panel" label="An Image">
   <field 
   type="text" 
   name="thefieldname" 
   label="Image Title" 
   note="Some details about the field" 
   hint="The place holder text" 
   default="A default value" 
   />
   <field 
   type="list" 
   name="thelist" 
   options="{'':'None','choice':'The Choice','newchoice':'New Choice'}" 
   default="choice" 
   label="The List" 
   />
   <field 
   type="textarea" 
   name="thedescription" 
   condition="{'thelist':'newchoice'}" 
   label="Description" 
   labelblock="1" 
   note="Some details about the field" 
   hint="The place holder text" 
   default="A default value" 
   />
  </fieldset>
 
</widget>
```
Elementor icons at https://elementor.github.io/elementor-icons/

| Widget In Group List | Widget In Use |
|-------|-------|
| ![example-xml-field](https://github.com/WebsiteDons/elementor-widget-creator-library/blob/main/newly-added-widget.jpg?raw=true "Example output") | ![example-xml-field](https://github.com/WebsiteDons/elementor-widget-creator-library/blob/main/example-xml-field.jpg?raw=true "Example output") |


### Comparison to PHP method (just the first text field)

To get the same controls result using the PHP methods would be quite complex and prone to errors.

```php
protected function _register_controls() 
{
	$this->start_controls_section('animage',[
	'label'=>'An Image',
	'tab'=>\Elementor\Controls_Manager::TAB_CONTENT
	]);
	
	$this->add_control('thefieldname', [
	'type' => \Elementor\Controls_Manager::TEXT,
	'label' => 'Image Title',
	'description' => 'Some details about the field',
	'placeholder' => 'The place holder text',
	'default' => 'A default value'
	]);
	
	$this->end_controls_section();
}

/* the library removes the need to ever view or edit the class by 
auto generating the controls from interpreted place holder attributes as written in the XML markup
so the above code is written as the following which just loads the static fields() method
and pass the widget name and object
*/
protected function _register_controls() {
	$icon = Cmse_Elementor_Widget::fields($this->get_name(),$this);
		
	return (object)$icon;
}
```



## XML Markup Attributes

[See In Depth Details](https://github.com/WebsiteDons/elementor-widget-creator-library/tree/main/elementor-widgets/widgets/image#xml-attributes)

**Widget attribs**
* icon - required to properly display the widget in the list (see above link for elementor icons)
* cat - required to determine the panel section where the widget will display. 
    * Elementor core panel categories are - basic | pro-elements | general | wordpress | woocommerce-elements
    * Your custom category must be set via constants file where set names are: **PANELID** and **PANELCAT** and **PANELICON**

**Fieldset Attribs**
* id - absolutely required to create unique panel ID
* tab - required to determine the tabbed group //available values -  tab | tabstyle | tabadv
* label - required for the panel title
* note - to add text description immediatedly below the panel handle

**Field Attribs**
* type - the field type
* name - the field name
* label - the field label
* description - text displayed below a field in admininistration
* hint - the placeholder attribute shorthand
* default - a default value set
* labelblock - an instruction to format the field and label vertically instead of default inline
* options - for select fieltype
* args - used in assocition with options if options are from a function
* note - text displayed before a field for various instructions
* min - used for number field
* max - used for number fields
* gtype - used for group controls to define the field type
* gtypes - used for various group controls to add variations
* selectors - used to apply array multiple value to {{WRAPPER}}
* selector - similar to previous but single value

## Field Types
* text
* textarea
* list - standard select field
* list2 - select2 select field with search
* num - number field type
* date - output date picker
* hr - a horizontal ruler for separation
* color - color chooser
* radio - elemetor switcher
* rich - WYSIWYG editor
* img - image/media selector
* hidden - standard hidden field
* url - elemetor ulr function
* choose - elementor choose switch
* gal - elementor gallery
* anim - elementor animation
* icon - elementor icon selector
* code - code editor
* font - font selector
* repeat - repeater fields
* controlgroup - trigger group controls type
* typo - elementor typography group control
* border - border group control
* shadow - box shadow group controls
* shadowtext - text shadow group controls
* cssfilter - css filter group controls

# Creating a distributable plugin

If creating a distributable plugin, do not place the `widgetloader.php` file in mu-plugins. Follow the below structure. Be sure to set the paths in the constants and load the class via file include within the plugin pilot file. EG: `include_once __DIR__.'/includes/widgetloader.php';`

### Widgets folder naming protocol

* Use unique names to avoid conflict with class names EG: **mywidget_mixcloud**
* Names must be **only** lowercase
* Do **not** use special characters except for underscore
* Do **not** use spaces

### Directory structure

* plugin-name
    * includes
        * copy
            * widget.php
            * widget.xml
            * display.php
        * widgetloader.php 
        * constants.php
    * widgets
        * mywidget_image
            * mywidget_image.php
            * mywidget_image.xml
            * display.php
        * mywidget_countdown
            * mywidget_countdown.php
            * mywidget_countdown.xml
            * display.php 
    * languages
        * plugin-name.pot
        * plugin-name-en_EN.po
        * plugin-name-en_EN.mo
    * plugin-name.php
    
### Coding for plugin pilot

`plugin-name.php`

```php
<?php
/**
Plugin Name: Your Elementor Widgets Builder
Plugin URI: https://your-site.com
Description: A library to make it simple to create widgets for Elementor page builder
Version: 1.0.0
Author: You
TextDomain: plugin-domain
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
*/

defined('ABSPATH') || exit('A message to bad guys who try to access this file directly');

//load widgetloader class
require_once __DIR__.'/includes/widgetloader.php';

/*
That's it!
No need to register the plugin if no setting options will be applied. 
Add new Elementor widgets to the widget directory and update the version value of the pilot file at the top where Version: 1.0.1
If distributed by WordPress repository, once the updated package is submitted, all users will be alerted
*/
```



---

# Known Errors

With `WP_DEBUG` enabled, all controls generated by the xml markup and passed through the `foreach()` loop returns a _non fatal_ **notice** that the element name is being redeclared.

`Notice:  Elementor\Controls_Manager::add_control_to_stack was called incorrectly. Cannot redeclare control with same name "panel_gallery"`

To eliminate the notice, the controls output uses the Elementor supplied option of `['overwrite' => true]` . The method `start_controls_section()` does not support the overwrite option so the notice is **suppressed** using `@`

Each widget parent file loads the XML file in `register_controls()` 

```php
protected function _register_controls() {
	$icon = Cmse_Elementor_Widgets::fields($this->get_name(),$this);
		
	return (object)$icon;
}
```



```xml
<fieldset id="panel_gallery" tab="tab" label="Set Images">
</fieldset>
```



```php
public static function fields($xml,$obj)
{
    $formfile = simplexml_load_file($cmse('widgetpath').'/'.$xml.'/'.$xml.'.xml');
	$form = $formfile->attributes();
	$icon = (string)$form->icon;
	$cat = (string)$form->cat;
	// source controls methods from local function
    // 'tab' => \Elementor\Controls_Manager::TAB_CONTENT
	$f = self::ctr();
    
    foreach($formfile->fieldset as $fieldset)
    {
        $fset = $fieldset->attributes();
        $fsetlbl = (string)$fset->label;
        $fsetid = (string)$fset->id;
        $tabtype = (string)$fset->tab;
        $fsetnote = (isset($fset->note) ? '<div class="tabnote">'.(string)$fset->note.'</div>':null);

        @$obj->start_controls_section($fsetid,['label'=>$fsetlbl,'tab'=>$f->$tabtype]);
        foreach($fieldset->field as $field) 
        {
            // control
            $obj->add_control($fv->name,['type'=>$f->$type],['overwrite'=>true]);
        }
        $obj->end_controls_section();
    }
}
```

Elementor method `add_control_to_stack()` at elementor/includes/managers/controls.php : 666

```php
public function add_control_to_stack( Controls_Stack $element, $control_id, $control_data, $options = [] ) {
	$default_options = [
		'overwrite' => false,
		'index' => null,
	];

	$options = array_merge( $default_options, $options );

	$default_args = [
		'type' => self::TEXT,
		'tab' => self::TAB_CONTENT,
	];

	$control_data['name'] = $control_id;

	$control_data = array_merge( $default_args, $control_data );

	$control_type_instance = $this->get_control( $control_data['type'] );

	if ( ! $control_type_instance ) {
		_doing_it_wrong( sprintf( '%1$s::%2$s', __CLASS__, __FUNCTION__ ), sprintf( 'Control type "%s" not found.', esc_html( $control_data['type'] ) ), '1.0.0' );
		return false;
	}

	if ( $control_type_instance instanceof Base_Data_Control ) {
		$control_default_value = $control_type_instance->get_default_value();

		if ( is_array( $control_default_value ) ) {
			$control_data['default'] = isset( $control_data['default'] ) ? array_merge( $control_default_value, $control_data['default'] ) : $control_default_value;
		} else {
			$control_data['default'] = isset( $control_data['default'] ) ? $control_data['default'] : $control_default_value;
		}
	}

	$stack_id = $element->get_unique_name();

	if ( ! $options['overwrite'] && isset( $this->stacks[ $stack_id ]['controls'][ $control_id ] ) ) {
		_doing_it_wrong( sprintf( '%1$s::%2$s', __CLASS__, __FUNCTION__ ), sprintf( 'Cannot redeclare control with same name "%s".', esc_html( $control_id ) ), '1.0.0' );

		return false;
	}

	$tabs = self::get_tabs();

	if ( ! isset( $tabs[ $control_data['tab'] ] ) ) {
		$control_data['tab'] = $default_args['tab'];
	}

	$this->stacks[ $stack_id ]['tabs'][ $control_data['tab'] ] = $tabs[ $control_data['tab'] ];

	$this->stacks[ $stack_id ]['controls'][ $control_id ] = $control_data;

	if ( null !== $options['index'] ) {
		$controls = $this->stacks[ $stack_id ]['controls'];

		$controls_keys = array_keys( $controls );

		array_splice( $controls_keys, $options['index'], 0, $control_id );

		$this->stacks[ $stack_id ]['controls'] = array_merge( array_flip( $controls_keys ), $controls );
	}

	return true;
}
```



# Examples

All the controls were created from XML markup

![example](https://websitedons.net/assets/sample1.jpg?raw=true "Example output")

```xml
<?xml version="1.0" encoding="utf-8"?>
<widget icon="eicon-gallery-grid" cat="basic">

    <fieldset id="panel_gallery" tab="tab" label="Set Images">
      <field 
      type="gal" 
      name="galimg" 
      />
	  <field 
	  type="list" 
	  name="imgsize" 
	  options="{'thumbnail':'Small','medium':'Medium','full':'Fullsize'}" 
	  default="medium" 
	  label="Thumb Size" 
	  />
	  <field 
	  type="num" 
	  name="imgheight" 
	  min="0" 
	  note="Adds a mask to maintain even height" 
	  selectors="{'{{WRAPPER}} .gallery-icon':'height: {{VALUE}}px'}" 
	  label="Image Height" 
	  />
	  <field 
	  type="num" 
	  name="column" 
	  min="1" 
	  max="12" 
	  default="4" 
	  label="Columns" 
	  />
	  <field 
	  type="list" 
	  name="sortby" 
	  options="{'':'Default','ID':'Image ID','title':'Image Name','post_date':'Upload Date','rand':'Random'}" 
	  default="" 
	  label="Sort By" 
	  />
	  <field 
	  type="list" 
	  name="orderdir" 
	  options="{'ASC':'Ascending','DESC':'Descending'}" 
	  default="ASC" 
	  label="Order Direction" 
	  />
	  <field 
	  type="list" 
	  name="linktype" 
	  options="{'file':'Image File','attachment':'Image Page','none':'No Link'}" 
	  default="file" 
	  label="Image Link To" 
	  />
	  <field 
	  type="url" 
	  name="linkimages"  
	  condition="{'linktype':'none'}" 
	  note="This link will append to all images in the gallery" 
	  label="Link Images" 
	  />
	  <field 
	  type="hidden" 
	  name="open_lightbox" 
	  default="yes" 
	  />
	  <field 
	  type="hidden" 
	  name="view" 
	  default="traditional" 
	  />
     </fieldset>
	  
     <!-- STYLE tab group -->
     <fieldset id="panenewstyle" tab="tabstyle" label="Item Styling">
	     <field 
		 type="list" 
		 name="template" 
		 options="{'pol':'Polaroid','kod':'Vintage Kodak','scat':'Scattered','masonry':'Masonry'}" 
		 default="pol" 
		 label="Template" 
		 />
	     <field 
		 type="controlgroup" 
		 gtype="typo" 
		 name="capfont" 
		 selectors="{{WRAPPER}} p.gallery-caption" 
		 label="Caption Font" 
		 />
		 
		 <field type="hr" title="Background" />
		 <field 
		 type="controlgroup" 
		 gtype="bg" 
		 gtypes="classic,gradient" 
		 name="galitembg" 
		 selectors="{{WRAPPER}} .gallery-item .inner" 
		 label="Inner Background" 
		 />
		 
		 <field type="hr" title="Border" />
		 <field 
		 type="controlgroup" 
		 gtype="border" 
		 name="inborder" 
		 selectors="{{WRAPPER}} .gallery-item .inner" 
		 label="Inner Border" 
		 />
		 
		 <field type="hr" title="Corners" />
	     <field 
		 type="slider" 
		 name="radborder" 
		 unit="px,%" 
		 label="Item Container Radius" 
		 labelblock="1" 
		 selectors="{
		 '{{WRAPPER}} .gallery-item .inner':'border-radius: {{SIZE}}{{UNIT}}'
		 }"
		 />
		 <field 
		 type="slider" 
		 name="imgborder" 
		 unit="px,%" 
		 label="Image Radius" 
		 labelblock="1" 
		 selectors="{
		 '{{WRAPPER}} .gallery-item .gallery-icon':'border-radius: {{SIZE}}{{UNIT}}',
		 '{{WRAPPER}} .gallery-item .gallery-icon img':'border-radius: {{SIZE}}{{UNIT}}'
		 }"
		 />
		 
		 <field type="hr" title="Padding" />
	     <field 
		 type="quad" 
		 name="padinner" 
		 unit="px,%" 
		 label="Item Container Padding" 
		 selectors="{
		 '{{WRAPPER}} .gallery-item .inner':'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}'
		 }"
		 />
		 <field 
		 type="quad" 
		 name="margininner" 
		 unit="px,%" 
		 label="Item Container Margin" 
		 selectors="{
		 '{{WRAPPER}} .gallery-item .inner':'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}'
		 }"
		 />
		 <field type="hr" title="Box Shadow" />
		 <field 
		 type="controlgroup" 
		 gtype="shadow" 
		 name="itemshadow" 
		 selectors="{{WRAPPER}} .gallery-item .inner" 
		 label="Item Container Shadow" 
		 />
	</fieldset>
		 
	<fieldset id="panecustomcss" tab="tabstyle" label="Custom CSS">
		<field 
		type="code" 
		name="customcss" 
		/>
	</fieldset>

</widget>

```

