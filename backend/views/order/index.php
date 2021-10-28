<?php

use common\models\Order;
use yii\bootstrap4\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\search\OrderSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Orders';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="order-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'id' => 'orderItems',
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            [
                'attribute' => 'id',
                'options' => [
                    "style" => "width: 80px"
                ]
            ],
            [
                'attribute' => 'fullname',
                'value' => function($model){
                    return $model->firstname . ' ' . $model->lastname;
                }
            ],
            [
                'attribute' => 'status',
                'format' => 'orderStatus',
                'filter' => Order::stausList(),
                'filterInputOptions' => [
                    'prompt' => 'All',
                    'class' => 'form-control'
                ],
            ],
            'total_price:currency',
            //'email:email',
            //'transaction_id',
            'created_at:datetime',
            //'created_by',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>


</div>
