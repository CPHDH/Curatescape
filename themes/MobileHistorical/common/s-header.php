<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title><?php echo settings('site_title');?></title>

<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta name="description" content="<?php echo settings('description'); ?>" />
<meta name="keywords" content="<?php echo get_theme_option('meta_key') ;?>" /> 

<link rel="stylesheet" media="all" type='text/css' href="<?php echo css('s-screen'); ?>" />
<?php if (mobile_device_detect()==true){;?>
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
<link rel="stylesheet" media="all" type='text/css' href="<?php echo css('sm-screen'); ?>" />
<?php };?>

<!-- Custom CSS via theme config -->
<?php echo '<style type="text/css">div #container{background-image:url('.mh_bg_lv_logo_url().');background-position:top center;background-color:#d7d7d7;background-repeat:none;}'.get_theme_option('custom_css').'</style>';?>

<!-- TypeKit -->
<script type="text/javascript" src="http://use.typekit.com/<?php echo get_theme_option('typekit');?>.js"></script>
<script type="text/javascript">try{Typekit.load();}catch(e){}</script>

<!-- Google Analytics -->
<?php echo mh_google_analytics();?>


</head>

<body class="stealth-mode"> 
<div id="wrapper">
	<div id="container">