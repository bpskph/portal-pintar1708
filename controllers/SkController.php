<?php

namespace app\controllers;

use app\models\Sk;
use app\models\SkSearch;
use Yii;
use yii\db\Query;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\UploadedFile;

/**
 * SkController implements the CRUD actions for Sk model.
 */
class SkController extends BaseController
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
                            'actions' => ['create', 'update', 'delete'],
                            'allow' => true,
                            'matchCallback' => function ($rule, $action) {
                                return !\Yii::$app->user->isGuest && (\Yii::$app->user->identity->level === 0) || \Yii::$app->user->identity->sk_maker === 1;
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

    /**
     * Lists all Sk models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new SkSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Sk model.
     * @param int $id_sk Id Sk
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id_sk)
    {
        if (Yii::$app->request->isAjax) {
            return $this->renderAjax('view', [
                'model' => $this->findModel($id_sk),
            ]);
        } else {
            return $this->render('view', [
                'model' => $this->findModel($id_sk),
            ]);
        }
    }

    /**
     * Creates a new Sk model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    // public function actionCreate()
    // {
    //     $model = new Sk();

    //     if ($this->request->isPost) {
    //         if ($model->load($this->request->post()) && $model->save()) {
    //             return $this->redirect(['view', 'id_sk' => $model->id_sk]);
    //         }
    //     } else {
    //         $model->loadDefaultValues();
    //     }

    //     return $this->render('create', [
    //         'model' => $model,
    //     ]);
    // }

    public function actionCreate()
    {
        $model = new Sk();

        Yii::$app->params['uploadPath'] = Yii::getAlias("@app") . '/sk';

        if ($this->request->isPost) {
            $model->load($this->request->post());
            $model->tanggal_sk = date("Y-m-d", strtotime($_POST['Sk']['tanggal_sk']));
            $model->reporter = Yii::$app->user->identity->username;
            $peserta = $model->nama_dalam_sk;
            if ($model->nama_dalam_sk != null) {
                $cek = implode("@bps.go.id, ", $peserta) . "@bps.go.id";
                $model->nama_dalam_sk = $cek;
            }
            $model->filepdf = UploadedFile::getInstance($model, 'filepdf');
            $query = new Query();
            $query->select('id_sk')->from('sk')->orderBy(['id_sk' => SORT_DESC])->limit(1);
            $latestId = $query->scalar();
            $path = Yii::$app->params['uploadPath'] . '/' . ($latestId + 1) . '.' . $model->filepdf->extension;

            if ($model->validate()) {
                if ($model->save()) {
                    if (isset($model->filepdf))
                        $model->filepdf->saveAs($path);
                    Yii::$app->session->setFlash('success', "Data SK berhasil ditambahkan. Terima kasih.");
                    return $this->redirect(['view', 'id_sk' => $model->id_sk]);
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

        Yii::$app->params['uploadPath'] = Yii::getAlias("@app") . '/sk';

        if ($model->reporter != Yii::$app->user->identity->username) {
            Yii::$app->session->setFlash('warning', "Maaf. Hanya pemilik data SK terkait yang dapat mengubah datanya.");
            return $this->redirect(['index']);
        }
        if ($model->deleted != 0) {
            Yii::$app->session->setFlash('warning', "Maaf. Data SK yang telah dihapus tidak dapat diubah kembali.");
            return $this->redirect(['index']);
        }

        if ($this->request->isPost) {
            $model->load($this->request->post());

            $model->tanggal_sk = date("Y-m-d", strtotime($_POST['Sk']['tanggal_sk']));
            $peserta = $model->nama_dalam_sk;
            if ($model->nama_dalam_sk != null) {
                $cek = implode("@bps.go.id, ", $peserta) . "@bps.go.id";
                $model->nama_dalam_sk = $cek;
            }

            $screenshot = UploadedFile::getInstance($model, 'filepdf');
            if ($screenshot) {
                // The user has uploaded a new file, so update the file attribute
                $model->filepdf = $screenshot;
                $path = Yii::$app->params['uploadPath'] . '/' . $model->id_sk . '.' . $model->filepdf->extension;
            } else {
                // The user has not uploaded a new file, so keep the existing file attribute
                $model->screenshot = $model->getOldAttribute('filepdf');
            }

            if ($model->filepdf !== null && $model->validate()) {
                $model->timestamp_lastupdate = date('Y-m-d H:i:s');
                if ($model->save()) {
                    if (isset($model->screenshot))
                        $model->screenshot->saveAs($path);
                    Yii::$app->session->setFlash('success', "Data SK berhasil diperbaiki. Terima kasih.");
                    return $this->redirect(['view', 'id_sk' => $model->id_sk]);
                }
            }
        }
        return $this->render('update', [
            'model' => $model,
        ]);
    }


    /**
     * Deletes an existing Sk model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $id_sk Id Sk
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id_sk)
    {
        $affected_rows = Sk::updateAll(['deleted' => 1, 'timestamp_lastupdate' => date('Y-m-d H:i:s')], 'id_sk = "' . $id_sk . '"');
        if ($affected_rows == 0) {
            Yii::$app->session->setFlash('warning', "Gagal. Mohon hubungi Admin.");
            return $this->redirect(['index']);
        } else {
            Yii::$app->session->setFlash('success', "Data SK berhasil dihapus. Terima kasih.");
            return $this->redirect(['index']);
        }
    }
    /**
     * Finds the Sk model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id_sk Id Sk
     * @return Sk the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id_sk)
    {
        if (($model = Sk::findOne(['id_sk' => $id_sk])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
