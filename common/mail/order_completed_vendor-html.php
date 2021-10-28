<?php
  /**
   * @var \common\models\Order $order
   */
  $orderAddress = $order->orderAddress;
?>

<h3>Order #<?php echo $order->id; ?> summary: </h3>
<hr>
<div class="row">
  <div class="col">
    <h5>Account inforamation</h5>
    <table class="table">
      <tr>
        <th>Firstname</th>
        <td><?php echo $oreder->firstname; ?></td>
      </tr>
      <tr>
        <th>Lastname</th>
        <td><?php echo $oreder->lastname; ?></td>
      </tr>
      <tr>
        <th>Email</th>
        <td><?php echo $oreder->email; ?></td>
      </tr>
    </table>

    <h5>Address inforamation</h5>
    <table class="table">
      <tr>
        <th>Address</th>
        <td><?php echo $orederAddress->address; ?></td>
      </tr>
      <tr>
        <th>City</th>
        <td><?php echo $orederAddress->city; ?></td>
      </tr>
      <tr>
        <th>State</th>
        <td><?php echo $orederAddress->state; ?></td>
      </tr>
      <tr>
        <th>Country</th>
        <td><?php echo $orederAddress->country; ?></td>
      </tr>
      <tr>
        <th>Zipcode</th>
        <td><?php echo $orederAddress->zipcode; ?></td>
      </tr>
    </table>
  </div>
  <div class="col">
    <h5>Products</h5>
    <table class="table table-sm">
      <thead>
        <th>Image</th>
        <th>Name</th>
        <th>Quantity</th>
        <th>Price</th>
      </thead>
      <tbody>
        <?php foreach($order->orderItems as $item): ?>
          <td><img src="<?php echo $item->product->getImageUrl() ?> style="width:50px" alt=""></td>
          <td><?php echo $item->product_name; ?></td>
          <td><?php echo $item->quantity; ?></td>
          <td><?php echo Yii::$app->formatter->asCurrency($item->quantity * $item->unit_price) ?></td>
        <?php endforeach; ?>
      </tbody>
    </table>
    <hr>
    <table class="table">
      <tr>
        <th>Total Items</th>
        <td><?php echo $order->getItemsQuantity() ?></td>
      </tr>
      <tr>
        <th>Total Price</th>
        <td><?php echo Yii::$app->formatter->asCurrency($order->total_price); ?></td>
      </tr>
    </table>
  </div>
</div>