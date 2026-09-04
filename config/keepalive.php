<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Keep-alive Token
    |--------------------------------------------------------------------------
    |
    | Token untuk endpoint GET /api/keepalive yang dipanggil GitHub Actions
    | agar service demo di Render tetap warm pada jam kerja dan Aiven MySQL
    | menerima aktivitas query. Harus sama dengan secret KEEPALIVE_TOKEN di
    | repository GitHub controller. Biarkan kosong untuk menonaktifkan
    | endpoint (semua request akan ditolak 404).
    |
    */

    'token' => env('KEEPALIVE_TOKEN'),

];
