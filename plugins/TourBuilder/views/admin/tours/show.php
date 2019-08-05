<?php
$tourTitle = strip_formatting( tour( 'title' ) );
if( $tourTitle != '' && $tourTitle != '[Untitled]' ) {
	$tourTitle = ': &quot;' . $tourTitle . '&quot; ';
} else {
	$tourTitle = '';
}
$tourTitle = 'Tour #' . tour( 'id' ) . $tourTitle;

echo head( array( 'title' => $tourTitle,
		'bodyclass' => 'show','bodyid'=>'tour' ) );
echo flash();
?>

<section class="seven columns alpha">

  <?php if( metadata( 'tour', 'Title' ) ): ?>
  <div id="tour-title" class="element">
    <h2>Title</h2>
    <div class="element-text">
      <?php echo nls2p( metadata( 'tour', 'Title' ) ); ?>
    </div>
  </div>
  <?php endif; ?>

  <?php if( metadata( 'tour', 'Credits' ) ): ?>
  <div id="tour-credits" class="element">
    <h2>Credits</h2>
    <div class="element-text">
      <?php echo metadata( 'tour', 'Credits' ); ?>
    </div>
  </div>
  <?php endif; ?>

  <?php if( metadata( 'tour', 'Description' ) ): ?>
  <div id="tour-description" class="element">
    <h2>Description</h2>
    <div class="element-text">
      <?php echo nls2p( metadata( 'tour', 'Description' ) ); ?>
    </div>
  </div>
  <?php endif; ?>

  <?php if( metadata( 'tour', 'postscript_text' ) ): ?>
  <div id="postscript_text" class="element">
    <h2>Postscript Text</h2>
    <div class="element-text">
      <?php echo '<em>'.htmlspecialchars_decode(metadata( 'tour', 'postscript_text' )).'</em>'; ?>
    </div>
  </div>
  <?php endif; ?>

  
  <?php
$items = $tour->getItems();
if( $tour->getItems() ): ?>
  <div id="tour-items" class="element">
    <h2>Items</h2>
    <div class="element-text">
      <ul>
        <?php foreach( $items as $item ):
		set_current_record( 'item', $item, true );
?>
        <li>
          <?php echo link_to_item(); ?>
        </li>
        <?php endforeach; ?>
      </ul>
    </div>
  </div>
  <?php endif; ?>

</section>

<section class="three columns omega">
  <div id="edit" class="panel">
    <?php if( is_allowed( 'TourBuilder_Tours', 'edit' ) ): ?>
    <a href="<?php echo url( array( 'action' => 'edit', 'id' => $tour->id ) ); ?>"
       class="edit big green button">
      <?php echo __('Edit'); ?>
    </a>
    <?php endif; ?>

    <a href="<?php echo html_escape( public_url( 'tours/show/' . $tour->id ) ); ?>"
       class="big blue button" target="_blank">
      <?php echo __('View Public Page'); ?>
    </a>

    <?php if( is_allowed( 'TourBuilder_Tours', 'delete' ) ): ?>
    <?php echo link_to_tour( __('Delete'),
		array( 'class' => 'delete-confirm big red button' ),
		'delete-confirm' ); ?>
    <?php endif; ?>
  </div>

  <div class="public-featured panel">
    <p>
      <span class="label">
        <?php echo __('Public'); ?>:
      </span>
      <?php echo ($tour->public) ? __('Yes') : __('No'); ?>
    </p>
    <p>
      <span class="label">
        <?php echo __('Featured'); ?>:
      </span>
      <?php echo ($tour->featured) ? __('Yes') : __('No'); ?>
    </p>
  </div>
</section>

<?php echo foot(); ?>
