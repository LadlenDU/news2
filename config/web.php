<?php

$params = require(__DIR__ . '/params.php');

$config = [
    'id' => 'basic',
    'name' => 'Простейший новостной сайт',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'language' => 'ru',
    'components' => [
        'view' => [
            'theme' => [
                'pathMap' => [
                    '@dektrium/user/views' => '@app/views/user',
                ],
            ],
        ],
        'request' => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => '56FTdysqWxfy8J7udXRBuR_aOb2so9mA',
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        /*'user' => [
            'identityClass' => 'app\models\User',
            'enableAutoLogin' => true,
        ],*/
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'mailer' => require(__DIR__ . '/mailer.php'),
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'db' => require(__DIR__ . '/db.php'),

        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'enableStrictParsing' => false,
            'rules' => [
                '<alias:\w+>' => 'site/<alias>',
            ],
        ],

    ],
    'params' => $params,
    'modules' => [
        'user' => [
            'class' => 'dektrium\user\Module',
            'controllerMap' => [
                'profile' => [
                    'class' => \dektrium\user\controllers\ProfileController::className(),
                    'on ' . \dektrium\user\controllers\ProfileController::EVENT_BEFORE_ACTION => function ($e) {
                        throw new \yii\web\NotFoundHttpException();
                        //Yii::$app->response->redirect(array('/user/security/login'))->send();
                        //Yii::$app->end();
                    }
                ],
                'settings' => [
                    'class' => \dektrium\user\controllers\SettingsController::className(),
                    'on ' . \dektrium\user\controllers\SettingsController::EVENT_BEFORE_ACTION => function ($e) {
                        if ($e->action->id == 'networks' || $e->action->id == 'disconnect') {
                            throw new \yii\web\NotFoundHttpException();
                        }
                    }
                ],
                /*'migrate' => [
                    'class' => 'yii\console\controllers\MigrateController',
                    'migrationNamespaces' => [
                        '@vendor/dektrium/yii2-user/migrations',
                        'app\migrations',
                    ],
                ],*/
            ],
        ],
    ],
];

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        //'allowedIPs' => ['127.0.0.1', '::1'],
    ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        //'allowedIPs' => ['127.0.0.1', '::1'],
    ];
}

return $config;
