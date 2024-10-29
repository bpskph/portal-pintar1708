<?php

namespace app\controllers;

use app\models\Agenda;
use app\models\Zooms;
use app\models\ZoomsSearch;
use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * ZoomsController implements the CRUD actions for Zooms model.
 */
class ZoomsController extends BaseController
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
                        'getlistpeserta' => ['POST'], // Restrict the HTTP method for this action to POST
                    ],
                ],
                'access' => [
                    'class' => \yii\filters\AccessControl::className(),
                    'rules' => [
                        [
                            'actions' => ['error', 'index', 'view'],
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

    /**
     * Lists all Zooms models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new ZoomsSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Zooms model.
     * @param int $id_zooms Id Zooms
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
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

    /**
     * Creates a new Zooms model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate($fk_agenda)
    {
        $model = new Zooms();

        if ($fk_agenda != '') {
            $cekmetode = Agenda::findOne(['id_agenda' => $fk_agenda]);
            if ($cekmetode->metode != 0 || $cekmetode->reporter != Yii::$app->user->identity->username) {
                Yii::$app->session->setFlash('warning', "Maaf. Hanya pengusul agenda <strong>online</strong> terkait yang dapat mengajukan Permohonan Zoom Meeting.");
                return $this->redirect(['index']);
            }
        }

        if ($this->request->isPost) {
            $model->load($this->request->post());
            $model->proposer = Yii::$app->user->identity->username;

            // Extracting values from the form
            $cekjenissurat = $_POST['Zooms']['fk_surat'];
            if (str_contains($cekjenissurat, '0-')) {
                $model->fk_surat = str_replace('0-', '', $cekjenissurat);
                $model->jenis_surat =  0;
            } else {
                $model->fk_surat = str_replace('1-', '', $cekjenissurat);
                $model->jenis_surat =  1;
            }
            // die(var_dump($model));
            // Loading form data after setting custom attributes
            // if ($model->load($this->request->post()) && $model->validate() && $model->save()) {
            if ($model->validate() && $model->save()) {
                return $this->redirect(['view', 'id' => $model->id_zooms]);
            } else {
                // Check for errors
                // print_r($model->errors);
            }
        } else {
            $model->loadDefaultValues();
        }

        return $this->render('create', [
            'model' => $model,
            'fk_agenda' => $fk_agenda
        ]);
    }

    /**
     * Updates an existing Zooms model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id_zooms Id Zooms
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id, $fk_agenda)
    {
        $model = $this->findModel($id);

        if ($model->fk_agenda != $fk_agenda) {
            Yii::$app->session->setFlash('warning', "Maaf. Keterangan Agenda dan Zoom tidak sesuai.");
            return $this->redirect(['index']);
        }
        $cekmetode = Agenda::findOne(['id_agenda' => $fk_agenda]);
        if ($cekmetode->reporter != Yii::$app->user->identity->username || $model->proposer != Yii::$app->user->identity->username) {
            Yii::$app->session->setFlash('warning', "Maaf. Hanya pengusul agenda dan zooms terkait yang dapat mengubah Permohonan Zoom Meeting.");
            return $this->redirect(['index']);
        }
        if ($cekmetode->progress == 1 || $cekmetode->progress == 3 || $cekmetode->deleted == 1) {
            Yii::$app->session->setFlash('warning', "Zoom untuk Agenda yang sudah selesai, batal atau sudah dihapus tidak dapat diubah kembali. Terima kasih.");
            return $this->redirect(['index']);
        }

        if ($this->request->isPost) {
            $model->load($this->request->post());
            $model->timestamp_lastupdate = date('Y-m-d H:i:s');
            if ($model->validate()) {
                $model->timestamp_lastupdate = date('Y-m-d H:i:s');
                if ($model->save()) {
                    Yii::$app->session->setFlash('success', "Usulan Zoom berhasil dimutakhirkan. Terima kasih.");
                    return $this->redirect(['view', 'id' => $model->id_zooms]);
                }
            }
        }

        return $this->render('update', [
            'model' => $model,
            'fk_agenda' => $fk_agenda
        ]);
    }

    /**
     * Deletes an existing Zooms model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $id_zooms Id Zooms
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */

    public function actionDelete($id)
    {
        $affected_rows = Zooms::updateAll(['deleted' => 1, 'timestamp_lastupdate' => date('Y-m-d H:i:s')], 'id_zooms = "' . $id . '"');
        if ($affected_rows == 0) {
            Yii::$app->session->setFlash('warning', "Gagal. Mohon hubungi Admin.");
            return $this->redirect(['index']);
        } else {
            Yii::$app->session->setFlash('success', "Usulan zoom berhasil dihapus. Terima kasih.");
            return $this->redirect(['index']);
        }
    }

    /**
     * Finds the Zooms model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id_zooms Id Zooms
     * @return Zooms the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id_zooms)
    {
        if (($model = Zooms::findOne(['id_zooms' => $id_zooms])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
