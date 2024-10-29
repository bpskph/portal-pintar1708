<?php
namespace app\controllers;
use app\models\Apel;
use app\models\ApelSearch;
use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
/**
 * ApelController implements the CRUD actions for Apel model.
 */
class ApelController extends BaseController
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
                            'actions' => ['error', 'view', 'index'],
                            'allow' => true,
                        ],
                        [
                            'actions' => ['create', 'delete', 'update'],
                            'allow' => true,
                            'matchCallback' => function ($rule, $action) {
                                return !\Yii::$app->user->isGuest && (\Yii::$app->user->identity->level === 0 || \Yii::$app->user->identity->issdmmember);
                            },
                        ],
                        [
                            'actions' => [''], // add all actions to take guest to login page
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
        $searchModel = new ApelSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }
    public function actionView($id)
    {
        if (Yii::$app->request->isAjax) {
            return $this->renderAjax('view', [
                'model' => $this->findModel($id),
            ]);
        } else {
            return $this->render('view', [
                'model' => $this->findModel($id),
            ]);
        }
    }
    public function actionCreate()
    {
        $model = new Apel();
        if ($this->request->isPost) {
            $model->load($this->request->post());
            $model->reporter = Yii::$app->user->identity->username;
            $peserta = $model->bendera;
            if ($model->bendera != null) {
                $cek = implode("@bps.go.id, ", $peserta) . "@bps.go.id";
                $model->bendera = $cek;
            }
            if ($model->validate()) {
                if ($model->save()) {
                    Yii::$app->session->setFlash('success', "Jadwal apel/upacara berhasil ditambahkan. Terima kasih.");
                    return $this->redirect(['view', 'id' => $model->id_apel]);
                }
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
        if ($this->request->isPost) {
            $model->load($this->request->post());
            $model->timestamp_apel_lastupdate = date('Y-m-d H:i:s');
            $peserta = $model->bendera;
            if ($model->bendera != null) {
                $cek = implode("@bps.go.id, ", $peserta) . "@bps.go.id";
                $model->bendera = $cek;
            }        
            if ($model->validate()) {
                $model->timestamp_apel_lastupdate = date('Y-m-d H:i:s');
                if ($model->save()) {
                    Yii::$app->session->setFlash('success', "Jadwal apel/upacara berhasil dimutakhirkan. Terima kasih.");
                    return $this->redirect(['view', 'id' => $model->id_apel]);
                }
            }
        }
        return $this->render('update', [
            'model' => $model,
        ]);
    }
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        $affected_rows = Apel::updateAll(['deleted' => 1, 'timestamp_apel_lastupdate' => date('Y-m-d H:i:s')], 'id_apel = "' . $id . '"');
        if ($affected_rows == 0) {
            Yii::$app->session->setFlash('warning', "Gagal. Mohon hubungi Admin.");
            return $this->redirect(['index']);
        } else {
            Yii::$app->session->setFlash('success', "Jadwal Apel/Upacara berhasil dihapus. Terima kasih.");
            return $this->redirect(['index']);
        }
    }
    protected function findModel($id_apel)
    {
        if (($model = Apel::findOne(['id_apel' => $id_apel])) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
