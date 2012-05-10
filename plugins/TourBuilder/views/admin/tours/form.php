
<?php echo flash(); ?>

<div id="public-featured">
    <?php if ( has_permission('TourBuilder_Tours', 'makePublic') ): ?>
        <div class="checkbox">
            <label for="public">Public:</label> 
            <div class="checkbox"><?php echo checkbox(array('name'=>'public', 'id'=>'public'), $tour->public); ?></div>
        </div>
    <?php endif; ?>
    <?php if ( has_permission('TourBuilder_Tours', 'makeFeatured') ): ?>
        <div class="checkbox">
            <label for="featured">Featured:</label> 
            <div class="checkbox"><?php echo checkbox(array('name'=>'featured', 'id'=>'featured'), $tour->featured); ?></div>
        </div>
    <?php endif; ?>
</div>
<div id="item-metadata">
<?php foreach ($tabs as $tabName => $tabContent): ?>
    <?php if (!empty($tabContent)): ?>
        <div id="<?php echo text_to_id(html_escape($tabName)); ?>-metadata">
        <fieldset class="set">
            <legend><?php echo html_escape($tabName); ?></legend>
            <?php echo $tabContent; ?>        
        </fieldset>
        </div>     
    <?php endif; ?>
<?php endforeach; ?>
</div>
