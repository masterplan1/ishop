<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\search\ProductSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Products';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="product-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Product', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            // ['class' => 'yii\grid\SerialColumn'],

            [
              'attribute' => 'id',
              'contentOptions' => [
                'style' => 'width:80px'
              ]
            ],
            [
              'attribute' => 'image',
              'content' => function($data){
                /**
                 * @var $data common\models\Product
                 */
                return Html::img($data->getImageUrl(), ['width' => '100px']);
              }
            ],
            'name',
            'description:raw',
            [
              'attribute' => 'price',
              'format' => 'currency'
            ],
            [
              'attribute' => 'status',
              'contentOptions' => ['style' => 'width: 40px'],
              'content' => function($model){
                /**
                 * @var $model common\models\Product
                 */
                return Html::tag('span', $model->status ? 'Active' : 'Draft', [
                  'class' => $model->status ? 'badge badge-success' : 'badge badge-danger'
                ]);
              }
            ],
            // 'created_at:datetime',
            [
              'attribute' => 'created_at',
              'format' => ['date'],
              'contentOptions' => ['style' => 'white-space:pre-line']
            ],
            [
              'attribute' => 'updated_at',
              'format' => ['date'],
              'contentOptions' => ['style' => 'white-space:pre-line']
            ],
            // 'createdBy.username',
            //'updated_by',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>


</div>
