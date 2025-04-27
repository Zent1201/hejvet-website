<?php

return [
    'paths' => ['api/*', 'storage/uploads/users/*','sanctum/csrf-cookie'],  // Tambahkan jalur penyimpanan
    'allowed_methods' => ['*'],  // Mengizinkan semua metode HTTP (GET, POST, PUT, DELETE)
    'allowed_origins' => ['*'],  // Mengizinkan semua origin (sebaiknya atur domain tertentu jika sudah produksi)
    'allowed_origins_patterns' => [],
    'allowed_headers' => ['*'],  // Mengizinkan semua header
    'exposed_headers' => [],
    'max_age' => 0,
    'supports_credentials' => false,
];
