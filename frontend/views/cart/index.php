<?php
/**
 * @var $items
 */

use common\models\Product;
use yii\helpers\Html;
use yii\helpers\Url;
?>

<div class="card">
  <div class="card-header">Cart items</div>
  <div class="card-body p-0">
  <?php if(is_array($items)) : ?>
    <table class="table table-hover">
      <thead>
        <tr>
          <th>Product</th>
          <th>Image</th>
          <th>Unit Price</th>
          <th>Quantity</th>
          <th>Total price</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach($items as $item) : ?>
          <tr data-id="<?php echo $item['product_id']; ?>" data-url="<?php echo Url::to(['cart/change-quantity']); ?>">
            <td><?php echo $item['name']; ?></td>
            <td><?php echo Html::img(Product::formatImageUrl($item['image']),
          [
            'width' => '50px'
          ]); ?></td>
            <td><?php echo Yii::$app->formatter->asCurrency($item['price']); ?></td>
            <td><input type="number" min="1" class="form-control item-quantity" style="width: 60px" value="<?php echo $item['quantity']; ?>"></td>
            <td><?php echo Yii::$app->formatter->asCurrency($item['price']); ?></td>
            <td>
              <?php echo Html::a('Delete', ['cart/delete', 'id' => $item['product_id']], [
              'class' => 'btn btn-outline-danger btn-sm',
              'data' => [
                'confirm' => 'are you shure?',
                'method' => 'post'
              ]
              // 'data-method' => 'post',
              // 'data-confirm' => 'are you shure?'
            ]) ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
    <?php endif; ?>
  </div>
  <div class="card-body text-right">
    <a href="<?php echo Url::to(['cart/checkout']) ?>" class="btn btn-primary">Checkout</a>
  </div>
</div>