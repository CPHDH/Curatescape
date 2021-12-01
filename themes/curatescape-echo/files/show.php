<?php
    $fileTitle = metadata('file', array('Dublin Core', 'Title')) ? strip_formatting(metadata('file', array('Dublin Core', 'Title'))) : __('Untitled');

    echo head(array('file'=>$file, 'maptype'=>'none','bodyid'=>'file','bodyclass'=>'show item-file','title' => $fileTitle ));
?>
<div id="content" role="main">

    <article class="page file show">

        <header id="file-header">
            <h2 class="title"><?php echo $fileTitle; ?></h2>
            <?php
            $info = array();
            ($fileid=metadata('file', 'id')) ? $info[]='<span class="file-id">ID: '.$fileid.'</span>' : null;
            ($record=get_record_by_id('Item', $file->item_id)) ? $info[]=__('This file appears in').': '.link_to_item(strip_tags($title), array('class'=>'file-appears-in-item'), 'show', $record) : null;
            echo count($info) ? '<span id="file-header-info" class="byline">'.implode(" | ", $info).'</span>' : null;
            
            ?>
        </header>
        
        <div id="item-primary" class="show">
            <div class="separator"></div>

            <figure id="single-file-show">
                <?php echo rl_single_file_show($file); ?>
                <?php 
                $caption = array();
                (($desc=metadata('file', array('Dublin Core','Description'))) ? $caption[] = $desc : null);
                ($creators=metadata('file', array('Dublin Core','Creator'), true)) ? $caption[] = '<span class="file-creator">'.__('Creator').': '.implode(', ', $creators).'</span>' : null;
                ($date=metadata('file', array('Dublin Core','Date'), true)) ? $caption[] = '<span class="file-date">'.__('Date').': '.implode(', ', $date).'</span>' : null; 
                ($source=metadata('file', array('Dublin Core','Source'))) ? $caption[] = '<span class="file-source">'.__('Source').': '.$source.'</span>' : null;
                ($rights = metadata('file', array('Dublin Core','Rights'))) ? $caption[] = __('Rights').': '.$rights : null;
                echo count($caption) ? '<figcaption class="single-file-caption">'.implode(" | ", $caption).'</figcaption>' : null;
                ?>
            </figure>
            
            <a class="button" href="<?php echo file_display_url($file,'original');?>" download="<?php echo $file->id.'-'.$fileTitle;?>"><?php echo __('Download Original File');?></a>
            
            
            <?php if($m=rl_file_metadata_additional()){
                echo '<div class="separator"></div><div class="additional_file_metadata">'. rl_file_metadata_additional().'</div>';
            }?>
            
 
            
            <div class="separator"></div>
            <div class="byline"><?php echo ($record=get_record_by_id('Item', $file->item_id)) ? __('"%s" appears in',strip_tags($fileTitle)).': '.link_to_item(strip_tags($title), array('class'=>'file-appears-in-item'), 'show', $record) : null;?></div>

        </div><!-- end primary -->

    </article>

</div> <!-- end content -->


<?php echo foot(); ?>