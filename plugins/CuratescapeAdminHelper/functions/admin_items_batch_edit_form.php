<?php 
$it_info=cah_item_type();
$it_name=$it_info['name'];	
?>	
<div class="field">
    <div class="two columns alpha">
        <label for="migration-helper"><?php echo __('Convert to %s?',$it_name); ?></label>
    </div>

    <div class="inputs five columns omega">
        <?php echo get_view()->formCheckbox('custom[migration-helper]'); ?>

        <p class="explanation"><?php echo __(
                  'Check this box to convert the selected items to the %s type and copy existing Dublin Core data to corresponding Item Type Metadata fields.',$it_name ); ?></p>

		<table>
		    <thead>
			    <th><?php echo __('Dublin Core');?></th>
			    <th><?php echo __('Item Type');?></th>
		    </thead>
		    <tr>
		    	<td><?php echo __('2nd Title');?></td><td><?php echo __('Subtitle');?></td>
		    </tr>
		    <tr>
		    	<td><?php echo __('Description');?></td><td><?php echo __('Story');?></td>
		    </tr>
		     <tr>
		    	<td><?php echo __('Relation');?></td><td><?php echo __('Related Resources');?></td>
		    </tr>
		</table>
	                    
    </div>
    
    
</div>   