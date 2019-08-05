<?php
$pageTitle = __('Browse Tours') . ' ' . __('(%s total)', $total_results );
$editable = is_allowed( 'TourBuilder_Tours', 'edit' );
$addUrl = url( array( 'action' => 'add' ) );

echo head( array( 'title' => $pageTitle, 'bodyid'=>'tour','bodyclass' => 'tours browse' ) );
echo flash();
?>

<?php if( $total_results ): ?>

<div class="table-actions">
  <?php if( is_allowed( 'TourBuilder_Tours', 'add' ) ): ?>
  <a class="add button small green" href="<?php echo $addUrl; ?>">
    <?php echo __('Add a Tour'); ?>
  </a>
  <?php endif; ?>
</div>

<div id="primary">
		<?php
		echo flash();
		if( has_tours() ):
		?>
		<div class="pagination"><?php echo pagination_links(); ?></div>
		<?php if( has_tours_for_loop() ): ?>
		 <table id="tours" class="simple" cellspacing="0" cellpadding="0">
		    <thead>
		       <tr>
		          <th scope="col">ID</th>
		          <th scope="col">Title</th>
		          <?php if( $editable ): ?>
		          <th scope="col">Edit?</th>
		          <?php endif; ?>
		       </tr>
		    </thead>
		    <tbody>
               
               <?php $key = 0;

				foreach( $tours as $tour ):
					$oddness = ((++$key % 2) == 1) ? 'odd' : 'even';
					$showUrl = url( array( 'action' => 'show','id' => $tour->id ), 'tourAction' );
					$editUrl = url( array( 'action' => 'edit','id' => $tour->id ), 'tourAction' );
				?>
               
               <tr class="tours <?php echo $oddness; ?>">
                  <td scope="row"><?php echo $tour->id; ?></td>
                  <td scope="row" <?php echo ($tour->featured) ? 'class="featured"' : null?>>
                    <a href="<?php echo $showUrl; ?>">
                      <?php echo $tour->title; ?>
                    </a>
                    <?php echo '<div class="admin-tour-browse-meta"><strong>Locations</strong>: '.count($tour->Items).( metadata( $tour, 'Credits' ) ? ' &middot; <strong>Credits</strong>: '.metadata( $tour, 'Credits' ).'' : null ).'<br><strong>Public</strong>: '.( (metadata( $tour, 'Public' ) == 1) ? 'Yes' : 'No' ).' &middot; <strong>Featured</strong>: '.( (metadata( $tour, 'Featured' ) == 1) ? 'Yes' : 'No' ).'<div>';?>

                    <?php // if( $tour->hasImage() ){ echo '<i class="fa fa-camera"></i>';} ?>

                  </td>
                  <?php if( $editable ): ?>
                  <td>
                    <a class="edit" href="<?php echo $editUrl; ?>">
                      <?php echo __('Edit'); ?>
                    </a>
                  </td>
                  <?php endif; ?>
               </tr>
               <?php endforeach; ?>
            </tbody>
         </table>
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
    <p><?php echo __('The query searched %s tours and returned no results.',
		total_records( 'Tour' ));
echo __('Would you like to %s?',
	link_to_tour_search( __('refine your search') ) ); ?></p>
  <?php endif; ?>

<?php endif; ?>

<?php echo foot(); ?>
