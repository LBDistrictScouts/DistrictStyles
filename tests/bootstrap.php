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

if (getenv('SECURITY_SALT') === false) {
    $testSecuritySalt = 'district-ui-test-security-salt-not-for-production';
    putenv('SECURITY_SALT=' . $testSecuritySalt);
    $_ENV['SECURITY_SALT'] = $testSecuritySalt;
    $_SERVER['SECURITY_SALT'] = $testSecuritySalt;
}

require_once $root . '/vendor/autoload.php';
require_once $root . '/vendor/cakephp/cakephp/tests/bootstrap.php';

if (file_exists($root . '/config/bootstrap.php')) {
    require $root . '/config/bootstrap.php';
}
