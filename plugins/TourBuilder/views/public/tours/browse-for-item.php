<?php
$tourTitle = strip_formatting( tour( 'title' ) );
if( $tourTitle != '' && $tourTitle != '[Untitled]' ) {
   $tourTitle = ': &quot;' . $tourTitle . '&quot; ';
} else {
   $tourTitle = '';
}
$tourTitle = 'Add Item To Tour #' . tour( 'id' ) . $tourTitle;

head( array( 'title' => $tourTitle, 'content_class' => 'vertical-nav',
   'bodyclass' => 'tours primary' ) );
?>
<table id="items" class="simple" cellspacing="0" cellpadding="0">
   <thead>
      <tr>
         <th scope="col">ID</th>
         <th scope="col">Item</th>
         <th scope="col">Add?</th>
      </tr>
   </thead>
   <tbody>
      <?php $key = 0; ?>
      <?php foreach( $this->items as $item ): ?>
      <tr class="items <?php echo (++$key%2 == 1) ? 'odd' : 'even'; ?>">
         <td scope="row"><?php echo $item->id ?></td>
         <td scope="row">
            <a href="<?php
               echo uri( array( 'module' => '', 'controller' => 'items',
                  'action' => 'show', 'id' => $item->id ) );
            ?>"><?php
               echo $this->itemMetadata( $item, 'Dublin Core', 'Title' );
            ?></a>
         </td>
         <td scope="row">
            <a class="add" href="<?php
            echo uri( array( 'action' => 'addItem',
               'tour' => $tour->id, 'item' => $item->id ) );
            ?>">Add</a>
         </td>
      </tr>
      <?php endforeach; ?>
   </tbody>
</table>

<?php foot();
