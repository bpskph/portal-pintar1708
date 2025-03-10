<?php
namespace app\controllers;
use Yii;
use app\models\Pengguna;
use app\models\PenggunaSearch;
use app\models\Projectmember;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use Exception;

class PenggunaController extends BaseController
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
                            'actions' => ['error', 'create'],
                            'allow' => true,
                        ],
                        [
                            'actions' => ['index', 'approverevokelevel', 'update', 'delete', 'aktifkanlagi'],
                            'allow' => true,
                            'matchCallback' => function ($rule, $action) {
                                return !\Yii::$app->user->isGuest && (\Yii::$app->user->identity->level === 0);
                            },
                        ],
                        [
                            'actions' => ['view', 'ubahpassword'], // add all actions to take guest to login page
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
        if ($action->id === 'delete') {
            $this->enableCsrfValidation = false; // Disable CSRF validation for the action
        }
        return parent::beforeAction($action);
    }
    public function actionIndex()
    {
        $searchModel = new PenggunaSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }
    public function actionCreate()
    {
        /* inisiasi API */
        if (Yii::$app->user->isGuest)
            $this->layout = 'main-register';
        $json = '';
        $url_base = 'https://sso.bps.go.id/auth/';
        $url_token = $url_base . 'realms/pegawai-bps/protocol/openid-connect/token';
        $url_api = $url_base . 'admin/realms/pegawai-bps/users';
        $client_id      = Yii::$app->params['ssoSatkerId'];
        $client_secret  = Yii::$app->params['ssoSatkerKey'];
        $ch = curl_init($url_token);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded'));
        curl_setopt($ch, CURLOPT_POSTFIELDS, "grant_type=client_credentials");
        curl_setopt($ch, CURLOPT_USERPWD, $client_id . ":" . $client_secret);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        if (stristr("127.0.0.1", $_SERVER["SERVER_NAME"]) || stristr("localhost", $_SERVER["SERVER_NAME"]))
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $response_token = curl_exec($ch);
        if (curl_errno($ch)) {
            throw new Exception(curl_error($ch));
            // Yii::$app->session->setFlash('warning', "Terjadi error. Mohon pastikan koneksi VPN BPS tersambung.");
            // return $this->redirect(['pengguna/index']);
        }
        curl_close($ch);
        $json_token = json_decode($response_token, true);
        $access_token = $json_token['access_token'];
        /* inisiasi variabel di views/_form.php */
        $ada = '';
        $namasat = '';
        $key = '';
        $bengkulu = '';
        /* ngambil form nginput username/email */
        $modelusername = new \app\models\EmailForm();
        if ($modelusername->load(Yii::$app->request->post())) {
            /* pencarian data community menurut username/email */
            $query_search = '?email=' . $_POST['EmailForm']['email']; //'?username={username}' atau '?email={email pegawai}'
            $ch = curl_init($url_api . $query_search);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Authorization: Bearer ' . $access_token));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            if (stristr("127.0.0.1", $_SERVER["SERVER_NAME"]) || stristr("localhost", $_SERVER["SERVER_NAME"]))
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            $response = curl_exec($ch);
            if (curl_errno($ch)) {
                throw new Exception(curl_error($ch));
            }
            curl_close($ch);
            $json = json_decode($response, true); //masukkan ke data hasil pencarian json
            /* memfilter hasil pencarian biar dapat precise result */
            $array = array();
            // $count = count($json);
            $count = count((is_countable($json) ? $json : []));
            for ($i = 0; $i < $count; $i++) {
                $array[] = $json[$i]['attributes']['attribute-email'][0];
            }
            $key = array_search($_POST['EmailForm']['email'], $array); // $key = 2;         
            /* ngecek bahwa data pengguna tidak ditemukan Community */
            if (!isset($json[$key]['attributes']['attribute-nip'][0]))
                $ada = 'COMMUNITY';
            else {
                /* ngecek bahwa pengguna belum masuk ke sistem menurut NIP */
                $a = $json[$key]['attributes']['attribute-nip-lama'][0];
                $namasat = Pengguna::find()->where(['nip' => $a])->one();
                if (isset($namasat))
                    $ada = 'YA';
                else
                    $ada = 'TIDAK';
                /* ngecek bahwa pengguna ada di Satuan Kerja Terkait menurut Community */
                $lokasi = $json[$key]['attributes']['attribute-kabupaten'][0];
                if ($lokasi == Yii::$app->params['namaSatkerSSO'])
                    $bengkulu = 'YA';
                else
                    $bengkulu = 'TIDAK';
            }
        }
        $model = new Pengguna();
        if ($model->load(Yii::$app->request->post())) {
            if (Yii::$app->user->isGuest)
                $model->level = 2; //akses viewer utk pengguna yang daftar sendiri
            if (isset($_POST['Pengguna']['nama'])) {
                $model->nama = $_POST['Pengguna']['nama'];
            } else {
                // handle the case where 'nama' is not set in $_POST
            }
            // $model->satker = $_POST['Pengguna']['satker'];
            if (isset($_POST['Pengguna']['satker'])) {
                $model->satker = $_POST['Pengguna']['satker'];
            } else {
                // handle the case where 'satker' is not set in $_POST
            }
            if ($model->level == 1)  //jika admin memilih hak akses viewer utk pengguna yang didaftarkan
                $model->level = 2;
            else
                $model->level = 1;
            if ($model->save()) {
                Yii::$app->session->setFlash('success', "Data berhasil direkam. Pengguna dapat login. <br/> Terima kasih..");
                if (Yii::$app->user->isGuest)
                    return $this->redirect(['site/login']); //akses viewer
                return $this->redirect(['pengguna/index']);
            } else {
                // print_r($model->errors);
                //echo $model->errors;
                //     return $this->render('create', [
                //         'model' => $model,
                //     ]);
            }
        } else {
            return $this->render('create', [
                'model' => $model,
                'profil' => $json,
                'modelusername' => $modelusername,
                'ada' => $ada,
                'namasat' => $namasat,
                'key' => $key,
                'bengkulu' => $bengkulu
            ]);
        }
    }
    public function actionUpdate($id)
    {
        $modelusername = new \app\models\EmailForm();
        $json = '';
        $ada = 'TIDAK';
        $namasat = '';
        $key = '';
        $bengkulu = '';
        $model = $this->findModel($id);
        if ($this->request->isPost && $model->load($this->request->post())) {
            date_default_timezone_set('Asia/Jakarta');
            $model->tgl_update = date('Y-m-d H:i:s');
            if ($model->save()) {
                return $this->redirect(['view', 'username' => $model->username]);
            }
        }
        return $this->render('update', [
            'model' => $model,
            'profil' => $json,
            'modelusername' => $modelusername,
            'ada' => $ada,
            'namasat' => $namasat,
            'key' => $key,
            'bengkulu' => $bengkulu,
        ]);
    }
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        $anggotaaktif = Projectmember::find()
            ->select('*')
            ->where(['pegawai' => $id])
            ->andWhere(['NOT', ['member_status' => 0]])
            ->count();
        $anggotalist = Projectmember::find()
            ->joinWith('projecte', 'penggunae')
            ->select('*, panggilan_project')
            ->where(['pegawai' => $id])
            ->andWhere(['tahun' => date("Y")])
            ->andWhere(['NOT', ['member_status' => 0]])
            ->all();
        $panggilanProjects = '';
        $count = count($anggotalist); // get the count of items in the array
        foreach ($anggotalist as $key => $anggota) {
            if ($key == $count - 1 && $key != 0) { // if it's the last item and there are more than one items
                $panggilanProjects .= " dan "; // add "and" before the last item
            } elseif ($key != 0) {
                $panggilanProjects .= ", "; // add comma before all items except the first one
            }
            $panggilanProjects .= $anggota->projecte->panggilan_project; // append the project name to the string
        }
        if (!empty($panggilanProjects)) {
            Yii::$app->session->setFlash('warning', "Saudara <b>" . $model->nama . "</b> masih terdaftar di Tim <b>" . $panggilanProjects . "</b>.<br/> Pegawai yang masih terdaftar di satu atau lebih tim belum dapat dihapus dari sistem. Terima kasih.");
            return $this->redirect(['index']);
        }
        date_default_timezone_set('Asia/Jakarta');
        $affected_rows = Pengguna::updateAll(['level' => 2, 'tgl_update' => date('Y-m-d H:i:s')], 'username = "' . $id . '"');
        if ($affected_rows == 0) {
            Yii::$app->session->setFlash('warning', "Gagal. Mohon hubungi Admin.");
            return $this->redirect(['index']);
        } else {
            Yii::$app->session->setFlash('success', "Pengguna berhasil di-nonaktifkan. Terima kasih.");
            return $this->redirect(['index']);
        }
    }
    protected function findModel($id)
    {
        if (($model = Pengguna::findOne($id)) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('The requested page does not exist.');
    }
    public function actionView($username)
    {
        $model = $this->findModel($username);
        if ($model->username != Yii::$app->user->identity->username && Yii::$app->user->identity->level != 0) {
            Yii::$app->session->setFlash('warning', "Maaf, Anda tidak diperbolehkan melihat profil pengguna lain.");
            return $this->redirect(['site/index']);
        }
        if (Yii::$app->request->isAjax) {
            return $this->renderAjax('view', [
                'model' => $model,
            ]);
        } else {
            return $this->render('view', [
                'model' => $model,
            ]);
        }
    }
    public function actionUbahpassword($id)
    {
        $model = new \app\models\UbahPasswordForm();
        $pengguna = Pengguna::findOne($id);
        if ($id != Yii::$app->user->identity->username && Yii::$app->user->identity->level != 0) {
            Yii::$app->session->setFlash('warning', "Maaf, Anda tidak diperbolehkan mengubah password pengguna lain.");
            return $this->redirect(['site/index']);
        }
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $model->attributes = $_POST['UbahPasswordForm'];
            Yii::$app->db->createCommand()
                ->update('pengguna', ['password' => md5($_POST['UbahPasswordForm']['password_baru'])], 'username = "' . $id . '"')
                ->execute();
            Yii::$app->session->setFlash('success', "Password berhasil diubah. Terima kasih.");
            return $this->redirect([
                'view', 'username' => $id
            ]);
        }
        return $this->render('ubahpassword', [
            'model' => $model,
        ]);
    }
    public function actionApproverevokelevel($id)
    {        
        $model = Pengguna::findOne($id);
        date_default_timezone_set('Asia/Jakarta');
        if ($model->level == 1) {
            $affected_rows = Pengguna::updateAll(['level' => 0, 'tgl_update' => date('Y-m-d H:i:s')], ['username' => $id]);
        } else
            $affected_rows = Pengguna::updateAll(['level' => 1, 'tgl_update' => date('Y-m-d H:i:s')], ['username' => $id]);
        if ($affected_rows === 0) {
            Yii::$app->session->setFlash('warning', "Gagal. Mohon hubungi Admin.");
            return $this->redirect(['index']);
        } else {
            Yii::$app->session->setFlash('success', "Status Admin pengguna berhasil di-approve/revoke. Terima kasih.");
            return $this->redirect(['index']);
        }
    }
    public function actionAktifkanlagi($id)
    {
        $model = $this->findModel($id);
        date_default_timezone_set('Asia/Jakarta');
        $affected_rows = Pengguna::updateAll(['level' => 1, 'tgl_update' => date('Y-m-d H:i:s')], 'username = "' . $id . '"');
        if ($affected_rows == 0) {
            Yii::$app->session->setFlash('warning', "Gagal. Mohon hubungi Admin.");
            return $this->redirect('view', [
                'id' => $id,
                'model' => $model,
            ]);
        } else {
            Yii::$app->session->setFlash('success', "Pengguna berhasil diaktifkan kembali. Terima kasih.");
            return $this->redirect(['index']);
        }
    }
}
