<div class="field">
    <div class="field-meta">
        <label for="simple_vocab_files"><?php echo __('Apply to Files'); ?></label>
    </div>
    <div class="inputs">
        <?php echo $view->formCheckbox(
            'simple_vocab_files',
            null,
            array(
                'id' => 'simple_vocab_files',
                'checked' => (bool) get_option('simple_vocab_files'),
            )
        ); ?>
        <p class="explanation"><?php echo __('By default, your simple vocabularies will only apply to item metadata. Check the above box if you want to apply your vocabularies to file metadata as well.'); ?></p>
    </div>
</div>
