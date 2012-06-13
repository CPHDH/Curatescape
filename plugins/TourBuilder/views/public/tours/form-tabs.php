<?php
$tabs = array();
foreach( array( 'Items', 'Tour Info' ) as $tabName ) {
   ob_start();
   switch( $tabName ) {
   case 'Tour Info':
      require 'metadata-form.php';
      break;
   case 'Items':
      require 'items-form.php';
      break;
   }
   $tabs[$tabName] = ob_get_contents();
   ob_end_clean();
}
?>

<!-- Create the sections for the various element sets -->
<ul id="section-nav" class="navigation tabs">
   <?php
   foreach ($tabs as $tabName => $tabContent):
      if (!empty($tabContent)): // Hide tabs with no content
      ?>
   <li><a href="#<?php echo html_escape(text_to_id($tabName) . '-metadata'); ?>"
      ><?php echo html_escape($tabName); ?></a>
   </li>
      <?php
      endif;
   endforeach;
   ?>
</ul>
