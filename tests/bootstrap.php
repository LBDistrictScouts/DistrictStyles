<?php
declare(strict_types=1);

$findRoot = function (string $root): string {
    do {
        $lastRoot = $root;
        $root = dirname($root);
        if (is_dir($root . '/vendor/cakephp/cakephp')) {
            return $root;
        }
    } while ($root !== $lastRoot);

    throw new RuntimeException('Cannot find the root of the application, unable to run tests');
};

$root = $findRoot(__FILE__);
unset($findRoot);

chdir($root);

require_once $root . '/vendor/autoload.php';
require_once $root . '/vendor/cakephp/cakephp/tests/bootstrap.php';

if (file_exists($root . '/config/bootstrap.php')) {
    require $root . '/config/bootstrap.php';
}
