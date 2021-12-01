<?php echo head(array('maptype'=>'none','title' => metadata('exhibit', 'title'), 'bodyclass'=>'exhibits summary show','bodyid' => 'exhibit')); ?>

<div id="content" role="main">
    <article class="exhibit page show">
        <h2 class="title"><?php echo metadata('exhibit', 'title'); ?></h2>
        <?php if (($exhibitCredits = metadata('exhibit', 'credits'))): ?>
        <div class="byline"><?php echo __('Curated by %s',$exhibitCredits); ?></div><br>
        <?php endif; ?>
        <?php if ($exhibitDescription = metadata('exhibit', 'description', array('no_escape' => true))): ?>
        <div class="exhibit-description">
            <?php echo $exhibitDescription; ?>
        </div>
        <?php endif; ?>
        <?php
        if ($start = $exhibit->getFirstTopPage()) {
            // add link button to first page
            echo '<a class="button button-primary" href="'.exhibit_builder_exhibit_uri($exhibit, $start).'">'.__('View Exhibit').'</a>';
        }
        
        ?>

        <nav id="exhibit-pages">
            <?php
            if ($pageTree = exhibit_builder_page_tree()) {
                echo '<div class="inner">';
                echo '<h3 class="h4 title exhibit-summary-link">'.__('Exhibit Summary').'</h3>';
                echo exhibit_builder_page_tree($exhibit);
                echo '</div';
            }?>
        </nav>

    </article>
</div> <!-- end content -->



<?php echo foot(); ?>