<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

use yii\widgets\ListView;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\NewsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Простейший новостной сайт';
?>
<div class="site-index">

    <?php Pjax::begin(); ?>    <?= ListView::widget([
        'dataProvider' => $dataProvider,
        'options' => [
            'tag' => 'div',
            'class' => 'list-wrapper',
            //'id' => 'list-wrapper',
            'style' => 'max-width: 600px; margin: 0 auto;',
        ],
        'itemView' => '_article_list_item',
        /*'itemOptions' => [
            'tag' => 'div',
            //'class' => 'element-wrapper',
            //'style' => 'max-width: 600px;',
        ],*/
    ]); ?>

    <?php Pjax::end(); ?>

</div>
