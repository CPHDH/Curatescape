<?php
queue_js_file('login');
$pageTitle = __('Log In');
echo head(array('bodyclass' => 'login', 'title' => $pageTitle), $header);
?>
<div id="content" role="main">
    <article class="page show users">
        <h2 class="page_title"><?php echo $pageTitle; ?></h2>

        <p id="login-links">
        <span id="backtosite"><?php echo link_to_home_page(__('Go to Home Page')); ?></span>  |  <span id="forgotpassword"><?php echo link_to('users', 'forgot-password', __('Lost your password?')); ?></span>
        </p>
        
        <?php echo flash(); ?>
            
        <?php echo $this->form->setAction($this->url('users/login')); ?>

    </article>
</div>

<?php echo foot(array(), $footer); ?>
