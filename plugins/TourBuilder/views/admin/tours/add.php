<?php
echo head( array( 'title' => 'Add Tour', 'content_class' => 'vertical-nav',
		'bodyclass' => 'tours primary add-tour-form' ) );
echo flash();
?>

<form method="post" enctype="multipart/form-data" id="tour-form" action="">
  
  <?php include( 'form.php' ); ?>
  
  <section class="three columns omega">
    <div id="save" class="panel">
      <?php echo $this->formSubmit( 'submit', __('Add Tour'),
	array( 'id' => 'save-changes',
		'class' => 'submit big green button' ) ); ?>
    </div>

    <div id="public-featured">
      <?php if( is_allowed( 'TourBuilder_Tours', 'makePublic' ) ): ?>
      <div class="checkbox">
        <label for="public">
          <?php echo __('Public'); ?>:
        </label>
        <div class="checkbox">
          <?php echo $this->formCheckbox( 'public', $tour->public,
	array(), array( '1', '0' ) ); ?>
        </div>
      </div>
      <?php endif; ?>

      <?php if( is_allowed( 'TourBuilder_Tours', 'makeFeatured' ) ): ?>
      <div class="checkbox">
        <label for="featured">
          <?php echo __('Featured'); ?>:
        </label>
        <div class="checkbox">
          <?php echo $this->formCheckbox( 'featured', $tour->featured,
	array(), array( '1', '0' ) ); ?>
        </div>
      </div>
      <?php endif; ?>

    </div>

  </section>

</form>

<?php echo foot(); ?>
