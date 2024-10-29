<?php
namespace app\controllers;
use app\models\Linkmat;
use app\models\LinkmatSearch;
use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
/**
 * LinkmatController implements the CRUD actions for Linkmat model.
 */
class LinkmatController extends BaseController
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
                            'actions' => ['error', 'view', 'index', 'updateviews', 'share'],
                            'allow' => true,
                        ],
                        [
                            'actions' => ['moderasi'],
                            'allow' => true,
                            'matchCallback' => function ($rule, $action) {
                                return !\Yii::$app->user->isGuest && (\Yii::$app->user->identity->level === 0);
                            },
                        ],
                        [
                            'actions' => ['create', 'update', 'delete'], // add all actions to take guest to login page
                            'allow' => true,
                            'roles' => ['@'],
                        ],
                    ],
                ],
            ]
        );
    }
    public function actionIndex()
    {
        $searchModel = new LinkmatSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }
    public function actionView($id)
    {
        if (Yii::$app->request->isAjax) {
            $model = Linkmat::findOne($id);
            $model->views++;
            $model->save(false); // don't validate to save time
            return $this->renderAjax('view', [
                'model' => $this->findModel($id),
            ]);
        } else {
            $model = Linkmat::findOne($id);
            $model->views++;
            $model->save(false); // don't validate to save time
            return $this->render('view', [
                'model' => $this->findModel($id),
            ]);
        }
    }
    public function actionCreate()
    {
        $model = new Linkmat();
        if ($this->request->isPost && $model->load($this->request->post())) {
            $model->owner = Yii::$app->user->identity->username;
            if ($model->save()) {
                Yii::$app->session->setFlash('success', "Link berhasil ditambahkan. Mohon tunggu moderasi dari Admin. Terima kasih.");
                return $this->redirect(['view', 'id' => $model->id_linkmat]);
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
            $model->active = 0;
            if ($model->save()) {
                Yii::$app->session->setFlash('success', "Link berhasil dimutakhirkan. Mohon tunggu moderasi dari Admin. Terima kasih.");
                return $this->redirect(['view', 'id' => $model->id_linkmat]);
            }
        }
        return $this->render('update', [
            'model' => $model,
        ]);
    }
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        $affected_rows = Linkmat::updateAll(['active' => 2, 'timestamp_lastupdate' => date('Y-m-d H:i:s')], 'id_linkmat = "' . $id . '"');
        if ($affected_rows == 0) {
            Yii::$app->session->setFlash('warning', "Gagal. Mohon hubungi Admin.");
            return $this->redirect(['index']);
        } else {
            Yii::$app->session->setFlash('success', "Link berhasil di-nonaktifkan. Terima kasih.");
            return $this->redirect(['index']);
        }
    }
    protected function findModel($id_linkmat)
    {
        if (($model = Linkmat::findOne(['id_linkmat' => $id_linkmat])) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('The requested page does not exist.');
    }
    public function actionModerasi($id)
    {
        $model = Linkmat::findOne($id);
        if ($model->active == 0) {
            $affected_rows = Linkmat::updateAll(['active' => 1], ['id_linkmat' => $id]);
        } elseif ($model->active == 1)
            $affected_rows = Linkmat::updateAll(['active' => 0], ['id_linkmat' => $id]);
        if ($affected_rows === 0) {
            Yii::$app->session->setFlash('warning', "Gagal. Mohon hubungi Admin.");
            return $this->redirect(['index']);
        } else {
            Yii::$app->session->setFlash('success', "Status link berhasil dimoderasi/batalkan. Terima kasih.");
            return $this->redirect(['index']);
        }
    }
    public function actionUpdateviews($id)
    {
        $model = Linkmat::findOne($id);
        $model->views++;
        $model->save(false); // don't validate to save time
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        return ['success' => true];
    }
}
