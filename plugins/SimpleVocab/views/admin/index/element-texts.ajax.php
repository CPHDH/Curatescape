<?php if (!$element_texts): ?>
<p class="error"><?php echo __('No texts for the selected element exist in Omeka.'); ?></p>
<?php else: ?>
<table>
    <tr>
        <th><?php echo __('Count'); ?></th>
        <th><?php echo __('Warnings'); ?></th>
        <th><?php echo __('Text'); ?></th>
    </tr>
    <?php foreach ($element_texts as $element_text): ?>
    <tr>
        <td><?php echo $element_text['count']; ?></td>
        <td<?php if ($element_text['warnings']) { echo ' class="error"'; } ?>><?php echo implode("<br />", $element_text['warnings']); ?></td>
        <td>
        <?php if (!get_option('simple_vocab_files')): ?>
            <?php $url = url('items/browse', array(
                'advanced' => array(
                    array(
                        'element_id' => $element_id,
                        'type' => 'is exactly',
                        'terms' => $element_text['text'],
                    ),
                ),
            )); ?>
            <a target="blank" href="<?php echo html_escape($url); ?>"><?php echo nl2br(snippet(html_escape($element_text['text']), 0, 600)); ?></a>
        <?php else: ?>
            <?php echo nl2br(snippet(html_escape($element_text['text']), 0, 600)); ?>
        <?php endif; ?>
        </td>
    </tr>
    <?php endforeach; ?>
</table>
<?php endif; ?>
