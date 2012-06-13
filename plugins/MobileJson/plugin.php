<?php
add_plugin_hook( 'install', 'json_output_install' );

add_filter( 'define_response_contexts', 'json_output_response_context' );
add_filter( 'define_action_contexts', 'json_output_action_context' );

function json_output() {}

function json_output_action_context( $context, $controller ) {
   if( $controller instanceof ItemsController ) {
      $context['browse'][] = 'mobile-json';
      $context['show'][] = 'mobile-json';
   }
   elseif( is_a( $controller, 'TourBuilder_ToursController' ) ) {
      $context['browse'][] = 'mobile-json';
      $context['show'][] = 'mobile-json';
   }

   return $context;
}

function json_output_response_context( $context ) {
   $context['mobile-json'] = array(
      'suffix'  => 'mjson', 
      'headers' => array(
         'Content-Type' => 'application/json',
      ),
   );
    
    return $context;
}
