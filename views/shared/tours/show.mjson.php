<?php
echo get_view()->Cache()->Config(option('curatescape_json_cache'));
echo json_encode(get_view()->JsonTour($tour, true ));