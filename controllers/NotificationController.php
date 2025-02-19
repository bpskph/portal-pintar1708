<?php

namespace app\controllers;

use Yii;
use app\models\Notification;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\filters\VerbFilter;

class NotificationController extends BaseController
{
    public function behaviors()
    {
        return array_merge(
            parent::behaviors(),
            [
                'verbs' => [
                    'class' => VerbFilter::className(),
                    'actions' => [
                        'delete' => ['POST'],
                    ],
                ],
                'access' => [
                    'class' => \yii\filters\AccessControl::className(),
                    'rules' => [
                        [
                            'actions' => ['error'],
                            'allow' => true,
                        ],
                        [
                            'actions' => ['index', 'mark-as-read', 'markallread', 'mark-as-read-and-view'], // add all actions to take guest to login page
                            'allow' => true,
                            'roles' => ['@'],
                        ],
                    ],
                ],
            ]
        );
    }
    public function beforeAction($action)
    {
        if ($action->id === 'mark-as-read-and-view') {
            $this->enableCsrfValidation = false; // Disable CSRF validation for the action
        }
        return parent::beforeAction($action);
    }
    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => Notification::find()->where(['user_id' => Yii::$app->user->id])->orderBy(['is_read' => SORT_ASC, 'created_at' => SORT_DESC]),
            'pagination' => [
                'pageSize' => 20,
            ],
        ]);

        if (Yii::$app->request->isAjax) {
            return $this->renderAjax('index', [
                'dataProvider' => $dataProvider,
            ]);
        } else {
            return $this->render('index', [
                'dataProvider' => $dataProvider,
            ]);
        }
        // return $this->render('index', ['notifications' => $notifications]);
    }
    public function actionMarkAsRead($id)
    {
        $notification = Notification::findOne($id);
        if ($notification && $notification->user_id == Yii::$app->user->id) {
            $notification->is_read = 1;
            $notification->read_at = date('Y-m-d H:i:s');
            $notification->save();
            return $this->redirect(['notification/index']);
        }
        throw new \yii\web\ForbiddenHttpException('You are not allowed to perform this action.');
    }
    public function actionMarkAsReadAndView($id)
    {
        $notification = Notification::findOne($id);
        if ($notification && $notification->user_id == Yii::$app->user->id) {
            $notification->is_read = 1;
            $notification->read_at = date('Y-m-d H:i:s');
            $notification->save();
            // return $this->redirect([$notification->link . '/' . $notification->link_id]);
        }
        // throw new \yii\web\ForbiddenHttpException('You are not allowed to perform this action.');
    }
    public function actionMarkallread()
    {
        $userId = Yii::$app->user->id;
        $notifications = Notification::find()->where(['user_id' => $userId, 'is_read' => 0])->all();

        foreach ($notifications as $notification) {
            $notification->is_read = 1;
            $notification->read_at = date('Y-m-d H:i:s');
            $notification->save();
        }

        return $this->redirect(['notification/index']);
    }
}
