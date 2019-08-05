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
        <td class="error"><?php echo implode("<br />", $element_text['warnings']); ?></td>
        <td><?php echo snippet(nl2br($element_text['text']), 0, 600); ?></td>
    </tr>
    <?php endforeach; ?>
</table>
<?php endif; ?>
