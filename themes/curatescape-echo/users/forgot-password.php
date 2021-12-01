<?php
$pageTitle = __('Forgot Password');
echo head(array('title' => $pageTitle, 'bodyclass' => 'login'), $header);
?>
<div id="content" role="main">
    <article class="page show users">
        <h2 class="page_title"><?php echo $pageTitle; ?></h2>
        <p id="login-links">
        <span id="backtologin"><?php echo link_to('users', 'login', __('Back to Log In')); ?></span>
        </p>
        
        <p class="clear"><?php echo __('Enter your email address to retrieve your password.'); ?></p>
        <?php echo flash(); ?>
        <form method="post" accept-charset="utf-8">
            <div class="field">        
                <label for="email"><?php echo __('Email'); ?></label>
                <?php echo $this->formText('email', @$_POST['email']); ?>
            </div>
        
            <input type="submit" class="submit" value="<?php echo __('Submit'); ?>" />
        </form>

    </article>
</div>
<?php echo foot(array(), $footer); ?>
