<?php 
    /**
     * @var $model common\models\Product
     */

use yii\helpers\Url;

?>

        <div class="card h-100">
            <!-- Product image-->
            <img class="card-img-top" src="<?php echo $model->getImageUrl(); ?>" alt="<?php echo $model->name; ?>" />
            <!-- Product details-->
            <div class="card-body">
                <h4 class="card-title">
                    <a href="#"><?php echo $model->name; ?></a>

                </h4>
                <h5>$<?php echo $model->price; ?></h5>
                <p class="card-text"><?php echo $model->getShortDescription(); ?></p>
            </div>
            <div class="card-footer text-right">
                <a href="<?php echo Url::to(['cart/add']) ?>" class="btn btn-primary btn-add-to-cart">Add to cart</a>
            </div>
        </div>
