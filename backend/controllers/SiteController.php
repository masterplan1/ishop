<?php

namespace backend\controllers;

use common\models\LoginForm;
use common\models\Order;
use common\models\OrderItem;
use common\models\Product;
use common\models\User;
use DateTime;
use Yii;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\web\Controller;
use yii\web\Response;

/**
 * Site controller
 */
class SiteController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['login', 'error', 'forgot-password'],
                        'allow' => true,
                    ],
                    [
                        'actions' => ['logout', 'index'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        $totalEarnings = Order::find()->paid()->sum('total_price');
        $totalOrders = Order::find()->paid()->count();
        $totalProducts = OrderItem::find()
            ->alias('oi')
            ->innerJoin(Order::tableName(). ' o', 'o.id = oi.order_id')
            ->andWhere(['o.status' => [Order::STATAUS_COMPLETED, Order::STATAUS_DONE]])
            ->sum('quantity');
        $totalUsers = User::find()->andWhere(['status' => User::STATUS_ACTIVE])->count();

        $orders = Order::findBySql("SELECT CAST(DATE_FORMAT(FROM_UNIXTIME(o.created_at), '%Y-%m-%d %H-%i-%s') as DATE) as `date`,
            SUM(o.total_price) as `total_price` FROM orders o
            WHERE o.status IN (:status_completed, :status_done)
            GROUP BY CAST(DATE_FORMAT(FROM_UNIXTIME(o.created_at), '%Y-%m-%d %H-%i-%s') as DATE)
            ORDER BY o.created_at", ['status_completed' => Order::STATAUS_COMPLETED, 'status_done' => Order::STATAUS_DONE])
            ->asArray()->all();
        
        $earningOrders = [];
        if(!empty($orders)){
            $labels = [];
            $d = new DateTime();
            $minOrderDate = $orders[0]['date'];
            $dateMin = new DateTime($minOrderDate);
            $orderArrayPriceMap = ArrayHelper::map($orders, 'date', 'total_price');
            while($dateMin->getTimestamp() < $d->getTimestamp()){
                $labels[] = $dateMin->format('Y-m-d');
                $earningOrders[] = (float)($orderArrayPriceMap[$dateMin->format('Y-m-d')] ?? 0);
                $dateMin->setTimestamp($dateMin->getTimestamp() + 86400);
            }
        }

        //Chart Pie
        $countriesPie = Order::findBySql("SELECT oa.country, SUM(o.total_price) as total_price
        FROM orders o INNER JOIN order_addresses oa ON o.id = oa.order_id 
        WHERE o.status IN (:status_completed, :status_done) 
        GROUP BY oa.country
        ORDER BY SUM(o.total_price)", [
            'status_completed' => Order::STATAUS_COMPLETED, 
            'status_done' => Order::STATAUS_DONE
            ])->asArray()->all();
        $countriesNames = ArrayHelper::getColumn($countriesPie, 'country');
        $countriesData = array_map(function($item){
            return (float)$item;
        }, ArrayHelper::getColumn($countriesPie, 'total_price'));

        $bgColors = [];
        foreach($countriesNames as $item){
            $bgColors[] = 'rgba('.rand(0, 255).', '.rand(0, 255).', '.rand(0, 255).')';
        }
        return $this->render('index', compact(
            'totalEarnings', 
            'totalOrders', 
            'totalProducts', 
            'totalUsers', 
            'earningOrders', 
            'labels', 
            'countriesNames', 
            'countriesData',
            'bgColors'
        ));
    }

    /**
     * Login action.
     *
     * @return string|Response
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $this->layout = 'blank';

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        }

        $model->password = '';

        return $this->render('login', [
            'model' => $model,
        ]);
    }

    

    /**
     * Logout action.
     *
     * @return Response
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }
    public function actionForgotPassword()
    {
        return 'Forgot Password';
    }
}
