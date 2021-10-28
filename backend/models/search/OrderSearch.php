<?php

namespace backend\models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Order;

/**
 * OrderSearch represents the model behind the search form of `common\models\Order`.
 */
class OrderSearch extends Order
{
    public $fullname;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'status', 'created_at', 'created_by'], 'integer'],
            [['total_price'], 'number'],
            [['firstname', 'lastname', 'fullname', 'email', 'transaction_id'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    public function fields(){
      $fields = parent::fields();
      return array_merge($fields, [
        'fullname' => function(){
          return $this->firstname . ' ' . $this->lastname;
        }
      ]);
    }
    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */

    public function search($params)
    {
        $query = Order::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
        $dataProvider->sort->defaultOrder = ['created_at' => SORT_DESC];
        $dataProvider->sort->attributes['fullname'] = [
          'label' => 'Full Name',
          'asc' => ['firstname' => SORT_ASC, 'lastname' => SORT_ASC],
          'desc' => ['firstname' => SORT_DESC, 'lastname' => SORT_DESC],
          'default' => SORT_DESC,
        ];
          // echo '<pre>';
          // print_r($dataProvider->sort->attributes);exit;
          // echo '</pre>';

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        if($this->fullname){
          $query->andWhere("CONCAT(firstname, ' ', lastname) LIKE :fullname" , ['fullname' => "%{$this->fullname}%"]);
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'total_price' => $this->total_price,
            'status' => $this->status,
            'created_at' => $this->created_at,
            'created_by' => $this->created_by,
        ]);

        $query->andFilterWhere(['like', 'firstname', $this->firstname])
            ->andFilterWhere(['like', 'lastname', $this->lastname])
            ->andFilterWhere(['like', 'email', $this->email])
            ->andFilterWhere(['like', 'transaction_id', $this->transaction_id]);
        return $dataProvider;
    }
}