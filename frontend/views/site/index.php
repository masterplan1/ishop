<?php

/* @var $this yii\web\View */

use yii\bootstrap4\LinkPager;
use yii\widgets\ListView;

$this->title = 'My Yii Application';
?>
<div class="site-index">



    <div class="body-content">

        <?php 
            echo ListView::widget([
                'dataProvider' => $dataProvider,
                'itemView' => '_product_item',
                'itemOptions' => [
                    'class' => 'col-lg-4 col-md-6 mb-4 product-item'
                ],
                // 'options' => [
                //     'class' => 'row'
                // ],
                'layout' => "{summary}<div class='row'>{items}</div>{pager}",
                'pager' => [
                    'class' => LinkPager::class
                ]
            ]);
        ?>

        
    </div>
</div>