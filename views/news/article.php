<?php

/* @var $model app\models\NewsSearch */

use yii\helpers\Html;
//use yii\helpers\Url;

?>

<article class="article">
    <h4><?= Html::encode($model->title) ?></h4>
    <div style="font-size: smaller"><?= Yii::t('app', 'Posted:')?> <?= \Yii::$app->formatter->asDatetime($model->updated_at) ?></div>

    <div class="item-body" style="margin-top: 5px">
        <?= Html::encode($model->body) ?>
    </div>
</article>

