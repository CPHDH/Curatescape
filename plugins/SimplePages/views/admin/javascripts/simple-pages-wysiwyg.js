jQuery(document).ready(function() {
    var selector;
    if (jQuery('#simple-pages-use-tiny-mce').is(':checked')) {
        selector = '#simple-pages-text';
    } else {
        selector = false;
    }
    Omeka.wysiwyg({
        selector: selector,
        menubar: 'edit view insert format table',
        plugins: 'lists link code paste media autoresize image table charmap hr',
        browser_spellcheck: true
    });
    // Add or remove TinyMCE control.
    jQuery('#simple-pages-use-tiny-mce').click(function() {
        if (jQuery(this).is(':checked')) {
            tinyMCE.EditorManager.execCommand('mceAddEditor', true, 'simple-pages-text');
        } else {
            tinyMCE.EditorManager.execCommand('mceRemoveEditor', true, 'simple-pages-text');
        }
    });
});
