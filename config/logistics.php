<?php

/*
|--------------------------------------------------------------------------
| Logistics settings (IBL CART 24)
|--------------------------------------------------------------------------
| SMS, courier (Steadfast) and database-backup settings.
| Everything is controlled from .env. Defaults are SAFE: SMS only writes to
| the log, the courier is disabled, so nothing real is sent until you add keys.
*/

return [

    'sms' => [
        // log = write to storage/logs only (default) | bulksmsbd = real SMS
        'driver'           => env('SMS_DRIVER', 'log'),
        'api_key'          => env('SMS_API_KEY', ''),
        'sender_id'        => env('SMS_SENDER_ID', ''),
        'url'              => env('SMS_API_URL', 'https://bulksmsbd.net/api/smsapi'),
        'on_order_placed'  => (bool) env('SMS_ON_ORDER_PLACED', true),
        'on_status_change' => (bool) env('SMS_ON_STATUS_CHANGE', true),
    ],

    'steadfast' => [
        'enabled'    => (bool) env('STEADFAST_ENABLED', false),
        'base_url'   => env('STEADFAST_BASE_URL', 'https://portal.packzy.com/api/v1'),
        'api_key'    => env('STEADFAST_API_KEY', ''),
        'secret_key' => env('STEADFAST_SECRET_KEY', ''),
        'timeout'    => (int) env('STEADFAST_TIMEOUT', 20),
    ],

    'backup' => [
        'path'      => storage_path('app/backups'),
        'keep_days' => (int) env('BACKUP_KEEP_DAYS', 14),
        'time'      => env('BACKUP_TIME', '03:00'),
    ],

];
