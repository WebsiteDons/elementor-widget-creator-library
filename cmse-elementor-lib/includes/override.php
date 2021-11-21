<?php

defined('ABSPATH') || exit('CMSEnergizer.com');

//Image Gallery
/*add_action('elementor/element/image-gallery/section_caption/before_section_end', function($element, $args) 
{
	$element->add_control('title_recolorsdwe',[
	'label' => 'Template' ,
	'type' => \Elementor\Controls_Manager::SELECT,
	'default' => 'red',
	'options' => [
		'red' => 'Red',
		'blue' => 'Blue',
	],
	 // set the section ID and the tab group
	'section' => 'section_gallery',
	'tab' => 'content',
	]);
},10,2);
*/
add_action('elementor/element/after_section_end',function($inst,$sectid,$args) 
{
	//$inst->start_controls_section('section_newting',['label' => 'Ceme Template']);
	 
	$inst->add_control('tmpl_part',[
	'label' => 'Templates In The' ,
	'type' => \Elementor\Controls_Manager::SELECT,
	'default' => 'red',
	'options' => [
		'red' => 'Red',
		'blue' => 'Blue',
	],
	 // set the section ID and the tab group
	'section' => 'section_gallery',
	'tab' => 'content',
	]);
	
	//$inst->end_controls_section();
},10,3);