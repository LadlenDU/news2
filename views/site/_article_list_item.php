<?php

/* @var $model app\models\NewsSearch */

use yii\helpers\Html;
use yii\helpers\Url;

$preview = substr($model->body, 0, 150);
if ($preview < $model->body) {
    $preview = substr($preview, 0, strrpos($preview, ' ')) . ' ...';
 }

$fPreview = \Yii::$app->formatter->asNtext($preview);

?>

<article class="article-item"><h4><?= Html::a(Html::encode($model->title), Url::toRoute(['news/view', 'id' => $model->id])) ?></h4>
    <span style="font-size: smaller">(<?= \Yii::$app->formatter->asDatetime($model->updated_at) ?>)</span>

    <div class="item-body" style="margin-top: 5px">
        <? if (\Yii::$app->user->isGuest){
            echo $fPreview;
        } else {
            echo Html::a($fPreview, Url::toRoute(['news/view', 'id' => $model->id]),
                ['title' => 'Нажмите чтобы увидеть полный текст', 'class' => 'article_preview']);
        }
        ?>
    </div>
</article>

