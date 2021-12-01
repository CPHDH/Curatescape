<?php
$pageTitle = __('User Activation');
echo head(array('title' => $pageTitle), $header);
?>
<div id="content" role="main">
    <article class="page show users">
        <h2 class="page_title"><?php echo $pageTitle; ?></h2>

        <?php echo flash(); ?>
        <h3><?php echo html_escape(__('Hello %s. Your username is %s', $user->name, $user->username)); ?></h3>
        
        <form method="post">
            <fieldset>
            <div class="field">
            <?php echo $this->formLabel('new_password1', __('Create a Password')); ?>
                <div class="inputs">
                    <input type="password" name="new_password1" id="new_password1" class="textinput" />
                </div>
            </div>
            <div class="field">
                <?php echo $this->formLabel('new_password2', __('Re-type the Password')); ?>
                <div class="inputs">
                    <input type="password" name="new_password2" id="new_password2" class="textinput" />
                </div>
            </div>
            </fieldset>
            <div>
            <input type="submit" class="submit" name="submit" value="<?php echo __('Activate'); ?>"/>
            </div>
        </form>
    </article>
</div>

<?php echo foot(array(), $footer); ?>
