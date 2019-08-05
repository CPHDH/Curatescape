<?php
queue_css_file('simple-pages');
queue_js_file('vendor/tinymce/tinymce.min');
queue_js_file('simple-pages-wysiwyg');
$head = array('bodyclass' => 'simple-pages primary', 
              'title' => __('Simple Pages | Edit "%s"', metadata('simple_pages_page', 'title')));
echo head($head);
?>

<?php echo flash(); ?>
<p><?php echo __('This page was created by <strong>%1$s</strong> on %2$s, and last modified by <strong>%3$s</strong> on %4$s.',
    metadata('simple_pages_page', 'created_username'),
    html_escape(format_date(metadata('simple_pages_page', 'inserted'), Zend_Date::DATETIME_SHORT)),
    metadata('simple_pages_page', 'modified_username'), 
    html_escape(format_date(metadata('simple_pages_page', 'updated'), Zend_Date::DATETIME_SHORT))); ?></p>
<?php echo $form; ?>
<?php echo foot(); ?>
