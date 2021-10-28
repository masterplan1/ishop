<?php

/**
 * @var \common\models\Order $order
 * @var \common\models\OrderAddress $orderAddress
 * @var array $cartItems
 * @var int $productQuantity
 */

use yii\bootstrap4\ActiveForm;
use yii\bootstrap4\Html;

?>

<?php $form = ActiveForm::begin([
    'id' => 'userInformation',
    // 'enableAjaxValidation' => true
]); ?>
<div class="row mb-3">
  <div class="col">
    <div class="card mb-3">
      <div class="card-header">
        <h5>Account inforamation</h5>
      </div>
      <div class="card-body">
        <div class="row">
          <div class="col-md-6">
            <?= $form->field($order, 'firstname')->textInput(['autofocus' => true]) ?>
          </div>
          <div class="col-md-6">
            <?= $form->field($order, 'lastname', ['enableAjaxValidation' => true])->textInput(['autofocus' => true]) ?>
          </div>
        </div>
        <?= $form->field($order, 'email')->textInput(['autofocus' => true]) ?>
      </div>
    </div>
    <div class="card">
      <div class="card-header">
        <h5>Account address</h5>
      </div>
      <div class="card-body">
        <?= $form->field($orderAddress, 'address')->textInput(['autofocus' => true]); ?>
        <?= $form->field($orderAddress, 'city')->textInput(['autofocus' => true]); ?>
        <?= $form->field($orderAddress, 'state')->textInput(['autofocus' => true]); ?>
        <?= $form->field($orderAddress, 'country')->textInput(['autofocus' => true]); ?>
        <?= $form->field($orderAddress, 'zipcode')->textInput(['autofocus' => true]); ?>

      </div>
    </div>
    
  </div>
  <div class="col">
    <div class="card">
      <div class="card-header">
        <h5>Order Summary</h5>
      </div>
      <div class="card-body">
        <table class="table">
          <tr>
            <td colspan="2"><?php echo $productQuantity; ?> Products</td>
          </tr>
          <tr>
            <td>Total price</td>
            <td class="text-right"><?php echo Yii::$app->formatter->asCurrency($totalPrice); ?></td>
          </tr>
        </table>
        <p class="text-right mt-3">
          <button type="submit" class="btn btn-secondary btn-checkout">Checkout</button>
        </p>
      </div>
    </div>
  </div>
</div>
<?php ActiveForm::end(); ?>





<?php
$js = <<< JS
$(function(){
  // const btnCheckout = $('.btn-checkout');
  const form  = $('#userInformation');
  // btnCheckout.on('click', (ev) => {
    form.on('submit', function(ev) {
    ev.preventDefault();
    const transactionId = Math.random().toString(36).substring(7);
    const status = 1;
    
    const data = form.serializeArray();
    data.push({
      name: 'transactionId',
      value: transactionId
    });
    data.push({
      name: 'status',
      value: status
    })
    const lastnameInput = $('#order-lastname');
    const lastnameVal = lastnameInput.val();
    if(lastnameVal == ''){
      lastnameInput.addClass('is-invalid');
      const errorDiv = lastnameInput.siblings('.invalid-feedback');
      errorDiv.html('Cant be blanc1');
      errorDiv.show();
    }else{
    // if($('input[name=Order[lastname]]'))
    $.ajax({
      method: 'post',
      url: '/cart/create-order',
      data: data,
      success: function (response) {
        if(response.success){
          alert("Thanks for your business");
          window.location.href = '';
        }else{
          console.log(response.errors);
        }
      }
    });
  }
  });
  
}());
JS;
$this->registerJs($js);
?>
