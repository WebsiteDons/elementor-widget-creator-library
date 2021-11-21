<?php
defined('ABSPATH') || exit('CMSEnergizer.com');

/*
Elementor Widget: cmse-widgetcat
Developer Guide: https://github.com/WebsiteDons/elementor-widget-creator-library
*/

$id = implode(',',wp_list_pluck($v->galimg, 'id'));

add_filter('wp_get_attachment_link', [$this, 'add_lightbox_data_to_image_link'], 10, 2);

echo $this->gallery([
'ids'=>$id,
'size'=>$v->imgsize,
'columns'=>$v->column,
'orderby'=>$v->sortby,
'order'=>$v->orderdir,
'link'=>$v->linktype,
'itemtag'=>'div',
'icontag'=>'span',
'captiontag'=>'p',
'showcaption'=>$v->showcaption
]);

remove_filter('wp_get_attachment_link', [$this, 'add_lightbox_data_to_image_link'], 10, 2);

?>
<style>
.gallery .gallery-item .inner {
	padding: 10px;
	margin: 5px;
}
.gallery .imgwrap {
	overflow: hidden;
	}
.gallery .gallery-item .gallery-icon {
	display: block;
	overflow: hidden;
	padding: 0;
	margin: 0;
}
.gallery img {height: auto !important;}

@media (max-width:680px) {
	.gallery .gallery-item {
		float: none !important;
		width: auto !important;
	}
}
</style>
