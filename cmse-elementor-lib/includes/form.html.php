<?php

defined('ABSPATH') || exit('CMSEnergizer.com');

$home ='admin.php?page=cmse-create-elementor-widget';
$pgurl = $home.'&pageaction=';

$doingPost=false;
if( 'cmse-create-elementor-widget' === $_GET['page'] && !empty($_POST) )
	$doingPost = true;

$pgaction = false;
if( isset($_GET['pageaction']) )
	$pgaction = $_GET['pageaction'];

$refer=false;
if( isset($_POST['_wp_http_referer']) )
	$refer = $_POST['_wp_http_referer'];

// get elementor editor panel categories
$wcats = \Elementor\Plugin::instance()->elements_manager->get_categories();
$wcat=[];$sel=[];
foreach($wcats as $id=>$title) {
	$wcat[] = '<option value="'.$id.'">'.$title['title'].'</option>';
	$sel[$id] = $title['title'];
}



// set CSS 
add_action('admin_footer', function() 
{
	$css = '
	<style>
	.headtext {margin-bottom: 30px;}
	.cmseform dl {margin-bottom: 15px; max-width: 320px;}
	.cmseform dt {margin-bottom: 8px; font-weight: 700;}
	.cmseform dd {margin: 0; padding: 0;}
	.cmseform input[type="text"] {width: 100%;}
	.cmseform small {display: block; font-weight: 100;}
	.cmseform h2 span {color: #02b948;}
	.cmseform .flexed {display: flex;}
	.cmseform .flexed .xmlcode {width: 50%; padding-right: 10px;}
	.cmseform .flexed .xmlcode textarea {width: 100%;}
	.cmseform .flexed .wedit {width: 30%;}
	.cmseform .flexed .wlist {width: 20%;}
	.cmseform .flexed .wlist p {margin: 0; padding: 0; padding-right: 10px;}
	.cmseform .flexed .wlist a {
		border: 1px solid #ddd; 
		padding: 4px; 
		display: block; 
		text-decoration: none;
		text-transform: capitalize;
		font-weight: 700;
		background: #fff;
		}
	</style>
	';
	
	echo $css;
});

?>

<h1><?php cmse::_tx('CMSE_FORM_PAGETITLE_OPTIONS_HEAD',1); ?></h1>
<!-- Page Navigation -->
<div class="cmsenav">
	<a href="<?php echo $home; ?>" class="button <?php echo (!$pgaction ? 'button-primary':''); ?>">
	<?php cmse::_tx('CMSE_FORM_PAGENAV_HOME',1); ?>
	</a>
	<a href="<?php echo $pgurl; ?>create-widget" class="button <?php echo ($pgaction == 'create-widget' ? 'button-primary':''); ?>">
	<?php cmse::_tx('CMSE_FORM_PAGENAV_CREATE_WID',1); ?>
	</a>
	<a href="<?php echo $pgurl; ?>edit-widget" class="button <?php echo ($pgaction == 'edit-widget' ? 'button-primary':''); ?>">
	<?php cmse::_tx('CMSE_FORM_PAGENAV_EDIT_WID',1); ?>
	</a>
</div>
<hr />

<form method="post" action="" class="cmseform">

<?php 
## Configs home
if( !$pgaction ) 
{
	if( $doingPost && !empty($_POST['widget_pane_nonce']) && $refer && $refer == '/wp-admin/'.$home ) 
	{
		if( !wp_verify_nonce($_REQUEST['widget_pane_nonce'],'widget_pane') ) {
			die(cmse::_tx('CMSE_NOTICE_FORMFAIL'));
		}else{
			$datafile = CMSETXTDB.'/configs.json';
			
			$data = [
			'widgetprefix' => wp_strip_all_tags($_POST['widget_prefix']),
			'widgetcat' => wp_strip_all_tags($_POST['widget_pane'])
			];
			
			file_put_contents($datafile, json_encode($data));
		}
	}
	?>
	
	<div class="headtext"><?php cmse::_tx('CMSE_FORM_CONFIGPG_README',1); ?></div>
	<dl>
		<dt><?php cmse::_tx('CMSE_FORM_LBL_PANELCAT',1); ?></dt>
		<dd>
			<div>
				<img src="<?php echo esc_url(CMSE_ASSETURL.'/images/panel-categories.jpg'); ?>" />
			</div>
			<input type="text" name="widget_pane" value="<?php echo esc_html(cmse::txtdb('configs','widgetcat','CMSE Widgets')); ?>" placeholder="CMSE Widgets" />
		</dd>
	</dl>
	
	<dl>
		<dt><?php cmse::_tx('CMSE_FORM_LBL_WID_PREFIX',1); ?></dt>
		<dd>
			<div>
				<img src="<?php echo esc_url(CMSE_ASSETURL.'/images/widget-prefix.jpg'); ?>" />
			</div>
			<input type="text" name="widget_prefix" value="<?php echo esc_html(cmse::txtdb('configs','widgetprefix','CMSE')); ?>" placeholder="CMSE" />
		</dd>
	</dl>

	<?php 
	wp_nonce_field('widget_pane','widget_pane_nonce'); 
	submit_button(cmse::_tx('CMSE_FORM_BTN_CONFIGS'));
}



if( $pgaction ) 
{
	## Create new widget
	if( $pgaction == 'create-widget' ) 
	{
		if( $doingPost && !empty($_POST['widget_name_nonce']) && $refer && $refer == '/wp-admin/'.$pgurl.$pgaction ) 
		{
			if( !wp_verify_nonce($_REQUEST['widget_name_nonce'],'widget_name') ) {
				die(cmse::_tx('CMSE_NOTICE_FORMFAIL'));
			}else
			{
				$attr=[];
				foreach($_POST as $k => $wpart) {
					$attr[$k] = strtolower(wp_strip_all_tags($wpart));
				}
				
				if( !empty($attr['widget_name']) ) 
				{
					$dir = preg_replace('#[^a-z]#', '',$attr['widget_name']);
					
					// create folder
					cmse::createFolder(CMSEPATH.'/widgets/'.$dir);
					
					$new = CMSEPATH.'/widgets/'.$dir;
					
					// continue proces if folder was created
					if( file_exists($new) )
					{
						// add files
						cmse::copyFile(CMSEPATH.'/includes/builder', $new,1);
					
						$xml = $new.'/widget_'.$dir.'.xml';
						
						// write values to xml
						$cat = $attr['widget_cat'];
						$icon = preg_replace('#[^a-z\-]#', '',$attr['widget_icon']);
						$keys = preg_replace('#[^a-z\,]#', '',$attr['widget_keywords']);
						$values = '<widget icon="'.$icon.'" cat="'.$cat.'" keywords="'.$keys.'">';
						
						$xmlstr = file_get_contents($xml);
						$edited = preg_replace('#\<widget(.*?)\>#i',$values,$xmlstr);
						file_put_contents($xml,$edited);
						
						// notify success
						if( file_exists($xml) && file_exists($new.'/widget_'.$dir.'.php') && file_exists($new.'/display.php') ) {
							add_action('cmse_notice', function() use($dir) {
								echo sprintf(cmse::_tx('CMSE_NOTICE_FORMSUCCESS',false,'info'),'<strong>'.$dir.'</strong>');
							});
							
							// complete
							cmse::redirect($_POST['_wp_http_referer'],3);
						}
					}
				}
			}
		}

		?>

		<h2><?php cmse::_tx('CMSE_PAGETITLE_CREATE_WID',1); ?></h2>
		<div class="headtext"><?php cmse::_tx('CMSE_FORM_README_CREATE_WID',1); ?></div>
		
		<?php do_action('cmse_notice'); ?>

		<dl>
			<dt><?php cmse::_tx('CMSE_FORM_LBL_WIDGET_NAME',1); ?></dt>
			<dd>
				<input type="text" name="widget_name" value="" placeholder="greatwidget" required="required" />
			</dd>
		</dl>

		<dl>
			<dt><?php cmse::_tx('CMSE_FORM_LBL_WID_CAT',1); ?></dt>
			<dd>
				<select id="catlist" name="widget_cat">
					<?php echo implode('',$wcat); ?>
				</select>
			</dd>
		</dl>

		<dl>
			<dt><?php cmse::_tx('CMSE_FORM_LBL_WID_ICON',1); ?></dt>
			<dd>
				<input type="text" name="widget_icon" value="" placeholder="eicon-play" />
				<?php cmse::_tx('CMSE_FORM_DESC_WID_ICON',1); ?>
			</dd>
		</dl>

		<dl>
			<dt><?php cmse::_tx('CMSE_FORM_LBL_WID_KEYS',1); ?></dt>
			<dd>
				<input type="text" name="widget_keywords" value="" placeholder="gallery,slideshow,mywidgets" />
			</dd>
		</dl>

		<?php 
		wp_nonce_field('widget_name','widget_name_nonce'); 
		submit_button(cmse::_tx('CMSE_FORM_BTN_CREATE_WID')); 
	}
	
	
	
	## Edit Widget
	if( $pgaction == 'edit-widget' ) 
	{
		$wname = (isset($_GET['editdata']) ? $_GET['editdata']:null);
		$wlist=[];
		foreach(cmse::folderlist(CMSEPATH.'/widgets') as $wid) {
			$wlist[] = '<p><a href="'.$pgurl.$pgaction.'&editdata='.$wid.'">'.$wid.'</a></p>';
		}
		
		$sectitle = cmse::_tx('CMSE_SECTITLE_EDIT_WIDGETS').(!empty($wname) ? ' - <span>'.$wname.'</span>':'');
		?>
		
		<h2><?php echo $sectitle; ?></h2>
		
		<div class="flexed">
		
			<div class="wlist">
			<?php echo implode('',$wlist); ?>
			</div>
		
		<?php
		// Edit Form
		if( !empty($wname) ) 
		{
			$xml = CMSEPATH.'/widgets/'.$wname.'/widget_'.$wname.'.xml';
			$val = cmse::widgetEdit($wname);
			$xmlstr = file_get_contents($xml);
			
			// Post Process
			if( $doingPost && !empty($_POST['widget_cat_nonce']) && $refer && $refer == '/wp-admin/'.$pgurl.$pgaction.'&editdata='.$wname ) 
			{
				if( !wp_verify_nonce($_REQUEST['widget_cat_nonce'],'widget_cat') ) {
					die(cmse::_tx('CMSE_NOTICE_FORMFAIL'));
				}else{
					$attr=[];
					foreach($_POST as $k => $wpart) {
						$attr[$k] = strtolower(wp_strip_all_tags($wpart));
					}
					
					$cat = $attr['widget_cat'];
					$icon = preg_replace('#[^a-z\-]#', '',$attr['widget_icon']);
					$keys = preg_replace('#[^a-z\,]#', '',$attr['widget_keywords']);
					$values = '<widget icon="'.$icon.'" cat="'.$cat.'" keywords="'.$keys.'">';
					
					$edited = preg_replace('#\<widget(.*?)\>#i',$values,$xmlstr);
					file_put_contents($xml,$edited);
					
					// completed reload to clear form request
					cmse::redirect($_POST['_wp_http_referer']);
				}
			}
			?>
			<!-- Widget Edit HTML -->
			<div class="wedit">
				<dl>
					<dt><?php cmse::_tx('CMSE_FORM_LBL_WID_CAT',1); ?></dt>
					<dt>current: <?php echo esc_html($sel[$val->cat]); ?></dt>
					<dd>
						<select id="catlist" name="widget_cat">
							<?php echo implode('',$wcat); ?>
						</select>
					</dd>
				</dl>
				
				<dl>
					<dt><?php cmse::_tx('CMSE_FORM_LBL_WID_ICON',1); ?></dt>
					<dd>
						<input type="text" name="widget_icon" value="<?php echo $val->icon; ?>" />
						<?php cmse::_tx('CMSE_FORM_DESC_WID_ICON',1); ?>
					</dd>
				</dl>

				<dl>
					<dt><?php cmse::_tx('CMSE_FORM_LBL_WID_KEYS',1); ?></dt>
					<dd>
						<input type="text" name="widget_keywords" value="<?php echo $val->keywords; ?>" />
					</dd>
				</dl>
				
				<?php
				wp_nonce_field('widget_cat','widget_cat_nonce'); 
				submit_button(cmse::_tx('CMSE_FORM_BTN_EDIT_WID'));
				?>
			</div>
			
		<?php 
		}
		?>
		
		</div>
		
		<?php
	}
}
?>

</form>