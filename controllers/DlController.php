<?php

namespace app\controllers;

use app\models\Dl;
use app\models\DlSearch;
use Yii;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

class DlController extends BaseController
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
                            'actions' => [''],
                            'allow' => true,
                            'matchCallback' => function ($rule, $action) {
                                return !\Yii::$app->user->isGuest && (\Yii::$app->user->identity->level === 0) || \Yii::$app->user->identity->approver_mobildinas === 1;
                            },
                        ],
                        [
                            'actions' => ['create', 'view', 'index', 'update', 'delete'], // add all actions to take guest to login page
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
        $searchModel = new DlSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }
    public function actionView($id_dl)
    {
        $model =  $this->findModel($id_dl);

        if ($model->deleted == 1) {
            Yii::$app->session->setFlash('warning', "Data perjalanan dinas ini sudah dihapus.");
            return $this->redirect(['index']);
        }

        if (Yii::$app->request->isAjax) {
            return $this->renderAjax('view', [
                'model' => $this->findModel($id_dl),
            ]);
        } else {
            return $this->render('view', [
                'model' => $this->findModel($id_dl),
            ]);
        }
    }
    public function actionCreate()
    {
        $model = new Dl();

        if ($this->request->isPost) {
            $model->load($this->request->post());
            $model->reporter = Yii::$app->user->identity->username;
            $model->tanggal_mulai = date("Y-m-d", strtotime($_POST['Dl']['tanggal_mulai']));
            $model->tanggal_selesai = date("Y-m-d", strtotime($_POST['Dl']['tanggal_selesai']));   
            $peserta = $model->pegawai; 
            if ($model->pegawai != null) {
                $cek = implode("@bps.go.id, ", $peserta) . "@bps.go.id";
                $model->pegawai = $cek;
            }        
            if ($model->validate() && $model->save()) {
                Yii::$app->session->setFlash('success', "Data perjalanan dinas berhasil ditambahkan. Terima kasih.");
                return $this->redirect(['view', 'id_dl' => $model->id_dl]);
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

        if ($model->reporter != Yii::$app->user->identity->username) {
            Yii::$app->session->setFlash('warning', "Maaf. Hanya penginput data DL terkait yang dapat mengubah informasinya.");
            return $this->redirect(['index']);
        }
        if ($model->deleted != 0) {
            Yii::$app->session->setFlash('warning', "Maaf. Data DL yang telah dihapus tidak dapat diubah kembali.");
            return $this->redirect(['index']);
        }       

        if ($this->request->isPost) {
            $model->load($this->request->post());
            date_default_timezone_set('Asia/Jakarta');
            $model->timestamp_lastupdate = date('Y-m-d H:i:s');
            $model->tanggal_mulai = date("Y-m-d", strtotime($_POST['Dl']['tanggal_mulai']));
            $model->tanggal_selesai = date("Y-m-d", strtotime($_POST['Dl']['tanggal_selesai']));
            $peserta = $model->pegawai; 
            if ($model->pegawai != null) {
                $cek = implode("@bps.go.id, ", $peserta) . "@bps.go.id";
                $model->pegawai = $cek;
            }   
            if ($model->validate()) {
                date_default_timezone_set('Asia/Jakarta');
                $model->timestamp_lastupdate = date('Y-m-d H:i:s');                
                if ($model->save()) {
                    Yii::$app->session->setFlash('success', "Data perjalanan dinas berhasil diperbaiki. Terima kasih.");
                    return $this->redirect(['view', 'id_dl' => $model->id_dl]);
                }
            }
        }
        return $this->render('update', [
            'model' => $model,
        ]);
    }
    public function actionDelete($id_dl)
    {
        date_default_timezone_set('Asia/Jakarta');
        $affected_rows = Dl::updateAll(['deleted' => 1, 'timestamp_lastupdate' => date('Y-m-d H:i:s')], 'id_dl = "' . $id_dl . '"');
        if ($affected_rows == 0) {
            Yii::$app->session->setFlash('warning', "Gagal. Mohon hubungi Admin.");
            return $this->redirect(['index']);
        } else {
            Yii::$app->session->setFlash('success', "Data DL berhasil dihapus. Terima kasih.");
            return $this->redirect(['index']);
        }
    }
    protected function findModel($id_dl)
    {
        if (($model = Dl::findOne(['id_dl' => $id_dl])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
