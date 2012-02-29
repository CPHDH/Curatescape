<?php
if (mobile_device_detect()==true){
//begin mobile header
?>
<!DOCTYPE html> 
<html> 

<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width,initial-scale=1.0"> 

	<!-- disable auto-linking, which interferes with image galleries, etc -->
	<meta name="format-detection" content="telephone=no">

    <!-- allow installation as offline iOS app 
    <meta name="apple-mobile-web-app-capable" content="yes" />
	<meta name="apple-mobile-web-app-status-bar-style" content="black" />
	-->
	<!-- iOS icons 
	<link rel="apple-touch-icon" href="<?php// echo img('Icon.png');?>" />
	<link rel="apple-touch-icon" sizes="72x72" href="<?php// echo img('Icon-72.png');?>" />
	<link rel="apple-touch-icon" sizes="114x114" href="<?php// echo img('Icon@2x.png');?>" />
	-->
	<!-- iPhone/iPodTouch loading image 
	<link rel="apple-touch-startup-image" href="<?php// echo img('Default.png');?>" />
	-->
	
	<!-- iPad loading image - landscape (1024x748) 
	<link rel="apple-touch-startup-image" href="<?php// echo img('Default-iPad-L.png');?>" media="screen and (min-device-width: 481px) and (max-device-width: 1024px) and (orientation:landscape)" />
	-->

	<!-- iPad loading image - portrait (768x1024) 
	<link rel="apple-touch-startup-image" href="<?php// echo img('Default-iPad-P');?>" media="screen and (min-device-width: 481px) and (max-device-width: 1024px) and (orientation:portrait)" />
	-->
    
	<title><?php echo settings('site_title'); echo $title ? ' | ' . $title : ''; ?></title>
	<?php 
	queue_css('m-jquerymobile1bmin');
	queue_css('m-screen');
	display_css();
	?>
  	
	<script src="http://code.jquery.com/jquery-1.6.2.min.js"></script>
	<script src="http://code.jquery.com/mobile/1.0b2/jquery.mobile-1.0b2.min.js"></script>
	
	<!-- TypeKit -->
	<script type="text/javascript" src="http://use.typekit.com/<?php echo get_theme_option('typekit');?>.js"></script>
	<script type="text/javascript">try{Typekit.load();}catch(e){}</script>
	
	<!-- Google Analytics -->
	<?php echo mh_google_analytics();?>
	
	<!-- start NETEYE Touch Gallery image viewer script (iOS only) -->
	<?php 
	//$ios=mobile_device_detect($iphone=true,$ipad=true,$android=false,$opera=false,$blackberry=false,$palm=false,$windows=false,$mobileredirect=false,$desktopredirect=false);
	//if ($ios==true)
	//{
	//echo js('jquery.touch-gallery-1.0.0.min');
	//echo js('ios-neteye');;
	//}
	?>
	<!-- end NETEYE Touch Gallery -->

  </head> 

	

<body <?php 
//set the body class for either mobile phone or iPad… TODO: detect other tablets
$ipad=mobile_device_detect($iphone=false,$ipad=true,$android=false,$opera=false,$blackberry=false,$palm=false,$windows=false,$mobileredirect=false,$desktopredirect=false);
$phone=mobile_device_detect($iphone=true,$ipad=false,$android=true,$opera=true,$blackberry=true,$palm=true,$windows=true,$mobileredirect=false,$desktopredirect=false);
if ($ipad==true){echo 'class="tablet"';}elseif ($phone==true){echo 'class="phone"';}else{echo 'class="other"';}?>> 
<div data-role="page">

	<div data-role="header" data-theme="a">

		<!--
		<img class="header-img" src="<?php// echo img('cle.png');?>"/>
		-->
		
		<h1><?php echo settings('site_title');?></h1>
		
		<div data-role="navbar">
    			<ul class="navigation">
    			    <li><a href="<?php echo uri('/')?>" data-icon="home" <?php if (get_theme_option('stealth_mode')==1){echo 'target="_self"';}else{echo 'data-direction="reverse"';} ;?>>Home</a></li>
    			    <li><a href="<?php echo uri('/items/')?>" data-icon="grid" >Browse</a></li>
    			    <li><a href="<?php echo uri('/tour-builder/tours/browse/')?>" data-icon="star" >Tours</a></li>
    			</ul>
		</div> <!-- end nav -->		
    	
	</div><!-- /header -->

<?	
//end mobile header
}
else{
//begin non-mobile header	
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<!-- Chrome Frame for IE -->
<meta http-equiv="X-UA-Compatible" content="IE=Edge,chrome=1"/>
<title><?php echo settings('site_title'); echo $title ? ' | ' . $title : ''; ?></title>


<!-- FLOWPLAYER -->
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js"></script> 
<?php echo js('flowplayer/flowplayer-3.2.6.min');?>
<?php echo js('flowplayer/flowplayer.playlist-3.0.8');?>
<link rel="stylesheet" type="text/css" href="<?php echo WEB_ROOT;?>/themes/MobileHistorical/javascripts/flowplayer/style.css"/> 
<!-- END FLOWPLAYER -->


<?php echo js('jquery-1.6.1');?>

<!-- Meta -->
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="description" content="<?php echo settings('description'); ?>" />
<meta name="keywords" content="<?php echo get_theme_option('meta_key') ;?>" /> 

<?php echo auto_discovery_link_tag(); ?>

<!-- Stylesheets -->
<link rel="stylesheet" media="screen" href="<?php echo html_escape(css('screen')); ?>" />
<link rel="stylesheet" media="print" href="<?php echo html_escape(css('print')); ?>" />

<!-- Custom CSS via theme config -->
<?php echo '<style type="text/css">'.get_theme_option('custom_css').'</style>';?>

<!-- JavaScripts -->
<?php echo js('default'); ?>

<!-- Plugin Stuff -->
<?php echo plugin_header(); ?>

<!-- Twitter -->
<link rel="stylesheet" href="<?php echo WEB_ROOT;?>/themes/MobileHistorical/javascripts/twitter/jquery.twitter.css" type="text/css" />


<!-- TypeKit -->
<script type="text/javascript" src="http://use.typekit.com/<?php echo get_theme_option('typekit');?>.js"></script>
<script type="text/javascript">try{Typekit.load();}catch(e){}</script>

<!-- Google Analytics -->
<?php echo mh_google_analytics();?>


<!-- load ClearBox -->
	<?php echo '<script src="'.WEB_ROOT.'/themes/MobileHistorical/javascripts/clearbox/clearbox.js?config=default" type="text/javascript"></script>';?>
<!-- end ClearBox -->

</head>
<body> 

	<div id="wrap">
	<?php 
	//end non-mobile header
	}?>