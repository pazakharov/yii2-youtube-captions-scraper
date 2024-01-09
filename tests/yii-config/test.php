<?php

return [
    'id' => 'testapp',
    'name' => 'testapp',
    'bootstrap' => [],
    'basePath' => __DIR__ . '/../',
    'vendorPath' => dirname(__DIR__) . '/../../vendor',
    'aliases' => [
        '@app' => \yii\helpers\FileHelper::normalizePath(__DIR__ . '/../../'),
        '@src' => '@app/src',
        '@tests' => '@app/tests',
    ],
    'components' => [
        'db' => [
            'class' => \yii\db\Connection::class,
            'dsn' => env('DB_DSN'),
            'username' => env('DB_USERNAME'),
            'password' => env('DB_PASSWORD'),
            'charset' => 'utf8mb4',
        ]
    ],
    'modules' => [],
    'controllerMap' => [
        // 'migrate' => [
        //     'class' => 'yii\console\controllers\MigrateController',
        //     'migrationPath' => '@src/migrations',
        // ]
    ],

];
