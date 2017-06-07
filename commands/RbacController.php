<?php

namespace app\commands;

use Yii;
use yii\console\Controller;

class RbacController extends Controller
{
    public function actionInit()
    {
        $auth = Yii::$app->authManager;

        $viewFullArticle = $auth->createPermission('view_full_article');
        $viewFullArticle->description = 'View full content of an article';
        $auth->add($viewFullArticle);

        $handleNews = $auth->createPermission('handle_news');
        $handleNews->description = 'CRUD for news';
        $auth->add($handleNews);

        $handleUsers = $auth->createPermission('handle_users');
        $handleUsers->description = 'CRUD for users';
        $auth->add($handleUsers);

        $reader = $auth->createRole('reader');
        $auth->add($reader);
        $auth->addChild($reader, $viewFullArticle);

        $moderator = $auth->createRole('moderator');
        $auth->add($moderator);
        $auth->addChild($moderator, $handleNews);
        $auth->addChild($moderator, $reader);

        $admin = $auth->createRole('admin');
        $auth->add($admin);
        $auth->addChild($admin, $handleUsers);
        $auth->addChild($admin, $moderator);
    }
}
