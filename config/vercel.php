<?php

// Otomatis arahkan cache dan views ke /tmp saat di Vercel
if (env('VERCEL')) {
    config([
        'view.compiled' => '/tmp/storage/framework/views',
        'cache.stores.file.path' => '/tmp/storage/framework/cache',
        'session.files' => '/tmp/storage/framework/sessions',
    ]);
}
