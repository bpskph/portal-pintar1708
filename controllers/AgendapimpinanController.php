<?php
namespace app\controllers;
use app\models\Agendapimpinan;
use app\models\AgendapimpinanSearch;
use DateTime;
use Yii;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

class AgendapimpinanController extends BaseController
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
                                return !\Yii::$app->user->isGuest && (\Yii::$app->user->identity->level === 0 || \Yii::$app->user->identity->issekretaris);
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
        $searchModel = new AgendapimpinanSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }
    public function actionView($id)
    {
        $model =  $this->findModel($id);

        if ($model->deleted == 1) {
            Yii::$app->session->setFlash('warning', "Data agenda pimpinan ini sudah dihapus.");
            return $this->redirect(['index']);
        }

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
        $model = new Agendapimpinan();
        $searchModel = new AgendapimpinanSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);
        $dataProvider->query
            ->andWhere(['>=', 'waktumulai', date('Y-m-d H:i:s')])
            ->andWhere(['<=', 'waktumulai', date('Y-m-d H:i:s', strtotime('+2 weeks'))])
            ->andWhere(['deleted' => '0']);
        $dataProvider->pagination = false;
        if ($this->request->isPost) {
            // die($_POST['Agenda']['pelaksana']);
            $model->load($this->request->post());
            $model->reporter = Yii::$app->user->identity->username;
            if ($model->pendamping != null) {
                $peserta = $model->pendamping;
                $cek = implode("@bps.go.id, ", $peserta) . "@bps.go.id";
                $model->pendamping = $cek;
            }
            if (str_contains($_POST['Agendapimpinan']['waktumulai'], 'WIB')) {
                /* WAKTU SELESAI */
                $datetimeStr = $_POST['Agendapimpinan']['waktumulai'];
                $datetime = DateTime::createFromFormat('Y-m-d H:i T', $datetimeStr);
                $formattedDatetime = $datetime->format('Y-m-d H:i:s');
                $model->waktumulai = $formattedDatetime;
            } else {
                $model->waktumulai = date("Y-m-d H:i:s", strtotime($_POST['Agendapimpinan']['waktumulai']));
            }
            if (str_contains($_POST['Agendapimpinan']['waktuselesai'], 'WIB')) {
                /* WAKTU SELESAI */
                $datetimeStr = $_POST['Agendapimpinan']['waktuselesai'];
                $datetime = DateTime::createFromFormat('Y-m-d H:i T', $datetimeStr);
                $formattedDatetime = $datetime->format('Y-m-d H:i:s');
                $model->waktuselesai = $formattedDatetime;
            } else {
                $model->waktuselesai = date("Y-m-d H:i:s", strtotime($_POST['Agendapimpinan']['waktuselesai']));
            }
            if ($model->validate()) {
                if ($model->save()) {
                    Yii::$app->session->setFlash('success', "Agenda pimpinan berhasil ditambahkan. Terima kasih.");
                    return $this->redirect(['view', 'id' => $model->id_agendapimpinan]);
                }
            }
        } else {
            $model->loadDefaultValues();
        }
        return $this->render('create', [
            'model' => $model,
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $searchModel = new AgendapimpinanSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);
        $dataProvider->query
            ->andWhere(['>=', 'waktumulai', date('Y-m-d H:i:s')])
            ->andWhere(['<=', 'waktumulai', date('Y-m-d H:i:s', strtotime('+2 weeks'))])
            ->andWhere(['deleted' => '0']);
        $dataProvider->pagination = false;
        // if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
        //     return $this->redirect(['view', 'id' => $model->id_agendapimpinan]);
        // }
        if ($this->request->isPost) {
            $model->load($this->request->post());
            date_default_timezone_set('Asia/Jakarta');
            $model->timestamp_agendapimpinan_lastupdate = date('Y-m-d H:i:s');
            if ($model->pendamping != null) {
                $peserta = $model->pendamping;
                $cek = implode("@bps.go.id, ", $peserta) . "@bps.go.id";
                $model->pendamping = $cek;
            }
            /* -------------- */
            if (str_contains($_POST['Agendapimpinan']['waktumulai'], 'WIB')) {
                /* WAKTU SELESAI */
                // Get the datetime string from the $_POST variable
                $datetimeStr = $_POST['Agendapimpinan']['waktumulai'];
                // Parse the datetime string and convert it to a DateTime object
                $datetime = DateTime::createFromFormat('Y-m-d H:i T', $datetimeStr);
                // Convert the DateTime object to a string in the desired format
                $formattedDatetime = $datetime->format('Y-m-d H:i:s');
                $model->waktumulai = $formattedDatetime;
            } else {
                $model->waktumulai = date("Y-m-d H:i:s", strtotime($_POST['Agendapimpinan']['waktumulai']));
            }
            if (str_contains($_POST['Agendapimpinan']['waktuselesai'], 'WIB')) {
                /* WAKTU SELESAI */
                // Get the datetime string from the $_POST variable
                $datetimeStr = $_POST['Agendapimpinan']['waktuselesai'];
                // Parse the datetime string and convert it to a DateTime object
                $datetime = DateTime::createFromFormat('Y-m-d H:i T', $datetimeStr);
                // Convert the DateTime object to a string in the desired format
                $formattedDatetime = $datetime->format('Y-m-d H:i:s');
                $model->waktuselesai = $formattedDatetime;
            } else {
                $model->waktuselesai = date("Y-m-d H:i:s", strtotime($_POST['Agendapimpinan']['waktuselesai']));
            }
            if ($model->validate()) {
                date_default_timezone_set('Asia/Jakarta');
                $model->timestamp_agendapimpinan_lastupdate = date('Y-m-d H:i:s');
                if ($model->save()) {
                    Yii::$app->session->setFlash('success', "Agenda pimpinan berhasil dimutakhirkan. Terima kasih.");
                    return $this->redirect(['view', 'id' => $model->id_agendapimpinan]);
                }
            }
        }
        return $this->render('update', [
            'model' => $model,
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }
    public function actionDelete($id)
    {
        date_default_timezone_set('Asia/Jakarta');
        $affected_rows = Agendapimpinan::updateAll(['deleted' => 1, 'timestamp_agendapimpinan_lastupdate' => date('Y-m-d H:i:s')], 'id_agendapimpinan = "' . $id . '"');
        if ($affected_rows == 0) {
            Yii::$app->session->setFlash('warning', "Gagal. Mohon hubungi Admin.");
            return $this->redirect(['index']);
        } else {
            Yii::$app->session->setFlash('success', "Agenda berhasil dihapus. Terima kasih.");
            return $this->redirect(['index']);
        }
    }
    protected function findModel($id_agendapimpinan)
    {
        if (($model = Agendapimpinan::findOne(['id_agendapimpinan' => $id_agendapimpinan])) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
