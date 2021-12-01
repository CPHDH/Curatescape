<?php
echo head(array('maptype'=>'focusarea','title'=>'404','bodyid'=>'error','bodyclass'=>'error_404')); ?>

<div id="content" role="main">
    <article class="error page show">
        <h2>404</h2>

        <div id="primary" class="show">
            <section id="text">
                <p><?php echo __('Sorry. The page you are looking for does not exist!');?></p>
            </section>
        </div>

    </article>
</div> <!-- end content -->
<?php echo foot(); ?>
