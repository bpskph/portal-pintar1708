<?php
namespace app\controllers;
use app\models\Beritarilis;
use app\models\BeritarilisSearch;
use DateTime;
use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
/**
 * BeritarilisController implements the CRUD actions for Beritarilis model.
 */
class BeritarilisController extends BaseController
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
            ]
        );
    }
    public function actionIndex()
    {
        $searchModel = new BeritarilisSearch();
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
        $model = new Beritarilis();
        $searchModel = new BeritarilisSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);
        $dataProvider->query
            ->andWhere(['>=', 'waktumulai', date('Y-m-d H:i:s')])
            ->andWhere(['<=', 'waktumulai', date('Y-m-d H:i:s', strtotime('+2 weeks'))])
            ->andWhere(['progress' => '0']);
        $dataProvider->pagination = false;
        if ($this->request->isPost) {
            // die($_POST['Beritarilis']['pelaksana']);
            $model->load($this->request->post());
            $model->reporter = Yii::$app->user->identity->username;
            $narasumber = $model->narasumber;
            $cek = implode("@bps.go.id, ", $narasumber) . "@bps.go.id";
            $model->narasumber = $cek;
            if (str_contains($_POST['Beritarilis']['waktumulai'], 'WIB')) {
                /* WAKTU SELESAI */
                // Get the datetime string from the $_POST variable
                $datetimeStr = $_POST['Beritarilis']['waktumulai'];
                // Parse the datetime string and convert it to a DateTime object
                $datetime = DateTime::createFromFormat('Y-m-d H:i T', $datetimeStr);
                // Convert the DateTime object to a string in the desired format
                $formattedDatetime = $datetime->format('Y-m-d H:i:s');
                $model->waktumulai = $formattedDatetime;
            } else {
                $model->waktumulai = date("Y-m-d H:i:s", strtotime($_POST['Beritarilis']['waktumulai']));
            }
            if (str_contains($_POST['Beritarilis']['waktuselesai'], 'WIB')) {
                /* WAKTU SELESAI */
                // Get the datetime string from the $_POST variable
                $datetimeStr = $_POST['Beritarilis']['waktuselesai'];
                // Parse the datetime string and convert it to a DateTime object
                $datetime = DateTime::createFromFormat('Y-m-d H:i T', $datetimeStr);
                // Convert the DateTime object to a string in the desired format
                $formattedDatetime = $datetime->format('Y-m-d H:i:s');
                $model->waktuselesai = $formattedDatetime;
            } else {
                $model->waktuselesai = date("Y-m-d H:i:s", strtotime($_POST['Beritarilis']['waktuselesai']));
            }
            if ($model->validate()) {
                if ($_POST['Beritarilis']['pilihtempat'] == 1) {
                    if ($_POST['Beritarilis']['tempattext'] != "") {
                        $model->lokasi = $_POST['Beritarilis']['tempattext'];
                    } else {
                        Yii::$app->session->setFlash('warning', "Detail Tempat Luar Kantor harus terisi jika Anda memilih Lokasi Eksternal. Terima kasih.");
                        return $this->render('create', [
                            'model' => $model,
                            'searchModel' => $searchModel,
                            'dataProvider' => $dataProvider,
                        ]);
                    }
                } else {
                    if ($_POST['Beritarilis']['lokasi'] != "") {
                        $model->lokasi = $_POST['Beritarilis']['lokasi'];
                    } else {
                        Yii::$app->session->setFlash('warning', "Detail Lokasi Kantor harus terisi jika Anda memilih Lokasi Internal. Terima kasih.");
                        return $this->render('create', [
                            'model' => $model,
                            'searchModel' => $searchModel,
                            'dataProvider' => $dataProvider,
                        ]);
                    }
                }
                if ($model->save()) {
                    Yii::$app->session->setFlash('success', "Beritarilis berhasil ditambahkan. Terima kasih.");
                    return $this->redirect(['view', 'id' => $model->id_Beritarilis]);
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
        $searchModel = new BeritarilisSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);
        $dataProvider->query
            ->andWhere(['>=', 'waktumulai', date('Y-m-d H:i:s')])
            ->andWhere(['<=', 'waktumulai', date('Y-m-d H:i:s', strtotime('+2 weeks'))])
            ->andWhere(['progress' => '0']);
        $dataProvider->pagination = false;
        if ($this->request->isPost) {
            $model->load($this->request->post());
            $model->timestamp_lastupdate = date('Y-m-d H:i:s');
            $narasumber = $model->narasumber;
            $cek = implode("@bps.go.id, ", $narasumber) . "@bps.go.id";
            $model->narasumber = $cek;
            if (str_contains($_POST['Beritarilis']['waktumulai'], 'WIB')) {
                /* WAKTU SELESAI */
                // Get the datetime string from the $_POST variable
                $datetimeStr = $_POST['Beritarilis']['waktumulai'];
                // Parse the datetime string and convert it to a DateTime object
                $datetime = DateTime::createFromFormat('Y-m-d H:i T', $datetimeStr);
                // Convert the DateTime object to a string in the desired format
                $formattedDatetime = $datetime->format('Y-m-d H:i:s');
                $model->waktumulai = $formattedDatetime;
            } else {
                $model->waktumulai = date("Y-m-d H:i:s", strtotime($_POST['Beritarilis']['waktumulai']));
            }
            if (str_contains($_POST['Beritarilis']['waktuselesai'], 'WIB')) {
                /* WAKTU SELESAI */
                // Get the datetime string from the $_POST variable
                $datetimeStr = $_POST['Beritarilis']['waktuselesai'];
                // Parse the datetime string and convert it to a DateTime object
                $datetime = DateTime::createFromFormat('Y-m-d H:i T', $datetimeStr);
                // Convert the DateTime object to a string in the desired format
                $formattedDatetime = $datetime->format('Y-m-d H:i:s');
                $model->waktuselesai = $formattedDatetime;
            } else {
                $model->waktuselesai = date("Y-m-d H:i:s", strtotime($_POST['Beritarilis']['waktuselesai']));
            }
            if ($model->validate()) {
                if ($_POST['Beritarilis']['pilihtempat'] == 1) {
                    if ($_POST['Beritarilis']['tempattext'] != "") {
                        $model->lokasi = $_POST['Beritarilis']['tempattext'];
                    } else {
                        Yii::$app->session->setFlash('warning', "Detail Tempat Luar Kantor harus terisi jika Anda memilih Lokasi Eksternal. Terima kasih.");
                        return $this->render('update', [
                            'model' => $model,
                            'searchModel' => $searchModel,
                            'dataProvider' => $dataProvider,
                        ]);
                    }
                } else {
                    if ($_POST['Beritarilis']['lokasi'] != "") {
                        $model->lokasi = $_POST['Beritarilis']['lokasi'];
                    } else {
                        Yii::$app->session->setFlash('warning', "Detail Lokasi Kantor harus terisi jika Anda memilih Lokasi Internal. Terima kasih.");
                        return $this->render('update', [
                            'model' => $model,
                            'searchModel' => $searchModel,
                            'dataProvider' => $dataProvider,
                        ]);
                    }
                }
                $model->timestamp_lastupdate = date('Y-m-d H:i:s');
                if ($model->save()) {
                    Yii::$app->session->setFlash('success', "Berita Rilis berhasil dimutakhirkan. Terima kasih.");
                    return $this->redirect(['view', 'id' => $model->id_beritarilis]);
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
        $affected_rows = Beritarilis::updateAll(['deleted' => 1, 'timestamp_lastupdate' => date('Y-m-d H:i:s')], 'id_beritarilis = "' . $id . '"');
        if ($affected_rows == 0) {
            Yii::$app->session->setFlash('warning', "Gagal. Mohon hubungi Admin.");
            return $this->redirect(['index']);
        } else {
            Yii::$app->session->setFlash('success', "Berita rilis berhasil dihapus. Terima kasih.");
            return $this->redirect(['index']);
        }
    }
    protected function findModel($id_beritarilis)
    {
        if (($model = Beritarilis::findOne(['id_beritarilis' => $id_beritarilis])) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
