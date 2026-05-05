<?php

// 1. Buat folder temporary secara paksa
$tmpFolder = '/tmp/storage/framework/views';
if (!is_dir($tmpFolder)) {
    mkdir($tmpFolder, 0777, true);
}

// 2. Set environment variable agar Laravel tahu harus menulis ke mana
putenv("VIEW_COMPILED_PATH=$tmpFolder");

// 3. Panggil aplikasi utama
require __DIR__ . '/../public/index.php';
