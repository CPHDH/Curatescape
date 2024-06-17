<?php
echo head( array( 'title' => 'Add Tour', 
  'content_class' => 'vertical-nav',
  'bodyid'=>'tour',
  'bodyclass' => 'tours primary add-tour-form add' 
));
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
      
      <?php if ( is_allowed('TourBuilder_Tours', 'makePublic') ): ?>
      <div class="field panel ordinal">
          <?php echo $this->formLabel( 'ordinal', __('Custom Order') ); ?>
          <?php echo $this->formText( 'ordinal', $tour->ordinal ); ?>
          <p class="explanation"><?php echo __('Optional: Enter a number greater than 0 to customize the order of this tour. Enter 0 to use the default order.');?></p>
      </div>
      <?php endif; ?>

      <div id="public-featured">
      <?php if( is_allowed( 'TourBuilder_Tours', 'makePublic' ) ): ?>
        <div class="checkbox">
          <label for="public">
            <?php echo __('Public'); ?>:
          </label>
          <div class="checkbox">
            <?php echo $this->formCheckbox( 'public', 
            $tour->public,
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
            <?php echo $this->formCheckbox( 'featured', 
            $tour->featured,
            array(), array( '1', '0' ) ); ?>
          </div>
        </div>
      <?php endif; ?>
      </div>
    </section>

</form>

<?php echo foot(); ?>
