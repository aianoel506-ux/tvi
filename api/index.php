<?php
header('Content-Type: text/plain');
echo "PHP Version: " . phpversion() . "\n";
echo "Current Dir: " . getcwd() . "\n";
echo "Dirname __DIR__: " . dirname(__DIR__) . "\n";
echo "Root index exists? " . (file_exists(dirname(__DIR__) . '/index.php') ? 'YES' : 'NO') . "\n";
echo "System dir exists? " . (is_dir(dirname(__DIR__) . '/system') ? 'YES' : 'NO') . "\n";
echo "Application dir exists? " . (is_dir(dirname(__DIR__) . '/application') ? 'YES' : 'NO') . "\n";
echo "Files in parent: " . implode(', ', @scandir(dirname(__DIR__)) ?: ['none']) . "\n";
echo "DB_HOST env: " . (getenv('DB_HOST') ?: 'not set') . "\n";
