<?php

// Jalankan ini agar Laravel tidak mencari folder storage yang dikunci
putenv('APP_CONFIG_CACHE=/tmp/config.php');
putenv('APP_ROUTES_CACHE=/tmp/routes.php');
putenv('VIEW_COMPILED_PATH=/tmp');

require __DIR__ . '/../public/index.php';
