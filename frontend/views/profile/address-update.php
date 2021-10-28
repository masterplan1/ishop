
<?php

/**
 * @var \common\models\UserAddress $userAddress
 */

 use yii\widgets\Pjax;
 use yii\bootstrap4\ActiveForm;

?>

  <?php if($success) : ?>
    <div class="alert alert-success">
      You address was successfuly updated
    </div>  
  <?php endif; ?>


        <?php $form = ActiveForm::begin([
          'action' => ['/profile/address-update'],
          'options' => [
            'data-pjax' => 1
          ]
        ]); ?>
        <?= $form->field($userAddress, 'address')->textInput(['autofocus' => true]); ?>
        <?= $form->field($userAddress, 'city')->textInput(['autofocus' => true]); ?>
        <?= $form->field($userAddress, 'state')->textInput(['autofocus' => true]); ?>
        <?= $form->field($userAddress, 'country')->textInput(['autofocus' => true]); ?>
        <?= $form->field($userAddress, 'zipcode')->textInput(['autofocus' => true]); ?>
        <button class="btn btn-primary">Update</button>
        <?php ActiveForm::end(); ?>