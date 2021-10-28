<?php

namespace common\models;

use Yii;
use yii\db\Exception;

/**
 * This is the model class for table "{{%orders}}".
 *
 * @property int $id
 * @property float $total_price
 * @property int $status
 * @property string $firstname
 * @property string $lastname
 * @property string $email
 * @property string|null $transaction_id
 * @property int|null $created_at
 * @property int|null $created_by
 *
 * @property User $createdAt
 * @property User $createdBy
 * @property OrderAddresses $orderAddresses
 * @property OrderItems[] $orderItems
 */
class Order extends \yii\db\ActiveRecord
{

    const STATAUS_DRAFT = 0;
    const STATAUS_COMPLETED = 1;
    const STATAUS_FAILURED = 2;
    const STATAUS_DONE = 10;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%orders}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['total_price', 'status', 'firstname', 'lastname', 'email'], 'required'],
            [['total_price'], 'number'],
            [['status', 'created_at', 'created_by'], 'integer'],
            [['firstname', 'lastname'], 'string', 'max' => 45],
            [['email', 'transaction_id'], 'string', 'max' => 255],
            [['created_at'], 'number'],
            [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['created_by' => 'id']],
        ];
    }

    public static function stausList() {
        return [
            // '' => 'All',
            self::STATAUS_COMPLETED => 'paid',
            self::STATAUS_DONE => 'done',
            self::STATAUS_FAILURED => 'failured',
            self::STATAUS_DRAFT => 'draft',
        ];
    }
    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'total_price' => 'Total Price',
            'status' => 'Status',
            'firstname' => 'Firstname',
            'lastname' => 'Lastname',
            'email' => 'Email',
            'transaction_id' => 'Transaction ID',
            'created_at' => 'Created At',
            'created_by' => 'Created By',
        ];
    }

    /**
     * Gets query for [[CreatedAt]].
     *
     * @return \yii\db\ActiveQuery|\common\models\query\UserQuery
     */
    public function getCreatedAt()
    {
        return $this->hasOne(User::className(), ['id' => 'created_at']);
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
     * Gets query for [[OrderAddresses]].
     *
     * @return \yii\db\ActiveQuery|\common\models\query\OrderAddressesQuery
     */
    public function getOrderAddress()
    {
        return $this->hasOne(OrderAddress::class, ['order_id' => 'id']);
    }

    /**
     * Gets query for [[OrderItems]].
     *
     * @return \yii\db\ActiveQuery|\common\models\query\OrderItemsQuery
     */
    public function getOrderItems()
    {
        return $this->hasMany(OrderItem::class, ['order_id' => 'id']);
    }

    /**
     * {@inheritdoc}
     * @return \common\models\query\OrderQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \common\models\query\OrderQuery(get_called_class());
    }
    public function saveOrderItems(){
        $transaction = Yii::$app->db->beginTransaction();
        $cartItems = CartItem::getItemsForUser(Yii::$app->user->id);

        foreach($cartItems as $cartItem){
            $orderItem = new OrderItem();
            $orderItem->product_name = $cartItem['name'];
            $orderItem->product_id = $cartItem['product_id'];
            $orderItem->unit_price = $cartItem['price'];
            $orderItem->order_id = $this->id;
            $orderItem->quantity = $cartItem['quantity'];
            if(!$orderItem->save()){
                $transaction->rollBack();
                throw new Exception('Products not saved '.implode(', ', $orderItem->getFirstErrors()));
            }
        }
        $transaction->commit();
        return true;
    }

    public function saveAddress($postData){
        $orderAddress = new OrderAddress();
        $orderAddress->order_id = $this->id;
        if($orderAddress->load($postData)  && $orderAddress->save()){
            return true;
        }else{
            throw new Exception("Could not save order address ".implode("<br>", $orderAddress->getFirstErrors()));
        }
    }

    public function sendEmailToVendor(){
        {
            return Yii::$app
                ->mailer
                ->compose(
                    ['html' => 'order_completed_vendor-html', 'text' => 'order_completed_vendor-text'],
                    ['order' => $this]
                )
                ->setFrom([Yii::$app->params['supportEmail'] => Yii::$app->name . ' robot'])
                ->setTo(Yii::$app->params['vendorEmail'])
                ->setSubject('You have a new order on ' . Yii::$app->name)
                ->send();
        }
    }
    public function sendEmailToCustomer(){
        {
            return Yii::$app
                ->mailer
                ->compose(
                    ['html' => 'order_completed_customer-html', 'text' => 'order_completed_customer-html'],
                    ['order' => $this]
                )
                ->setFrom([Yii::$app->params['supportEmail'] => Yii::$app->name . ' robot'])
                ->setTo($this->email)
                ->setSubject('You order successfully created. Order number is '. $this->id)
                ->send();
        }
    }
		public function getItemsQuantity(){
			$total_quantity = 0;
			// foreach($this->orderItems as $item){
			// 	$total_quantity += $item->quantity;
			// }
			return $total_quantity;
		}
}
