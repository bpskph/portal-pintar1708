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
                                'gettemplate',
                                'lihatscan',
                                'uploadscan',
                                'uploadword',
                                'gettemplatelampiran',
                                'cetaklampiran',
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
    /**
     * Updates an existing Suratrepo model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id_suratrepo Id Suratrepo
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
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
            if (($model->lampiran == '') || ($model->lampiran == '-') || ($model->lampiran == null)) {
                $model->isi_lampiran = null;
                $model->isi_lampiran_orientation = 0;
            }
            $model->timestamp_suratrepo_lastupdate = date('Y-m-d H:i:s');
            // die($_POST['Suratrepo']['isi_suratrepo']);
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
    public function actionCetaksurat($id)
    {
        $model = $this->findModel($id);
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
        switch ($jenis) {
            case 0: // biasa
                $kop = '
                    <table width="500" border="0" bordercolor="33FFFF" align="center" cellpadding="3" cellspacing="00" style="margin-top: -50px;">
                        <tr>
                            <td height="40" colspan="0" width="10" align="left"><img src="data:image/png;base64,' . Yii::$app->params['imagebase64'] . '" height="60" width="82" /> 
                            </td>
                            <td height="40" vertical-align="middle"><h4 style="color: #007bff; margin-left: 6px"><i>BADAN PUSAT STATISTIK<br/>KABUPATEN BENGKULU SELATAN</h4></i></td>
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
                                    ' . $model->ttd_by_jabatan . '
                                    <br />
                                    <br />
                                    <br />
                                    <br />
                                    <b>' . $model->ttd_by . '</b>
                                <center>
                            </td>
                        </tr>
                    </table>                    
                    ';
                break;
            case 1: // nota dinas
                $kop = '
                <table width="500" border="0" bordercolor="33FFFF" align="center" cellpadding="3" cellspacing="00">
                    <tr style="">
                        <h4 style="text-align: center;margin-top: -20px;">BADAN PUSAT STATISTIK<br/>KABUPATEN BENGKULU SELATAN</h4> 
                    </tr>
                    <tr style="">
                        <h4 style="text-align: center; text-decoration: underline">N O T A  D I N A S</h4> 
                    </tr>
                    <tr style="">
                        <p style="text-align: center; margin-top:-20px">Nomor : ' . $model->nomor_suratrepo . '</p> 
                    </tr>
                </table>
                <br/>
                <br/>
                <table width="500" border="0" bordercolor="33FFFF" align="center" cellpadding="3" cellspacing="00">                    
                    <div class="row">
                        <div class="col-sm-12 d-flex">
                            <div class="table-responsive">
                                <table class="table table-sm align-self-end">
                                    <tbody valign="top">
                                        <tr>
                                            <td width="75">Yth </td>
                                            <td width="8">: </td>
                                            <td>' . $model->penerima_suratrepo . '</td>
                                        </tr>
                                        <tr>
                                            <td>D a r i </td>
                                            <td>: </td>
                                            <td>' . $model->ttd_by_jabatan . '</td>
                                        </tr>
                                        <tr>
                                            <td>H a l </td>
                                            <td>: </td>
                                            <td>' . $model->perihal_suratrepo . '</td>
                                        </tr>
                                        <tr>
                                            <td>Tanggal </td>
                                            <td>: </td>
                                            <td>' . $waktutampil  . '</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    </tr>
                </table>
                <hr style="border-top: 0.01px solid black;"/>
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
                    </table>
                    ';
                break;
            case 2: //keterangan
                $kop = '
                    <table width="500" border="0" bordercolor="33FFFF" align="center" cellpadding="3" cellspacing="00" style="margin-top: -50px;">
                        <tr>
                            <td height="40" colspan="0" width="10" align="left"><img src="data:image/png;base64,' . Yii::$app->params['imagebase64'] . '" height="60" width="82" /> 
                            </td>
                            <td height="40" vertical-align="middle"><h4 style="margin-left: 6px"><i>BADAN PUSAT STATISTIK<br/>KABUPATEN BENGKULU SELATAN</h4></i></td>
                            <td height="40" colspan="0" align="right"><br>
                            </td>
                        </tr>
                    </table>
                    <table width="500" border="0" bordercolor="33FFFF" align="center" cellpadding="3" cellspacing="00">                    
                        <tr style="">
                            <h4 style="text-align: center; text-decoration: underline">SURAT KETERANGAN</h4> 
                        </tr>
                        <tr style="">
                            <p style="text-align: center; margin-top:-20px">Nomor : ' . $model->nomor_suratrepo . '</p> 
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
                                    ' . $model->ttd_by_jabatan . '
                                    <br />
                                    <br />
                                    <br />
                                    <br />
                                    <b>' . $model->ttd_by . '</b>
                                <center>
                            </td>
                        </tr>
                    </table>
                    ';
                break;
            case 3: //bast
                $kop = '
                <table width="500" border="0" bordercolor="33FFFF" align="center" cellpadding="3" cellspacing="00" style="margin-top: -50px;">
                    <tr>
                        <td height="40" colspan="0" width="10" align="left"><img src="data:image/png;base64,' . Yii::$app->params['imagebase64'] . '" height="60" width="82" /> 
                        </td>
                        <td height="40" vertical-align="middle"><h4 style="margin-left: 6px"><i>BADAN PUSAT STATISTIK<br/>KABUPATEN BENGKULU SELATAN</h4></i></td>
                        <td height="40" colspan="0" align="right"><br>
                        </td>
                    </tr>
                </table>
                <table width="500" border="0" bordercolor="33FFFF" align="center" cellpadding="3" cellspacing="00">                    
                    <tr style="">
                        <h4 style="text-align: center; text-decoration: underline">BERITA ACARA SERAH TERIMA</h4> 
                    </tr>
                    <tr style="">
                        <p style="text-align: center; margin-top:-20px">Nomor : ' . $model->nomor_suratrepo . '</p> 
                    </tr>
                </table>
                <br/>
                    ';
                $kop2 =
                    '
                    <table width="500" border="0" bordercolor="33FFFF" align="center" cellpadding="0" cellspacing="0">
                        <tr>                            
                            <td>
                                <center>
                                    <b>PIHAK PERTAMA</b>                                                                      
                                    <br />
                                    <br />
                                    <br />
                                    <br />
                                    ' . $model->pihakpertamae->nama . '
                                    <br/>
                                    NIP. ' . $model->pihakpertamae->nipbaru . '
                                <center>
                            </td>
                            <td>
                                <center>
                                    <b>PIHAK PERTAMA</b>                                                                      
                                    <br />
                                    <br />
                                    <br />
                                    <br />
                                    ' . $model->pihakkeduae->nama . '
                                    <br/>
                                    NIP. ' . $model->pihakkeduae->nipbaru . '
                                <center>
                            </td>                            
                        </tr>
                        <tr>                                                   
                            <td colspan="2">
                                <br/>
                                <br/>
                                <center>
                                    Mengetahui, <br/>                                                                   
                                    Kepala BPS Kabupaten Bengkulu Selatan
                                    <br />
                                    Selaku Kuasa Pengguna Barang
                                    <br />
                                    <br />
                                    <br />
                                    <br />
                                    Ir. Win Rizal, M.E.
                                    <br/>
                                    NIP. 196608251988021001   
                                <center>
                            </td>
                        </tr>
                    </table>
                    ';
                break;
            default:
                $kop = '';
                $kop2 = '';
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
                            <span  style="text-align: justify">' . $model->isi_suratrepo . '</span>
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
                        <center>Jalan Affan Bachsin No.108A RT.07 Pasar Baru Kota Manna 38512 
                            <br>Telp./Fax.: 0739-21048, E-mail: bps1701@bps.go.id
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
        ob_end_clean();
        $dompdf->stream($model->perihal_suratrepo . ".pdf", array("Attachment" => 0));
    }
    public function actionGettemplate($id, $action, $surat)
    {
        if ($action == 'create') {
            $template = '';
            switch ($id) {
                case 0: // surat biasa
                    $template = '
                    <p style="text-indent:.5in; text-align: justify">Dalam rangka peningkatan pemahaman Reformasi Birokrasi dalam tim sekretariat RB BPS Kabupaten Bengkulu Selatan, bersama ini  mengundang Bapak/Ibu untuk hadir pada:</p>
                    <p>Hari/Tanggal&nbsp; &nbsp; &nbsp; &nbsp;: Jumat/2 Februari 2023</p>
                    <p>Waktu&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;: 14.00 WIB s.d. selesai</p>
                    <p>Agenda&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;: Review Pilar dan Rencana Kegiatan Bulanan</p>
                    <p>Tempat &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; : Ruang Agro BPS Kabupaten Bengkulu Selatan</p>
                    <p style="text-indent:.5in; text-align: justify">Demikian disampaikan, atas perhatian diucapkan terima kasih.</p>
                    <br/> 
                    ';
                    break;
                case 1: //nota dinas
                    $template = '
                    <p style="text-indent:.5in; text-align: justify">Sehubungan pelaksananaan lapangan Survei Pariwisata Tahun 2023, maka dimohon untuk dapat dilakukan pencetakan Buku Pedoman dan Kuesioner yang dapat didownload pada link <a href="https://s.id/par23">https://s.id/par23</a>, dibebankan pada mata anggaran persediaan kegiatan Survei Keuangan, TI dan pariwisata (2908.BMA.004.051.521811) dengan rincian sebagai berikut.</p>
                    <p style="text-indent:.5in; text-align: justify">Selanjutnya agar dapat didistribusikan kepada BPS Kabupaten dan Kota se-Kabupaten Bengkulu Selatan.  Demikian disampaikan atas perhatian dan Kerjasamanya diucapkan terima kasih.</p>
                    ';
                    break;
                case 2: // surat keterangan
                    $template = '
                    <p>Saya yang bertanda tangan di bawah ini,</span></p>
                    <br/>
                    <p>Nama&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&nbsp;:&nbsp; &nbsp;&nbsp;Ir. Win Rizal, M.E.</p>
                    <p>NIP&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;:&nbsp; &nbsp;&nbsp;196608251988021001</p>
                    <p>Jabatan&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;:&nbsp; &nbsp;&nbsp;Kepala BPS Kabupaten Bengkulu Selatan selaku Kuasa Pengguna Barang</p>
                    <p>
                    dengan ini menerangkan bahwa di BPS Kabupaten Bengkulu Selatan terdapat Barang Milik Negara (BMN) yang diaktifkan kembali penggunaannya. Dengan rincian sebagai berikut:
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
                    break;
                case 3: //bast
                    $template = '
                    <p style="text-align:justify;">Pada hari ini, Senin tanggal Dua Puluh Sembilan bulan Mei tahun dua ribu dua puluh tiga
                        (26-05-2023) bertempat di Badan Pusat Statistik Kabupaten Bengkulu Selatan, berdasarkan Risalah Lelang Nomor: 140/18/2023
                        tanggal 23 Mei 2023, kami yang bertanda tangan di bawah ini:
                    </p>
                    <br />
                    <table style="border-collapse:collapse;border: none;" width="100%">
                        <colgroup>
                            <col width="34" />
                            <col width="200" />
                            <col width="42" />
                            <col width="276" />
                        </colgroup>
                        <tbody>
                            <tr>
                                <td>
                                    I
                                </td>
                                <td>
                                    Nama
                                </td>
                                <td>
                                    :
                                </td>
                                <td>
                                    Rey Ronald Purba, S.Stat.
                                </td>
                            </tr>
                            <tr>
                                <td>
                                </td>
                                <td>
                                    NIP
                                </td>
                                <td>
                                    :
                                </td>
                                <td>
                                    19870518200921001
                                </td>
                            </tr>
                            <tr>
                                <td>
                                </td>
                                <td>
                                    Jabatan
                                </td>
                                <td>
                                    :
                                </td>
                                <td>
                                    Pejabat Penjual Lelang BMN Milik BPS Kabupaten Bengkulu Selatan
                                </td>
                            </tr>
                            <tr>
                                <td>
                                </td>
                                <td colspan="3">
                                    Selanjutnya disebut <b>PIHAK PERTAMA</b>
                                </td>
                                <td>
                                </td>
                                <td>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    II
                                </td>
                                <td>
                                    Nama
                                </td>
                                <td>
                                    :
                                </td>
                                <td>
                                    Hendarto, S.E.
                                </td>
                            </tr>
                            <tr>
                                <td>
                                </td>
                                <td>
                                    NIK
                                </td>
                                <td>
                                    :
                                </td>
                                <td>
                                    1771060407830002
                                </td>
                            </tr>
                            <tr>
                                <td>
                                </td>
                                <td>
                                    Pekerjaan
                                </td>
                                <td>
                                    :
                                </td>
                                <td>
                                    Pegawai Negeri Sipil (PNS)
                                </td>
                            </tr>
                            <tr>
                                <td>
                                </td>
                                <td>
                                    Alamat
                                </td>
                                <td>
                                    :
                                </td>
                                <td>
                                    Preumahan Raflesia. RT 14/05, Tempel Rejo, Curup Selatan.
                                </td>
                            </tr>
                            <tr>
                                <td>
                                </td>
                                <td colspan="3">
                                    Selanjutnya disebut <b>PIHAK KEDUA</b>
                                </td>
                                <td>
                                </td>
                                <td>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <p>Dengan ketentuan bahwa:</p>
                    <br/>
                    <ol>
                        <li>
                            <b>PIHAK PERTAMA</b> telah menyerahkan kepada <b>PIHAK KEDUA</b> barang hasil penjualan lelang 1 (satu) Unit
                            Terminal.
                        </li>
                        <li>
                            <b>PIHAK KEDUA</b> telah menerima barang hasil lelang dari <b>PIHAK PERTAMA</b> dengan rincian sebagai berikut:
                        </li>
                        <table style="border-collapse:collapse;border: none;" width="100%">
                            <tbody valign="top">
                                <tr>
                                    <td width="5%" style="border:solid windowtext 1.0pt; text-align: center">No </td>
                                    <td width="35%" style="border:solid windowtext 1.0pt;">Nama Barang</td>
                                    <td width="30%" style="border:solid windowtext 1.0pt;">Jumlah (Unit)</td>
                                    <td style="border:solid windowtext 1.0pt;">Harga Lelang (Rp)</td>
                                </tr>
                                <tr>
                                    <td width="5%" style="border:solid windowtext 1.0pt; text-align: center">1. </td>
                                    <td width="35%" style="border:solid windowtext 1.0pt;">Terminal</td>
                                    <td width="35%" style="border:solid windowtext 1.0pt;">1</td>
                                    <td style="border:solid windowtext 1.0pt;">319.250</td>
                                </tr>
                            </tbody>
                        </table>
                    </ol>
                    <p style="text-align: justify; text-indent: 0.5in">Demikian Berita Acara Serah Terima ini dibuat dengan sebenarnya sesuai dengan kesepakatan bersama, adapaun barang
                        tersebut dalam keadaan sesuai saat pelelangan. Setelah penandatanganan Berita Acara ini, maka barang tersebut
                        menjadi milih <b>PIHAK KEDUA</b>.
                    ';
                    break;
                case 4:
                    $template = '';
                    break;
                default:
                    $template = $template;
            }
            return $template;
        } elseif ($action = 'update') {
            $isisurat = Suratrepo::findOne(['id_suratrepo' => $surat]);
            return $isisurat->isi_suratrepo;
        }
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
        if (count($names) > 1)
            $autofillString = '<ol style="margin-top: 0px">' . $listItems . '</ol>';
        else
            $autofillString = '<p style="text-indent: 0.25in">' . $names[0] . '</p>';
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
                            <td height="40" vertical-align="middle"><h4 style="color: #007bff; margin-left: 6px; font-family: Tahoma, sans-serif !important;font-size: 18.7px; font-weight: bold;"><i>BADAN PUSAT STATISTIK<br/>KABUPATEN BENGKULU SELATAN</h4></i></td>
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
                        </tr>
                    </table>
                    <table width="500" border="0" bordercolor="33FFFF" align="center" cellpadding="0" cellspacing="0">
                        <tr>
                            <span style="font-size: 15px">
                            <br/>
                            <span class="tulisan">Yang Terhormat : <br/>
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
                                    ' . $model->ttd_by_jabatan . '
                                    <br />
                                    <br />
                                    <br />
                                    <br />
                                    <b>' . $model->ttd_by . '</b>
                                <center>
                            </td>
                        </tr>
                    </table>                    
                    ';
                break;
            case 1: // nota dinas
                $kop = '
                <table width="100%" border="0" bordercolor="33FFFF" align="center" cellpadding="3" cellspacing="00">
                    <tr style="">
                        <h4 style="text-align: center;margin-top: -20px;">BADAN PUSAT STATISTIK<br/>KABUPATEN BENGKULU SELATAN</h4> 
                    </tr>
                    <tr style="">
                        <h4 style="text-align: center; text-decoration: underline">N O T A  D I N A S</h4> 
                    </tr>
                    <tr style="">
                        <p style="text-align: center; margin-top:-10px">Nomor : ' . $model->nomor_suratrepo . '</p> 
                    </tr>
                </table>
                <br/>
                <br/>
                <table width="100%" border="0" bordercolor="33FFFF" align="center" cellpadding="3" cellspacing="00">                 
                                <table class="table table-sm align-self-end">
                                    <tbody valign="top">
                                        <tr>
                                            <td width="75">Yth </td>
                                            <td width="8">: </td>
                                            <td>' . $model->penerima_suratrepo . '</td>
                                        </tr>
                                        <tr>
                                            <td>D a r i </td>
                                            <td>: </td>
                                            <td>' . $model->ttd_by_jabatan . '</td>
                                        </tr>
                                        <tr>
                                            <td>H a l </td>
                                            <td>: </td>
                                            <td>' . $model->perihal_suratrepo . '</td>
                                        </tr>
                                        <tr>
                                            <td>Tanggal </td>
                                            <td>: </td>
                                            <td>' . $waktutampil  . '</td>
                                        </tr>
                                    </tbody>
                                </table>                            
                    </tr>
                </table>
                <hr style="border-top: 0.01px solid black;"/>
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
                                    ' . $model->ttd_by_jabatan . '
                                    <br />
                                    <br />
                                    <br />
                                    <br />
                                    <b>' . $model->ttd_by . '</b>
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
                            <td height="40" vertical-align="middle"><h4 style="margin-left: 6px"><i>BADAN PUSAT STATISTIK<br/>KABUPATEN BENGKULU SELATAN</h4></i></td>
                            <td height="40" colspan="0" align="right"><br>
                            </td>
                        </tr>
                    </table>
                    <table width="100%" border="0" bordercolor="33FFFF" align="center" cellpadding="3" cellspacing="00">                    
                        <tr style="">
                            <h4 style="text-align: center; text-decoration: underline">SURAT KETERANGAN</h4> 
                        </tr>
                        <tr style="">
                            <p style="text-align: center; margin-top:-10px">Nomor : ' . $model->nomor_suratrepo . '</p> 
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
                                    ' . $model->ttd_by_jabatan . '
                                    <br />
                                    <br />
                                    <br />
                                    <br />
                                    <b>' . $model->ttd_by . '</b>
                                <center>
                            </td>
                        </tr>
                    </table>
                    ';
                break;
            case 3: //bast
                $kop = '
                <table width="100%" border="0" bordercolor="33FFFF" align="center" cellpadding="3" cellspacing="00" style="margin-top: -50px;">
                    <tr>
                        <td height="40" colspan="0" width="10" align="left"><img src="data:image/png;base64,' . Yii::$app->params['imagebase64'] . '" height="60" width="82" /> 
                        </td>
                        <td height="40" vertical-align="middle"><h4 style="margin-left: 6px"><i>BADAN PUSAT STATISTIK<br/>KABUPATEN BENGKULU SELATAN</h4></i></td>
                        <td height="40" colspan="0" align="right"><br>
                        </td>
                    </tr>
                </table>
                <table width="100%" border="0" bordercolor="33FFFF" align="center" cellpadding="3" cellspacing="00">                    
                    <tr style="">
                        <h4 style="text-align: center; text-decoration: underline">BERITA ACARA SERAH TERIMA</h4> 
                    </tr>
                    <tr style="">
                        <p style="text-align: center; margin-top:-10px">Nomor : ' . $model->nomor_suratrepo . '</p> 
                    </tr>
                </table>
                <br/>
                    ';
                $kop2 =
                    '
                    <table width="100%" border="0" bordercolor="33FFFF" align="center" cellpadding="0" cellspacing="0">
                        <tr>                            
                            <td>
                                <center>
                                    <b>PIHAK PERTAMA</b>                                                                      
                                    <br />
                                    <br />
                                    <br />
                                    <br />
                                    ' . $model->pihakpertamae->nama . '
                                    <br/>
                                    NIP. ' . $model->pihakpertamae->nipbaru . '
                                <center>
                            </td>
                            <td>
                                <center>
                                    <b>PIHAK PERTAMA</b>                                                                      
                                    <br />
                                    <br />
                                    <br />
                                    <br />
                                    ' . $model->pihakkeduae->nama . '
                                    <br/>
                                    NIP. ' . $model->pihakkeduae->nipbaru . '
                                <center>
                            </td>                            
                        </tr>
                        <tr>                                                   
                            <td colspan="2">
                                <br/>
                                <br/>
                                <center>
                                    Mengetahui, <br/>                                                                   
                                    Kepala BPS Kabupaten Bengkulu Selatan
                                    <br />
                                    Selaku Kuasa Pengguna Barang
                                    <br />
                                    <br />
                                    <br />
                                    <br />
                                    Ir. Win Rizal, M.E.
                                    <br/>
                                    NIP. 196608251988021001   
                                <center>
                            </td>
                        </tr>
                    </table>
                    ';
                break;
            default:
                $kop = '';
                $kop2 = '';
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
                                <span  style="text-align: justify">' . $model->isi_suratrepo . '</span>
                            </tr>
                        </table>
                        <br/>
                        <br/>
                        ' . $kop2 . '
                        <br/>
                        ' . ($model->tembusan != null ? '<p style="margin-bottom: 0px">Tembusan: </p>' . $autofillString2 : '') . '                    
                        <div style="font-size:10px" class="tulisan">                    
                                <center>Jalan Affan Bachsin No.108A RT.07 Pasar Baru Kota Manna 38512 
                                    <br>Telp./Fax.: 0739-21048, E-mail: bps1701@bps.go.id
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
                'html' => $html,
                'base64Pdf' => $cetak_undangan
            ]);
        } else {
            return $this->render('view', [
                'model' => $this->findModel($id),
                'header' => $header,
                'waktutampil' => $waktutampil,
                // 'html' => $html,
                'base64Pdf' => $cetak_undangan
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
            $isisurat = Suratrepo::findOne(['id_suratrepo' => $surat]);
            if (isset($isisurat))
                return $isisurat->isi_lampiran;
            else
                return '';
        }
    }
    public function actionCetaklampiran($id)
    {
        $model = $this->findModel($id);
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
        ob_end_clean();
        $dompdf->stream("Lampiran - " . $model->perihal_suratrepo . ".pdf", array("Attachment" => 0));
    }
}
