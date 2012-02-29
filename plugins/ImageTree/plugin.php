<?php

add_plugin_hook( 'define_routes', 'imagetree_define_routes' );

function imagetree_define_routes( $router )
{
   $router->addRoute(
      'image_fragement',
      new Zend_Controller_Router_Route(
         'files/download/:id/fragment/:scale/:x/:y',
         array(
            'module' => 'image-tree',
            'controller' => 'Tree',
            'action' => 'get'
         ),
         array()
      )
   );
}
