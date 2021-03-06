<?php

/* @var $this \yii\web\View */
/* @var $content string */

use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use app\assets\AppAsset;

AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body>
<?php $this->beginBody() ?>

<div class="wrap">
    <?php
    NavBar::begin([
        'brandLabel' => 'НОВОСТИ',
        'brandUrl' => Yii::$app->homeUrl,
        'options' => [
            'class' => 'navbar-inverse navbar-fixed-top',
        ],
    ]);
    echo Nav::widget([
        'options' => ['class' => 'navbar-nav navbar-right'],
        'items' => [
            ['label' => 'На главную', 'url' => ['/']],
            ['label' => 'О сайте', 'url' => ['/site/about']],
            //['label' => 'Contact', 'url' => ['/site/contact']],
            /*Yii::$app->user->isGuest ? (
                ['label' => 'Войти', 'url' => ['/user/login']]
            ) : (
                //ActiveField::labe
                //['label' => 'Contact222', 'url' => ['/site/contact']],
            //Nav::widget(['items' => ['label' => 'Contact ddd', 'url' => ['/site/contact']]]) .
                //Html::label('Contact222', null, ['url' => ['/site/contact']]) .
            Html::tag('li', Html::a('Профиль', '/user/settings/profile')) .
                '<li>'
                . Html::beginForm(['/user/logout'], 'post')
                . Html::submitButton(
                    'Выйти (' . Yii::$app->user->identity->username . ')',
                    ['class' => 'btn btn-link logout']
                )
                . Html::endForm()
                . '</li>'
            )*/
            ['label' => 'Профиль', 'url' => ['/user/settings/profile'], 'visible' => !Yii::$app->user->isGuest],

            ['label' => 'Новости', 'url' => ['/admin/news/index'], 'visible' => Yii::$app->user->can('handle_news'),
                'options' => ['title' => 'Управление новостями', 'style' => ['font-style' => 'italic']]],
            ['label' => 'Пользователи', 'url' => ['/user/admin/index'], 'visible' => Yii::$app->user->can('admin'),
                'options' => ['title' => 'Управление пользователями', 'style' => ['font-style' => 'italic']]],

            ['label' => 'Регистрация', 'url' => ['/user/registration/register'], 'visible' => Yii::$app->user->isGuest],
            Yii::$app->user->isGuest ?
                ['label' => 'Войти', 'url' => ['/user/security/login']] :
                ['label' => 'Выйти (' . Yii::$app->user->identity->username . ')',
                    'url' => ['/user/security/logout'],
                    'linkOptions' => ['data-method' => 'post']],

        ],
    ]);
    NavBar::end();
    ?>

    <div class="container">
        <?= Breadcrumbs::widget([
            'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
        ]) ?>
        <?= $content ?>
    </div>
</div>

<footer class="footer">
    <div class="container">
        <p class="pull-left">&copy; My Company <?= date('Y') ?></p>

        <p class="pull-right"><?= Yii::powered() ?></p>
    </div>
</footer>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
