<?php

use Illuminate\Support\Facades\Artisan;

// Pastikan jalur ini benar mengarah ke public/index.php
require __DIR__ . '/../public/index.php';

// Tambahkan ini di bawah require untuk memaksa refresh cache
Artisan::call('config:clear');
Artisan::call('view:clear');
