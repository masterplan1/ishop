<?php
namespace frontend\base;

use common\models\CartItem;
use Yii;
use yii\web\Controller as WebController;

class Controller extends WebController
{
  public function beforeAction($action)
  {
    // $itemCount = array_sum(CartItem::find()->select(['quantity'])->userId(Yii::$app->user->id)->column());
    $this->view->params['itemCount'] = CartItem::getTotalItemsQuantity();
    return parent::beforeAction($action);
  }
}