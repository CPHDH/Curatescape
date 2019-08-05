<table class="full">
    <thead>
        <tr>
            <?php echo browse_sort_links(array(
                __('Title') => 'title',
                __('Slug') => 'slug',
                __('Last Modified') => 'updated'), array('link_tag' => 'th scope="col"', 'list_tag' => ''));
            ?>
        </tr>
    </thead>
    <tbody>
    <?php foreach (loop('simple_pages_pages') as $simplePage): ?>
        <tr>
            <td>
                <span class="title">
                    <a href="<?php echo html_escape(record_url('simple_pages_page')); ?>">
                        <?php echo metadata('simple_pages_page', 'title'); ?>
                    </a>
                    <?php if(!metadata('simple_pages_page', 'is_published')): ?>
                        (<?php echo __('Private'); ?>)
                    <?php endif; ?>
                </span>
                <ul class="action-links group">
                    <li><a class="edit" href="<?php echo html_escape(record_url('simple_pages_page', 'edit')); ?>">
                        <?php echo __('Edit'); ?>
                    </a></li>
                    <li><a class="delete-confirm" href="<?php echo html_escape(record_url('simple_pages_page', 'delete-confirm')); ?>">
                        <?php echo __('Delete'); ?>
                    </a></li>
                </ul>
            </td>
            <td><?php echo metadata('simple_pages_page', 'slug'); ?></td>
            <td><?php echo __('<strong>%1$s</strong> on %2$s',
                metadata('simple_pages_page', 'modified_username'),
                html_escape(format_date(metadata('simple_pages_page', 'updated'), Zend_Date::DATETIME_SHORT))); ?>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
