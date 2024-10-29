<?php

namespace app\controllers;

use app\models\Popups;
use app\models\PopupsSearch;
use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * PopupsController implements the CRUD actions for Popups model.
 */
class PopupsController extends BaseController
{
    /**
     * @inheritDoc
     */
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
                            'actions' => ['create', 'update', 'delete'],
                            'allow' => true,
                            'matchCallback' => function ($rule, $action) {
                                return !\Yii::$app->user->isGuest && \Yii::$app->user->identity->level === 0;
                            },
                        ],
                        [
                            'actions' => ['index'], // add all actions to take guest to login page
                            'allow' => true,
                            'roles' => ['@'],
                        ],
                    ],
                ],
            ]
        );
    }

    /**
     * Lists all Popups models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new PopupsSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        if (Yii::$app->request->isAjax) {
            return $this->renderAjax('index', [
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider,
            ]);
        } else {
            return $this->render('index', [
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider,
            ]);
        }
    }

    public function actionCreate()
    {
        $model = new Popups();

        if ($this->request->isPost && $model->load($this->request->post())) {
            if ($model->save()) {
                Yii::$app->session->setFlash('success', "Popup berhasil ditambahkan.Terima kasih.");
                return $this->redirect(['index']);
            }
        } else {
            $model->loadDefaultValues();
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        if ($this->request->isPost && $model->load($this->request->post())) {
            $model->timestamp_lastupdate = date('Y-m-d H:i:s');
            if ($model->save()) {
                Yii::$app->session->setFlash('success', "Data popup berhasil dimutakhirkan. Terima kasih.");
                return $this->redirect(['index']);
            }
        }
        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Popups model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $id_popups Id Popups
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    
    public function actionDelete($id)
    {
        $affected_rows = Popups::updateAll(['deleted' => 1, 'timestamp_lastupdate' => date('Y-m-d H:i:s')], 'id_popups = "' . $id . '"');
        if ($affected_rows == 0) {
            Yii::$app->session->setFlash('warning', "Gagal. Mohon hubungi Admin.");
            return $this->redirect(['index']);
        } else {
            Yii::$app->session->setFlash('success', "Data popups berhasil dihapus. Terima kasih.");
            return $this->redirect(['index']);
        }
    }

    /**
     * Finds the Popups model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id_popups Id Popups
     * @return Popups the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id_popups)
    {
        if (($model = Popups::findOne(['id_popups' => $id_popups])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
