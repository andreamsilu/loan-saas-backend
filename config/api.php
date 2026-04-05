<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Per-tenant API rate limit
    |--------------------------------------------------------------------------
    |
    | Maximum HTTP requests per minute per resolved tenant (see TenantMiddleware).
    |
    */

    'tenant_rate_limit_per_minute' => (int) env('API_RATE_LIMIT_PER_MINUTE', 120),

];
