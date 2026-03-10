<?php
echo get_view()->CuratescapeCache()->Config(option('curatescape_json_cache'));
$data = get_view()->JsonTour()->JsonToursShow($tour, true);
if ($data) echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);