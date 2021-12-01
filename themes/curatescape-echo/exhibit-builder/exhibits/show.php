<?php echo head(array('maptype'=>'none','title' => html_escape(metadata('exhibit_page', 'title') . ' : '. metadata('exhibit', 'title')), 'bodyclass' => 'exhibits show', 'bodyid' => 'exhibit')); ?>

<div id="content" role="main">
    <article class="page show">

        <h2 class="title"><?php echo metadata('exhibit_page', 'title'); ?></h2>

        <?php exhibit_builder_render_exhibit_page(); ?>
        
        <nav id="exhibit-pages">
            <?php
            // Put the (linked) summary page in the nav list
            if ($exhibit->use_summary_page) {
                echo '<div class="inner">';
                echo '<h3 class="h4 exhibit-summary-link">'.exhibit_builder_link_to_exhibit($exhibit, __('Exhibit Summary')).'</h3>';
                echo exhibit_builder_page_tree($exhibit, $exhibit_page);
                echo '</div>';
            } else {
               echo exhibit_builder_page_tree($exhibit, $exhibit_page);
            }?>
        </nav>

    </article>
    
    <div id="exhibit-page-navigation">
        <div id="exhibit-nav-prev">
            <?php
            if ($prevLink = exhibit_builder_link_to_previous_page(__('Previous'), array('class'=>'button'))) {
                echo $prevLink;
            } elseif ($exhibit->use_summary_page) {
                // summary page should be part of nav history
                echo exhibit_builder_link_to_exhibit($exhibit, __('Previous'), array('class'=>'button'));
            } ?>
        </div>
        <div id="exhibit-nav-next">
            <?php
            if ($nextLink = exhibit_builder_link_to_next_page(__('Next'), array('class'=>'button'))) {
                echo $nextLink;
            } ?>
        </div>
    </div>
</div> <!-- end content -->


<?php echo foot(); ?>