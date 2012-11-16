<?php
head( array( 'title' => 'Add Tour', 'content_class' => 'vertical-nav',
   'bodyclass' => 'tours primary add-tour-form' ) );
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
            id="add_tour" value="Add Tour" />
      </div>
   </form>
</div>
<?php foot();
