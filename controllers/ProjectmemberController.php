<?php

namespace app\controllers;

use app\models\Projectmember;
use app\models\ProjectmemberSearch;
use Yii;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

class ProjectmemberController extends BaseController
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
                            'actions' => ['error', 'index'],
                            'allow' => true,
                        ],
                        [
                            'actions' => ['delete', 'aktifkanlagi', 'toggleketua'],
                            'allow' => true,
                            'matchCallback' => function ($rule, $action) {
                                return !\Yii::$app->user->isGuest && (\Yii::$app->user->identity->level === 0);
                            },
                        ],
                        [
                            'actions' => ['create', 'update', 'toggleoperator'], // add all actions to take guest to login page
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
        if ($action->id === 'delete' || $action->id === 'toggleoperator') {
            $this->enableCsrfValidation = false; // Disable CSRF validation for the action
        }
        return parent::beforeAction($action);
    }
    public function actionIndex($year)
    {
        $searchModel = new ProjectmemberSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);
        if ($year == date("Y"))
            $dataProvider->query->andWhere(['tahun' => date("Y")]);
        elseif ($year != '')
            $dataProvider->query->andWhere(['tahun' => $year]);
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }
    public function actionView($id_projectmember)
    {
        return $this->render('view', [
            'model' => $this->findModel($id_projectmember),
        ]);
    }
    public function actionCreate()
    {
        $model = new Projectmember();
        if ($this->request->isPost) {
            if ($model->load($this->request->post()) && $model->save()) {
                Yii::$app->session->setFlash('success', "Data berhasil ditambahkan. Terima kasih.");
                return $this->redirect(['index', 'year' => '']);
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
        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', "Data berhasil dimutakhirkan. Terima kasih.");
            return $this->redirect(['index', 'year' => '']);
        }
        return $this->render('update', [
            'model' => $model,
        ]);
    }
    public function actionDelete($id)
    {
        date_default_timezone_set('Asia/Jakarta');
        $affected_rows = Projectmember::updateAll(['member_status' => 0, 'timetstamp_projectmember_lastupdate' => date('Y-m-d H:i:s')], 'id_projectmember = "' . $id . '"');
        if ($affected_rows == 0) {
            Yii::$app->session->setFlash('warning', "Gagal. Mohon hubungi Admin.");
            return $this->redirect(['index', 'year' => '']);
        } else {
            Yii::$app->session->setFlash('success', "Pengguna berhasil di-nonaktifkan dari project terkait. Terima kasih.");
            return $this->redirect(['index', 'year' => '']);
        }
    }
    public function actionAktifkanlagi($id)
    {
        date_default_timezone_set('Asia/Jakarta');
        $affected_rows = Projectmember::updateAll(['member_status' => 1, 'timetstamp_projectmember_lastupdate' => date('Y-m-d H:i:s')], 'id_projectmember = "' . $id . '"');
        if ($affected_rows == 0) {
            Yii::$app->session->setFlash('warning', "Gagal. Mohon hubungi Admin.");
            return $this->redirect(['index', 'year' => '']);
        } else {
            Yii::$app->session->setFlash('success', "Pengguna berhasil diaktifkan kembali pada project terkait. Terima kasih.");
            return $this->redirect(['index', 'year' => '']);
        }
    }
    protected function findModel($id_projectmember)
    {
        if (($model = Projectmember::findOne(['id_projectmember' => $id_projectmember])) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('The requested page does not exist.');
    }
    public function actionToggleketua($id)
    {
        $model = Projectmember::findOne($id);
        if ($model->member_status == 2) {
            $affected_rows = Projectmember::updateAll(['member_status' => 1], ['id_projectmember' => $id]);
        } else
            $affected_rows = Projectmember::updateAll(['member_status' => 2], ['id_projectmember' => $id]);
        if ($affected_rows === 0) {
            Yii::$app->session->setFlash('warning', "Gagal. Mohon hubungi Admin.");
            return $this->redirect(['index', 'year' => '']);
        } else {
            Yii::$app->session->setFlash('success', "Status ketua berhasil ditetap/batalkan. Terima kasih.");
            return $this->redirect(['index', 'year' => '']);
        }
    }
    public function actionToggleoperator($id)
    {
        $model = Projectmember::findOne($id);
        $pengguna = Yii::$app->user->identity->username;
        $ketua = Projectmember::find()
            ->select('*')
            ->where(['fk_project' => $model->fk_project])
            ->andWhere(['pegawai' => $pengguna])
            ->andWhere(['member_status' => 2])
            ->count();
        if (Yii::$app->user->identity->level != 0 && $ketua <= 0) {
            Yii::$app->session->setFlash('warning', "Anda hanya dapat mengatur data project yang Anda ketuai. Terima kasih.");
            return $this->redirect(['index', 'year' => '']);
        }
        if ($model->member_status == 3) {
            $affected_rows = Projectmember::updateAll(['member_status' => 1], ['id_projectmember' => $id]);
        } else
            $affected_rows = Projectmember::updateAll(['member_status' => 3], ['id_projectmember' => $id]);
        if ($affected_rows === 0) {
            Yii::$app->session->setFlash('warning', "Gagal. Mohon hubungi Admin.");
            return $this->redirect(['index', 'year' => '']);
        } else {
            Yii::$app->session->setFlash('success', "Status operator pengguna berhasil ditetap/batalkan. Terima kasih.");
            return $this->redirect(['index', 'year' => '']);
        }
    }
}
