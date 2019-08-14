<!DOCTYPE html>
<!--[if lt IE 7 ]><html lang="en"  class="ie ie6 lte9 lte8 lte7 no-js"> <![endif]-->
<!--[if IE 7 ]><html lang="en"  class="ie ie7 lte9 lte8 lte7 no-js"> <![endif]-->
<!--[if IE 8 ]><html lang="en"  class="ie ie8 lte9 lte8 no-js"> <![endif]-->
<!--[if IE 9 ]><html lang="en"  class="ie ie9 lte9 no-js"> <![endif]-->
<!--[if (gt IE 9)|!(IE)]><!--><html lang="en" class="notie no-js"> <!--<![endif]-->
<head>
<meta charset="UTF-8">

<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=5,viewport-fit=cover">

<?php echo auto_discovery_link_tags(); ?>

<?php
$title = (isset($title)) ? $title : null;
$item = (isset($item)) ? $item : null;
$tour = (isset($tour)) ? $tour : null;
$file = (isset($file)) ? $file : null;
?>
    
<title><?php echo mh_seo_pagetitle($title,$item); ?></title>
<meta name="description" content="<?php echo mh_seo_pagedesc($item,$tour,$file); ?>" />

<!-- FB Open Graph stuff -->
<meta property="og:title" content="<?php echo mh_seo_pagetitle($title,$item); ?>"/>
<meta property="og:image" content="<?php echo mh_seo_pageimg($item,$file);?>"/>
<meta property="og:site_name" content="<?php echo option('site_title');?>"/>
<meta property="og:description" content="<?php echo mh_seo_pagedesc($item,$tour,$file); ?>"/>

<!-- Twitter Card stuff-->
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="<?php echo mh_seo_pagetitle($title,$item); ?>">
<meta name="twitter:description" content="<?php echo mh_seo_pagedesc($item,$tour,$file); ?>">
<meta name="twitter:image" content="<?php echo mh_seo_pageimg($item,$file);?>">
<?php echo ($twitter=get_theme_option('twitter_username')) ?  '<meta name="twitter:site" content="@'.$twitter.'"> ' : '';?> 

<!-- Apple Stuff -->
<link rel="apple-touch-icon-precomposed" href="<?php echo mh_apple_icon_logo_url();?>"/>
<?php echo mh_ios_smart_banner(); ?>

<!-- Windows stuff -->
<meta name="msapplication-TileColor" content="#ffffff"/>
<meta name="msapplication-TileImage" content="<?php echo mh_apple_icon_logo_url();?>"/>

<!-- Icon -->
<link rel="shortcut icon" href="<?php echo ($favicon=get_theme_option('favicon')) ? WEB_ROOT.'/files/theme_uploads/'.$favicon : img('favicon.ico');?>"/>
<link rel="icon" href="<?php echo mh_apple_icon_logo_url(); ?>"/> 
<link rel='mask-icon' href='<?php echo img('favicon.svg')?>' color='#1EAEDB'> <!-- Safari -->

<!-- Plugin Stuff -->
<?php fire_plugin_hook('public_head',array('view'=>$this)); ?>

<!-- Fonts -->
<?php echo mh_web_font_loader();?>

<!-- Assets -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
<script>
	/*!
	loadJS: load a JS file asynchronously. 
	[c]2014 @scottjehl, Filament Group, Inc. (Based on http://goo.gl/REQGQ by Paul Irish). 
	Licensed MIT 
	*/
	(function(w){var loadJS=function(src,cb,ordered){"use strict";var tmp;var ref=w.document.getElementsByTagName("script")[0];var script=w.document.createElement("script");if(typeof(cb)==='boolean'){tmp=ordered;ordered=cb;cb=tmp;}
	script.src=src;script.async=!ordered;ref.parentNode.insertBefore(script,ref);if(cb&&typeof(cb)==="function"){script.onload=cb;}
	return script;};if(typeof module!=="undefined"){module.exports=loadJS;}
	else{w.loadJS=loadJS;}}(typeof global!=="undefined"?global:this));
	
	/*!
	loadCSS: load a CSS file asynchronously.
	[c]2014 @scottjehl, Filament Group, Inc.
	Licensed MIT
	*/
	function loadCSS(href,before,media){"use strict";var ss=window.document.createElement("link");var ref=before||window.document.getElementsByTagName("script")[0];var sheets=window.document.styleSheets;ss.rel="stylesheet";ss.href=href;ss.media="only x";ref.parentNode.insertBefore(ss,ref);function toggleMedia(){var defined;for(var i=0;i<sheets.length;i++){if(sheets[i].href&&sheets[i].href.indexOf(href)>-1){defined=true;}}
	if(defined){ss.media=media||"all";}
	else{setTimeout(toggleMedia);}}
	toggleMedia();return ss;}
</script>
<?php 
//queue_css_file('font-awesome/css/font-awesome.min','all',false,'fonts');
echo head_css(); 
echo mh_theme_css();
echo head_js(false); 
?>

<script>
	// Async CSS 	
	loadCSS('<?php echo src('font-awesome/css/font-awesome.min.css','fonts');?>'); // font awesome css
	loadCSS('<?php echo src('jquery.mmenu/mmenu.css','javascripts');?>'); // mmenu css
	loadCSS('<?php echo src('photoswipe/dist/photoswipe.all.min.css','javascripts');?>'); // photoswipe css
	// Async JS 
	loadJS('<?php echo src('global.js','javascripts');?>'); // global.js
	<?php if( 0 === strpos(current_url(), '/items/show') ):?>
		loadJS('<?php echo src('items-show.js','javascripts');?>'); // items-show.js
	<?php endif;?>	
</script>

<!-- Custom CSS via theme config -->
<?php echo mh_configured_css();?>

<!-- Theme Display Settings -->
<?php
$bodyid = isset($bodyid) ? $bodyid : 'default';
$bodyclass = isset($bodyclass) ? $bodyclass.' curatescape' : 'default curatescape';
$bodyStyle= (get_theme_option('bg_img')) ? 'background-image: url('.mh_bg_url().')' : 'background: linear-gradient(to bottom, rgba(255,255,255,1) 0%,rgba(250,250,250,1) 50%,rgba(234,234,234,1) 100%);background-attachment:fixed;';
?>

</head>
<body id="<?php echo $bodyid;?>" class="<?php echo $bodyclass;?>" style="<?php echo $bodyStyle;?>"> 
<nav aria-label="<?php echo __('Skip Navigation');?>"><a id="skip-nav" href="#content"><?php echo __('Skip to main content');?></a></nav>
<noscript>
	<div id="no-js-message">
		<span><?php echo __('For full functionality please enable JavaScript in your browser settings.');?> <a target="_blank" href="https://goo.gl/koeeaJ"><?php echo __('Need Help?');?></a></span>
	</div>
</noscript>

<div id="page-content">
	<?php fire_plugin_hook('public_body', array('view'=>$this)); ?>
	<header class="container header-nav">
		<?php echo mh_global_header();?>
	</header>
	
	
	<div id="wrap" class="container">
		<?php fire_plugin_hook('public_content_top', array('view'=>$this)); ?>
