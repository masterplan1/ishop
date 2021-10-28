<?php

namespace frontend\controllers;

use common\models\CartItem;
use common\models\Order;
use common\models\OrderAddress;
use common\models\Product;
use common\models\UserAddress;
use frontend\base\Controller;
use Yii;
use yii\bootstrap4\ActiveForm;
use yii\filters\ContentNegotiator;
use yii\filters\VerbFilter;
use yii\grid\CheckboxColumn;
use yii\helpers\VarDumper;
use yii\web\BadRequestHttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class CartController extends Controller
{
  public function behaviors()
  {
    return [
      [
        'class' => ContentNegotiator::class,
        'only' => ['add', 'create-order', 'change-quantity'],
        'formats' => [
          'application/json' => Response::FORMAT_JSON,
        ],
      ],
      [
        'class' => VerbFilter::class,
        'actions' => [
          'delete' => ['POST', 'DELETE'],
          'create-order' => ['POST']
        ]
      ]
    ];
  }
  public function actionIndex()
  {
    $cartItems = CartItem::getItemsForUser(Yii::$app->user->id);
    return $this->render('index', [
      'items' => $cartItems
    ]);
  }

  public function actionAdd()
  {
    // Yii::$app->response->format = Response::FORMAT_JSON;
    $id = Yii::$app->request->post('id');
    $product = Product::find()->id($id)->published()->one();
    if (!$product) {
      throw new NotFoundHttpException("Product not found");
    }

    if (isGuest()) {

      $cartItems = Yii::$app->session->get(CartItem::PRODUCT_CART, []);

      $found = false;
      foreach ($cartItems as &$item) {
        if ($item['id'] == $id) {
          $item['quantity']++;
          $found = true;
          break;
        }
      }
      if (!$found) {
        $cartItem = [];
        $cartItem['product_id'] = $id;
        $cartItem['name'] = $product->name;
        $cartItem['image'] = $product->image;
        $cartItem['price'] = $product->price;
        $cartItem['quantity'] = 1;
        $cartItem['total_price'] = $product->price;

        $cartItems[] = $cartItem;
      }
      Yii::$app->session->set(CartItem::PRODUCT_CART, $cartItems);

      // id, name,image,price,quantity,total_price
      // save in session
    } else {
      $userId = Yii::$app->user->id;
      $cartItem = CartItem::find()->userId($userId)->productId($product->id)->one();
      if ($cartItem) {
        $cartItem->quantity++;
      } else {
        $cartItem = new CartItem();
        $cartItem->product_id = $product->id;
        $cartItem->created_by = Yii::$app->user->id;
        $cartItem->quantity = 1;
      }
      if ($cartItem->save()) {
        return [
          'success' => true,
          'product' => $cartItem
        ];
      } else {
        return [
          'success' => false,
          'error' => $product->errors
        ];
      }
    }
  }

  public function actionDelete($id)
  {
    if (isGuest()) {
      $cartItems = Yii::$app->session->get(CartItem::PRODUCT_CART, []);
      foreach ($cartItems as $i => $cartItem) {
        if ($cartItem['product_id'] == $id) {
          array_splice($cartItems, $i, 1);
          break;
        }
      }
      Yii::$app->session->set(CartItem::PRODUCT_CART, $cartItems);
    } else {
      $userId = Yii::$app->user->id;
      CartItem::deleteAll(['product_id' => $id, 'created_by' => $userId]);
    }
    return $this->redirect('index');
  }

  public function actionChangeQuantity()
  {
    $id = Yii::$app->request->post('id');
    $product = Product::find()->id($id)->published()->one();
    if (!$product) {
      throw new NotFoundHttpException("Product not found");
    }
    $quantity = Yii::$app->request->post('quantity');

    $cartItems = Yii::$app->session->get(CartItem::PRODUCT_CART, []);

    if (isGuest()) {
      foreach ($cartItems as &$cartItem) {
        if ($cartItem['id'] === $id) {
          $cartItem['quantity'] = $quantity;
          break;
        }
      }
      Yii::$app->session->set(CartItem::PRODUCT_CART, $cartItems);
    } else {
      $cartItem = CartItem::find()->userId(Yii::$app->user->id)->productId($product->id)->one();
      if ($cartItem) {
        $cartItem->quantity = $quantity;
        $cartItem->save();
      }
    }
    return [
      'quantity' => CartItem::getTotalItemsQuantity(),
      'price' => Yii::$app->formatter->asCurrency(CartItem::getTotalPricePerItem($id, Yii::$app->user->id))
    ];
  }

  public function actionCheckout(){
    $cartItems = CartItem::getItemsForUser(Yii::$app->user->id);
    if(empty($cartItems)){
      return $this->redirect(Yii::$app->homeUrl);
    }
    $order = new Order();
    $orderAddress = new OrderAddress();

    if(!isGuest()){
      /** @var \common\models\User $user */
      $user = Yii::$app->user->identity;
      $userAddress = $user->getAddress();

      $order->firstname = $user->firstname;
      $order->lastname = $user->lastname;
      $order->status = Order::STATAUS_DRAFT;
      $order->email = $user->email;

      $orderAddress->address = $userAddress->address;
      $orderAddress->state = $userAddress->state;
      $orderAddress->city = $userAddress->city;
      $orderAddress->country = $userAddress->country;
      $orderAddress->zipcode = $userAddress->zipcode;
    }
    $productQuantity = CartItem::getTotalItemsQuantity();
    $totalPrice = CartItem::getTotalPrice(Yii::$app->user->id);

    return $this->render('checkout', [
      'order' => $order,
      'orderAddress' => $orderAddress,
      'cartItems' => $cartItems,
      'productQuantity' => $productQuantity,
      'totalPrice' => $totalPrice
    ]);
  }
  public function actionCreateOrder(){
    $transaction = Yii::$app->db->beginTransaction();
    $order = new Order();
    $totalPrice = CartItem::getTotalPrice(Yii::$app->user->id);
    if($totalPrice === null){
      throw new BadRequestHttpException();
    }
    $order->total_price = $totalPrice;
    
    $order->status = "COMPLETED" ? Order::STATAUS_COMPLETED : Order::STATAUS_DRAFT;
    $order->created_by = Yii::$app->user->id;
    $order->created_at = time();
    $order->transaction_id = Yii::$app->request->post('transactionId');
    if($order->load(Yii::$app->request->post())
     && $order->save() 
     && $order->saveAddress(Yii::$app->request->post())
     && $order->saveOrderItems()){
      $transaction->commit();
      CartItem::clearCartItem(Yii::$app->user->id);
      // todo send email to admin
      if(!$order->sendEmailToVendor()){
        Yii::error("email to the vendor is not sent");
      }
      if(!$order->sendEmailToCustomer()){
        Yii::error("email to the customer is not sent");
      }
      return [
        'success' => true
      ];
    }else{
      $transaction->rollBack();
      Yii::error("Order wasn't saved. Data: ".VarDumper::dumpAsString($order->toArray()). 
      '. Errors: '. VarDumper::dumpAsString($order->errors));
      
      return [
        'success' => false,
        'errors' => $order->errors
      ];
    }
  }
}
