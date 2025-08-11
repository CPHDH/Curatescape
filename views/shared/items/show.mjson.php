<?php
echo get_view()->CuratescapeCache()->Config(option('curatescape_json_cache'));
echo json_encode( get_view()->JsonItem($item , true));