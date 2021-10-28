<?php

namespace frontend\controllers;

use frontend\base\Controller;
use Yii;
use yii\base\Behavior;
use yii\filters\AccessControl;
use yii\web\ForbiddenHttpException;

class ProfileController extends Controller
{
  public function behaviors()
  {
    return [
      'access' => [
        'class' => AccessControl::class,
        'rules' => [
            [
                'actions' => ['index', 'address-update', 'user-acount'],
                'allow' => true,
                'roles' => ['@'],
            ],
        ],
    ],
    ];
  }
  public function actionIndex()
  {
      $user = Yii::$app->user->identity;
      // $userAddresses = $user->addresses;
      $userAddress = $user->getAddress();
      return $this->render('index', ['user' => $user, 'userAddress' => $userAddress]);
  }

  public function actionAddressUpdate(){
    if(!Yii::$app->request->isAjax){
      throw new ForbiddenHttpException('You are not allowed to access this page');
    }
      $user = Yii::$app->user->identity;
      $userAddress = $user->getAddress();
      $success = false;

      if($userAddress->load(Yii::$app->request->post()) &&  $userAddress->save()){
          $success = true;
      }

      return $this->renderAjax('address-update', [
          'userAddress' => $userAddress,
          'success' => $success
      ]);
  }

  public function actionUserAcount(){
    if(!Yii::$app->request->isAjax){
      throw new ForbiddenHttpException('You are not allowed to access this page');
    }
      $user = Yii::$app->user->identity;
      $success = false;
      if($user->load(Yii::$app->request->post()) && $user->saveWithPass()){
          $success = true;
      }
      return $this->renderAjax('user-acount', compact('user', 'success'));
  }
}