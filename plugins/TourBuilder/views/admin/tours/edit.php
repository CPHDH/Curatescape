<?php
$tourTitle = strip_formatting( tour( 'title' ) );
if( $tourTitle != '' && $tourTitle != '[Untitled]' ) {
   $tourTitle = ': &quot;' . $tourTitle . '&quot; ';
} else {
   $tourTitle = '';
}
$tourTitle = 'Edit Tour #' . tour( 'id' ) . $tourTitle;

head( array( 'title' => $tourTitle, 'content_class' => 'vertical-nav',
   'bodyclass' => 'tours primary' ) );
?>

<script type="text/javascript" charset="utf-8">
   document.observe( 'dom:loaded', function() {
      new Control.Tabs( 'section-nav' );
   });
</script>

<h1>Add a Tour</h1>
<?php include( 'form-tabs.php' ); ?>
<div id="primary">
   <form method="post" enctype="multipart/form-data" id="tour-form" action="">
      <?php include( 'form.php' ); ?>
      <div>
         <input type="submit" name="submit" class="submit submit-medium"
            id="save-changes" value="Save Changes" />
      </div>

   </form>
  
    <p id="delete_tour_link">
	<?php echo delete_button(null, 'delete-tour', 'Delete this Tour'); ?>
	</p>
	 
</div>
<?php foot();
