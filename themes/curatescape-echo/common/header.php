<?php
// set html class for dark mode capability
// see also global.js for cookie management
$htmlclass = (get_theme_option('enable_dark_mode')) ? 'darkallowed' : 'darkdisabled_admin';
if(isset($_COOKIE['neverdarkmode']) && $_COOKIE['neverdarkmode']=="1"){
    $htmlclass = 'darkdisabled_user';
}
?>
<!DOCTYPE html>
<html lang="<?php echo get_html_lang();?>" class="<?php echo $htmlclass;?>">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=5,viewport-fit=cover">
    <meta name="theme-color" content="<?php echo get_theme_option('header_footer_color');?>" />

    <?php // PHP Variables
    $title = (isset($title)) ? $title : null;
    $item = (isset($item)) ? $item : null;
    $tour = (isset($tour)) ? $tour : null;
    $file = (isset($file)) ? $file : null;
    $bodyid = isset($bodyid) ? $bodyid : 'default';
    $bodyclass = isset($bodyclass) ? $bodyclass.' curatescape' : 'default curatescape';
    ?>

    <!-- Meta / SEO -->
    <title><?php echo rl_seo_pagetitle($title, $item); ?></title>
    <meta name="description" content="<?php echo rl_seo_pagedesc($item, $tour, $file); ?>" />

    <!-- FB Open Graph stuff -->
    <meta property="og:title" content="<?php echo rl_seo_pagetitle($title, $item); ?>" />
    <meta property="og:image" content="<?php echo rl_seo_pageimg($item, $file);?>" />
    <meta property="og:site_name" content="<?php echo option('site_title');?>" />
    <meta property="og:description" content="<?php echo rl_seo_pagedesc($item, $tour, $file); ?>" />

    <!-- Twitter Card stuff-->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?php echo rl_seo_pagetitle($title, $item); ?>">
    <meta name="twitter:description" content="<?php echo rl_seo_pagedesc($item, $tour, $file); ?>">
    <meta name="twitter:image" content="<?php echo rl_seo_pageimg($item, $file);?>">
    <?php echo ($twitter=get_theme_option('twitter_username')) ? '<meta name="twitter:site" content="@'.$twitter.'"> ' : '';?>

    <!-- Favicons -->
    <link rel="icon" type="image/svg+xml" href="<?php echo rl_favicon_svg_url();?>">
    <link rel="alternate icon" sizes="any" href="<?php echo rl_favicon_ico_url();?>">
    <link rel="apple-touch-icon" href="<?php echo rl_touch_icon_url();?>" />

    <!-- iOS Smart Banner -->
    <?php echo rl_ios_smart_banner(); ?>

    <!-- Fonts -->
    <?php echo rl_font_loader();?>

    <!-- RSS -->
    <?php echo auto_discovery_link_tags(); ?>
    
    <!-- CSS var() polyfill for IE 11 -->
    <script>window.MSInputMethodContext && document.documentMode && document.write('<script src="https://cdn.jsdelivr.net/gh/nuxodin/ie11CustomProperties@4.1.0/ie11CustomProperties.min.js"><\/script>');</script>

    <!-- Assets -->
    <?php
    $includeJQuery = ((0 === strpos(current_url(), '/items/search')) || (0 === strpos(current_url(), '/guest-user/')) || (0 === strpos(current_url(), '/contribution/'))) ? true : false;
    fire_plugin_hook('public_head', array('view'=>$this));
    rl_assets_blacklist($this, array('/plugins/Geolocation','admin-bar','family=Arvo:400'));
    rl_theme_css();
    echo head_css();
    echo head_js($includeJQuery);
    ?>

    <style>
    <?php echo rl_configured_css();
    ?>
    </style>
    
    <!-- Preconnect to Map JSON -->
    <?php if (is_current_url('/items/show') || is_current_url('/tours/show')):?>
        <link rel="preconnect" href="?output=mobile-json">
    <?php elseif(is_current_url('/') || is_current_url('/items/map')):?>
        <link rel="preconnect" href="/items/browse?output=mobile-json">
    <?php elseif(is_current_url('/items/browse') && $_SERVER['QUERY_STRING']):?>
        <link rel="preconnect" href="?<?php echo $_SERVER['QUERY_STRING'];?>&output=mobile-json">
    <?php elseif(is_current_url('/items/browse')):?>
        <link rel="preconnect" href="?output=mobile-json">
    <?php endif;?>

    <!-- Async Assets -->
    <script>
    /*!
		loadJS: load a JS file asynchronously. 
		[c]2014 @scottjehl, Filament Group, Inc. (Based on http://goo.gl/REQGQ by Paul Irish). 
		Licensed MIT 
		*/
    (function(w) {
        var loadJS = function(src, cb, ordered) {
            "use strict";
            var tmp;
            var ref = w.document.getElementsByTagName("script")[0];
            var script = w.document.createElement("script");
            if (typeof(cb) === 'boolean') {
                tmp = ordered;
                ordered = cb;
                cb = tmp;
            }
            script.src = src;
            script.async = !ordered;
            ref.parentNode.insertBefore(script, ref);
            if (cb && typeof(cb) === "function") {
                script.onload = cb;
            }
            return script;
        };
        if (typeof module !== "undefined") {
            module.exports = loadJS;
        } else {
            w.loadJS = loadJS;
        }
    }(typeof global !== "undefined" ? global : this));

    /*!
    loadCSS: load a CSS file asynchronously.
    [c]2014 @scottjehl, Filament Group, Inc.
    Licensed MIT
    */
    function loadCSS(href, before, media) {
        "use strict";
        var ss = window.document.createElement("link");
        var ref = before || window.document.getElementsByTagName("script")[0];
        var sheets = window.document.styleSheets;
        ss.rel = "stylesheet";
        ss.href = href;
        ss.media = "only x";
        ref.parentNode.insertBefore(ss, ref);

        function toggleMedia() {
            var defined;
            for (var i = 0; i < sheets.length; i++) {
                if (sheets[i].href && sheets[i].href.indexOf(href) > -1) {
                    defined = true;
                }
            }
            if (defined) {
                ss.media = media || "all";
            } else {
                setTimeout(toggleMedia);
            }
        }
        toggleMedia();
        return ss;
    }

    // Async JS 
    loadJS('<?php echo src('global.js', 'javascripts');?>');
    <?php if (is_current_url('/items/show')):?>
        loadJS('<?php echo src('items-show.js', 'javascripts');?>');
    <?php elseif (is_current_url('/tours/show') || is_current_url('/items/browse') || is_current_url('/') || is_current_url('/items/map')):?>
        loadJS('<?php echo src('multi-map.js', 'javascripts');?>');
    <?php endif;?>
    </script>
    
    <noscript>
        <link href="<?php echo css_src('noscript'); ?>" media="all" rel="stylesheet" type="text/css" />
        <?php if(isset($noscript_styles)){
            echo $noscript_styles;
        } ?>
    </noscript>
</head>

<body id="<?php echo $bodyid;?>" class="<?php echo $bodyclass;?>">

    <div id="site-content">

        <nav aria-label="<?php echo __('Skip Navigation');?>"><a id="skip-nav" href="#page-content"><?php echo __('Skip to main content');?></a></nav>

        <?php fire_plugin_hook('public_body', array('view'=>$this)); ?>

        <header class="primary">
            <?php echo rl_global_header();?>
        </header>


        <div id="page-content" class="container">
            <?php fire_plugin_hook('public_content_top', array('view'=>$this)); ?>