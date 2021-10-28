<?php 

function isGuest(){
  return Yii::$app->user->isGuest;
}
function dump($arg){
  echo '<pre>';
  var_dump($arg);
  echo '</pre>';
  exit;
}