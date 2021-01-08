<?php
     $options = array(
         
            __('Admin Dashboard Settings')=>
            array(
                array('name'=>'cah_enable_dashboard_stats','label'=>__('File Statistics'),'explanation'=>__('Display file statistics on the dashboard.')),
                array('name'=>'cah_enable_dashboard_resources','label'=>__('Resources'),'explanation'=>__('Display the Curatescape Resources widget on the dashboard.')),
                array('name'=>'cah_enable_dashboard_components','label'=>__('Components'),'explanation'=>__('Display the Curatescape Components widget on the dashboard.')),
                ),
            
            __('Item and File form settings')=>
            array(
                array('name'=>'cah_enable_item_file_tab_notes','label'=>__('Helper Notes'),'explanation'=>__('Display Curatescape-related instructions, tips and other notes on the item and file forms.')),
                array('name'=>'cah_enable_item_file_toggle_dc','label'=>__('Toggle Metadata'),'explanation'=>__('By default, only display Curatescape-related metadata fields on the item and file forms. (Other fields are still accessible with a single click.)')),
                array('name'=>'cah_enable_file_edit_links','label'=>__('File Edit Links'),'explanation'=>__('Display "edit" links below each file on item records.')),
                array('name'=>'cah_hide_add_input_where_unsupported','label'=>__('Hide Add Input Buttons'),'explanation'=>__('Show Add Input buttons only on fields that are supported by the Curatescape theme.')),
                array('name'=>'cah_hide_html_checkbox_where_unsupported','label'=>__('Hide "Use HTML" Checkboxes'),'explanation'=>__('Show "Use HTML" checkboxes only on fields that are supported by the Curatescape theme.')),
                ),
            
            __('Theme Option settings')=>
            array(
                array('name'=>'cah_theme_options_accordion','label'=>__('Enhance form'),'explanation'=>__('Display theme options as a collapsible list of sections.')),
                ),
                
            __('Advanced settings')=>
            array(
                array('name'=>'cah_theme_options_batch_convert','label'=>__('Migration Helper'),'explanation'=>__('Display an option to batch-convert existing items to the Curatescape Story item type, migrating content from Dublin Core fields to related custom Item Type fields. <br><br><strong>Note:</strong> this option is <em>not recommended</em> for most projects. Please contact your project manager for more information.')),
                ),
                
    );
    
foreach ($options as $section_title=>$infos) {
    ?>
<div class="field">

    <h2><?php echo $section_title; ?></h2>

    <?php foreach ($infos as $info) { ?>

    <div id="<?php echo $info['name']; ?>" class="two columns alpha">
        <label for="<?php echo $info['name']; ?>"><?php echo $info['label']; ?></label>
    </div>

    <div class="inputs five columns omega">

        <?php echo get_view()->formCheckbox(
        $info['name'],
        true,
        array('checked'=>(boolean)get_option($info['name']))
    ); ?>

        <p class="explanation"><?php echo $info['explanation']; ?></p>

    </div>

    <?php } ?>

</div>
<?php
} ?>