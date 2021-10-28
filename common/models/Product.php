<?php

namespace common\models;

use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\helpers\FileHelper;
use yii\helpers\StringHelper;

/**
 * This is the model class for table "{{%products}}".
 *
 * @property int $id
 * @property string $name
 * @property string|null $description
 * @property string|null $image
 * @property float $price
 * @property int $status
 * @property int|null $created_at
 * @property int|null $updated_at
 * @property int|null $created_by
 * @property int|null $updated_by
 *
 * @property CartItems[] $cartItems
 * @property User $createdBy
 * @property OrderItems[] $orderItems
 * @property User $updatedBy
 * @property yii\web\UploadedFile imageFile
 */
class Product extends \yii\db\ActiveRecord
{
    public $imageFile;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%products}}';
    }

    public function behaviors()
    {
      return [
        TimestampBehavior::class,
        BlameableBehavior::class
      ];
    }
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'price', 'status'], 'required'],
            [['description'], 'string'],
            [['price'], 'number'],
            [['imageFile'], 'image', 'extensions' => 'png jpg jpeg'],
            [['status', 'created_at', 'updated_at', 'created_by', 'updated_by'], 'integer'],
            [['name'], 'string', 'max' => 255],
            [['image'], 'string', 'max' => 2000],
            [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['created_by' => 'id']],
            [['updated_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['updated_by' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'description' => 'Description',
            'image' => 'Image',
            'price' => 'Price',
            'status' => 'Status',
            'imageFile' => 'Product Image',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'created_by' => 'Created By',
            'updated_by' => 'Updated By',
        ];
    }

    /**
     * Gets query for [[CartItems]].
     *
     * @return \yii\db\ActiveQuery|\common\models\query\CartItemsQuery
     */
    public function getCartItems()
    {
        return $this->hasMany(CartItem::className(), ['product_id' => 'id']);
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
     * Gets query for [[OrderItems]].
     *
     * @return \yii\db\ActiveQuery|\common\models\query\OrderItemsQuery
     */
    public function getOrderItems()
    {
        return $this->hasMany(OrderItem::className(), ['product_id' => 'id']);
    }

    /**
     * Gets query for [[UpdatedBy]].
     *
     * @return \yii\db\ActiveQuery|\common\models\query\UserQuery
     */
    public function getUpdatedBy()
    {
        return $this->hasOne(User::className(), ['id' => 'updated_by']);
    }

    /**
     * {@inheritdoc}
     * @return \common\models\query\ProductQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \common\models\query\ProductQuery(get_called_class());
    }

    private static function getImagePath($imageUrl){
      return Yii::getAlias('@frontend/web/storage') . $imageUrl;
    }

    public function save($runValidation = true, $attributeNames = null)
    {
        if($this->imageFile){
            $this->image = '/products/'. Yii::$app->security->generateRandomString(32) . $this->imageFile->name;
        }
        $transaction = Yii::$app->db->beginTransaction();
        $ok = parent::save($runValidation, $attributeNames);
        if($ok && !empty($this->imageFile)){
            $fullpath = self::getImagePath($this->image);
            $dir = dirname($fullpath);
            if(!FileHelper::createDirectory($dir) || !$this->imageFile->saveAs($fullpath)){
                $transaction->rollBack();
                return false;
            }
        }
        $transaction->commit();
        return $ok;
    }

    public function getImageUrl(){
    
        return self::formatImageUrl($this->image);
      
    }

    public static function formatImageUrl($imageUrl){
        if($imageUrl && file_exists(self::getImagePath($imageUrl))){
        return Yii::$app->params['frontendUrl'] . 'storage' . $imageUrl;
        }
        return Yii::$app->params['frontendUrl'] . 'img/no_image.svg';
    }

    public function getShortDescription(){
        return StringHelper::truncateWords(strip_tags($this->description), 30);
    }
}
