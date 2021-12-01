<?php
$stealthMode=(get_theme_option('stealth_mode')==1)&&(is_allowed('Items', 'edit')!==true) ? true : false;
$classname='home'.($stealthMode ? ' stealth' : null);
echo head(
    array(
    'bodyid'=>'home',
    'bodyclass'=>$classname)
); ?>

<div id="content" role="main" class="wide">
    
    <?php 
    if(!$stealthMode){
        if(get_theme_option('homepage_map_placement') == 'top'){
                echo rl_homepage_map();
            }
            echo rl_homepage_featured();
            echo rl_homepage_recent_random();
            if(get_theme_option('homepage_map_placement') == 'middle'){
                echo rl_homepage_map();
            }
            echo rl_homepage_tours();
            echo rl_homepage_tags();
            if(get_theme_option('homepage_map_placement') == 'bottom'){
                echo rl_homepage_map();
            }
            echo rl_homepage_projectmeta();
    }else{
       echo rl_homepage_stealthmode(); 
    }
    ?>
    
</div>

<?php echo foot(); ?>