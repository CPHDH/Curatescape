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
    <meta name="apple-mobile-web-app-capable" content="no" />
	<meta name="apple-mobile-web-app-status-bar-style" content="black" />
	-->
	<!-- iOS icon : Generic -->
	<link rel="apple-touch-icon" href="<?php echo mh_apple_icon_logo_url();?>" />
	<link rel="icon" href="<?php echo mh_apple_icon_logo_url();?>" />
    
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
	
	<!-- Custom CSS via theme config -->
	<?php echo '<style type="text/css">.ui-body-c .ui-link{color:'.mh_link_color().'}</style>';?>	
	
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
			<?php echo mh_global_nav('mobile_head'); ?>
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

    <link rel="shortcut icon" href="<?php echo img('favicon.ico');?>"/>
    
<title><?php echo settings('site_title'); echo $title ? ' | ' . $title : ''; ?></title>


<!-- FLOWPLAYER -->
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js"></script> 
<?php echo js('flowplayer/flowplayer-3.2.6.min');?>
<?php echo js('flowplayer/flowplayer.playlist-3.0.8');?>
<link rel="stylesheet" type="text/css" href="<?php echo WEB_ROOT;?>/themes/MobileHistorical/javascripts/flowplayer/style.css"/> 
<!-- END FLOWPLAYER -->




<!-- Meta -->
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="description" content="<?php echo settings('description'); ?>" />
<meta name="keywords" content="<?php echo get_theme_option('meta_key') ;?>" /> 

<?php echo auto_discovery_link_tag(); ?>

<!-- Stylesheets -->
<link rel="stylesheet" media="screen" href="<?php echo html_escape(css('screen')); ?>" />
<link rel="stylesheet" media="print" href="<?php echo html_escape(css('print')); ?>" />

<!-- Custom CSS via theme config -->
<?php echo '<style type="text/css">#content-home{background:url('.mh_bg_home_logo_url().') no-repeat center top #bbb;}#content{background:url('.mh_bg_lv_logo_url().') no-repeat center top #d7d7d7;}p.view-items-link a, div#footer a, div#footer a:link, #page-col-left div.subjects li a, h3 a, h3 a:link, h3 a:visited,h3 a:active,#primary.show a{color:'.mh_link_color().'}'.get_theme_option('custom_css').'</style>';?>

<!-- JavaScripts -->
<?php echo js('default'); ?>

<!-- Plugin Stuff -->
<?php echo plugin_header(); ?>


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