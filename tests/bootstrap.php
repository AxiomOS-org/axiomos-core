<?php

declare(strict_types=1);

if (! extension_loaded('pdo_pgsql')) {
    $extensionDirectory = ini_get('extension_dir');

    if (is_string($extensionDirectory) && $extensionDirectory !== '') {
        if (PHP_OS_FAMILY === 'Windows') {
            if (! extension_loaded('pdo_pgsql') && is_file($extensionDirectory . DIRECTORY_SEPARATOR . 'php_pdo_pgsql.dll')) {
                dl('php_pdo_pgsql.dll');
            }

            if (! extension_loaded('pgsql') && is_file($extensionDirectory . DIRECTORY_SEPARATOR . 'php_pgsql.dll')) {
                dl('php_pgsql.dll');
            }
        }
    }
}

if (! extension_loaded('pdo_pgsql')) {
    fwrite(
        STDERR,
        "pdo_pgsql is required. Enable php_pdo_pgsql in php.ini or run tests via composer test (loads extensions on Windows).\n",
    );

    exit(1);
}

require dirname(__DIR__) . '/vendor/autoload.php';
