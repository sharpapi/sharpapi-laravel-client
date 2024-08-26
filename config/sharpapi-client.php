<?php

declare(strict_types=1);

return [
    'api_key' => env('SHARP_API_KEY'),
    'base_url' => env('SHARP_API_BASE_URL', 'https://sharpapi.com/api/v1'), // as ENV is mock server needed
    // how long (in seconds) the client should wait in polling mode for results
    'api_job_status_polling_wait' => env('SHARP_API_JOB_STATUS_POLLING_WAIT', 180),
    // how many seconds the client should wait between each result request
    // usually Retry-After header is used (default 10s), this value won't have an effect unless
    // api_job_status_use_polling_interval is set to TRUE
    'api_job_status_polling_interval' => env('SHARP_API_JOB_STATUS_POLLING_INTERVAL', 10),
    'api_job_status_use_polling_interval' => env('SHARP_API_JOB_STATUS_USE_POLLING_INTERVAL', false),
    // for affiliate program members use
];
