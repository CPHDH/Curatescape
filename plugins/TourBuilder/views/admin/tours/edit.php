<?php
$tourTitle = strip_formatting( tour( 'title' ) );
if( $tourTitle != '' && $tourTitle != '[Untitled]' ) {
  $tourTitle = ': &quot;' . $tourTitle . '&quot; ';
} else {
  $tourTitle = '';
}
$tourTitle = 'Edit Tour #' . tour( 'id' ) . $tourTitle;

echo head( array( 'title' => $tourTitle, 'content_class' => 'vertical-nav', 'bodyclass' => 'tours edit','bodyid'=>'tour' ) );
echo flash();
?>

<form method="post" enctype="multipart/form-data" id="tour-form" action="">
  <?php include "form.php" ?>

  <section class="three columns omega" id="tour-editor-control-panel">
    <div id="save" class="panel">
      <?php echo $this->formSubmit( 'submit', __('Save Changes'), array( 'id' => 'save-changes','class' => 'submit big green button' ) ); ?>
      <a href="<?php echo html_escape( public_url( 'tours/show/' . $tour->id ) ); ?>"
         class="big blue button" target="_blank">
        <?php echo __('View Public Page'); ?>
      </a>
      <?php echo link_to_tour( __('Delete'), array( 'class' => 'delete-confirm big red button' ),'delete-confirm' ); ?>
    </div>

    <?php if ( is_allowed('TourBuilder_Tours', 'makePublic') ): ?>
    <div class="field panel ordinal">
        <?php echo $this->formLabel( 'ordinal', __('Custom Order') ); ?>
        <?php echo $this->formText( 'ordinal', $tour->ordinal ); ?>
        <p class="explanation"><?php echo __('Optional: Enter a number greater than 0 to customize the order of this tour. Enter 0 to use the default order.');?></p>
    </div>
    <?php endif; ?>

    <div id="public-featured">
      <?php if ( is_allowed('TourBuilder_Tours', 'makePublic') ): ?>
        <div class="checkbox">
          <label for="public">
            <?php echo __('Public'); ?>:
          </label>
          <div class="checkbox">
            <?php echo $this->formCheckbox(
              'public', $tour->public,
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
            <?php echo $this->formCheckbox('featured', $tour->featured,array(), array( '1', '0' ) ); ?>
          </div>
        </div>
      <?php endif; ?>
    </div>
  </section>
</form>

<?php echo foot(); ?>
