<?php

namespace app\controllers;

use app\models\Agenda;
use app\models\Laporan;
use app\models\Pengguna;
use app\models\Projectmember;
use Yii;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use Dompdf\DOMPDF; //untuk di local
// use Dompdf\Dompdf; //untuk di webapps
use Dompdf\Options;
use yii\web\UploadedFile;

class LaporanController extends BaseController
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
                            'actions' => ['error', 'view'],
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
                            'actions' => ['create', 'update', 'setujui', 'cetaklaporan'], // add all actions to take guest to login page
                            'allow' => true,
                            'roles' => ['@'],
                        ],
                    ],
                ],
            ]
        );
    }
    public function actionView($id)
    {
        $header = $this->findHeader($id);
        $waktutampil = $this->findWaktutampil($id);
        $dataagenda = Agenda::findOne(['id_agenda' => $id]);
        if (Yii::$app->request->isAjax) {
            return $this->renderAjax('view', [
                'model' => $this->findModel($id),
                'header' => $header,
                'waktutampil' => $waktutampil,
                'dataagenda' => $dataagenda,
            ]);
        } else {
            return $this->render('view', [
                'model' => $this->findModel($id),
                'header' => $header,
                'waktutampil' => $waktutampil,
                'dataagenda' => $dataagenda,
            ]);
        }
    }
    public function actionCreate($agenda)
    {
        $model = new Laporan();
        $dataagenda = Agenda::findOne(['id_agenda' => $agenda]);
        $people = Projectmember::find()
            ->where(['fk_project' => $dataagenda->pelaksana])
            ->andWhere(['NOT', ['member_status' => 0]])
            ->asArray()
            ->all();
        $member = [];
        foreach ($people as $value) {
            array_push($member, $value['pegawai']);
        }
        if (!in_array(Yii::$app->user->identity->username, $member) &&  $dataagenda->reporter !== Yii::$app->user->identity->username) {
            Yii::$app->session->setFlash('warning', "Laporan hanya dapat diinput oleh operator project pelaksana kegiatan. Terima kasih.");
            return $this->redirect(['agenda/index', 'owner' => '', 'year' => '', 'nopage' => 0]);
        }
        if ($dataagenda->progress != 1) {
            Yii::$app->session->setFlash('warning', "Mohon tandai agenda telah selesai terlebih dahulu. Terima kasih.");
            return $this->redirect(['agenda/index', 'owner' => '', 'year' => '', 'nopage' => 0]);
        }
        $datalaporan = Laporan::findOne(['id_laporan' => $agenda]);
        if (!empty($datalaporan)) {
            Yii::$app->session->setFlash('warning', "Sudah ada laporan untuk agenda ini. Terima kasih.");
            return $this->redirect(['agenda/index', 'owner' => '', 'year' => '', 'nopage' => 0]);
        }
        $header = $this->findHeader($agenda);
        $waktutampil = $this->findWaktutampil($agenda);
        if ($this->request->isPost) {
            $model->id_laporan = $agenda;
            $model->laporan = null;
            $model->dokumentasi = $_POST['Laporan']['dokumentasi'];
            $model->filepdf = UploadedFile::getInstance($model, 'filepdf');
            // Check if there's an existing file and delete it
            if ($model->filepdf && $model->id_laporan) {
                if (file_exists(Yii::getAlias('@webroot/laporans/' . $model->id_laporan . '.pdf'))) {
                    unlink(Yii::getAlias('@webroot/laporans/') . $model->id_laporan . '.pdf');
                }
            }
            if ($model->save() && $model->upload()) {
                Yii::$app->session->setFlash('success', "Laporan Agenda berhasil ditambahkan. Terima kasih.");
                return $this->redirect(['view', 'id' => $model->id_laporan]);
            }
        }
        // } else {
        //     $model->loadDefaultValues();
        // }
        return $this->render('create', [
            'model' => $model,
            'dataagenda' => $dataagenda,
            'header' => $header,
            'waktutampil' => $waktutampil
        ]);
    }
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $dataagenda = Agenda::findOne(['id_agenda' => $id]);
        $people = Projectmember::find()
            ->where(['fk_project' => $dataagenda->pelaksana])
            ->andWhere(['NOT', ['member_status' => 0]])
            ->asArray()
            ->all();
        $member = [];
        foreach ($people as $value) {
            array_push($member, $value['pegawai']);
        }
        if (Yii::$app->user->identity->username !== $dataagenda->reporter) {
            Yii::$app->session->setFlash('warning', "Laporan hanya dapat diinput oleh operator project pelaksana kegiatan. Terima kasih.");
            return $this->redirect(['agenda/index', 'owner' => '', 'year' => '', 'nopage' => 0]);
        }
        $header = $this->findHeader($id);
        $waktutampil = $this->findWaktutampil($id);
        date_default_timezone_set('Asia/Jakarta');
        $model->timestamp_laporan_lastupdate = date('Y-m-d H:i:s');
        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', "Laporan Agenda berhasil dimutakhirkan. Terima kasih.");
            return $this->redirect(['view', 'id' => $model->id_laporan]);
        }
        return $this->render('update', [
            'model' => $model,
            'dataagenda' => $dataagenda,
            'header' => $header,
            'waktutampil' => $waktutampil
        ]);
    }
    protected function findModel($id_laporan)
    {
        if (($model = Laporan::findOne(['id_laporan' => $id_laporan])) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('The requested page does not exist.');
    }
    public static function findWaktutampil($id_laporan)
    {
        $dataagenda = Agenda::findOne(['id_agenda' => $id_laporan]);
        $waktutampil = '';
        if ($dataagenda->waktumulai_tunda != NULL && $dataagenda->waktuselesai_tunda) {
            $formatter = Yii::$app->formatter;
            $formatter->locale = 'id-ID'; // set the locale to Indonesian
            $timezone = new \DateTimeZone('Asia/Jakarta'); // create a timezone object for WIB
            $waktumulai_tunda = new \DateTime($dataagenda->waktumulai_tunda, new \DateTimeZone('UTC')); // create a datetime object for waktumulai_tunda with UTC timezone
            $waktumulai_tunda->setTimeZone($timezone); // set the timezone to WIB
            $waktumulai_tundaFormatted = $formatter->asDatetime($waktumulai_tunda, 'd MMMM Y, H:mm'); // format the waktumulai_tunda datetime value
            $waktuselesai_tunda = new \DateTime($dataagenda->waktuselesai_tunda, new \DateTimeZone('UTC')); // create a datetime object for waktuselesai_tunda with UTC timezone
            $waktuselesai_tunda->setTimeZone($timezone); // set the timezone to WIB
            $waktuselesai_tundaFormatted = $formatter->asDatetime($waktuselesai_tunda, 'H:mm'); // format the waktuselesai_tunda time value only
            if ($waktumulai_tunda->format('Y-m-d') === $waktuselesai_tunda->format('Y-m-d')) {
                // if waktumulai_tunda and waktuselesai_tunda are on the same day, format the time range differently
                $waktumulai_tundaFormatted = $formatter->asDatetime($waktumulai_tunda, 'd MMMM Y, H:mm'); // format the waktumulai_tunda datetime value with the year and time
                $waktutampil =  $waktumulai_tundaFormatted . ' - ' . $waktuselesai_tundaFormatted . ' WIB'; // concatenate the formatted dates
            } else {
                // if waktumulai_tunda and waktuselesai_tunda are on different days, format the date range normally
                $waktuselesai_tundaFormatted = $formatter->asDatetime($waktuselesai_tunda, 'd MMMM Y, H:mm'); // format the waktuselesai_tunda datetime value
                $waktutampil =  $waktumulai_tundaFormatted . ' WIB s.d ' . $waktuselesai_tundaFormatted . ' WIB'; // concatenate the formatted dates
            }
        } else {
            $formatter = Yii::$app->formatter;
            $formatter->locale = 'id-ID'; // set the locale to Indonesian
            $timezone = new \DateTimeZone('Asia/Jakarta'); // create a timezone object for WIB
            $waktumulai = new \DateTime($dataagenda->waktumulai, new \DateTimeZone('UTC')); // create a datetime object for waktumulai with UTC timezone
            $waktumulai->setTimeZone($timezone); // set the timezone to WIB
            $waktumulaiFormatted = $formatter->asDatetime($waktumulai, 'd MMMM Y, H:mm'); // format the waktumulai datetime value
            $waktuselesai = new \DateTime($dataagenda->waktuselesai, new \DateTimeZone('UTC')); // create a datetime object for waktuselesai with UTC timezone
            $waktuselesai->setTimeZone($timezone); // set the timezone to WIB
            $waktuselesaiFormatted = $formatter->asDatetime($waktuselesai, 'H:mm'); // format the waktuselesai time value only
            if ($waktumulai->format('Y-m-d') === $waktuselesai->format('Y-m-d')) {
                // if waktumulai and waktuselesai are on the same day, format the time range differently
                $waktumulaiFormatted = $formatter->asDatetime($waktumulai, 'd MMMM Y, H:mm'); // format the waktumulai datetime value with the year and time
                $waktutampil =  $waktumulaiFormatted . ' - ' . $waktuselesaiFormatted . ' WIB'; // concatenate the formatted dates
            } else {
                // if waktumulai and waktuselesai are on different days, format the date range normally
                $waktuselesaiFormatted = $formatter->asDatetime($waktuselesai, 'd MMMM Y, H:mm'); // format the waktuselesai datetime value
                $waktutampil =  $waktumulaiFormatted . ' WIB s.d ' . $waktuselesaiFormatted . ' WIB'; // concatenate the formatted dates
            }
        }
        return $waktutampil;
    }
    public static function findHeader($id_laporan)
    {
        $dataagenda = Agenda::findOne(['id_agenda' => $id_laporan]);
        $waktutampil = LaporanController::findWaktutampil($id_laporan);
        $header = '
            <div class="row">
                <div class="col-sm-12">
                    <div class="table-responsive">
                        <table class="table table-sm align-self-end ' . ((!Yii::$app->user->isGuest && Yii::$app->user->identity->theme == 0) ? '' : 'table-dark') . '">
                            <tbody>
                                <tr>
                                    <td class="col-sm-2">Nama Kegiatan</td>
                                    <td>: ' . $dataagenda->kegiatan . '</td>
                                </tr>                            
                                <tr>
                                    <td>Waktu</td>
                                    <td>: ' . $waktutampil . '</td>
                                </tr>
                                <tr>
                                    <td>Pemimpin Rapat/Kegiatan</td>
                                    <td>: ' . $dataagenda->pemimpine->nama . '</td>
                                </tr>
                                <tr>
                                    <td>Reporter/Notulis</td>
                                    <td>: ' . $dataagenda->reportere->nama . '</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            ';
        return $header;
    }
    public function actionSetujui($id)
    {
        $model = $this->findModel($id);
        date_default_timezone_set('Asia/Jakarta');
        $affected_rows = Laporan::updateAll(['approval' => 1, 'timestamp_laporan_lastupdate' => date('Y-m-d H:i:s')], 'id_laporan = "' . $id . '"');
        if ($affected_rows == 0) {
            Yii::$app->session->setFlash('warning', "Gagal. Mohon hubungi Admin.");
            return $this->redirect(['view', 'id' => $model->id_laporan]);
        } else {
            $dataagenda = Agenda::findOne(['id_agenda' => $id]);
            $approver = \app\models\Pengguna::findOne(['username' => $dataagenda->pemimpin]);
            \app\models\Notification::createNotification($dataagenda->reporter, 'Laporan Kegiatan Anda <strong>' . $dataagenda->kegiatan . '</strong> sudah disetujui oleh <strong>' . $approver->nama . '</strong>.', Yii::$app->controller->id, $model->id_laporan);
            Yii::$app->session->setFlash('success', "Laporan berhasil disetujui. Terima kasih.");
            return $this->redirect(['view', 'id' => $model->id_laporan]);
        }
    }
    public static function curl_get_file_contents($URL)
    {
        $c = curl_init();
        curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($c, CURLOPT_URL, $URL);
        $contents = curl_exec($c);
        curl_close($c);
        if ($contents) return $contents;
        else return FALSE;
    }
    public function actionCetaklaporan($id)
    {
        //<td height="55" colspan="0" align="center"><img src="' . $base64 . '" width="60" height="50" /><br>
        $model = $this->findModel($id);
        $header = $this->findHeader($id);
        $waktutampil = $this->findWaktutampil($id);
        // die($model);
        $dataagenda = Agenda::findOne(['id_agenda' => $id]);
        // Step 1: Get the list of email addresses from the peserta attribute in the agenda table
        $emailList = explode(', ', $dataagenda->peserta);
        // Step 2: Extract the username (without "@bps.go.id") from each email address
        $usernames = [];
        foreach ($emailList as $email) {
            $username = substr($email, 0, strpos($email, '@'));
            $usernames[] = $username;
        }
        // Step 3: Query the pengguna table for the list of names that correspond to the extracted usernames
        $names = Pengguna::find()
            ->select('nama')
            ->where(['in', 'username', $usernames])
            ->column();
        // Step 4: Convert the list of names to a string in the format that can be used for autofill
        // $autofillString = implode('<br> ', $names);
        $listItems = '';
        foreach ($names as $key => $name) {
            $listItems .= '<li>' .  ' ' . $name . '</li>';
        }
        $autofillString = '<b>Peserta Kegiatan :</b> <ol>' . $listItems . '</ol>';
        // print_r($autofillString);
        // Step 5: Set the content of the editor using the html option
        if (empty($model)) {
            Yii::$app->session->setFlash('warning', "Belum ada laporan untuk agenda ini.");
            return $this->redirect(['index']);
        } else {
            include_once('_librarycetaklaporan.php');
            $fileName = Yii::$app->request->hostInfo . Yii::$app->request->baseUrl . Yii::getAlias("@images/bps.png");
            // $data = file_get_contents($fileName);
            $data = LaporanController::curl_get_file_contents($fileName);
            $base64 = 'data:image/png;base64,' . base64_encode($data);
            $html =
                '<!DOCTYPE html>
                <html>
                <head>
                    ' . $style . '
                    <table width="500" border="0" bordercolor="33FFFF" align="center" cellpadding="3" cellspacing="00">
                        <tr>
                            <td height="55" colspan="0" align="center"><img src="data:image/png;base64,' . Yii::$app->params['imagebase64'] . '" height="50" width="60" /><br>
                                <h4><i>BADAN PUSAT STATISTIK<br/>PROVINSI BENGKULU</h4></i>
                            </td>
                        </tr>
                    </table>
                    <table>
                        <tr>
                        <br>
                        <br>
                        </tr>
                        <tr>
                        ' . $header . '
                        </tr>
                    </table>
                </head>
                <body style="font-size:14px">
                    <table width="500" border="0" bordercolor="33FFFF" align="center" cellpadding="0" cellspacing="0">
                        <tr>
                            ' . $model->laporan . '
                        </tr>
                    </table>
                    <br/>                    
                    ' . $autofillString . '
                    <br/>
                    <table width="500" border="0" bordercolor="33FFFF" align="center" cellpadding="0" cellspacing="0">
                        <tr>
                            <small>
                                <b>
                                ' . (($model->approval == 0) ? '<span style="color: #dc3545">Laporan belum disetujui oleh ketua rapat/kegiatan di Sistem Portal Pintar</span>' : '<span style="color: #28a745">Laporan telah disetujui oleh ketua rapat/kegiatan di Sistem Portal Pintar</span>') . '
                                </b>
                            </small>
                        </tr>
                    </table>
                    <table>
                        <br/>
                        <br/>
                        <br/>
                        <tr>
                            <td width="300"></td>
                            <td></td>
                            <td></td>
                            <td>
                                <center>
                                    <b>Notulis</b>
                                    <br />
                                    <br />
                                    <br />
                                    <br />
                                <center>' . $model->agendae->reportere->nama . '
                            </td>
                        </tr>
                    </table>
                </body>
                <foot style="font-size:10px">
                    <div class="footer">
                        <center>Jalan Adam Malik Km.8 Bengkulu, 38225, Telepon (0736) 349117-118, Kepala (0736) 349116
                            <br>Fax. (0736) 349115, E-mail: bps1700@bps.go.id
                        </center>
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
            $dompdf->stream("Laporan Kegiatan " . $dataagenda->kegiatan . ".pdf", array("Attachment" => 1));
        }
    }
}
