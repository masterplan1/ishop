<?php
  /**
   * @var \common\models\Order $order
   */
  $orderAddress = $order->orderAddress;
?>

Order #<?php echo $order->id; ?> summary: 


    Account inforamation      
        Firstname
        <?php echo $oreder->firstname; ?>
        Lastname
        <?php echo $oreder->lastname; ?>
        Email
        <?php echo $oreder->email; ?>
    Address inforamation
        Address
        <?php echo $orederAddress->address; ?>
        City
        <?php echo $orederAddress->city; ?>
        State
        <?php echo $orederAddress->state; ?>
        Country
        <?php echo $orederAddress->country; ?>
        Zipcode
        <?php echo $orederAddress->zipcode; ?>
    Products
        Image
        Name
        Quantity
        Price


        <?php foreach($order->orderItems as $item): ?>
          <?php echo $item->product_name; ?>
          <?php echo $item->quantity; ?>
          <?php echo Yii::$app->formatter->asCurrency($item->quantity * $item->unit_price) ?>
        <?php endforeach; ?>
      
        Total Items
        <?php echo $order->getItemsQuantity() ?>
      
      
        Total Price
        <?php echo Yii::$app->formatter->asCurrency($order->total_price); ?>
      