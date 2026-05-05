<?php

// Pastikan folder-folder yang dibutuhkan Laravel ada di /tmp
$storageFolders = [
    '/tmp/storage/framework/views',
    '/tmp/storage/framework/cache',
    '/tmp/storage/framework/sessions',
    '/tmp/bootstrap/cache',
];

foreach ($storageFolders as $folder) {
    if (!is_dir($folder)) {
        mkdir($folder, 0777, true);
    }
}

// Redirect Laravel untuk menggunakan folder /tmp tersebut
putenv('VIEW_COMPILED_PATH=/tmp/storage/framework/views');
putenv('CACHE_DRIVER=file');
putenv('SESSION_DRIVER=file');

require __DIR__ . '/../public/index.php';
