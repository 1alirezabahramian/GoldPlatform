<?php

return [
    'request_logging_enabled' => env('OBSERVABILITY_REQUEST_LOGGING_ENABLED', true),

    'slow_request_threshold_ms' => (int) env('OBSERVABILITY_SLOW_REQUEST_THRESHOLD_MS', 1000),

    'log_successful_requests' => env('OBSERVABILITY_LOG_SUCCESSFUL_REQUESTS', false),
];
