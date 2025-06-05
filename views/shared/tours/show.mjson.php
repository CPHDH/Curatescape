<?php
echo cacheConfig(option('curatescape_json_cache'));
echo json_encode( get_view()->JsonTour( $tour, true ) );