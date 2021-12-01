<?php echo head(); ?>
<div id="content" role="main">
    <article class="page show contribution">
        <div id="primary">
            <h2><?php echo __('My Contributions'); ?></h2>
            <?php echo flash(); ?>
            <form method='post'>
                <table>
                    <thead>
                        <tr>
                            <th><?php echo __('Public'); ?></th>
                            <th><?php echo __('Anonymous'); ?></th>
                            <th><?php echo __('Item'); ?></th>
                            <th><?php echo __('Added'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach (loop('contrib_items') as $contribItem): ?>
                        <?php $item = $contribItem->Item; ?>
                        <tr>
                            <td><?php echo $this->formCheckbox("contribution_public[{$contribItem->id}]", null, array('checked'=>$contribItem->public)); ?>
                            </td>
                            <td><?php echo $this->formCheckbox("contribution_anonymous[{$contribItem->id}]", null, array('checked'=>$contribItem->anonymous)); ?>
                            </td>
                            <td><?php echo link_to($item, 'show', metadata($item, array('Dublin Core', 'Title'))); ?></td>
                            <td><?php echo metadata($item, 'added'); ?></td>
    
                        </tr>
    
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <input id="save-changes" class="submit big button" type="submit" value="Save Changes" name="submit">
            </form>
        </div>
    </article>
</div>
<?php echo foot(); ?>