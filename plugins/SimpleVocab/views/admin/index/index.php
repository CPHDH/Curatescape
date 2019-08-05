<?php
$head = array('title' => __('Simple Vocab'));
echo head($head);
?>
<script type="text/javascript" charset="utf-8">
//<![CDATA[
jQuery(window).load(function () {
  jQuery('#element-id').change(function() {
    var elementId = jQuery('#element-id').val();
    var submitButton = jQuery('#edit_vocab');
    var textarea = jQuery('#terms');
    submitButton.prop('disabled', true);
    textarea.prop('disabled', true);
    if ('' === elementId) {
        textarea.val('');
    } else {
        jQuery.ajax({
          url: <?php echo js_escape(url(array('action' => 'element-terms', 'format' => 'html'))); ?>,
          data: {element_id: elementId},
          success: function(data) {
            jQuery('#terms').val(data);
            submitButton.prop('disabled', false);
            textarea.prop('disabled', false);
          }
        });
    }
  });
  jQuery('#display-texts').click(function() {
    jQuery.ajax({
      url: <?php echo js_escape(url(array('action' => 'element-texts', 'format' => 'html'))); ?>,
      data: {element_id: jQuery('#element-id').val()},
      success: function(data) {
        jQuery('#texts').html(data);
      }
    });
  });
});
//]]>
</script>
<?php echo flash(); ?>
<form method="post" action="<?php echo url('simple-vocab/index/edit-element-terms'); ?>">
<section class="seven columns alpha">
    <div class="field">
        <div id="element-id-label" class="one column alpha">
            <label for="element-id"><?php echo __('Element'); ?></label>
        </div>
        <div class="inputs six columns omega">
            <?php echo $this->formSelect('element_id', null, array('id' => 'element-id'), $options_for_select) ?>
            <p class="explanation"><?php echo __('Select an element to manage its ' 
            . 'custom vocabulary. Elements with a custom vocabulary are marked with ' 
            . 'an asterisk (*).'); ?></p>
        </div>
    </div>
    <div class="field">
        <label for="terms"><?php echo __('Vocabulary Terms'); ?></label>
        <div class="inputs">
            <?php echo $this->formTextarea('terms', null, array('id' => 'terms', 'rows' => '10', 'disabled' => true)) ?>
            <p class="explanation"><?php echo __('Enter the custom vocabulary ' 
            . 'terms for this element, one per line. To delete the vocabulary, ' 
            . 'simply remove the terms and submit this form.'); ?></p>
        </div>
    </div>
<p><?php echo __('%sClick here%s to display a list of texts for the selected element ' 
. 'that currently exist in Omeka. You may use this list as a reference to build a ' 
. 'vocabulary, but be aware of some caveats:', 
'<a id="display-texts" href="#display-texts"><strong>', 
'</strong></a>'); ?></p>
<ul>
    <li><?php echo __('Vocabulary terms must not contain newlines (line breaks).'); ?></li>
    <li><?php echo __('Vocabulary terms are typically short and concise. If your ' 
    . 'existing texts are otherwise, avoid using a controlled vocabulary for this ' 
    . 'element.'); ?></li>
    <li><?php echo __('Vocabulary terms must be identical to their corresponding texts.'); ?></li>
    <li><?php echo __('Existing texts that are not in the vocabulary will be ' 
    . 'preserved — however, they cannot be selected in the item edit page, and ' 
    . 'will be deleted once you save the item.'); ?></li>
</ul>
</section>
<section class="three columns omega">
    <div id="edit" class="panel">
        <?php echo $this->formSubmit('edit_vocab', __('Save Changes'), array('class' => 'submit big green button', 'disabled' => true)); ?>
    </div>
</section>
</form>
<section id="texts" class="ten columns alpha"></section>
<?php echo foot(); ?>
