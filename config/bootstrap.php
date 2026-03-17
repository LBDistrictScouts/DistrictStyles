<?php
declare(strict_types=1);

use Cake\Core\Configure;

$pluginDefaults = [
    'stylesheets' => ['DistrictUI.district-ui'],
    'iconStylesheets' => ['DistrictUI./font/bootstrap-icons', 'DistrictUI./font/bootstrap-icon-sizes'],
    'scripts' => ['DistrictUI.bootstrap.bundle.min'],
    'build' => [
        'distCss' => 'dist/css/district-styles.css',
        'distMinCss' => 'dist/css/district-styles.min.css',
        'distAssetsDir' => 'dist/assets',
        'bootstrapJs' => 'node_modules/bootstrap/dist/js/bootstrap.bundle.min.js',
        'bootstrapIconsCss' => 'node_modules/bootstrap-icons/font/bootstrap-icons.css',
        'bootstrapIconsFontsDir' => 'node_modules/bootstrap-icons/font/fonts',
        'webrootCssDir' => 'webroot/css',
        'webrootJsDir' => 'webroot/js',
        'webrootFontDir' => 'webroot/font',
        'webrootAssetsDir' => 'webroot/assets',
    ],
];

$current = (array)Configure::read('DistrictUI', []);
Configure::write('DistrictUI', array_replace_recursive($pluginDefaults, $current));
