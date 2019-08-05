<section class="seven columns alpha">
    <div class="field">
        <div class="two column alpha">
            <label for="simple_vocab_files">Apply to Files</label>
        </div>
        <div class="inputs five columns omega">
            <?php echo $view->formCheckbox(
                'simple_vocab_files',
                null,
                array(
                    'id' => 'simple_vocab_files',
                    'checked' => (bool) get_option('simple_vocab_files'),
                )
            ); ?>
            <p class="explanation">By default, your simple vocabularies will only
            apply to item metadata. Check the above box if you want to apply your
            vocabularies to file metadata as well.</p>
        </div>
    </div>
</section>
