<?php
defined('ABSPATH') || exit('CMSEnergizer.com');

/*
Elementor widget: gallery
*/

class Cmse_widget_gallery_Widget extends \Elementor\Widget_Base
{
	public function get_name() {
		$name = str_replace(['Cmse_','_Widget'],'',__CLASS__);
		return strtolower($name);
	}
	
	public function get_title() {
		global $cmse;
		return $cmse('widgetprefix').' '.str_replace('widget_','',$this->get_name());
	}
	
	public function get_icon() {
		return $this->_register_controls()->icon;
	}
	
	public function get_categories() {
		return [$this->_register_controls()->category];
	}
	
	public function get_keywords() {
		return [$this->_register_controls()->keywords];
	}
	
	protected function _register_controls() {
		$icon = Cmse_Elementor_Widgets::fields($this->get_name(),$this);
		
		return (object)$icon;
	}
	
	// front end output
	protected function render() {
		$v = (object)$this->get_settings_for_display();
		if( file_exists(__DIR__.'/display.php') ) {
			include __DIR__.'/display.php';
		}else{
			echo 'The display file is missing. Check the widget folder for file display.php at <pre>'.__DIR__.'</pre>';
		}
	}
	
	protected function content_template() {
	}
	
	
	
	// wordpress gallery 
	public static function gallery($attr) 
	{
		$post = get_post();
	 
		static $instance = 0;
		$instance++;
	 
		if( !empty($attr['ids']) ) {
			if( empty($attr['orderby']) ) {
				$attr['orderby'] = 'post__in';
			}
			$attr['include'] = $attr['ids'];
		}

		$output = apply_filters('post_gallery', '', $attr, $instance);
	 
		if( !empty($output) ) {
			return $output;
		}
	 
		$html5 = current_theme_supports('html5', 'gallery');
		$atts  = shortcode_atts([
				'order'      => 'ASC',
				'orderby'    => 'menu_order ID',
				'id'         => $post ? $post->ID : 0,
				'itemtag'    => $html5 ? 'figure' : 'dl',
				'icontag'    => $html5 ? 'div' : 'dt',
				'captiontag' => $html5 ? 'figcaption' : 'dd',
				'showcaption'=>1,
				'columns'    => 3,
				'size'       => 'thumbnail',
				'include'    => '',
				'exclude'    => '',
				'link'       => '',
			],$attr,'gallery');
	 
		$id = (int)$atts['id'];
	 
		if( !empty($atts['include']) ) 
		{
			$_attachments = get_posts([
					'include'        => $atts['include'],
					'post_status'    => 'inherit',
					'post_type'      => 'attachment',
					'post_mime_type' => 'image',
					'order'          => $atts['order'],
					'orderby'        => $atts['orderby'],
				]);
	 
			$attachments = [];
			foreach($_attachments as $key => $val) {
				$attachments[$val->ID] = $_attachments[$key];
			}
		}elseif( !empty($atts['exclude']) ) 
		{
			$attachments = get_children([
					'post_parent'    => $id,
					'exclude'        => $atts['exclude'],
					'post_status'    => 'inherit',
					'post_type'      => 'attachment',
					'post_mime_type' => 'image',
					'order'          => $atts['order'],
					'orderby'        => $atts['orderby'],
				]);
		}else{
			$attachments = get_children([
					'post_parent'    => $id,
					'post_status'    => 'inherit',
					'post_type'      => 'attachment',
					'post_mime_type' => 'image',
					'order'          => $atts['order'],
					'orderby'        => $atts['orderby'],
				]);
		}
	 
		if( empty($attachments) ) {
			return '';
		}
	 
		if( is_feed() ) 
		{
			$output = "\n";
			foreach($attachments as $att_id => $attachment) 
			{
				if( !empty($atts['link']) ) {
					if ( 'none' === $atts['link'] ) {
						$output .= wp_get_attachment_image( $att_id, $atts['size'], false, $attr );
					} else {
						$output .= wp_get_attachment_link( $att_id, $atts['size'], false );
					}
				} else {
					$output .= wp_get_attachment_link( $att_id, $atts['size'], true );
				}
				$output .= "\n";
			}
			return $output;
		}
	 
		$itemtag    = tag_escape($atts['itemtag']);
		$captiontag = tag_escape($atts['captiontag']);
		$showcaption = tag_escape($atts['showcaption']);
		$icontag    = tag_escape($atts['icontag']);
		$valid_tags = wp_kses_allowed_html('post');
		
		if( !isset($valid_tags[$itemtag]) ) {
			$itemtag = 'dl';
		}
		if( !isset($valid_tags[$captiontag]) ) {
			$captiontag = 'dd';
		}
		if( !isset($valid_tags[$icontag]) ) {
			$icontag = 'dt';
		}
	 
		$columns   = (int) $atts['columns'];
		$itemwidth = $columns > 0 ? floor( 100 / $columns ) : 100;
		$float     = is_rtl() ? 'right' : 'left';
	 
		$selector = "gallery-{$instance}";
	 
		$gallery_style = '';
	 
		if( apply_filters('use_default_gallery_style', !$html5) ) 
		{
			$type_attr = current_theme_supports('html5', 'style') ? '' : ' type="text/css"';
	 
			$gallery_style = "
			<style{$type_attr}>
				#{$selector} {
					margin: auto;
				}
				#{$selector} .gallery-item {
					float: {$float};
					margin-top: 10px;
					text-align: center;
					width: {$itemwidth}%;
				}
				#{$selector} img {
					border: 2px solid #cfcfcf;
				}
				#{$selector} .gallery-caption {
					margin-left: 0;
				}
			</style>\n\t\t";
		}
	 
		$size_class  = sanitize_html_class( is_array( $atts['size'] ) ? implode( 'x', $atts['size'] ) : $atts['size'] );
		$gallery_div = "<div id='$selector' class='gallery galleryid-{$id} gallery-columns-{$columns} gallery-size-{$size_class}'>";
	 
		$output = apply_filters( 'gallery_style', $gallery_style . $gallery_div );
	 
		$i = 0;
	 
		foreach($attachments as $id => $attachment) 
		{
			$attr = (trim($attachment->post_excerpt)) ? array('aria-describedby' => "$selector-$id") : '';
	 
			if( !empty($atts['link']) && 'file' === $atts['link'] ) {
				$image_output = wp_get_attachment_link( $id, $atts['size'], false, false, false, $attr );
			} elseif ( ! empty( $atts['link'] ) && 'none' === $atts['link'] ) {
				$image_output = wp_get_attachment_image( $id, $atts['size'], false, $attr );
			} else {
				$image_output = wp_get_attachment_link( $id, $atts['size'], true, false, false, $attr );
			}
	 
			$image_meta = wp_get_attachment_metadata($id);
	 
			$orientation = '';
	 
			if( isset($image_meta['height'], $image_meta['width']) ) {
				$orientation = ($image_meta['height'] > $image_meta['width']) ? 'portrait' : 'landscape';
			}
	 
			$output .= "<{$itemtag} class='gallery-item'>";
			$output .= '<div class="inner">';
			$output .= "
				<div class='imgwrap'>
				<{$icontag} class='gallery-icon {$orientation}'>
					$image_output
				</{$icontag}>
				</div>";
	 
			if( $captiontag && trim($attachment->post_excerpt) && $showcaption ) {
				$output .= "
					<{$captiontag} class='wp-caption-text gallery-caption' id='$selector-$id'>
					" . wptexturize( $attachment->post_excerpt ) . "
					</{$captiontag}>";
			}
			
			$output .= '</div>';
			$output .= "</{$itemtag}>";
	 
			if ( ! $html5 && $columns > 0 && 0 === ++$i % $columns ) {
				$output .= '<br style="clear: both" />';
			}
		}
	 
		if( !$html5 && $columns > 0 && 0 !== $i % $columns ) {
			$output .= "
				<br style='clear: both' />";
		}
	 
		$output .= "
			</div>\n";
	 
		return $output;
	}
}