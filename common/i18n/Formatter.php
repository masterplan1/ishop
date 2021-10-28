<?php

namespace common\i18n;

use yii\i18n\Formatter as I18nFormatter;
use common\models\Order;
use yii\bootstrap4\Html;
class Formatter extends I18nFormatter
{
  public function asOrderStatus($status)
  {
    if ($status === Order::STATAUS_COMPLETED) {
      return Html::tag('span', 'paid', [
        'class' => 'badge badge-success'
      ]);
    } elseif ($status === Order::STATAUS_DRAFT) {
      return Html::tag('span', 'unpaid', [
        'class' => 'badge badge-primary'
      ]);
    }
    elseif ($status === Order::STATAUS_FAILURED) {
      return Html::tag('span', 'failured', [
        'class' => 'badge badge-danger'
      ]);
    }
    elseif ($status === Order::STATAUS_DONE) {
      return Html::tag('span', 'done', [
        'class' => 'badge badge-secondary'
      ]);
    }
  }
}
