<?php

/**
 * @var \common\models\User $user
 * @var \common\models\UserAddress $userAddress
 * @var yii\web\View $this
 */
use yii\widgets\Pjax;
?>

<div class="row">
  <div class="col">
    <div class="card">
      <div class="card-header">
        account address
      </div>
      <div class="card-body">
      <?php Pjax::begin([
        'enablePushState' => false
      ]) ?>
      <?php echo $this->render('address-update', compact('userAddress')) ?>
      <?php Pjax::end(); ?>
      </div>
    </div>
  </div>
  <div class="col">
    <div class="card">
      <div class="card-header">
        Account inforamation
      </div>
      <div class="card-body">
      <?php Pjax::begin([
        'enablePushState' => false
      ]) ?>
      <?php echo $this->render('user-acount', compact('user')) ?>
      <?php Pjax::end(); ?>
      </div>
    </div>
  </div>
</div>