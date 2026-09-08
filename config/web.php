<?php

declare(strict_types=1);

$params = require __DIR__ . '/params.php';
$db = require __DIR__ . '/db.php';
$configureContainer = require __DIR__ . '/container.php';
$configureContainer(Yii::$container);

$cookieValidationKey = (string) env('COOKIE_VALIDATION_KEY', '');
if ($cookieValidationKey === '') {
    throw new RuntimeException('COOKIE_VALIDATION_KEY is not set. Copy .env.example to .env.');
}

$config = [
    'id' => 'book-catalog',
    'name' => 'Book Catalog',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'language' => 'ru-RU',
    'sourceLanguage' => 'en-US',
    'defaultRoute' => 'book/index',
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm' => '@vendor/npm-asset',
    ],
    'components' => [
        'request' => [
            'cookieValidationKey' => $cookieValidationKey,
        ],
        'cache' => [
            'class' => yii\caching\DummyCache::class,
        ],
        'user' => [
            'identityClass' => app\models\User::class,
            'enableAutoLogin' => true,
            'loginUrl' => ['site/login'],
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => yii\log\FileTarget::class,
                    'levels' => ['error', 'warning'],
                    'logVars' => [],
                ],
            ],
        ],
        'db' => $db,
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
                '' => 'book/index',
                'report' => 'report/top-authors',
                'login' => 'site/login',
                'logout' => 'site/logout',
            ],
        ],
        'formatter' => [
            'class' => yii\i18n\Formatter::class,
            'locale' => 'ru-RU',
        ],
    ],
    'params' => $params,
];

if (YII_ENV_DEV && class_exists(yii\debug\Module::class)) {
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => yii\debug\Module::class,
        'allowedIPs' => ['127.0.0.1', '::1', '172.*', '192.168.*', '10.*'],
    ];
}

return $config;
