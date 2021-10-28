<?php

use common\i18n\Formatter;

return [
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
    'components' => [
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'formatter' => [
            'class' => Formatter::class,
            'dateFormat' => 'php:d.m.y',
            'datetimeFormat' => 'php:d.m.y H:i',
            'currencyCode' => 'EUR'
        ]
    ],
];
