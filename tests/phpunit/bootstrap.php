<?php
declare(strict_types = 1);

ini_set('memory_limit', '2G');

if (file_exists(__DIR__ . '/bootstrap.local.php')) {
  require_once __DIR__ . '/bootstrap.local.php';
}
