<?php

return [
    'truncate_stats_days' => 6 * 30,
    'dns_server' => env('DNS_SERVER', null),
    'schools' => [
        'Engineering',
        'Maths&Stats',
        'Compsci',
    ],
    'api_key' => env('API_KEY', \Illuminate\Support\Str::random(32)),
];
