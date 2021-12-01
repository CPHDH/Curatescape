<?php

/*
** 2 changes from the Omeka default:
** Shortens "Next Page" text label to "Next"
** Shortens "Previous Page" text label to "Previous"
*/

if ($this->pageCount > 1):
    $getParams = $_GET;
?>
<nav class="pagination-nav" aria-label="<?php echo __('Pagination'); ?>">
    <ul class="pagination">
        <?php if (isset($this->previous)): ?>
        <!-- Previous page link -->
        <li class="pagination_previous">
            <?php $getParams['page'] = $previous; ?>
            <a class="button" rel="prev" href="<?php echo html_escape($this->url(array(), null, $getParams)); ?>"><?php echo __('Previous'); ?></a>
        </li>
        <?php endif; ?>

        <?php if (isset($this->next)): ?>
        <!-- Next page link -->
        <li class="pagination_next">
            <?php $getParams['page'] = $next; ?>
            <a class="button" rel="next" href="<?php echo html_escape($this->url(array(), null, $getParams)); ?>"><?php echo __('Next'); ?></a>
        </li>
        <?php endif; ?>
    </ul>
</nav>

<?php endif; ?>