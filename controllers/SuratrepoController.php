<?php

namespace app\controllers;

use app\models\Agenda;
use app\models\Suratrepo;
use app\models\SuratrepoSearch;
use app\models\Suratsubkode;
use DateTime;
use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
//use Dompdf\DOMPDF; //untuk di local
use Dompdf\Dompdf; //untuk di webapps
use Dompdf\Options;
use yii\helpers\Html;
use yii\web\UploadedFile;

class SuratrepoController extends BaseController
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
                                'lihatscan',
                                'uploadscan',
                                'uploadword',
                                'cetakundangan'
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
        $searchModel = new SuratrepoSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);
        if ($owner != '')
            $dataProvider->query->andWhere(['owner' => $owner]);
        if ($year == date("Y"))
            $dataProvider->query->andWhere(['YEAR(tanggal_suratrepo)' => date("Y")]);
        elseif ($year != '')
            $dataProvider->query->andWhere(['YEAR(tanggal_suratrepo)' => $year]);
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }
    public function actionList($agenda)
    {
        $searchModel = new SuratrepoSearch();
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
        $model = new Suratrepo();
        $surats = Suratrepo::find()
            ->select('*')
            ->where(['owner' => Yii::$app->user->identity->username])
            ->andWhere(['deleted' => 0])
            ->andWhere(
                ['>', 'DATEDIFF(NOW(), DATE(timestamp_suratrepo_lastupdate))', 3], // diinput dalam span 3 hari
            )
            ->asArray()
            ->all();
        // Get the current date and time
        $currentDate = new DateTime();
        // Subtract 2 days from the current date
        $threeDaysAgo = $currentDate->modify('-2 days');
        // Loop through each $surats and check if the file exists
        $missingFiles = [];
        $missingNumbers = [];
        $missingTitles = [];
        foreach ($surats as $surat) {
            $filePath = Yii::getAlias('@webroot/surat/internal/pdf/' . $surat['id_suratrepo'] . '.pdf');
            if (!file_exists($filePath)) {
                // File does not exist, add the id_suratrepoeks to the missingFiles array
                $missingFiles[] = $surat['id_suratrepo'];
                $missingNumbers[] = $surat['nomor_suratrepo'];
                $missingTitles[] = $surat['perihal_suratrepo'];
            }
        }
        // Print the list of id_suratrepoeks without corresponding files
        if (!empty($missingFiles)) {
            $teks = '<ol>';
            for ($i = 0; $i < count($missingFiles); $i++) {
                // $teks .= Html::a('<li><i class="fas fa-upload"></i>  ' . $missingNumbers[$i] . ' - ' . $missingTitles[$i] . '</li>', ['suratrepoeks/uploadscan/' . $missingFiles[$i]], []);
                $teks .= '<li>' . $missingNumbers[$i] . ' - ' . $missingTitles[$i] . Html::a(' <i class="fas fa-upload"></i> ', ['suratrepo/uploadscan/' . $missingFiles[$i]], []) . '</li>';
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
                    return $this->redirect(['view', 'id' => $model->id_suratrepo]);
                }
            } else {
                $model->loadDefaultValues();
            }
        } else {
            $dataagenda = Agenda::findOne(['id_agenda' => $id]);
            $header = LaporanController::findHeader($id);
            $waktutampil = LaporanController::findWaktutampil($id);
            if ($dataagenda->reporter != Yii::$app->user->identity->username) {
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
                    if (($dataagenda->metode == 0 || $dataagenda->tempat == 13) && $dataagenda->progress == 0) {
                        if ($model->is_undangan == 0) { //bukan surat undangan
                            Yii::$app->session->setFlash('success', "surat berhasil ditambahkan. Jika memerlukan, silahkan lanjutkan pengisian Permohonan Zoom. Terima kasih.");
                            return $this->redirect(['zooms/create', 'fk_agenda' => $dataagenda->id_agenda]);
                        } else { //surat undangan
                            Yii::$app->session->setFlash('success', "Surat Undangan telah berhasil di-generate oleh Portal Pintar. Gunakan tombol Download untuk mengunduh surat. Terima kasih.");
                            return $this->redirect(['view', 'id' => $model->id_suratrepo]);
                            // return $this->redirect(['suratrepo/cetakundangan', 'id' => $model->id_suratrepo]);
                        }
                    } else {
                        Yii::$app->session->setFlash('success', "Surat berhasil ditambahkan. Terima kasih.");
                        return $this->redirect(['view', 'id' => $model->id_suratrepo]);
                    }
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
    public function actionCetakundangan($id)
    {
        $model = $this->findModel($id);
        $dataagenda = Agenda::findOne(['id_agenda' => $model->fk_agenda]);
        // die(var_dump($dataagenda));
        if ($model->is_undangan != 1) {
            Yii::$app->session->setFlash('success', "Portal Pintar hanya menyediakan fitur cetak surat untuk Undangan Agenda Internal. Terima kasih.");
            return $this->redirect(['view', 'id' => $model->id_suratrepo]);
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
        $waktutampil = new \DateTime($model->tanggal_suratrepo, new \DateTimeZone('UTC')); // create a datetime object for waktumulai with UTC timezone
        $waktutampil->setTimeZone($timezone); // set the timezone to WIB
        $waktutampil = $formatter->asDatetime($waktutampil, 'd MMMM Y'); // format the waktumulai datetime value
        // Ambil daftar KEPADA
        $names = explode(', ', $model->penerima_suratrepo);
        $listItems = '';
        foreach ($names as $key => $name) {
            $listItems .= '<li>' .  ' ' . $name . '</li>';
        }
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
        $kop = '
            <table width="500" border="0" bordercolor="33FFFF" align="center" cellpadding="3" cellspacing="00" style="border-bottom: 1px solid #000000;">
                <tr>
                    <td height="40" colspan="0" width="10" align="left"><img src="data:image/png;base64,' . Yii::$app->params['imagebase64'] . '" height="60" width="82" /> 
                    </td>
                    <td height="40" vertical-align="middle">
                        <h4 style="font-size: 18px; line-height: 1; margin: 0; font-weight: bold"><i>BADAN PUSAT STATISTIK<br/>KABUPATEN BENGKULU SELATAN</h4></i>
                        <p style="font-size: 10px; line-height: 1.1; margin:0">
                            Jalan Affan Bachsin No.108A RT.07 Pasar Baru Kota Manna 38512<br/>
                            Website: bengkuluselatankab.bps.go.id, e-mail: bps1701@bps.go.id
                        </p>
                    </td>                            
                </tr>
            </table>
            <table width="500" border="0" bordercolor="33FFFF" align="center" cellpadding="3" cellspacing="00">
                <td class="col-sm-8" style="width:60%">
                    <div class="table-responsive">
                        <table class="table table-sm align-self-end">
                            <tbody valign="top">
                                <tr>
                                    <td width="75" style="padding: 0px">Nomor </td>
                                    <td width="8" style="padding: 0px">: </td>
                                    <td style="padding: 0px">' . $model->nomor_suratrepo . '</td>
                                </tr>
                                <tr>
                                    <td style="padding: 0px">Sifat </td>
                                    <td style="padding: 0px">: </td>
                                    <td style="padding: 0px">Biasa</td>
                                </tr>
                                <tr>
                                    <td style="padding: 0px">Lampiran </td>
                                    <td style="padding: 0px">: </td>
                                    <td style="padding: 0px">' . $model->lampiran . '</td>
                                </tr>
                                <tr>
                                    <td style="padding: 0px">Perihal </td>
                                    <td style="padding: 0px">: </td>
                                    <td style="padding: 0px">' . $model->perihal_suratrepo . '</td>
                                </tr>
                            </tbody>
                        </table>
                </td>
                <td class="col-sm-4" style="width:40%; vertical-align: top;">
                    <p style="text-align: right; margin-top: 0px; margin-right: 2px">Bengkulu, ' . $waktutampil . '</p>
                </td>
                </div>
                </tr>
            </table>
            <table width="500" border="0" bordercolor="33FFFF" align="center" cellpadding="0" cellspacing="0">
                <tr>
                    <span style="font-size: 15px; line-height: 1.5; margin:0">
                        <br />
                        Yth. : <br />
                        <p style="text-indent:.5in; margin: 0px">Bapak/Ibu/Saudara/i (Daftar Terlampir) </p>
                        <span style="margin-top: -10px">di-</span>
                        <p style="text-indent:.5in; margin: 0px">Tempat</p>
                        <br />
                    </span>
                </tr>
            </table>
        ';
        $content = '
            <table width="500" border="0" bordercolor="33FFFF" align="center" cellpadding="0" cellspacing="0">
                <tr>
                    <td>
                        <p style="text-indent: 0.5in; line-height:1.5; margin-bottom:0px; text-align: justify">Sehubungan dengan Agenda 
                        <strong>' . $model->perihal_suratrepo . '</strong>, bersama ini kami mengundang Bapak/Ibu dalam kegiatan rapat yang akan diadakan pada:
                        </p>
                    </td>
                </tr>
                
            </table>
            <table width="500" border="0" bordercolor="33FFFF" align="center" cellpadding="0" cellspacing="0">
                <tr style="padding:0">
                    <td style="width:0.5in; padding:0"></td>
                    <td style="padding:0">
                        <div class="table-responsive">
                            <table class="table table-sm align-self-end ' . ((!Yii::$app->user->isGuest && Yii::$app->user->identity->theme == 0) ? '' : 'table-dark') . '">
                                <tbody>                           
                                    <tr>
                                        <td style="vertical-align: top">Waktu</td>
                                        <td style="vertical-align: top">: </td>
                                        <td style="vertical-align: top">' . LaporanController::findWaktutampil($dataagenda->id_agenda) . '</td>
                                    </tr>                                    
                                    <tr>
                                        <td style="vertical-align: top">Tempat</td>
                                        <td style="vertical-align: top">: </td>
                                        <td style="vertical-align: top">' . $dataagenda->getTempate() . '</td>
                                    </tr>
                                    <tr>
                                        <td style="vertical-align: top">Pemimpin Rapat</td>
                                        <td style="vertical-align: top">: </td>
                                        <td style="vertical-align: top">' . $dataagenda->pemimpine->nama . '</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </td>
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
                            ' . $model->ttd_by_jabatan . '
                            <br />
                            <br />
                            <br />
                            <br />
                            <b>' . $model->ttd_by . '</b>
                            <center>
                    </td>
                </tr>
            </table>';
        // Step 1: Get the list of email addresses from the peserta attribute in the agenda table
        $emailList = explode(', ', $dataagenda->peserta);
        // Step 2: Extract the username (without "@bps.go.id") from each email address
        $usernames = [];
        foreach ($emailList as $email) {
            $username = substr($email, 0, strpos($email, '@'));
            $usernames[] = $username;
        }
        // Step 3: Query the pengguna table for the list of names that correspond to the extracted usernames
        $names = \app\models\Pengguna::find()
            ->select('nama')
            ->where(['in', 'username', $usernames])
            ->column();
        // Step 4: Convert the list of names to a string in the format that can be used for autofill
        // $autofillString = implode('<br> ', $names);
        $listItems = '';
        foreach ($names as $key => $name) {
            $listItems .= '<li>' .  ' ' . $name . '</li>';
        }
        $autofillString2 = '<b>Daftar Undangan:</b> <ol>' . $listItems . '</ol>';
        $autofillString2 =  $autofillString2 . (($dataagenda->peserta_lain != null) ? '<b>Peserta Tambahan : </b><br/>' . $dataagenda->peserta_lain : '');
        $content2 =
            '
                Lampiran
                <table width="500" border="0" bordercolor="33FFFF" align="center" cellpadding="0" cellspacing="0">
                <tr style="padding:0">
                    <td style="padding:0">
                        <div class="table-responsive">
                            <table class="table table-sm align-self-end ' . ((!Yii::$app->user->isGuest && Yii::$app->user->identity->theme == 0) ? '' : 'table-dark') . '">
                                <tbody>
                                    <tr>
                                        <td class="col-sm-2" style="padding:0">Nomor:</td>
                                        <td style="padding:0">: ' . $model->nomor_suratrepo . '</td>
                                    </tr>                            
                                    <tr>
                                        <td style="padding:0">Tanggal</td>
                                        <td style="padding:0">: ' . $waktutampil . '</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td style="padding:0">
                    ' . $autofillString2 . '
                    </td>
                </tr>
            </table>
            ';
        $html =
            '<!DOCTYPE html>
                <html>
                <head>
                    ' . $style . $kop . '
                </head>
                <body>                   
                    ' . $content . '                    
                    <br/>
                    <br/>                     
                    ' . $kop2 . '
                    <foot style="font-size:10px">
                        <div class="footer">                        
                            <i style="font-size: 8px;">
                                This document is generated by Portal Pintar
                            </i>
                        </div>
                    </foot>
                    <div style="page-break-before: always;">' . $content2 . '</div>
                    <br/>
                    ' . ($model->tembusan != null ? '<p style="margin-bottom: 0px">Tembusan: </p>' . $autofillString2 : '') . '
                </body>
                <foot style="font-size:10px">
                    <div class="footer">                        
                        <i style="font-size: 8px;">
                            This document is generated by Portal Pintar
                        </i>
                    </div>
                </foot>
                </html>';
        $options = new Options();
        $options->set('defaultFont', 'Courier');
        $options->set('isRemoteEnabled', TRUE);
        $options->set('debugKeepTemp', TRUE);
        $options->set('isHtml5ParserEnabled', TRUE);
        $dompdf = new \Dompdf\Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        // Capture the PDF output
        $pdfOutput = $dompdf->output();

        // Encode the PDF output in base64 for embedding in the view
        $base64Pdf = base64_encode($pdfOutput);

        // Render the view, passing the base64-encoded PDF content
        return $base64Pdf;
    }

    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        if (Yii::$app->user->identity->username !== $model['owner']) {
            Yii::$app->session->setFlash('warning', "Maaf. Hanya pemilik surat yang dapat mengubah data dan isi surat. Terima kasih.");
            return $this->redirect(['index', 'owner' => '', 'year' => '']);
        }
        if (file_exists(Yii::getAlias('@webroot/surat/internal/pdf/' . $id . '.pdf'))) {
            Yii::$app->session->setFlash('warning', "Maaf. Surat internal yang sudah diunggah tidak dapat diubah kembali. Terima kasih.");
            return $this->redirect(['index', 'owner' => '', 'year' => '']);
        }
        if ($model->fk_agenda == null) {
            $dataagenda = 'noagenda';
            $header = 'noagenda';
            $waktutampil = 'noagenda';
        } else {
            $header = LaporanController::findHeader($model->fk_agenda);
            $waktutampil = LaporanController::findWaktutampil($model->fk_agenda);
            $dataagenda = Agenda::findOne(['id_agenda' => $model->fk_agenda]);
            if ($dataagenda->progress == 3) {
                Yii::$app->session->setFlash('warning', "Agenda ini sudah dibatalkan. Terima kasih.");
                return $this->redirect(['index', 'owner' => '', 'year' => '']);
            }
        }
        if ($this->request->isPost) {
            // die($_POST['Suratrepo']['jenis']);
            if ($_POST['Suratrepo']['jenis'] == 3) { //selain bast tidak usah pihak_pertama, pihak_kedua
                Suratrepo::updateAll(['ttd_by' => null, 'ttd_by_jabatan' => null], 'id_suratrepo = "' . $id . '"');
            } elseif ($_POST['Suratrepo']['jenis'] == 0 || $_POST['Suratrepo']['jenis'] == 1 || $_POST['Suratrepo']['jenis'] == 2) { //selain bast tidak usah pihak_pertama, pihak_kedua
                // die ('haha');
                Suratrepo::updateAll(['pihak_pertama' => null, 'pihak_kedua' => null], 'id_suratrepo = "' . $id . '"');
            }
            $model->timestamp_suratrepo_lastupdate = date('Y-m-d H:i:s');
            if ($model->load($this->request->post()) && $model->save()) {
                Yii::$app->session->setFlash('success', "Surat berhasil dimutakhirkan. Terima kasih.");
                return $this->redirect(['view', 'id' => $model->id_suratrepo]);
            }
        }
        return $this->render('update', [
            'model' => $model,
            'dataagenda' => $dataagenda,
            'header' => $header,
            'waktutampil' => $waktutampil
        ]);
    }
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        $affected_rows = Suratrepo::updateAll(['deleted' => 1, 'timestamp_suratrepo_lastupdate' => date('Y-m-d H:i:s')], 'id_suratrepo = "' . $id . '"');
        if ($affected_rows == 0) {
            Yii::$app->session->setFlash('warning', "Gagal. Mohon hubungi Admin.");
            return $this->redirect(['index', 'owner' => '', 'year' => '']);
        } else {
            Yii::$app->session->setFlash('success', "Surat berhasil dihapus. Terima kasih.");
            return $this->redirect(['index', 'owner' => '', 'year' => '']);
        }
    }
    protected function findModel($id_suratrepo)
    {
        if (($model = Suratrepo::findOne(['id_suratrepo' => $id_suratrepo])) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('The requested page does not exist.');
    }
    public function actionGetnomorsurat($id, $tanggal, $action)
    {
        $bulan = date("m", strtotime($tanggal));
        $tahun = date("Y", strtotime($tanggal));
        $jadwal = Suratrepo::find()
            ->where(['YEAR(tanggal_suratrepo)' => $tahun])
            ->andWhere(['deleted' => 0])
            ->all();
        $nosurats = [];
        foreach ($jadwal as $value) {
            if (preg_match('/-(\d+)\//', $value->nomor_suratrepo, $matches)) {
                $nosurat = $matches[1];
            } else {
                // Handle cases where the pattern does not match (optional)
                $nosurat = null; // or any default value
            }
            array_push($nosurats, $nosurat);
        }
        sort($nosurats);
        $idterakhir = end($nosurats);
        // die ($idterakhir);
        $sortedJadwal = Suratrepo::find() //cek kalau ada duplikat nomor
            ->where(['like', 'nomor_suratrepo', '-' . $idterakhir . '/'])
            ->andWhere(['YEAR(tanggal_suratrepo)' => $tahun])
            ->andWhere(['deleted' => 0])
            ->one();
        // die(var_dump($sortedJadwal));
        $sifat = 0;
        // die($sortedJadwal->nomor_suratrepoeks);
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
            return $kode . '-' . '001' . '/17510/' . $suratsubkode->fk_suratkode . '.' . $suratsubkode->kode_suratsubkode . '/' . (($tahun == 2023) ? ($bulan . '/' . $tahun) : $tahun);
        } else {
            $suratajuan = strtotime($tanggal); //tanggal pada form
            $suratterakhir = strtotime($sortedJadwal->tanggal_suratrepo); //tanggal surat dengan ID terakhir
            if ($suratajuan >= $suratterakhir) { // tanggal yang diajukan setelah tanggal dengan ID terakhir
                // $nosurat = substr($jadwal->nomor_suratrepoeks, 2, 3);
                $str = $sortedJadwal->nomor_suratrepo;
                if (preg_match('/-(\d+)\//', $str, $matches)) {
                    $nosurat = ($matches[1]);
                }
                // die($nosurat);
                $nosurat += 1;
                if (strlen($nosurat) == 2)
                    $nosurat = '0' . $nosurat;
                elseif (strlen($nosurat) == 1)
                    $nosurat = '00' . $nosurat;
                return $kode . '-' . $nosurat . '/17510/' . $suratsubkode->fk_suratkode . '.' . $suratsubkode->kode_suratsubkode . '/' . (($tahun == 2023) ? ($bulan . '/' . $tahun) : $tahun);
            } else {
                $jadwalsisip = Suratrepo::find()->where(['<=', 'tanggal_suratrepo', $tanggal])->andWhere(['deleted' => 0])->andWhere(['YEAR(tanggal_suratrepo)' => $tahun])->all();
                if (count($jadwalsisip) < 1)
                    $jadwalsisip = Suratrepo::find()->where(['>=', 'tanggal_suratrepo', $tanggal])->andWhere(['deleted' => 0])->andWhere(['YEAR(tanggal_suratrepo)' => $tahun])->orderBy(['tanggal_suratrepo' => SORT_DESC])->all();
                // die(var_dump($jadwalsisip));
                $nosuratsisips = [];
                foreach ($jadwalsisip as $value) {
                    if (preg_match('/-(\d+)\//', $value->nomor_suratrepo, $matches)) {
                        $nosuratsisip = $matches[1];
                    }
                    array_push($nosuratsisips, $nosuratsisip);
                }
                sort($nosuratsisips);
                $idterakhirsisip = end($nosuratsisips);
                if (!empty($jadwalsisip)) {
                    $jadwalsisipsorted = Suratrepo::find() //cek kalau ada duplikat nomor
                        ->where(['like', 'nomor_suratrepo', '-' . $idterakhirsisip . '/'])
                        ->andWhere(['YEAR(tanggal_suratrepo)' => $tahun])
                        ->andWhere(['deleted' => 0])
                        ->one();
                    $str = $jadwalsisipsorted->nomor_suratrepo;
                } else
                    return 'Portal Pintar hanya menerima data sejak 24 Mei 2023.';
                // return $str;
                if (preg_match('/-(\d+)\//', $str, $matches)) {
                    $nosurat = $matches[1];
                }
                $checksuratsisip = strtok($jadwalsisipsorted->nomor_suratrepo, '/'); //ambil nomor tanpa karakter setelah garis miring
                $checksuratsisip = substr($checksuratsisip, 2); ///ambil nomor tanpa B
                $tes = preg_replace('/[^A-Z]/', '', $checksuratsisip);
                // return $checksuratsisip;
                $duplikat = Suratrepo::find() //cek kalau ada duplikat nomor
                    ->where(['like', 'nomor_suratrepo', $checksuratsisip])
                    ->andWhere(['YEAR(tanggal_suratrepo)' => $tahun])
                    ->andWhere(['deleted' => 0])
                    ->count();
                $listduplikat = Suratrepo::find()
                    ->where(['like', 'nomor_suratrepo', $checksuratsisip])
                    ->andWhere(['YEAR(tanggal_suratrepo)' => $tahun])
                    ->andWhere(['deleted' => 0])
                    ->orderBy(['nomor_suratrepo' => SORT_DESC])->one(); //ambil duplikat dengan nomor terakhir
                // die(var_dump($listduplikat));
                if ($duplikat > 0) {
                    // return $listduplikat->nomor_suratrepoeks; //untuk menghindari duplikat
                    $checksuratsisip = strtok($listduplikat->nomor_suratrepo, '/'); //ambil nomor tanpa karakter setelah garis miring
                    $checksuratsisip = substr($checksuratsisip, 2); ///ambil nomor tanpa B
                    $tes = preg_replace('/[^A-Z]/', '', $checksuratsisip);
                }
                // die($tes);
                if ($tes != "") { // Check if there are letters
                    // Get the letter part
                    $letterPart = preg_replace('/[^A-Z]/', '', $checksuratsisip);
                    // Get the number part
                    $numberPart = preg_replace('/[^0-9]/', '', $checksuratsisip);
                    // Increment the letter part
                    $newLetterPart = SuratrepoController::incrementLetterPart($letterPart);
                    // Combine the number and new letter parts
                    $newChecksuratsisip = $numberPart . $newLetterPart;
                    // die ($newChecksuratsisip);
                    $cekduplikatsisip = Suratrepo::find() //cek kalau ada duplikat nomor
                        ->where(['like', 'nomor_suratrepo', '-' . $newChecksuratsisip . '/'])
                        ->andWhere(['YEAR(tanggal_suratrepo)' => $tahun])
                        ->andWhere(['deleted' => 0])
                        ->one();
                    // die(var_dump($cekduplikatsisip));
                    while (!empty($cekduplikatsisip)) {
                        $newLetterPart = SuratrepoController::incrementLetterPart($newLetterPart);
                        $newChecksuratsisip = $numberPart . $newLetterPart;
                        $cekduplikatsisip = Suratrepo::find()
                            ->where(['like', 'nomor_suratrepo', '-' . $newChecksuratsisip . '/'])
                            ->andWhere(['YEAR(tanggal_suratrepo)' => $tahun])
                            ->andWhere(['deleted' => 0])
                            ->one();
                    }
                    return $kode . '-' . $newChecksuratsisip . '/17510/' . $suratsubkode->fk_suratkode . '.' . $suratsubkode->kode_suratsubkode . '/' . (($tahun == 2023) ? ($bulan . '/' . $tahun) : $tahun);
                } else {
                    return $kode . '-' . $nosurat . 'A' . '/17510/' . $suratsubkode->fk_suratkode . '.' . $suratsubkode->kode_suratsubkode . '/' . (($tahun == 2023) ? ($bulan . '/' . $tahun) : $tahun);
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
        $letterNumber = SuratrepoController::alphabetToNumber($letterPart);
        $letterNumber += 1;
        // $newLetterPart = $alphabet[$letterNumber]; // returns alphabet
        $newLetterPart = SuratrepoController::numberToAlphabet($letterNumber);
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
        if (Yii::$app->request->isAjax) {
            return $this->renderAjax('lihatscan', [
                'model' => $this->findModel($id),
            ]);
        } else {
            return $this->render('lihatscan', [
                'model' => $this->findModel($id),
            ]);
        }
    }
    public function actionUploadscan($id)
    {
        $model = $this->findModel($id);
        if ($model->owner != Yii::$app->user->identity->username) {
            Yii::$app->session->setFlash('warning', "Upload surat hanya dapat dilakukan oleh pemilik data surat. Terima kasih.");
            return $this->redirect(['index', 'owner' => '', 'year' => '']);
        }
        if (Yii::$app->request->isPost) {
            $model->filepdf = UploadedFile::getInstance($model, 'filepdf');
            // Check if there's an existing file and delete it
            if ($model->filepdf && $model->id_suratrepo) {
                if (file_exists(Yii::getAlias('@webroot/surat/internal/pdf/' . $model->id_suratrepo . '.pdf'))) {
                    unlink(Yii::getAlias('@webroot/surat/internal/pdf/') . $model->id_suratrepo . '.pdf');
                }
            }
            if ($model->upload()) {
                Yii::$app->session->setFlash('success', "Upload surat berhasil. Terima kasih.");
                return $this->redirect(['lihatscan', 'id' => $model->id_suratrepo]);
            }
        }
        return $this->render('uploadscan', [
            'model' => $model,
        ]);
    }

    public function actionUploadword($id)
    {
        $model = $this->findModel($id);
        if ($model->owner != Yii::$app->user->identity->username) {
            Yii::$app->session->setFlash('warning', "Upload surat hanya dapat dilakukan oleh pemilik data surat. Terima kasih.");
            return $this->redirect(['index', 'owner' => '', 'year' => '']);
        }
        if (Yii::$app->request->isPost) {
            $model->fileword = UploadedFile::getInstance($model, 'fileword');
            // Check if there's an existing file and delete it
            if ($model->fileword && $model->id_suratrepo) {
                if (file_exists(Yii::getAlias('@webroot/surat/internal/word/' . $model->id_suratrepo . '.' . $model->fileword->extension))) {
                    unlink(Yii::getAlias('@webroot/surat/internal/word/') . $model->id_suratrepo . '.' . $model->fileword->extension);
                }
            }
            if ($model->uploadWord()) {
                Yii::$app->session->setFlash('success', "Upload surat berhasil. Terima kasih.");
                return $this->redirect(['view', 'id' => $model->id_suratrepo]);
            }
        }
        return $this->render('uploadword', [
            'model' => $model,
        ]);
    }

    public function actionView($id)
    {
        $model =  $this->findModel($id);
        $cetak_undangan = '';
        if ($model->is_undangan == 1) // kalau surat ada mark generator surat portal pintar
            $cetak_undangan = SuratrepoController::actionCetakundangan($id);

        // die(var_dump($model));
        if (isset($model->fk_agenda)) {
            $header = LaporanController::findHeader($model->fk_agenda);
            $waktutampil = LaporanController::findWaktutampil($model->fk_agenda);
        } else {
            $header = '';
            $waktutampil = '';
        }
        include_once('_librarycetaksuratnew.php');
        $fileName = Yii::$app->request->hostInfo . Yii::$app->request->baseUrl . Yii::getAlias("@images/bps.png");
        $data = LaporanController::curl_get_file_contents($fileName);
        $base64 = 'data:image/png;base64,' . base64_encode($data);
        $waktutampil = '';
        $formatter = Yii::$app->formatter;
        $formatter->locale = 'id-ID'; // set the locale to Indonesian
        $timezone = new \DateTimeZone('Asia/Jakarta'); // create a timezone object for WIB
        $waktutampil = new \DateTime($model->tanggal_suratrepo, new \DateTimeZone('UTC')); // create a datetime object for waktumulai with UTC timezone
        $waktutampil->setTimeZone($timezone); // set the timezone to WIB
        $waktutampil = $formatter->asDatetime($waktutampil, 'd MMMM Y'); // format the waktumulai datetime value
        // Ambil daftar KEPADA
        $names = explode(', ', $model->penerima_suratrepo);
        $listItems = '';
        foreach ($names as $key => $name) {
            $listItems .= '<li>' .  ' ' . $name . '</li>';
        }
        if (Yii::$app->request->isAjax) {
            return $this->renderAjax('view', [
                'model' => $this->findModel($id),
                'header' => $header,
                'waktutampil' => $waktutampil,
                'base64Pdf' => $cetak_undangan
            ]);
        } else {
            return $this->render('view', [
                'model' => $this->findModel($id),
                'header' => $header,
                'waktutampil' => $waktutampil,
                'base64Pdf' => $cetak_undangan
            ]);
        }
    }
}
