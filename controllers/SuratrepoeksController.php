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
                                'view',
                                'list',
                                'setujui',
                                'lihatscan',
                                'uploadscan',
                                'uploadword',
                                'komentar',
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

            Ykh. Sekretaris BPS Kabupaten Bengkulu Selatan, Surat dari ' . $pengguna->nama . ' dengan  Nomor *' . $model->nomor_suratrepoeks  . '* sudah disetujui oleh *' . $approver->nama . '*, mohon meng-upload PDF surat yang telah ditandatangani ke Sistem Portal Pintar 2.0 di https://webapps.bps.go.id/bengkulu/portalpintar/. Terima kasih.
            
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

Bapak/Ibu ' . $pengguna->nama . ', Berkas Surat Anda Nomor *' . $model->nomor_suratrepoeks  . '* sudah diupload oleh *Sekretaris BPS Kabupaten Bengkulu Selatan*, dan dapat diunduh di Sistem Portal Pintar 2.0 di https://webapps.bps.go.id/bengkulu/portalpintar/. Terima kasih.

_#pesan ini dikirim oleh Portal Pintar dan tidak perlu dibalas_';

                    $isi_notif_wa_approver = '*Portal Pintar 2.0 - WhatsApp Notification Blast*

Bapak/Ibu ' . $approver->nama . ', Berkas Surat Nomor *' . $model->nomor_suratrepoeks  . '* dari ' . $pengguna->nama . ' sudah diupload oleh *Sekretaris BPS Kabupaten Bengkulu Selatan*, dan dapat diunduh di Sistem Portal Pintar 2.0 di https://webapps.bps.go.id/bengkulu/portalpintar/. Terima kasih.

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
        $jenis = $model->jenis;
        
        if (Yii::$app->request->isAjax) {
            return $this->renderAjax('view', [
                'model' => $this->findModel($id),
                'header' => $header,
                'waktutampil' => $waktutampil,
            ]);
        } else {
            return $this->render('view', [
                'model' => $this->findModel($id),
                'header' => $header,
                'waktutampil' => $waktutampil,
            ]);
        }
    }
}
