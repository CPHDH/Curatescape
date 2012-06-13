<?php

require_once 'Omeka/Controller/Action.php';

class ImageTree_TreeController extends Omeka_Controller_Action
{
   public function init()
   {
      $this->_modelClass = 'File';

      $this->img_caching = 1;
   }

   # http://localhost/~greyson/omeka/files/download/1/fullsize
   public function getAction()
   {
      # Get the parameters
      $file = $this->findById();
      $scale = $this->getRequest()->getParam( 'scale' );
      $x = $this->getRequest()->getParam( 'x' );
      $y = $this->getRequest()->getParam( 'y' );

      $f = $file->getWebPath( 'archive' );
      $this->_helper->viewRenderer->setNoRender();
      if( $this->img_caching )
      {
         # create (or retrieve) tile filename
         $a = $this->createTile( $f, $scale, $x, $y );
         $path = $this->cachepath() . '/' . $a;

         # Redirect to the tiled file
         header( 'Location: ' . $path );
         echo "Redirecting to: $path\n";
      }
      else
      {
         # Start rendering.
         header( 'Content-type: ' . $file->getMimeType() );
         $this->createTile( $f, $scale, $x, $y );
      }
   }

   function cachepath()
   {
      return WEB_FILES . '/fragments';
   }

   function cachedir()
   {
      return FILES_DIR . DIRECTORY_SEPARATOR . "fragments";
   }

   function get_resolution( $image )
   {
      # Identify the width and height of the image
      $cmdimg = escapeshellarg( $image );
      $i = chop( `identify -format "%w %h" $cmdimg` );

      # Parse them into a usable array of integers
      $a = explode( " ", $i );
      $r = array( intval($a[0]), intval($a[1]) );

      return $r;
   }

   function createTile( $image, $scale, $x, $y )
   {
      $unitsize = 256;

      if( $this->img_caching )
      {
         $cachedir = $this->cachedir();

         # Build the resulting file path
         $i = pathinfo( $image );
         $e = $i[ 'extension' ];
         $b = $i[ 'filename' ];
         $d = $this->cachedir() . DIRECTORY_SEPARATOR . $b;
         $out = $d . DIRECTORY_SEPARATOR . "${scale}_${x}_${y}.$e";
         $web_address= sprintf( '%s/%d_%d_%d.%s', $b, $scale, $x, $y, $e );

         # Build directories (if needed) and bail if the image exists
         if( ! is_dir( $d ) ) {
            if( ! is_dir( $cachedir ) ) { mkdir( $cachedir ); }
            mkdir( $d );
         } else if( is_file( $out ) ) {
            return $web_address;
         }
      }

      # Ensure scale is divisor
      if( $scale < 1 )
      {
         $scale = 1 / $scale;
      }

      # Prepare for image conversion
      $im_command = array();
      $im_command[] = sprintf( 'convert %s', escapeshellarg( $image ) );

      # Apply image scaling
      if( $scale > 1 )
      {
         #$r = sprintf( '-resize $f%%', 100 / $scale );
         #$im_command[] = $r;
         $r = $this->get_resolution( $image );
         $sg = array( $r[0] / $scale, $r[1] / $scale );

         $c = sprintf( '-scale %dx%d', $sg[0], $sg[1] );
         $im_command[] = $c;
      }

      # Basic crop for image position
      $s = $unitsize;
      $crop = sprintf( '-crop %dx%d+%d+%d', $s, $s, $x * $s, $y * $s );
      $im_command[] = $crop;

      if( $this->img_caching )
      {
         # Execute the command to a file
         $im_command[] = escapeshellarg( $out );
         $c = join( ' ', $im_command );
         `$c`;

         return $web_address;
      }
      else
      {
         # Execute the command to stdout
         $im_command[] = '-';
         $c = join( ' ', $im_command );
         system( $c );
      }
   }
}
