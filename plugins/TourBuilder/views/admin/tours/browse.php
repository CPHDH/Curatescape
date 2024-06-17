<?php
if (isset($_GET['featured']) && $_GET['featured'] == 1) {
  $pageTitle = __('Featured Tours (%s total)', $total_results);
}elseif(isset($_GET['tags']) ){
  $pageTitle = __('Tours tagged "%s"', htmlspecialchars($_GET['tags'])) . ' ' . __('(%s total)', $total_results );
}else{
  $pageTitle = __('All Tours') . ' ' . __('(%s total)', $total_results );
}

$editable = is_allowed( 'TourBuilder_Tours', 'edit' );
$addUrl = url( array( 'action' => 'add' ) );
if(has_tours()){
  $tours = active_sort_tours($tours);
}
echo head( array( 'title' => $pageTitle, 'bodyid'=>'tour','bodyclass' => 'tours browse' ) );
echo flash();
?>

<?php if( $total_results ): ?>

<div class="tour-actions">
  <?php if( is_allowed( 'TourBuilder_Tours', 'add' ) ): ?>
    <a class="add button small green" href="<?php echo $addUrl; ?>">
    <?php echo __('Add a Tour'); ?>
    </a>
  <?php endif; ?>
</div>

<div id="primary">
<?php echo flash();
  if( has_tours() ):?>
    <?php if( has_tours_for_loop() ): ?>
      <table id="tours" class="simple" cellspacing="0" cellpadding="0">
        <thead>
          <tr>
            <?php
            $browseHeadings[__('Title')] = 'title';
            $browseHeadings[__('ID')] = 'id';
            $browseHeadings[__('Custom Order')] = 'ordinal';
            echo browse_sort_links($browseHeadings, array('link_tag' => 'th scope="col"', 'list_tag' => ''));
            ?>
          </tr>
        </thead>
      <tbody>
      <?php
      $key = 0;
      foreach( $tours as $tour ):
        $oddness = ((++$key % 2) == 1) ? 'odd' : 'even';
        $showUrl = url( array( 'action' => 'show','id' => $tour->id ), 'tourAction' );
        $editUrl = url( array( 'action' => 'edit','id' => $tour->id ), 'tourAction' );
        ?>
        <tr class="tours <?php echo $oddness; ?>">
          <td scope="row" <?php echo ($tour->featured) ? 'class="featured"' : null?> >
            <span class="title"><a href="<?php echo $showUrl; ?>">
              <?php echo $tour->title; ?>
            </a>
            <?php if(metadata( $tour, 'Featured' ) == 1):?>
            <div class="featured-icon">
                <span class="featured" aria-hidden="true" title="<?php echo __('Featured'); ?>"></span>
                <span class="sr-only icon-label"><?php echo __('Featured'); ?></span>
            </div>
            <?php endif;?>
            <?php if(metadata( $tour, 'Public' ) !== 1): ?>
                <span class="private"><?php echo __('(Private)'); ?></span>
            <?php endif; ?>
            </span>
            <div class="action-links group">
              <a href="javascript:void(0)" class="details-link"><?php echo __('Details');?></a> 
              <?php if($editable):?>
                <span class="middot">&middot;</span>
                <a href="<?php echo $editUrl;?>" class="edit"><?php echo __('Edit')?></a>
              <?php endif;?>
              <div class="details admin-tour-browse-meta hidden">
                <div><strong><?php echo __('Credits');?></strong>:&nbsp;<?php echo metadata( $tour, 'Credits' ) ? metadata( $tour, 'Credits' ) : __('None');?></div>&nbsp;&middot;&nbsp;
                <div><strong><?php echo __('Locations');?></strong>: <?php echo count($tour->Items);?></div>&nbsp;&middot;&nbsp;
                <div><strong><?php echo __('Tags');?></strong>:&nbsp;<?php echo ($tags = tag_string($tour, 'tours')) ? $tags : __('None'); ?></div>
              </div>
            </div>
          </td>
          <td scope="row"><?php echo $tour->id; ?></td>
          <td scope="row"><?php echo $tour->ordinal ? $tour->ordinal : __('None'); ?></td>
        </tr>
      <?php endforeach; ?>
      </tbody>
   </table>

   <script>
    let btns = document.querySelectorAll('a.details-link');
    btns.forEach((b)=>{
      b.addEventListener('click',(a)=>{
        let details = a.target.parentElement.lastElementChild;
        details.classList.toggle('hidden')
      });
    });
   </script>

  <?php endif; ?>
<?php endif; ?>
</div>
<?php else: ?>
  <?php if( total_records( 'Tour' ) === 0 ): ?>
    <h2><?php echo __('You have no tours.'); ?></h2>
    <?php if( is_allowed( 'TourBuilder_Tours', 'add' ) ): ?>
      <p><?php echo __('Get started by adding your first tour.'); ?></p>
      <a class="add big green button" href="<?php echo $addUrl; ?>">
        <?php echo __('Add a Tour'); ?>
      </a>
    <?php endif; ?>
  <?php else: ?>
    <p>
      <?php echo __('The query searched %s tours and returned no results.',total_records( 'Tour' ));
      echo __('Would you like to %s?', link_to_tour_search( __('refine your search') ) ); ?>
    </p>
  <?php endif; ?>
<?php endif; ?>
<?php echo foot(); ?>
