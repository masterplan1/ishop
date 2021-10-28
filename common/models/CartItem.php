<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "{{%cart_items}}".
 *
 * @property int $id
 * @property int $product_id
 * @property int $quantity
 * @property int|null $created_by
 *
 * @property User $createdBy
 * @property Product $product
 */
class CartItem extends \yii\db\ActiveRecord
{
    const PRODUCT_CART = 'product_cart';
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%cart_items}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['product_id', 'quantity'], 'required'],
            [['product_id', 'quantity', 'created_by'], 'integer'],
            [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['created_by' => 'id']],
            [['product_id'], 'exist', 'skipOnError' => true, 'targetClass' => Product::class, 'targetAttribute' => ['product_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'product_id' => 'Product ID',
            'quantity' => 'Quantity',
            'created_by' => 'Created By',
        ];
    }

    /**
     * Gets query for [[CreatedBy]].
     *
     * @return \yii\db\ActiveQuery|\common\models\query\UserQuery
     */
    public function getCreatedBy()
    {
        return $this->hasOne(User::className(), ['id' => 'created_by']);
    }

    /**
     * Gets query for [[Product]].
     *
     * @return \yii\db\ActiveQuery|\common\models\query\ProductsQuery
     */
    public function getProduct()
    {
        return $this->hasOne(Product::class, ['id' => 'product_id']);
    }

    /**
     * {@inheritdoc}
     * @return \common\models\query\CartItemQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \common\models\query\CartItemQuery(get_called_class());
    }

    public static function getTotalItemsQuantity(){
        if(isGuest()){
          $cartItems = Yii::$app->session->get(CartItem::PRODUCT_CART, []);
    
          $sum = 0;
          foreach($cartItems as $cartItem){
            $sum += $cartItem['quantity'];
          }
        }else{
          $sum = CartItem::findBySql(
            "SELECT SUM(quantity) FROM cart_items WHERE created_by = :userId", 
            ['userId' => Yii::$app->user->id]
          )->scalar();
        }
        return $sum;
      }
    
    public static function getItemsForUser($userId){
      if (Yii::$app->user->isGuest) {
        $cartItems = Yii::$app->session->get(CartItem::PRODUCT_CART, []);
      } else {
        $cartItems = CartItem::findBySql(
          "SELECT 
            p.id as product_id,
            p.name,
            p.image,
            p.price,
            c.quantity,
            c.quantity * p.price as total_price
          FROM products p LEFT JOIN cart_items c ON p.id = c.product_id
          WHERE c.created_by = :created_by",
          ["created_by" => $userId]
        )->asArray()->all();;
      }
      return $cartItems;
    }

    public static function getTotalPrice($userId){

      if(isGuest()){
        $cartItems = Yii::$app->session->get(CartItem::PRODUCT_CART, []);
        $totalPrice = 0;
        foreach($cartItems as $item){
          $totalPrice += $item['price']*$item['quantity'];
        }
      }else{
        $totalPrice = self::findBySql(
          "SELECT SUM(c.quantity * p.price) FROM products p LEFT JOIN cart_items c 
          ON p.id = c.product_id
          WHERE c.created_by = :created_by", ["created_by" => $userId])->scalar();
      }
      return $totalPrice;
    }

    public static function clearCartItem($userId){
      if(isGuest()){
        Yii::$app->session->remove(self::PRODUCT_CART);
      }else{
        self::deleteAll(['created_by' => $userId]);
      }
    }

    
    public static function getTotalPricePerItem($productId, $userId){

      if(isGuest()){
        $cartItems = Yii::$app->session->get(CartItem::PRODUCT_CART, []);
        $totalPrice = 0;
        foreach($cartItems as $item){
          if($productId === $item['product_id']){
            $totalPrice += $item['price']*$item['quantity'];
          }
        }
      }else{
        $totalPrice = self::findBySql(
          "SELECT SUM(c.quantity * p.price) FROM products p LEFT JOIN cart_items c 
          ON p.id = c.product_id
          WHERE c.product_id = :p_id AND c.created_by = :created_by", ["p_id" => $productId, "created_by" => $userId])->scalar();
      }
      return $totalPrice;
    }
}
