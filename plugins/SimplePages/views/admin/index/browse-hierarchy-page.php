<?php
/*
This is a hack.  
Apparently, $this !== get_view().
So $this->simplePage != get_view()->simplePage 
So when the subsequent helper functions try to get the current simple page, they would not find them, 
Unless we explicitly set the current simple page.
If you try to fix this, see simple_pages_display_hierarchy, especially the call to get_view()->partial
*/
set_current_record('simple_pages_page', $this->simple_pages_page);
?>

<p><a href="<?php echo html_escape(record_url($simple_pages_page)); ?>">
<?php echo metadata('simple_pages_page', 'title'); ?></a> 
 (<?php echo metadata('simple_pages_page', 'slug'); ?>)<br/> 
 <?php echo __('<strong>%1$s</strong> on %2$s',
                html_escape(metadata('simple_pages_page', 'modified_username')),
                html_escape(format_date(metadata('simple_pages_page', 'updated'), Zend_Date::DATETIME_SHORT))); ?>
 <a class="edit" href="<?php echo html_escape(record_url($simple_pages_page, 'edit')) ?>"><?php echo __('Edit'); ?></a><br/>
 <?php echo (metadata('simple_pages_page', 'is_published') ? __('Published') : __('Not Published')); ?>
</p>
