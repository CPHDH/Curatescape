<?php
head( array( 'title' => 'Browse Tours', 'content_class' => 'horizontal-nav',
   'bodyclass' => 'tours primary browse-tours' ) );
?>
<h1>Browse Tours (<?php echo $total_records; ?> total)</h1>

<?php if( has_permission( 'TourBuilder_Tours', 'add' ) ): ?>
<p id="add-tour" class="add-button">
   <a class="add"
      href="<?php echo $this->url( array( 'action' => 'add' ) ); ?>">Add a Tour</a>
</p>
<?php endif; ?>

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
                  <?php if( has_permission( 'TourBuilder_Tours', 'edit' ) ): ?>
                  <th scope="col">Edit?</th>
                  <?php endif; ?>
               </tr>
            </thead>
            <tbody>
               <?php $key = 0; ?>
               <?php while( loop_tours() ): ?>
               <tr class="tours <?php if( ++$key%2==1) echo 'odd'; else echo 'even'; ?>">
                  <td scope="row"><?php echo tour( 'id' ); ?></td>
                  <td scope="row"><a href="<?php
                     echo $this->url( array(
                        'action' => 'show', 'id' => tour( 'id' ) ) );
                     ?>"><?php echo tour( 'title' ); ?></a></td>
                  <?php if( has_permission( 'TourBuilder_Tours', 'edit' ) ): ?>
                  <td><a class="edit" href="<?php echo $this->url(
                     array( 'action' => 'edit', 'id' => tour( 'id' ) ) ); ?>"
                     >Edit</a>
                  <?php endif; ?>
               </tr>
               <?php endwhile; ?>
            </tbody>
         </table>
      <?php endif; ?>
   <?php endif; ?>
</div>

<?php foot();
