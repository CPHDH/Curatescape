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
	
				<!-- start NETEYE Touch Gallery image viewer script (iOS only) -->
				<?php 
				$ios=mobile_device_detect($iphone=true,$ipad=true,$android=false,$opera=false,$blackberry=false,$palm=false,$windows=false,$mobileredirect=false,$desktopredirect=false);
				if ($ios==true)
				{
				echo js('jquery.touch-gallery-1.0.0.min');
				echo js('ios-neteye');;
				}
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

		<img class="header-img" src="<?php echo img('cle.png');?>"/>
		
		<h1><?php echo settings('site_title');?></h1>
		
		<div data-role="navbar">
    			<ul class="navigation">
    			    <li><a href="<?php echo uri('/')?>" data-icon="home" data-direction="reverse">Home</a></li>
    			    <li><a href="<?php echo uri('/items/')?>" data-icon="grid" >Browse</a></li>
    			    <li><a href="<?php echo uri('/tour-builder/tours/browse/')?>" data-icon="star" >Tours</a></li>
    			</ul>
		</div> <!-- end nav -->		
    	
	</div><!-- /header -->
