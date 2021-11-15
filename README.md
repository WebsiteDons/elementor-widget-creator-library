# About This Class
**This is not a plugin for a general user** It is for basic developers who wish to quickly create widgets specifically for [Elementor](https://elementor.com) without writing complex Elementor form controls PHP coding.

This class which serves as a simplified method to easily and quickly create widgets for WordPress page builder plugin called Elementor. Simply create widget form controls using XML markup instead of writing complex PHP.

---

## Page Menu

* MU-PLUGINS method
* Distributable plugin method
* Creating Widgets
* XML Structure
* XML Attributes
* Field Types
* Known errors

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
Plugin Name: My Great Plugin Name
Plugin URI: https://mysite.com
Description: A package of great Elementor widgets.
Version: 1.0.0
Author: Me
License:     GPLv2 or later
License URI: http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
Requires at least: 4.9
Requires PHP: 5.2.6
*/

defined('ABSPATH') || exit('A message to bad guys who try to access this file directly');

//load widgetloader class
include_once __DIR__.'/includes/widgetloader.php';

/*
That's it!
No need to register the plugin if no setting options will be applied. 
Add new Elementor widgets to the widget directory and update the version value of the pilot file at the top where Version: 1.0.1
If distributed by WordPress repository, once the updated package is submitted, all users will be alerted
*/
```



---

# Known Errors

With `WP_DEBUG` enabled, all controls generated by the xml markup and passed through the `foreach()` loop returns a _non fatal_ error **notice** that the element name is being redeclared.

`Notice:  Elementor\Controls_Manager::add_control_to_stack was called incorrectly. Cannot redeclare control with same name "panel_gallery"`

To eliminate the errors, the controls output uses the Elementor supplied option of `['overwrite' => true]` . The method `start_controls_section()` does not support the overwrite option so the notice is **suppressed** using `@`

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

        $obj->start_controls_section($fsetid,['label'=>$fsetlbl,'tab'=>$f->$tabtype]);
        foreach($fieldset->field as $field) 
        {
            // control
        }
        $obj->end_controls_section();
    }
}
```

Elementor method `add_control_to_stack()` at elementor/includes/managers/controls.php

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

