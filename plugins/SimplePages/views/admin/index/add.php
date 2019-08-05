<?php
queue_css_file('simple-pages');
queue_js_file('vendor/tinymce/tinymce.min');
queue_js_file('simple-pages-wysiwyg');
$head = array('bodyclass' => 'simple-pages primary', 
              'title' => html_escape(__('Simple Pages | Add Page')));
echo head($head);
?>

<?php echo flash(); ?>
<?php echo $form; ?>
<?php echo foot(); ?>
