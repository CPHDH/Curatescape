<?php
$user = current_user();
$pageTitle =  get_option('guest_user_dashboard_label');
echo head(array('title' => $pageTitle));
?>
<div id="content" role="main">
    <article class="page show guest-user">
        <h2 class="page_title"><?php echo $pageTitle; ?></h2>
        
        <?php echo flash(); ?>
    
        <?php foreach ($widgets as $index=>$widget): ?>
            <div class='guest-user-widget <?php if ($index & 1): ?>guest-user-widget-odd <?php else:?>guest-user-widget-even<?php endif;?>'>
                <?php echo GuestUserPlugin::guestUserWidget($widget); ?>
            </div>
        <?php endforeach; ?>
        
    </article>
</div>
<?php echo foot(); ?>