<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap4\ActiveForm */
/* @var $model \common\models\LoginForm */

use yii\bootstrap4\ActiveForm;
use yii\bootstrap4\Html;
use yii\helpers\Url;

$this->title = 'Login';
?>


<div class="row">
    <div class="col-lg-6 d-none d-lg-block bg-login-image"></div>
    <div class="col-lg-6">
        <div class="p-5">
            <div class="text-center">
                <h1 class="h4 text-gray-900 mb-4">Welcome Back!</h1>
            </div>
            <?php $form = ActiveForm::begin([
                'id' => 'login-form',
                'options' => ['class' => 'user']
             ]); ?>

                <?= $form->field($model, 'username', [
                    'inputOptions' => [
                        'class' => 'form-control form-control-user',
                        'placeholder' => 'Enteryour username'
                    ]])->textInput(['autofocus' => true]) ?>

                <?= $form->field($model, 'password', [
                    'inputOptions' => [
                        'class' => 'form-control form-control-user',
                        'placeholder' => 'Password'
                    ]])->passwordInput() ?>

                <?= $form->field($model, 'rememberMe')->checkbox() ?>
                <?= Html::submitButton('Login', ['class' => 'btn btn-primary btn-block btn-user', 'name' => 'login-button']) ?>
            <?php ActiveForm::end(); ?>
            <hr>
            <div class="text-center">
                <a class="small" href="<?php echo Url::to(['site/forgot-password']); ?>">Forgot Password?</a>
            </div>
        </div>
    </div>
</div>