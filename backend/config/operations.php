<?php

return [
    'slow_query_ms' => max(1, (int) env('SLOW_QUERY_MS', 500)),
];
