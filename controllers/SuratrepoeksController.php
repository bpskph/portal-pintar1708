<?php

namespace app\controllers;

use app\models\Agenda;
use app\models\Suratrepoeks;
use app\models\SuratrepoeksSearch;
use app\models\Suratsubkode;
use DateTime;
use Yii;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use Dompdf\DOMPDF; //untuk di local
//use Dompdf\Dompdf; //untuk di webapps
use Dompdf\Options;
use yii\helpers\Html;
use yii\web\UploadedFile;

class SuratrepoeksController extends BaseController
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
                                return !\Yii::$app->user->isGuest && (\Yii::$app->user->identity->level === 0);
                            },
                        ],
                        [
                            'actions' => [
                                'index',
                                'create',
                                'update',
                                'delete',
                                'getnomorsurat',
                                'cetaksurat',
                                'view',
                                'list',
                                'setujui',
                                'gettemplate',
                                'lihatscan',
                                'uploadscan',
                                'uploadword',
                                'komentar',
                                'gettemplatelampiran',
                                'cetaklampiran'
                            ], // add all actions to take guest to login page
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
        $this->enableCsrfValidation = false;
        return parent::beforeAction($action);
    }
    public function actionIndex($owner, $year)
    {
        $searchModel = new SuratrepoeksSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);
        if ($owner != '')
            $dataProvider->query->andWhere(['owner' => $owner]);
        if ($year == date("Y"))
            $dataProvider->query->andWhere(['YEAR(tanggal_suratrepoeks)' => date("Y")]);
        elseif ($year != '')
            $dataProvider->query->andWhere(['YEAR(tanggal_suratrepoeks)' => $year]);
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }
    public function actionList($agenda)
    {
        $searchModel = new SuratrepoeksSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);
        $dataProvider->query->where(['fk_agenda' => $agenda]);
        $dataagenda = Agenda::findOne(['id_agenda' => $agenda]);
        $waktutampil = LaporanController::findWaktutampil($agenda);
        if (Yii::$app->request->isAjax) {
            return $this->renderAjax('list', [
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider,
                'dataagenda' => $dataagenda,
                'waktutampil' => $waktutampil
            ]);
        } else {
            return $this->render('list', [
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider,
                'dataagenda' => $dataagenda,
                'waktutampil' => $waktutampil
            ]);
        }
    }
    public function actionCreate($id)
    {
        $model = new Suratrepoeks();
        // Get the current date and time
        $currentDate = new DateTime();
        // Subtract 2 days from the current date
        $threeDaysAgo = $currentDate->modify('-2 days');
        if (date("Y") == 2023) {
            $surats = Suratrepoeks::find()
                ->select('*')
                ->where(['owner' => Yii::$app->user->identity->username])
                ->andWhere(['deleted' => 0])
                ->andWhere(['approval' =>  1])
                ->andWhere([
                    'or',
                    ['>', 'id_suratrepoeks', 826],
                    ['<', 'id_suratrepoeks', 85],
                ])
                ->andWhere(
                    ['>', 'DATEDIFF(NOW(), DATE(timestamp_suratrepoeks_lastupdate))', 3], // diinput dalam span 3 hari
                )
                ->asArray()
                ->all();
        } else {
            $surats = Suratrepoeks::find()
                ->select('*')
                ->where(['owner' => Yii::$app->user->identity->username])
                ->andWhere(['deleted' => 0])
                ->andWhere(['approval' =>  1])
                ->andWhere([
                    'or',
                    ['>', 'id_suratrepoeks', 826],
                    ['<', 'id_suratrepoeks', 85],
                ])
                ->asArray()
                ->andWhere(
                    ['>', 'DATEDIFF(NOW(), DATE(timestamp_suratrepoeks_lastupdate))', 3], // diinput dalam span 3 hari
                )
                ->all();
        }
        // Loop through each $surats and check if the file exists
        $missingFiles = [];
        $missingNumbers = [];
        $missingTitles = [];
        foreach ($surats as $surat) {
            $filePath = Yii::getAlias('@webroot/surat/eksternal/pdf/' . $surat['id_suratrepoeks'] . '.pdf');
            if (!file_exists($filePath)) {
                // File does not exist, add the id_suratrepoeks to the missingFiles array
                $missingFiles[] = $surat['id_suratrepoeks'];
                $missingNumbers[] = $surat['nomor_suratrepoeks'];
                $missingTitles[] = $surat['perihal_suratrepoeks'];
            }
        }
        $cek = Suratrepoeks::find()->select('*')
            ->where(['owner' => Yii::$app->user->identity->username])
            ->andWhere(['deleted' => 0])
            ->andWhere(['approval' =>  0])
            ->andWhere(
                ['>', 'DATEDIFF(NOW(), DATE(timestamp_suratrepoeks_lastupdate))', 3], // diinput dalam span 3 hari
            )
            ->count();
        if ($cek > 0) {
            Yii::$app->session->setFlash('warning', "Maaf, sebelum " . $threeDaysAgo->format('d F Y') . ", Anda masih memiliki surat yang belum disetujui. Mohon untuk konfirmasi kepada Penyetuju Surat untuk menambahkan surat baru.
            <br/>Terima kasih.");
            return $this->redirect(['index', 'owner' => '', 'year' => '']);
        }
        // Print the list of id_suratrepoeks without corresponding files
        if (!empty($missingFiles)) {
            $teks = '<ol>';
            for ($i = 0; $i < count($missingFiles); $i++) {
                // $teks .= Html::a('<li><i class="fas fa-upload"></i>  ' . $missingNumbers[$i] . ' - ' . $missingTitles[$i] . '</li>', ['suratrepoeks/uploadscan/' . $missingFiles[$i]], []);
                $teks .= '<li>' . $missingNumbers[$i] . ' - ' . $missingTitles[$i] . Html::a(' <i class="fas fa-upload"></i> ', ['suratrepoeks/uploadscan/' . $missingFiles[$i]], []) . '</li>';
            }
            $teks .= '</ol>';
            Yii::$app->session->setFlash('warning', "Maaf. Mohon upload terlebih dahulu, scan surat-surat Anda sebelum " . $threeDaysAgo->format('d F Y') . " berikut:" . $teks);
            return $this->redirect(['index', 'owner' => '', 'year' => '']);
        }
        if ($id == 0) {
            // die ($id);
            $dataagenda = 'noagenda';
            $header = 'noagenda';
            $waktutampil = 'noagenda';
            if ($this->request->isPost) {
                $model->owner = Yii::$app->user->identity->username;
                $model->fk_agenda = NULL;
                if ($model->load($this->request->post()) && $model->save()) {
                    Yii::$app->session->setFlash('success', "Surat berhasil ditambahkan. Terima kasih.");
                    return $this->redirect(['view', 'id' => $model->id_suratrepoeks]);
                }
            } else {
                $model->loadDefaultValues();
            }
        } else {
            $dataagenda = Agenda::findOne(['id_agenda' => $id]);
            $header = LaporanController::findHeader($id);
            $waktutampil = LaporanController::findWaktutampil($id);
            if ($dataagenda->repoeksrter != Yii::$app->user->identity->username) {
                Yii::$app->session->setFlash('warning', "Surat hanya dapat dibuat oleh pengusul agenda. Terima kasih.");
                return $this->redirect(['index', 'owner' => '', 'year' => '']);
            }
            if ($dataagenda->progress == 3) {
                Yii::$app->session->setFlash('warning', "Agenda ini sudah dibatalkan. Terima kasih.");
                return $this->redirect(['index', 'owner' => '', 'year' => '']);
            }
            if ($this->request->isPost) {
                $model->owner = Yii::$app->user->identity->username;
                $model->fk_agenda = $id;
                if ($model->load($this->request->post()) && $model->save()) {
                    Yii::$app->session->setFlash('success', "Surat berhasil ditambahkan. Terima kasih.");
                    return $this->redirect(['view', 'id' => $model->id_suratrepoeks]);
                }
            } else {
                $model->loadDefaultValues();
            }
        }
        return $this->render('create', [
            'model' => $model,
            'dataagenda' => $dataagenda,
            'header' => $header,
            'waktutampil' => $waktutampil,
        ]);
    }
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        if ($model->approval == 1) {
            Yii::$app->session->setFlash('warning', "Surat sudah disetujui dan tidak dapat diubah kembali. Terima kasih.");
            return $this->redirect(['index', 'owner' => '', 'year' => '']);
        }
        if (
            Yii::$app->user->identity->username !== $model->owner //datanya sendiri   
            && !Yii::$app->user->identity->issekretaris
        ) {
            Yii::$app->session->setFlash('warning', "Surat hanya dapat diubah oleh pengusul surat atau Sekretaris. Terima kasih.");
            return $this->redirect(['index', 'owner' => '', 'year' => '']);
        }
        if ($model->fk_agenda == null) {
            $dataagenda = 'noagenda';
            $header = 'noagenda';
            $waktutampil = 'noagenda';
        } else {
            $header = LaporanController::findHeader($id);
            $waktutampil = LaporanController::findWaktutampil($id);
            $dataagenda = Agenda::findOne(['id_agenda' => $model->fk_agenda]);
            if ($dataagenda->progress == 3) {
                Yii::$app->session->setFlash('warning', "Agenda ini sudah dibatalkan. Terima kasih.");
                return $this->redirect(['index', 'owner' => '', 'year' => '']);
            }
        }
        if ($this->request->isPost && $model->load($this->request->post())) {
            if (($model->lampiran == '') || ($model->lampiran == '-') || ($model->lampiran == null)) {
                $model->isi_lampiran = null;
                $model->isi_lampiran_orientation = 0;
            }
            date_default_timezone_set('Asia/Jakarta');
            $model->timestamp_suratrepoeks_lastupdate = date('Y-m-d H:i:s');
            $model->approval = 0;
            if ($model->save()) {
                Yii::$app->session->setFlash('success', "Surat berhasil dimutakhirkan. Terima kasih.");
                return $this->redirect(['view', 'id' => $model->id_suratrepoeks]);
            }
        }
        // if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
        //     // die($_POST['Suratrepoeks']['isi_suratrepoeks']);
        //     Yii::$app->session->setFlash('success', "Surat berhasil dimutakhirkan. Terima kasih.");
        //     return $this->redirect(['view', 'id' => $model->id_suratrepoeks]);
        // }
        return $this->render('update', [
            'model' => $model,
            'dataagenda' => $dataagenda,
            'header' => $header,
            'waktutampil' => $waktutampil
        ]);
    }
    public function actionDelete($id)
    {
        date_default_timezone_set('Asia/Jakarta');
        $affected_rows = Suratrepoeks::updateAll(['deleted' => 1, 'timestamp_suratrepoeks_lastupdate' => date('Y-m-d H:i:s')], 'id_suratrepoeks = "' . $id . '"');
        if ($affected_rows == 0) {
            Yii::$app->session->setFlash('warning', "Gagal. Mohon hubungi Admin.");
            return $this->redirect(['index', 'owner' => '', 'year' => '']);
        } else {
            Yii::$app->session->setFlash('success', "Surat berhasil dihapus. Terima kasih.");
            return $this->redirect(['index', 'owner' => '', 'year' => '']);
        }
    }
    public function actionSetujui($id)
    {
        $model = $this->findModel($id);

        $filePath = Yii::getAlias('@webroot/surat/eksternal/word/' . $model->id_suratrepoeks . '.docx');
        $filePath2 = Yii::getAlias('@webroot/surat/eksternal/word/' . $model->id_suratrepoeks . '.doc');
        if (!file_exists($filePath) && !file_exists($filePath2)) {
            Yii::$app->session->setFlash('warning', "Maaf. Untuk ketertiban administrasi, draft surat perlu diupload agar dapat disetujui.");
            return $this->redirect(['index', 'owner' => '', 'year' => '']);
        }

        $approver = \app\models\Pengguna::findOne($model->approver);
        date_default_timezone_set('Asia/Jakarta');
        $affected_rows = Suratrepoeks::updateAll(['approval' => 1, 'timestamp_suratrepoeks_lastupdate' => date('Y-m-d H:i:s')], 'id_suratrepoeks = "' . $id . '"');
        if ($affected_rows == 0) {
            Yii::$app->session->setFlash('warning', "Gagal. Mohon hubungi Admin.");
            return $this->redirect(['index', 'owner' => '', 'year' => '']);
        } else {
            /* NOTIFIKASI UNTUK PEMBUAT SURAT */
            $pengguna = \app\models\Pengguna::findOne($model->owner);

            $isi_notif_wa = '*Portal Pintar 2.0 - WhatsApp Notification Blast*

Bapak/Ibu ' . $pengguna->nama . ', Surat Anda Nomor *' . $model->nomor_suratrepoeks  . '* sudah disetujui oleh *' . $approver->nama . '*, berkas PDF surat yang telah ditandatangani akan diupload oleh Sekretaris ke Sistem Portal Pintar 2.0 di https://webapps.bps.go.id/bengkulu/portalpintar/. Terima kasih.

_#pesan ini dikirim oleh Portal Pintar dan tidak perlu dibalas_';

            $response = AgendaController::wa_engine($pengguna->nomor_hp, $isi_notif_wa);
            \app\models\Notification::createNotification($model->owner, 'Surat Anda Nomor <strong>' . $model->nomor_suratrepoeks . '</strong> sudah disetujui oleh <strong>' . $approver->nama . '</strong>, berkas PDF surat yang telah ditandatangani akan diupload oleh Sekretaris.', Yii::$app->controller->id, $model->id_suratrepoeks);

            /* NOTIFIKASI UNTUK SEKRETARIS */
            $sekretaris = \app\models\Pengguna::findOne('sekbps17');
            $isi_notif_wa_sek = '*Portal Pintar 2.0 - WhatsApp Notification Blast*

            Ykh. Sekretaris BPS Provinsi Bengkulu, Surat dari ' . $pengguna->nama . ' dengan  Nomor *' . $model->nomor_suratrepoeks  . '* sudah disetujui oleh *' . $approver->nama . '*, mohon meng-upload PDF surat yang telah ditandatangani ke Sistem Portal Pintar 2.0 di https://webapps.bps.go.id/bengkulu/portalpintar/. Terima kasih.
            
            _#pesan ini dikirim oleh Portal Pintar dan tidak perlu dibalas_';
            $response2 = AgendaController::wa_engine($sekretaris->nomor_hp, $isi_notif_wa_sek);
            Yii::$app->session->setFlash('success', "Surat berhasil disetujui. Terima kasih.");
            return $this->redirect(['index', 'owner' => '', 'year' => '']);
        }
    }
    protected function findModel($id_suratrepoeks)
    {
        if (($model = Suratrepoeks::findOne(['id_suratrepoeks' => $id_suratrepoeks])) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('The requested page does not exist.');
    }
    public function actionGetnomorsurat($id, $tanggal, $sifat, $action)
    {
        $bulan = date("m", strtotime($tanggal));
        $tahun = date("Y", strtotime($tanggal));
        $jadwal = Suratrepoeks::find()
            ->where(['YEAR(tanggal_suratrepoeks)' => $tahun])
            ->andWhere(['deleted' => 0])
            ->all();
        $nosurats = [];
        foreach ($jadwal as $value) {
            if (preg_match('/-(\d+)\//', $value->nomor_suratrepoeks, $matches)) {
                $nosurat = $matches[1];
            } else {
                // Handle cases where the pattern does not match (optional)
                $nosurat = null; // or any default value
            }
            array_push($nosurats, $nosurat);
        }
        sort($nosurats);
        $idterakhir = end($nosurats);
        // die (var_dump($idterakhir));
        $sortedJadwal = Suratrepoeks::find() //cek kalau ada duplikat nomor
            ->where(['like', 'nomor_suratrepoeks', '-' . $idterakhir . '/'])
            ->andWhere(['YEAR(tanggal_suratrepoeks)' => $tahun])
            ->andWhere(['deleted' => 0])
            ->one();
        switch ($sifat) {
            case 0:
                $kode = 'B';
                break;
            case 1:
                $kode = 'P';
                break;
            case 2:
                $kode = 'R';
                break;
            default:
                $kode = 'B';
        }
        $suratsubkode = Suratsubkode::findOne(['id_suratsubkode' => $id]);
        if (count($jadwal) < 1) {
            return $kode . '-' . '001' . '/17000/' . $suratsubkode->fk_suratkode . '.' . $suratsubkode->kode_suratsubkode . '/' . (($tahun == 2023) ? ($bulan . '/' . $tahun) : $tahun);
        } else {
            $suratajuan = strtotime($tanggal); //tanggal pada form
            $suratterakhir = strtotime($sortedJadwal->tanggal_suratrepoeks); //tanggal surat dengan ID terakhir
            if ($suratajuan >= $suratterakhir) { // tanggal yang diajukan setelah tanggal dengan ID terakhir
                // $nosurat = substr($jadwal->nomor_suratrepoeks, 2, 3);
                $str = $sortedJadwal->nomor_suratrepoeks;
                if (preg_match('/-(\d+)\//', $str, $matches)) {
                    $nosurat = ($matches[1]);
                }
                // die($nosurat);
                $nosurat += 1;
                if (strlen($nosurat) == 2)
                    $nosurat = '0' . $nosurat;
                elseif (strlen($nosurat) == 1)
                    $nosurat = '00' . $nosurat;
                return $kode . '-' . $nosurat . '/17000/' . $suratsubkode->fk_suratkode . '.' . $suratsubkode->kode_suratsubkode . '/' . (($tahun == 2023) ? ($bulan . '/' . $tahun) : $tahun);
            } else { //tanggal yang diajukan sebelum tanggal dengan ID terakhir
                $jadwalsisip = Suratrepoeks::find()->where(['<=', 'tanggal_suratrepoeks', $tanggal])->andWhere(['deleted' => 0])->andWhere(['YEAR(tanggal_suratrepoeks)' => $tahun])->all();
                if (count($jadwalsisip) < 1)
                    $jadwalsisip = Suratrepoeks::find()->where(['>=', 'tanggal_suratrepoeks', $tanggal])->andWhere(['deleted' => 0])->andWhere(['YEAR(tanggal_suratrepoeks)' => $tahun])->orderBy(['tanggal_suratrepoeks' => SORT_DESC])->all();
                $nosuratsisips = [];
                // die(var_dump($jadwalsisip));
                foreach ($jadwalsisip as $value) {
                    if (preg_match('/-(\d+)\//', $value->nomor_suratrepoeks, $matches)) {
                        $nosuratsisip = $matches[1];
                    }
                    array_push($nosuratsisips, $nosuratsisip);
                }
                sort($nosuratsisips);
                $idterakhirsisip = end($nosuratsisips);
                if (!empty($jadwalsisip)) {
                    $jadwalsisipsorted = Suratrepoeks::find() //cek kalau ada duplikat nomor
                        ->where(['like', 'nomor_suratrepoeks', '-' . $idterakhirsisip . '/'])
                        ->andWhere(['YEAR(tanggal_suratrepoeks)' => $tahun])
                        ->andWhere(['deleted' => 0])
                        ->one();
                    $str = $jadwalsisipsorted->nomor_suratrepoeks;
                } else
                    return 'Portal Pintar hanya menerima data sejak 24 Mei 2023.';
                // return $str;
                if (preg_match('/-(\d+)\//', $str, $matches)) {
                    $nosurat = $matches[1];
                }
                $checksuratsisip = strtok($jadwalsisipsorted->nomor_suratrepoeks, '/'); //ambil nomor tanpa karakter setelah garis miring
                $checksuratsisip = substr($checksuratsisip, 2); ///ambil nomor tanpa B
                $tes = preg_replace('/[^A-Z]/', '', $checksuratsisip);
                // return $checksuratsisip;
                $duplikat = Suratrepoeks::find() //cek kalau ada duplikat nomor
                    ->where(['like', 'nomor_suratrepoeks', $checksuratsisip])
                    ->andWhere(['YEAR(tanggal_suratrepoeks)' => $tahun])
                    ->andWhere(['deleted' => 0])
                    ->count();
                $listduplikat = Suratrepoeks::find()
                    ->where(['like', 'nomor_suratrepoeks', $checksuratsisip])
                    ->andWhere(['YEAR(tanggal_suratrepoeks)' => $tahun])
                    ->andWhere(['deleted' => 0])
                    ->orderBy(['nomor_suratrepoeks' => SORT_DESC])->one(); //ambil duplikat dengan nomor terakhir
                if ($duplikat > 0) {
                    // return $listduplikat->nomor_suratrepoeks; //untuk menghindari duplikat
                    $checksuratsisip = strtok($listduplikat->nomor_suratrepoeks, '/'); //ambil nomor tanpa karakter setelah garis miring
                    $checksuratsisip = substr($checksuratsisip, 2); ///ambil nomor tanpa B
                    $tes = preg_replace('/[^A-Z]/', '', $checksuratsisip);
                }
                // die(var_dump($tes));
                if ($tes != "") { // Check if there are letters
                    // Get the letter part
                    $letterPart = preg_replace('/[^A-Z]/', '', $checksuratsisip);
                    // Get the number part
                    $numberPart = preg_replace('/[^0-9]/', '', $checksuratsisip);
                    // Increment the letter part
                    $newLetterPart = SuratrepoeksController::incrementLetterPart($letterPart);
                    // Combine the number and new letter parts
                    $newChecksuratsisip = $numberPart . $newLetterPart;
                    $cekduplikatsisip = Suratrepoeks::find() //cek kalau ada duplikat nomor
                        ->where(['like', 'nomor_suratrepoeks', '-' . $newChecksuratsisip . '/'])
                        ->andWhere(['YEAR(tanggal_suratrepoeks)' => $tahun])
                        ->andWhere(['deleted' => 0])
                        ->one();
                    // die(var_dump($cekduplikatsisip));
                    while (!empty($cekduplikatsisip)) {
                        $newLetterPart = SuratrepoeksController::incrementLetterPart($newLetterPart);
                        $newChecksuratsisip = $numberPart . $newLetterPart;
                        $cekduplikatsisip = Suratrepoeks::find()
                            ->where(['like', 'nomor_suratrepoeks', '-' . $newChecksuratsisip . '/'])
                            ->andWhere(['YEAR(tanggal_suratrepoeks)' => $tahun])
                            ->andWhere(['deleted' => 0])
                            ->one();
                    }
                    // Code execution continues after the loop
                    return $kode . '-' . $newChecksuratsisip . '/17000/' . $suratsubkode->fk_suratkode . '.' . $suratsubkode->kode_suratsubkode . '/' . (($tahun == 2023) ? ($bulan . '/' . $tahun) : $tahun);
                } else {
                    return $kode . '-' . $nosurat . 'A' . '/17000/' . $suratsubkode->fk_suratkode . '.' . $suratsubkode->kode_suratsubkode . '/' . (($tahun == 2023) ? ($bulan . '/' . $tahun) : $tahun);
                }
            }
        }
    }
    private function incrementLetterPart($letterPart)
    {
        $length = strlen($letterPart);
        // Check if the letter part is empty
        if ($length === 0) {
            return 'A';
        }
        // $alphabet = range('A', 'Z');
        // $letterNumber = array_search($letterPart, $alphabet); // returns number
        $letterNumber = SuratrepoeksController::alphabetToNumber($letterPart);
        $letterNumber += 1;
        // $newLetterPart = $alphabet[$letterNumber]; // returns alphabet
        $newLetterPart = SuratrepoeksController::numberToAlphabet($letterNumber);
        return $newLetterPart;
    }
    private function numberToAlphabet($number)
    {
        $alphabet = range('A', 'Z');
        $result = '';
        $base = count($alphabet);
        while ($number > 0) {
            $remainder = ($number - 1) % $base;
            $result = $alphabet[$remainder] . $result;
            $number = intval(($number - 1) / $base);
        }
        return $result;
    }
    private function alphabetToNumber($alphabet)
    {
        $alphabet = strtoupper($alphabet); // Convert to uppercase for consistency
        $result = 0;
        $base = 26;
        $length = strlen($alphabet);
        for ($i = 0; $i < $length; $i++) {
            $charValue = ord($alphabet[$i]) - ord('A') + 1;
            $result = $result * $base + $charValue;
        }
        return $result;
    }
    public function actionCetaksurat($id)
    {
        $model = $this->findModel($id);
        if (Yii::$app->user->identity->username != $model['owner'] && Yii::$app->user->identity->username != $model['approver'] && !Yii::$app->user->identity->issekretaris) {
            Yii::$app->session->setFlash('warning', "Surat eksternal hanya dapat dilihat oleh Sekretaris dan Pengguna yang menginput atau menyetujui. Terima kasih.");
            return $this->redirect(['index', 'owner' => '', 'year' => '']);
        }
        // die($model);
        include_once('_librarycetaksurat.php');
        $fileName = Yii::$app->request->hostInfo . Yii::$app->request->baseUrl . Yii::getAlias("@images/bps.png");
        $data = LaporanController::curl_get_file_contents($fileName);
        $base64 = 'data:image/png;base64,' . base64_encode($data);
        $waktutampil = '';
        $formatter = Yii::$app->formatter;
        $formatter->locale = 'id-ID'; // set the locale to Indonesian
        $timezone = new \DateTimeZone('Asia/Jakarta'); // create a timezone object for WIB
        $waktutampil = new \DateTime($model->tanggal_suratrepoeks, new \DateTimeZone('UTC')); // create a datetime object for waktumulai with UTC timezone
        $waktutampil->setTimeZone($timezone); // set the timezone to WIB
        $waktutampil = $formatter->asDatetime($waktutampil, 'd MMMM Y'); // format the waktumulai datetime value
        // Ambil daftar KEPADA
        $names = explode(', ', $model->penerima_suratrepoeks);
        $listItems = '';
        foreach ($names as $key => $name) {
            $listItems .= '<li>' .  ' ' . $name . '</li>';
        }
        if (count($names) <= 1)
            $autofillString = $names[0] . '<br/>';
        else
            $autofillString = '<ol style="margin-top: 0px">' . $listItems . '</ol>';
        // Ambil daftar TEMBUSAN
        if ($model->tembusan != null) {
            $names = explode(', ', $model->tembusan);
            $listItems = '';
            foreach ($names as $key => $name) {
                $listItems .= '<li>' .  ' ' . $name . '</li>';
            }
            $autofillString2 = '<ol>' . $listItems . '</ol>';
        } else {
            $autofillString2 = '';
        }
        $kop = '';
        $jenis = $model->jenis;
        switch ($jenis) {
            case 0: // biasa
                $kop = '
                    <table width="500" border="0" bordercolor="33FFFF" align="center" cellpadding="3" cellspacing="00" style="margin-top: -50px;">
                        <tr>
                            <td height="40" colspan="0" width="10" align="left"><img src="data:image/png;base64,' . Yii::$app->params['imagebase64'] . '" height="60" width="82" /> 
                            </td>
                            <td height="40" vertical-align="middle"><h4 style="color: #007bff; margin-left: 6px;" class="tulisanbps"><i>BADAN PUSAT STATISTIK<br/>PROVINSI BENGKULU</h4></i></td>
                            <td height="40" colspan="0" align="right"><img src="data:image/png;base64,' . Yii::$app->params['imagebase64_st2023'] . '" height="70" width="170" />
                            </td>
                        </tr>
                    </table>
                    <table width="500" border="0" bordercolor="33FFFF" align="center" cellpadding="3" cellspacing="00" >
                        <p style="text-align: right; margin-top: -10px; margin-right: 2px">Bengkulu, ' . $waktutampil . '</p>
                        <div class="row">
                            <div class="col-sm-12 d-flex">
                                <div class="table-responsive">
                                    <table class="table table-sm align-self-end">
                                        <tbody valign="top">
                                            <tr>
                                                <td width="75" style="padding: 0px;">Nomor </td>
                                                <td width="8" style="padding: 0px;">: </td>
                                                <td style="padding: 0px;">' . $model->nomor_suratrepoeks . '</td>
                                            </tr>
                                            <tr>
                                                <td style="padding: 0px;">Sifat </td>
                                                <td style="padding: 0px;">: </td>
                                                <td style="padding: 0px;">Biasa</td>
                                            </tr>                            
                                            <tr>
                                                <td style="padding: 0px;">Lampiran </td>
                                                <td style="padding: 0px;">: </td>
                                                <td style="padding: 0px;">' . $model->lampiran . '</td>
                                            </tr>
                                            <tr>
                                                <td style="padding: 0px;">Hal </td>
                                                <td style="padding: 0px;">: </td>
                                                <td style="padding: 0px;">' . $model->perihal_suratrepoeks . '</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        </tr>
                    </table>
                    <table width="500" border="0" bordercolor="33FFFF" align="center" cellpadding="0" cellspacing="0">
                        <tr>
                            <span style="font-size: 15px">
                            <br/>
                            Yang Terhormat : <br/>
                            ' . $autofillString . '                        
                            <span style="margin-top: -10px">di-</span>                       
                            <p style="text-indent:.5in; margin-top: -10px">Tempat</p>
                            <br/>
                            </span>
                        </tr>
                    </table>
                    ';
                $kop2 =
                    '
                    <table width="500" border="0" bordercolor="33FFFF" align="center" cellpadding="0" cellspacing="0">
                        <tr>
                            <td width="300"></td>
                            <td></td>
                            <td></td>
                            <td>
                                <center>                                  
                                    ' . (empty($model->ttd_by) ? '-' : $model->ttdbye->jabatan) . '
                                    <br />
                                    <br />
                                    <br />
                                    <br />
                                    <b>' . (empty($model->ttd_by) ? '-' : $model->ttdbye->nama) . '</b>
                                <center>
                            </td>
                        </tr>
                    </table>                    
                    ';
                break;
            case 1: // surat perintah lembur
                $kop = '
                    <table width="500" border="0" bordercolor="33FFFF" align="center" cellpadding="3" cellspacing="00" style="margin-top: -50px;">
                        <tr>
                            <td height="40" colspan="0" width="10" align="left"><img src="data:image/png;base64,' . Yii::$app->params['imagebase64'] . '" height="60" width="82" /> 
                            </td>
                            <td height="40" vertical-align="middle"><h4 style="margin-left: 6px" class="tulisanbps"><i>BADAN PUSAT STATISTIK<br/>PROVINSI BENGKULU</h4></i></td>
                            <td height="40" colspan="0" align="right"><br>
                            </td>
                        </tr>
                    </table>
                    <table width="500" border="0" bordercolor="33FFFF" align="center" cellpadding="3" cellspacing="00">                    
                        <tr style="">
                            <h4 style="text-align: center; text-decoration: underline">SURAT PERINTAH LEMBUR</h4> 
                        </tr>
                        <tr style="">
                            <p style="text-align: center; margin-top:-10px">Nomor : ' . $model->nomor_suratrepoeks . '</p> 
                        </tr>
                    </table>
                    <br/>
                ';
                $kop2 =
                    '
                    <table width="500" border="0" bordercolor="33FFFF" align="center" cellpadding="0" cellspacing="0">
                        <tr>
                            <td width="300"></td>
                            <td></td>
                            <td></td>
                            <td>
                                <center>
                                    Bengkulu, ' . $waktutampil . '<br/>                                    
                                    ' . (empty($model->ttd_by) ? '-' : $model->ttdbye->jabatan) . '
                                    <br />
                                    <br />
                                    <br />
                                    <br />
                                    <b>' . (empty($model->ttd_by) ? '-' : $model->ttdbye->nama) . '</b>
                                <center>
                            </td>
                        </tr>
                    </table>
                    ';
                break;
            case 2: //keterangan
                $kop = '
                    <table width="500" border="0" bordercolor="33FFFF" align="center" cellpadding="3" cellspacing="00" style="margin-top: -50px;">
                        <tr>
                            <td height="40" colspan="0" width="10" align="left"><img src="data:image/png;base64,' . Yii::$app->params['imagebase64'] . '" height="60" width="82" /> 
                            </td>
                            <td height="40" vertical-align="middle"><h4 style="margin-left: 6px" class="tulisanbps"><i>BADAN PUSAT STATISTIK<br/>PROVINSI BENGKULU</h4></i></td>
                            <td height="40" colspan="0" align="right"><br>
                            </td>
                        </tr>
                    </table>
                    <table width="500" border="0" bordercolor="33FFFF" align="center" cellpadding="3" cellspacing="00">                    
                        <tr style="">
                            <h4 style="text-align: center; text-decoration: underline">SURAT KETERANGAN</h4> 
                        </tr>
                        <tr style="">
                            <p style="text-align: center; margin-top:-10px">Nomor : ' . $model->nomor_suratrepoeks . '</p> 
                        </tr>
                    </table>
                    <br/>
                ';
                $kop2 =
                    '
                    <table width="500" border="0" bordercolor="33FFFF" align="center" cellpadding="0" cellspacing="0">
                        <tr>
                            <td width="300"></td>
                            <td></td>
                            <td></td>
                            <td>
                                <center>
                                    Bengkulu, ' . $waktutampil . '<br/>                                    
                                    ' . (empty($model->ttd_by) ? '-' : $model->ttdbye->jabatan) . '
                                    <br />
                                    <br />
                                    <br />
                                    <br />
                                    <b>' . (empty($model->ttd_by) ? '-' : $model->ttdbye->nama) . '</b>
                                <center>
                            </td>
                        </tr>
                    </table>
                    ';
                break;
            default:
                $kop = '';
        }
        $html =
            '<!DOCTYPE html>
                <html>
                <head>
                    ' . $style . $kop . '
                </head>
                <body>                   
                    <table width="500" border="0" bordercolor="33FFFF" align="center" cellpadding="0" cellspacing="0">
                        <tr>
                            <span  style="text-align: justify">' . $model->isi_suratrepoeks . '</span>
                        </tr>
                    </table>
                    <br/>
                    <br/>
                    ' . $kop2 . '
                    <br/>
                    ' . ($model->tembusan != null ? '<p style="margin-bottom: 0px">Tembusan: </p>' . $autofillString2 : '') . '
                </body>
                <foot style="font-size:10px">
                    <div class="footer">
                        <center>Jalan Adam Malik Km.8 Bengkulu, 38225, Telepon (0736) 349117-118, Kepala (0736) 349116
                            <br>Fax. (0736) 349115, E-mail: bps1700@bps.go.id
                        </center>
                        <i style="font-size: 8px;">
                            Generated in Portal Pintar
                        </i>
                    </div>
                </foot>
                </html>';
        $options = new Options();
        $options->set('defaultFont', 'Courier');
        $options->set('isRemoteEnabled', TRUE);
        $options->set('debugKeepTemp', TRUE);
        $options->set('isHtml5ParserEnabled', TRUE);
        $dompdf = new DOMPDF($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        //$dompdf->stream('laporan_'.$nama.'.pdf');
        // $canvas = $dompdf->getCanvas();
        //require_once("dompdf/include/font_metrics.cls.php");
        // $font = Font_Metrics::get_font("Times new roman", "");
        // $canvas->page_text(16, 800, "Page: {PAGE_NUM} of {PAGE_COUNT}", $font, 8, array(0, 0, 0));
        ob_end_clean();
        $dompdf->stream($model->perihal_suratrepoeks . ".pdf", array("Attachment" => 0));
        // $output = $dompdf->output();
        // file_put_contents("report.pdf", $output);
    }
    public function actionGettemplate($id, $action, $surat)
    {
        if ($action == 'create') {
            $template = '';
            switch ($id) {
                case 0: // surat biasa
                    $template = '
                    <p style="text-align: justify; text-indent: 0.5in">
                        Berdasarkan Peraturan Menteri Keuangan nomor 246/PMK.06/2014 tentang Tata Cara Pelaksanaan Pengunaan Barang Milik Negara (BMN) sebagaimana telah diubah dengan Peraturan Menteri Keuangan nomor 76/PMK.06/2019 bahwa Usulan Penetapan Status Penggunaan (PSP) &nbsp;BMN paling lama 6 (enam) bulan setelah asset diterima/diperoleh.
                    </p>
                    <p style="text-align: justify; text-indent: 0.5in">
                    Selanjutnya untuk mengimplementasikan peraturan tersebut, saudara diminta untuk melengkapi usulan PSP sesuai dengan petunjuk pada link: <a href="http://s.bps.go.id/SimpelPSP">http://s.bps.go.id/SimpelPSP</a>. Usulan PSP Semester I Tahun 2023 Ke BPS Pusat akan dilakukan secara kolektif pada minggu I Bulan Juni 2023. Oleh karena itu, berkas Usulan PSP sudah dapat dikirimkan ke BPS Provinsi selambat-lambatnya 26 Mei 2022 ke email &nbsp;<a href="mailto:tu1700@bps.go.id">tu1700@bps.go.id</a>&nbsp; dan ditembuskan (cc) <a href="mailto:reyronald@bps.go.id">reyronald@bps.go.id</a> untuk dikompilasi dan dikirim secara kolektif. Dokumen asli dan kebenaran isi dokumen menjadi tanggungjawab satker.
                    </p>
                    ';
                    break;
                case 1: //spk lembur
                    $template = '
                    <p>Yang bertandatangan di bawah ini :</p>
                    <p>Nama&nbsp; &nbsp; &nbsp; &nbsp;&nbsp;: Ir. Win Rizal, ME</p>
                    <p>Jabatan &nbsp; &nbsp; : Kepala BPS Provinsi Bengkulu</p>
                    <p style="text-align:center;"><strong>MEMERINTAHKAN :</strong></p>
                    <br/>
                    <table style="border-collapse:collapse;border: none;" width="100%">
                        <tbody valign="top">
                            <tr>
                                <td width="5%" style="border:solid windowtext 1.0pt; text-align: center">No </td>
                                <td width="25%" style="border:solid windowtext 1.0pt;">Nama Pegawai yang Ditugaskan </td>
                                <td width="20%" style="border:solid windowtext 1.0pt;">Status</td>
                                <td width="25%" style="border:solid windowtext 1.0pt;">Tugas</td>
                                <td width="25%" style="border:solid windowtext 1.0pt;">Volume</td>
                            </tr>
                            <tr>
                                <td width="5%" style="border:solid windowtext 1.0pt; text-align: center">1. </td>
                                <td width="25%" style="border:solid windowtext 1.0pt;">Budi Ansori</td>
                                <td width="20%" style="border:solid windowtext 1.0pt;">PNS</td>
                                <td width="25%" style="border:solid windowtext 1.0pt;">
                                    <ol>
                                        <li>Mengawasi pembersihan dan pengecetan pondasi tiang bendera</li>
                                        <li>Mengawasi pembersihan tumpukan barang tidak berguna di gudang</li>
                                        <li>Mengawasi pemindahan barang BMN rusak ke gudang museum</li>
                                        <li>Mengawasi dan membersihkan dinding koridor lantai 1</li>
                                        <li>Mengawasi dan membersihkan toilet kantor (L/P) lantai 1</li>
                                    </ol>
                                </td>
                                <td width="25%" style="border:solid windowtext 1.0pt;">
                                <ol>
                                    <li>1 unit tiang bendera</li>
                                    <li>1 ruangan BMN</li>
                                    <li>2 kali angkutan barang BMN</li>
                                    <li>2 unit toilet di lantai 1 (toilet laki-laki dan perempuan)</li>
                                </ol>
                                </td>
                            </tr>
                            <tr>
                                <td width="5%" style="border:solid windowtext 1.0pt; text-align: center">2. </td>
                                <td width="25%" style="border:solid windowtext 1.0pt;">Eka Putrawansyah </td>
                                <td width="20%" style="border:solid windowtext 1.0pt;">PNS</td>
                                <td width="25%" style="border:solid windowtext 1.0pt;">
                                    <ol>
                                        <li>Mengawasi pembersihan pondasi tiang bendera</li>
                                        <li>Mengecet pondasi tiang bendera</li>
                                        <li>Membersihkan tumpukan barang tidak berguna di gudang</li>
                                        <li>Mengawasi pemindahan barang BMN rusak ke gudang museum</li>
                                    </ol>
                                </td>
                                <td width="25%" style="border:solid windowtext 1.0pt;">
                                <ol>
                                    <li>1 unit tiang bendera</li>
                                    <li>1 ruangan BMN</li>
                                    <li>2 kali angkutan barang BMN</li>                                    
                                </ol>
                                </td>
                            </tr>
                            <tr>
                                <td width="5%" style="border:solid windowtext 1.0pt; text-align: center">3. </td>
                                <td width="25%" style="border:solid windowtext 1.0pt;">Saharudin</td>
                                <td width="20%" style="border:solid windowtext 1.0pt;">PNS</td>
                                <td width="25%" style="border:solid windowtext 1.0pt;">
                                    <ol>
                                        <li>Membersihkan dinding koridor lantai 1 untuk persiapan plamir (3m2)</li>
                                        <li>Memasang plamir dinding koridor lantai 1 (3m2)</li>
                                        <li>Membersihkan toilet kantor (L/P) lantai 1</li>
                                    </ol>
                                </td>
                                <td width="25%" style="border:solid windowtext 1.0pt;">
                                <ol>
                                    <li>2 unit toilet di lantai 1 (toilet laki-laki dan perempuan)</li>
                                    <li>Dinding koridor lantai 1 seluas 6m<sup>2</sup></li>                                  
                                </ol>
                                </td>
                            </tr>    
                        </tbody>
                    </table>
                    <br/>
                    <table class="table table-sm align-self-end" width="100%">
                        <tbody valign="top">
                            <tr>
                                <td width="75" style="padding: 0px;">Untuk </td>
                                <td width="8" style="padding: 0px;">: </td>
                                <td style="padding: 0px; text-align: justify">
                                Melaksanakan lembur Mempersiapkan kelengkapan berkas dan hal lainnya untuk kegiatan Pengambilan Sumpah dan Pelantikan Pejabat Pengawas serta Pejabat Fungsional yang dilaksanakan pada Tanggal 7-8 Januari 2023 hari Sabtu dan Minggu.
                                </td>
                            </tr>       
                        </tbody>
                    </table>
                    <p style="text-align: justify; text-indent: 0.5in">
                    Demikian surat perintah lembur ini dibuat, untuk dipergunakan sebagaimana mestinya dan dilaksanakan dengan penuh tanggung jawab.
                    </p>
                    ';
                    break;
                case 2: // surat keterangan
                    $template = '
                    <p>Saya yang bertanda tangan di bawah ini,</span></p>
                    <br/>
                    <p>Nama&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&nbsp;:&nbsp; &nbsp;&nbsp;Ir. Win Rizal, M.E.</p>
                    <p>NIP&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;:&nbsp; &nbsp;&nbsp;196608251988021001</p>
                    <p>Jabatan&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;:&nbsp; &nbsp;&nbsp;Kepala BPS Provinsi Bengkulu selaku Kuasa Pengguna Barang</p>
                    <p>
                    dengan ini menerangkan bahwa di BPS Provinsi Bengkulu terdapat Barang Milik Negara (BMN) yang diaktifkan kembali penggunaannya. Dengan rincian sebagai berikut:
                    </p>
                    <br/>
                    <table style="border-collapse:collapse;border: none;" width="100%">
                        <tbody valign="top">
                            <tr>
                                <td width="5%" style="border:solid windowtext 1.0pt; text-align: center">No. </td>
                                <td width="15%" style="border:solid windowtext 1.0pt;">Kode Barang</td>
                                <td width="10%" style="border:solid windowtext 1.0pt;">NUP</td>
                                <td width="10%" style="border:solid windowtext 1.0pt;">Nama Barang</td>
                                <td width="10%" style="border:solid windowtext 1.0pt;">Merk/Tipe</td>
                                <td width="10%" style="border:solid windowtext 1.0pt;">Nilai Perolehan (Rp)</td>
                            </tr>
                            <tr>
                                <td width="5%" style="border:solid windowtext 1.0pt; text-align: center">1. </td>
                                <td width="15%" style="border:solid windowtext 1.0pt;">3.06.02.01.010</td>
                                <td width="10%" style="border:solid windowtext 1.0pt;">1</td>
                                <td width="10%" style="border:solid windowtext 1.0pt;">Facsimile</td>
                                <td width="10%" style="border:solid windowtext 1.0pt;">Panasonic</td>
                                <td width="10%" style="border:solid windowtext 1.0pt;">926.000</td>
                            </tr>
                            <tr>
                                <td width="5%" style="border:solid windowtext 1.0pt; text-align: center">2. </td>
                                <td width="15%" style="border:solid windowtext 1.0pt;">6.01.01.02.999</td>
                                <td width="10%" style="border:solid windowtext 1.0pt;">5.645</td>
                                <td width="10%" style="border:solid windowtext 1.0pt;">Serial Lainnya</td>
                                <td width="10%" style="border:solid windowtext 1.0pt;">Statistik Daerah Kota Bengkulu 2013</td>
                                <td width="10%" style="border:solid windowtext 1.0pt;">178.200.000</td>
                            </tr>        
                        </tbody>
                    </table>
                    <p style="text-align: justify">
                    Adapun koreksi ini dilakukan setelah Tim Kerja BMN melakukan evaluasi terhadap kondisi BMN yang ada di Laporan BMN dengan kondisi sebenarnya.
                    </p>
                    <p style="text-align: justify; text-indent: 0.5in">
                    Demikian surat keterangan ini dibuat dengan sebenarnya untuk dipergunakan sebagaimana mestinya.
                    </p>
                    ';
                default:
                    $template = $template;
            }
            return $template;
        } elseif ($action = 'update') {
            $isisurat = Suratrepoeks::findOne(['id_suratrepoeks' => $surat]);
            return $isisurat->isi_suratrepoeks;
        }
    }
    public function actionLihatscan($id)
    {
        $model = $this->findModel($id);
        if ($model->owner != Yii::$app->user->identity->username && $model->approver != Yii::$app->user->identity->username && !Yii::$app->user->identity->issekretaris && $model->visibletome == false) {
            Yii::$app->session->setFlash('warning', "Surat eksternal hanya dapat dilakukan oleh pemilik/penyetuju data surat atau Sekretaris. Terima kasih.");
            return $this->redirect(['index', 'owner' => '', 'year' => '']);
        }
        if ($model->approval == 0) {
            Yii::$app->session->setFlash('warning', "Surat belum disetujui. Terima kasih.");
            return $this->redirect(['index', 'owner' => '', 'year' => '']);
        }
        if (Yii::$app->request->isAjax) {
            return $this->renderAjax('lihatscan', [
                'model' => $model,
            ]);
        } else {
            return $this->render('lihatscan', [
                'model' => $model,
            ]);
        }
    }
    public function actionUploadscan($id)
    {
        $model = $this->findModel($id);
        if ($model->owner != Yii::$app->user->identity->username && !Yii::$app->user->identity->issekretaris) {
            Yii::$app->session->setFlash('warning', "Upload surat eksternal hanya dapat dilakukan oleh pemilik data surat atau Sekretaris. Terima kasih.");
            return $this->redirect(['index', 'owner' => '', 'year' => '']);
        }
        if ($model->approval == 0) {
            Yii::$app->session->setFlash('warning', "Surat belum disetujui. Terima kasih.");
            return $this->redirect(['index', 'owner' => '', 'year' => '']);
        }
        if (Yii::$app->request->isPost) {
            $model->filepdf = UploadedFile::getInstance($model, 'filepdf');
            // Check if there's an existing file and delete it
            if ($model->filepdf && $model->id_suratrepoeks) {
                if (file_exists(Yii::getAlias('@webroot/surat/eksternal/pdf/' . $model->id_suratrepoeks . '.pdf'))) {
                    unlink(Yii::getAlias('@webroot/surat/eksternal/pdf/') . $model->id_suratrepoeks . '.pdf');
                }
            }
            if ($model->upload()) {
                $pengguna = \app\models\Pengguna::findOne($model->owner);
                $approver = \app\models\Pengguna::findOne($model->approver);
                /* NOTIFIKASI UNTUK PEMBUAT SURAT */
                if (Yii::$app->user->identity->username == 'sekbps17') {

                    $isi_notif_wa = '*Portal Pintar 2.0 - WhatsApp Notification Blast*

Bapak/Ibu ' . $pengguna->nama . ', Berkas Surat Anda Nomor *' . $model->nomor_suratrepoeks  . '* sudah diupload oleh *Sekretaris BPS Provinsi Bengkulu*, dan dapat diunduh di Sistem Portal Pintar 2.0 di https://webapps.bps.go.id/bengkulu/portalpintar/. Terima kasih.

_#pesan ini dikirim oleh Portal Pintar dan tidak perlu dibalas_';

                    $isi_notif_wa_approver = '*Portal Pintar 2.0 - WhatsApp Notification Blast*

Bapak/Ibu ' . $approver->nama . ', Berkas Surat Nomor *' . $model->nomor_suratrepoeks  . '* dari ' . $pengguna->nama . ' sudah diupload oleh *Sekretaris BPS Provinsi Bengkulu*, dan dapat diunduh di Sistem Portal Pintar 2.0 di https://webapps.bps.go.id/bengkulu/portalpintar/. Terima kasih.

_#pesan ini dikirim oleh Portal Pintar dan tidak perlu dibalas_';

                    $response = AgendaController::wa_engine($pengguna->nomor_hp, $isi_notif_wa);
                    $response_approver = AgendaController::wa_engine($approver->nomor_hp, $isi_notif_wa_approver);
                } else {
                    $isi_notif_wa = '*Portal Pintar 2.0 - WhatsApp Notification Blast*

Bapak/Ibu ' . $approver->nama . ', Berkas Surat Nomor *' . $model->nomor_suratrepoeks  . '* dari ' . $pengguna->nama . ' sudah diupload oleh yang bersangkutan, dan dapat diunduh di Sistem Portal Pintar 2.0 di https://webapps.bps.go.id/bengkulu/portalpintar/. Terima kasih.

_#pesan ini dikirim oleh Portal Pintar dan tidak perlu dibalas_';

                    $response = AgendaController::wa_engine($approver->nomor_hp, $isi_notif_wa);
                }
                Yii::$app->session->setFlash('success', "Upload surat berhasil. Terima kasih.");
                return $this->redirect(['lihatscan', 'id' => $model->id_suratrepoeks]);
            }
        }
        return $this->render('uploadscan', [
            'model' => $model,
        ]);
    }
    public function actionUploadword($id)
    {
        $model = $this->findModel($id);
        if ($model->owner != Yii::$app->user->identity->username && !Yii::$app->user->identity->issekretaris) {
            Yii::$app->session->setFlash('warning', "Upload surat eksternal hanya dapat dilakukan oleh pemilik data surat atau Sekretaris. Terima kasih.");
            return $this->redirect(['index', 'owner' => '', 'year' => '']);
        }
        if (Yii::$app->request->isPost) {
            $model->fileword = UploadedFile::getInstance($model, 'fileword');
            // Check if there's an existing file and delete it
            if ($model->fileword && $model->id_suratrepoeks) {
                if (file_exists(Yii::getAlias('@webroot/surat/eksternal/word/' . $model->id_suratrepoeks . '.' . $model->fileword->extension))) {
                    unlink(Yii::getAlias('@webroot/surat/eksternal/word/') . $model->id_suratrepoeks . '.' . $model->fileword->extension);
                }
            }
            if ($model->uploadWord()) {
                Yii::$app->session->setFlash('success', "Upload draft surat berhasil. Terima kasih.");
                return $this->redirect(['view', 'id' => $model->id_suratrepoeks]);
            }
        }
        return $this->render('uploadword', [
            'model' => $model,
        ]);
    }
    public function actionKomentar($id)
    {
        $model = $this->findModel($id);
        if ($model->owner != Yii::$app->user->identity->username && $model->approver != Yii::$app->user->identity->username && !Yii::$app->user->identity->issekretaris) {
            Yii::$app->session->setFlash('warning', "Fitur koreksi surat hanya dapat dilakukan oleh pemilik data surat, penyetuju atau Sekretaris. Terima kasih.");
            return $this->redirect(['index', 'owner' => '', 'year' => '']);
        }
        if (Yii::$app->request->isPost) {
            date_default_timezone_set('Asia/Jakarta');
            $model->timestamp_suratrepoeks_lastupdate = date('Y-m-d H:i:s');
            $model->approval = 0;
            $model->jumlah_revisi = $model->jumlah_revisi + 1;
            if ($model->load($this->request->post()) && $model->save()) {
                Yii::$app->session->setFlash('success', "Koreksi surat berhasil dikirim. Terima kasih.");
                return $this->redirect(['view', 'id' => $model->id_suratrepoeks]);
                // return;
            }
        }
        if (Yii::$app->request->isAjax) {
            if (Yii::$app->user->identity->username == $model['approver'])
                return $this->renderAjax('komentar', [
                    'model' => $model,
                ]);
            else
                return $this->renderAjax('bacakomentar', [
                    'model' => $model,
                ]);
        } else {
            if (Yii::$app->user->identity->username == $model['approver'])
                return $this->render('komentar', [
                    'model' => $model,
                ]);
            else
                return $this->render('bacakomentar', [
                    'model' => $model,
                ]);
        }
    }
    public function actionView($id)
    {
        $model =  $this->findModel($id);
        if (Yii::$app->user->identity->username != $model['owner'] && Yii::$app->user->identity->username != $model['approver'] && !Yii::$app->user->identity->issekretaris) {
            Yii::$app->session->setFlash('warning', "Surat ini bersifat rahasia atau diatur invisibility-nya dan hanya dapat dilihat oleh yang menginput dan/atau Sekretaris. Terima kasih.");
            return $this->redirect(['index', 'owner' => '', 'year' => '']);
        }
        if ($model->deleted == 1) {
            Yii::$app->session->setFlash('warning', "Surat ini sudah dihapus.");
            return $this->redirect(['index', 'owner' => '', 'year' => '']);
        }
        if (isset($model->fk_agenda)) {
            $header = LaporanController::findHeader($model->fk_agenda);
            $waktutampil = LaporanController::findWaktutampil($model->fk_agenda);
        } else {
            $header = '';
            $waktutampil = '';
        }
        // die($model);
        include_once('_librarycetaksuratnew.php');
        $waktutampil = '';
        $formatter = Yii::$app->formatter;
        $formatter->locale = 'id-ID'; // set the locale to Indonesian
        $timezone = new \DateTimeZone('Asia/Jakarta'); // create a timezone object for WIB
        $waktutampil = new \DateTime($model->tanggal_suratrepoeks, new \DateTimeZone('UTC')); // create a datetime object for waktumulai with UTC timezone
        $waktutampil->setTimeZone($timezone); // set the timezone to WIB
        $waktutampil = $formatter->asDatetime($waktutampil, 'd MMMM Y'); // format the waktumulai datetime value
        // Ambil daftar KEPADA
        $names = explode(', ', $model->penerima_suratrepoeks);
        $listItems = '';
        foreach ($names as $key => $name) {
            $listItems .= '<li>' .  ' ' . $name . '</li>';
        }
        if (count($names) <= 1)
            $autofillString = $names[0] . '<br/>';
        else
            $autofillString = '<ol style="margin-top: 0px">' . $listItems . '</ol>';
        // Ambil daftar TEMBUSAN
        if ($model->tembusan != null) {
            $names = explode(', ', $model->tembusan);
            $listItems = '';
            foreach ($names as $key => $name) {
                $listItems .= '<li>' .  ' ' . $name . '</li>';
            }
            $autofillString2 = '<ol>' . $listItems . '</ol>';
        } else {
            $autofillString2 = '';
        }
        $kop = '';
        $jenis = $model->jenis;
        switch ($jenis) {
            case 0: // biasa
                $kop = '
                    <table width="100%" border="0" bordercolor="33FFFF" align="center" cellpadding="3" cellspacing="00" style="margin-top: -30px;">
                        <tr>
                            <td height="40" colspan="0" width="10" align="left"><img src="data:image/png;base64,' . Yii::$app->params['imagebase64'] . '" height="60" width="82" /> 
                            </td>
                            <td height="40" vertical-align="middle"><h4 style="color: #007bff; margin-left: 6px; font-family: Tahoma, sans-serif !important;font-size: 18.7px; font-weight: bold;"><i>BADAN PUSAT STATISTIK<br/>PROVINSI BENGKULU</h4></i></td>
                            <td height="40" colspan="0" align="right"><img src="data:image/png;base64,' . Yii::$app->params['imagebase64_st2023'] . '" height="70" width="170" />
                            </td>
                        </tr>
                    </table>
                    <table width="100%" border="0" bordercolor="33FFFF" align="center" cellpadding="3" cellspacing="00" >
                        <p style="text-align: right;">Bengkulu, ' . $waktutampil . '</p>
                        <br/>  
                                    <table class="table table-sm align-self-end">
                                        <tbody valign="top">
                                            <tr>
                                                <td width="75" style="padding: 0px;">Nomor </td>
                                                <td width="8" style="padding: 0px;">: </td>
                                                <td style="padding: 0px;">' . $model->nomor_suratrepoeks . '</td>
                                            </tr>
                                            <tr>
                                                <td style="padding: 0px;">Sifat </td>
                                                <td style="padding: 0px;">: </td>
                                                <td style="padding: 0px;">Biasa</td>
                                            </tr>                            
                                            <tr>
                                                <td style="padding: 0px;">Lampiran </td>
                                                <td style="padding: 0px;">: </td>
                                                <td style="padding: 0px;">' . $model->lampiran . '</td>
                                            </tr>
                                            <tr>
                                                <td style="padding: 0px;">Hal </td>
                                                <td style="padding: 0px;">: </td>
                                                <td style="padding: 0px;">' . $model->perihal_suratrepoeks . '</td>
                                            </tr>
                                        </tbody>
                                    </table>                                
                        </tr>
                    </table>
                    <table width="500" border="0" bordercolor="33FFFF" align="center" cellpadding="0" cellspacing="0">
                        <tr>
                            <span style="font-size: 15px">
                            <br/>
                            <span class="tulisan">Yang Terhormat :<br/>
                            ' . $autofillString . '</span>                        
                            <p style="">di- </p>                
                            <p style="text-indent:.5in;">Tempat</p>
                            <br/>
                            </span>
                        </tr>
                    </table>
                    ';
                $kop2 =
                    '
                    <table width="100%" border="0" bordercolor="33FFFF" align="center" cellpadding="0" cellspacing="0">
                        <tr>
                            <td width="60%"></td>
                            <td></td>
                            <td></td>
                            <td>
                                <center>                                  
                                    ' . (empty($model->ttd_by) ? '-' : $model->ttdbye->jabatan) . '
                                    <br />
                                    <br />
                                    <br />
                                    <br />
                                    <b>' . (empty($model->ttd_by) ? '-' : $model->ttdbye->nama) . '</b>
                                <center>
                            </td>
                        </tr>
                    </table>                    
                    ';
                break;
            case 1: // surat perintah lembur
                $kop = '
                    <table width="100%" border="0" bordercolor="33FFFF" align="center" cellpadding="3" cellspacing="00">
                        <tr>
                            <td height="40" colspan="0" width="10" align="left"><img src="data:image/png;base64,' . Yii::$app->params['imagebase64'] . '" height="60" width="82" /> 
                            </td>
                            <td height="40" vertical-align="middle"><h4 style="margin-left: 6px; font-family: Tahoma, sans-serif !important;font-size: 18.7px; font-weight: bold;"><i>BADAN PUSAT STATISTIK<br/>PROVINSI BENGKULU</h4></i></td>
                            <td height="40" colspan="0" align="right"><br>
                            </td>
                        </tr>
                    </table>
                    <table width="100%" border="0" bordercolor="33FFFF" align="center" cellpadding="3" cellspacing="00">                    
                        <tr style="">
                            <h4 style="text-align: center; text-decoration: underline">SURAT PERINTAH LEMBUR</h4> 
                        </tr>
                        <tr style="">
                            <p style="text-align: center; margin-top:-10px">Nomor : ' . $model->nomor_suratrepoeks . '</p> 
                        </tr>
                    </table>
                    <br/>
                ';
                $kop2 =
                    '
                    <table width="100%" border="0" bordercolor="33FFFF" align="center" cellpadding="0" cellspacing="0">
                        <tr>
                            <td width="60%"></td>
                            <td></td>
                            <td></td>
                            <td>
                                <center>
                                    Bengkulu, ' . $waktutampil . '<br/>                                    
                                    ' . (empty($model->ttd_by) ? '-' : $model->ttdbye->jabatan) . '
                                    <br />
                                    <br />
                                    <br />
                                    <br />
                                    <b>' . (empty($model->ttd_by) ? '-' : $model->ttdbye->nama) . '</b>
                                <center>
                            </td>
                        </tr>
                    </table>
                    ';
                break;
            case 2: //keterangan
                $kop = '
                    <table width="100%" border="0" bordercolor="33FFFF" align="center" cellpadding="3" cellspacing="00" style="margin-top: -50px;">
                        <tr>
                            <td height="40" colspan="0" width="10" align="left"><img src="data:image/png;base64,' . Yii::$app->params['imagebase64'] . '" height="60" width="82" /> 
                            </td>
                            <td height="40" vertical-align="middle"><h4 style="margin-left: 6px; font-family: Tahoma, sans-serif !important;font-size: 18.7px; font-weight: bold;"><i>BADAN PUSAT STATISTIK<br/>PROVINSI BENGKULU</h4></i></td>
                            <td height="40" colspan="0" align="right"><br>
                            </td>
                        </tr>
                    </table>
                    <table width="100%" border="0" bordercolor="33FFFF" align="center" cellpadding="3" cellspacing="00">                    
                        <tr style="">
                            <h4 style="text-align: center; text-decoration: underline">SURAT KETERANGAN</h4> 
                        </tr>
                        <tr style="">
                            <p style="text-align: center; margin-top:-10px">Nomor : ' . $model->nomor_suratrepoeks . '</p> 
                        </tr>
                    </table>
                    <br/>
                ';
                $kop2 =
                    '
                    <table width="100%" border="0" bordercolor="33FFFF" align="center" cellpadding="0" cellspacing="0">
                        <tr>
                            <td width="50%"></td>
                            <td></td>
                            <td></td>
                            <td>
                                <center>
                                    Bengkulu, ' . $waktutampil . '<br/>                                    
                                    ' . (empty($model->ttd_by) ? '-' : $model->ttdbye->jabatan) . '
                                    <br />
                                    <br />
                                    <br />
                                    <br />
                                    <b>' . (empty($model->ttd_by) ? '-' : $model->ttdbye->nama) . '</b>
                                <center>
                            </td>
                        </tr>
                    </table>
                    ';
                break;
            default:
                $kop = '';
        }
        $html =
            '<!DOCTYPE html>
                <html>                
                    <head>
                        ' . $style . $kop . '
                    </head>
                    <body>                   
                        <table width="500" border="0" bordercolor="33FFFF" align="center" cellpadding="0" cellspacing="0">
                            <tr>
                                <span  style="text-align: justify">' . $model->isi_suratrepoeks . '</span>
                            </tr>
                        </table>
                        <br/>
                        <br/>
                        ' . $kop2 . '
                        <br/>
                        ' . ($model->tembusan != null ? '<p style="margin-bottom: 0px">Tembusan: </p>' . $autofillString2 : '') . '                   
                        <div style="font-size:10px" class="tulisan">                    
                                <center>Jalan Adam Malik Km.8 Bengkulu, 38225, Telepon (0736) 349117-118, Kepala (0736) 349116
                                    <br>Fax. (0736) 349115, E-mail: bps1700@bps.go.id
                                </center>
                                <i style="font-size: 8px;">
                                    <center>Generated in Portal Pintar</center>
                                </i>                   
                        </div>
                    </body>                
                </html>';
        if (Yii::$app->request->isAjax) {
            return $this->renderAjax('view', [
                'model' => $this->findModel($id),
                'header' => $header,
                'waktutampil' => $waktutampil,
                'html' => $html
            ]);
        } else {
            return $this->render('view', [
                'model' => $this->findModel($id),
                'header' => $header,
                'waktutampil' => $waktutampil,
                'html' => $html
            ]);
        }
    }
    public function actionGettemplatelampiran($id, $action, $surat)
    {
        if ($action == 'create' || $action == 'update') {
            $template = '';
            if ($id !== '-' && !empty($id))
                $template = '
                <table width="100%" border="0">
                    <tr>
                        <td></td>
                        <td></td>
                        <td width="40%" colspan="0" style="padding: 0;">
                            <b>Lampiran Surat:</b>
                            <br />
                            Nomor : B-809/17000/KU.010/06/2023
                            <br />
                            Tanggal : 31 Mei 2023
                        </td>
                    </tr>
                </table>
                <br/>
                <center><b>Ketentuan dan Persyaratan Rekrutmen Petugas Pengolahan ST2023</b></center>
                <br/>
                <ol>
                    <li>Rekrutmen petugas pengolahan dilaksanakan pada bulan Mei s/d Juni 2023 dengan rincian sebagai berikut:<br />
                        <ol type="a">
                            <li>Proses pendaftaran petugas pengolahan dilaksanakan tanggal 31 Mei s/d 14 Juni 2023 melalui aplikasi
                                SOBAT BPS.</li>
                            <li>Seluruh calon petugas pengolahan ST2023 harus mendaftar terlebih dahulu di aplikasi SOBAT BPS, termasuk
                                untuk calon petugas pengolahan ST2023 yang baru pertama kali melamar sebagai mitra BPS.</li>
                            <li>Tim Pelaksana ST2023 BPS RI telah menyediakan aplikasi e-learning untuk melakukan seleksi secara online.
                                Penjelasan mengenai admin e-learning, pedoman seleksi petugas pengolahan ST2023 menggunakan aplikasi
                                e-learning, serta rencana jadwal pelaksanaan seleksi secara online dapat dilihat pada Lampiran 2.
                                Narahubung untuk penggunaan aplikasi e-learning adalah:
                                <ul>
                                    <li>Sdri. Ndaru Nuswantari (No HP/WA: 0856 9472 7488) – terkait prosedur rekrutmen</li>
                                    <li>Sdri. Erika Siregar (No HP/WA: 0812 2590 5757) – terkait e-learning.</li>
                                </ul>
                            </li>
                        </ol>
                    </li>
                    <li>
                        Petugas pengolahan data terdiri dari:
                        <ol type="a">
                            <li>Petugas <i>Receiving-Batching</i></li>
                            <li>Petugas <i>Editing-Coding</i></li>
                            <li>Petugas Operator Entri</li>
                            <li>Pengawas Pengolahan</li>
                        </ol>
                    </li>
                </ol>
                ';
            else
                $template = '';
            return $template;
        } elseif ($action = 'hoho') {
            $isisurat = Suratrepoeks::findOne(['id_suratrepoeks' => $surat]);
            if (isset($isisurat))
                return $isisurat->isi_lampiran;
            else
                return '';
        }
    }
    public function actionCetaklampiran($id)
    {
        $model = $this->findModel($id);
        if (Yii::$app->user->identity->username != $model['owner'] && Yii::$app->user->identity->username != $model['approver'] && !Yii::$app->user->identity->issekretaris) {
            Yii::$app->session->setFlash('warning', "Surat eksternal hanya dapat dilihat oleh Sekretaris dan Pengguna yang menginput atau menyetujui. Terima kasih.");
            return $this->redirect(['index', 'owner' => '', 'year' => '']);
        }
        // die($model);
        include_once('_librarycetaksurat.php');
        $html =
            '<!DOCTYPE html>
                <html>
                <head>
                    ' . $style . '
                </head>
                <body>  
                    ' . $model->isi_lampiran . '
                </body>
                </html>';
        $options = new Options();
        $options->set('defaultFont', 'Courier');
        $options->set('isRemoteEnabled', TRUE);
        $options->set('debugKeepTemp', TRUE);
        $options->set('isHtml5ParserEnabled', TRUE);
        $dompdf = new DOMPDF($options);
        $dompdf->loadHtml($html);
        if ($model->isi_lampiran_orientation == 0)
            $dompdf->setPaper('A4', 'portrait');
        else
            $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();
        //$dompdf->stream('laporan_'.$nama.'.pdf');
        // $canvas = $dompdf->getCanvas();
        //require_once("dompdf/include/font_metrics.cls.php");
        // $font = Font_Metrics::get_font("Times new roman", "");
        // $canvas->page_text(16, 800, "Page: {PAGE_NUM} of {PAGE_COUNT}", $font, 8, array(0, 0, 0));
        ob_end_clean();
        $dompdf->stream("Lampiran - " . $model->perihal_suratrepoeks . ".pdf", array("Attachment" => 0));
        // $output = $dompdf->output();
        // file_put_contents("report.pdf", $output);
    }
}
