<?php

namespace app\controllers;

use app\models\Agenda;
use app\models\Suratrepo;
use app\models\AgendaSearch;
use app\models\EmailblastForm;
use app\models\Pengguna;
use app\models\Popups;
use app\models\Projectmember;
use app\models\Zooms;
use DateTime;
use Yii;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\Html;
use yii\helpers\Json;

class AgendaController extends BaseController
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
                        'getlistpeserta' => ['POST'], // Restrict the HTTP method for this action to POST
                    ],
                ],
                'access' => [
                    'class' => \yii\filters\AccessControl::className(),
                    'rules' => [
                        [
                            'actions' => ['error', 'view', 'index', 'picker', 'calendar'],
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
                            'actions' => ['create', 'update', 'delete', 'batal', 'selesai', 'rencana', 'tunda', 'emailblast', 'editpeserta', 'getlistpeserta', 'wa_blast'], // add all actions to take guest to login page
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
        if ($action->id === 'getlistpeserta' || $action->id === 'selesai' || $action->id === 'delete' || $action->id = 'batal') {
            $this->enableCsrfValidation = false; // Disable CSRF validation for the action
        }
        return parent::beforeAction($action);
    }

    // Menampilkan agenda dalam bentuk kalender
    public function actionCalendar()
    {
        $todayList = new AgendaSearch();
        $today = $todayList->search(Yii::$app->request->queryParams);
        $today->query->andWhere('waktumulai < DATE(NOW())')
            ->andWhere('waktuselesai > DATE(NOW())');

        /*Untuk Kalender*/
        $events = Agenda::find()
            ->joinWith(['reportere', 'projecte', 'pemimpine'])
            ->andWhere(['deleted' => 0])
            ->all();
        $tasks = [];

        foreach ($events as $eve) {
            $formatter = Yii::$app->formatter;
            $formatter->locale = 'id-ID'; // set the locale to Indonesian
            $timezone = new \DateTimeZone('Asia/Jakarta'); // create a timezone object for WIB
            $waktumulai = new \DateTime($eve->waktumulai, new \DateTimeZone('UTC')); // create a datetime object for waktumulai with UTC timezone
            $waktumulai->setTimeZone($timezone); // set the timezone to WIB
            $waktumulaiFormatted = $formatter->asDatetime($waktumulai, 'd MMMM Y, H:mm'); // format the waktumulai datetime value
            $waktuselesai = new \DateTime($eve->waktuselesai, new \DateTimeZone('UTC')); // create a datetime object for waktuselesai with UTC timezone
            $waktuselesai->setTimeZone($timezone); // set the timezone to WIB
            $waktuselesaiFormatted = $formatter->asDatetime($waktuselesai, 'H:mm'); // format the waktuselesai time value only
            if ($waktumulai->format('Y-m-d') === $waktuselesai->format('Y-m-d')) {
                // if waktumulai and waktuselesai are on the same day, format the time range differently
                $waktumulaiFormatted = $formatter->asDatetime($waktumulai, 'd MMMM Y, H:mm'); // format the waktumulai datetime value with the year and time
                $waktuFormatted = $waktumulaiFormatted . ' - ' . $waktuselesaiFormatted . ' WIB'; // concatenate the formatted dates
            } else {
                // if waktumulai and waktuselesai are on different days, format the date range normally
                $waktuselesaiFormatted = $formatter->asDatetime($waktuselesai, 'd MMMM Y, H:mm'); // format the waktuselesai datetime value
                $waktuFormatted = $waktumulaiFormatted . ' WIB s.d ' . $waktuselesaiFormatted . ' WIB'; // concatenate the formatted dates
            }
            $detail = '<a href="' . Yii::$app->request->baseUrl . '/../bengkulu/portalpintar/agenda/' . $eve->id_agenda . '" target="_blank"><i class="fas fa-eye"></i> Lihat</a>';
            $tasks[] = [
                'id'   => $eve->id_agenda,
                'title'   => $eve->kegiatan,
                'start'   => $eve->waktumulai,
                'end'   => $eve->waktuselesai,
                'extendedProps' => [
                    'project' => $eve->pelaksanae,
                    'leader' => $eve->pemimpine->nama,
                    'reporter' => $eve->reportere->nama,
                    'waktu' => $waktuFormatted,
                    'detail' => $detail,
                ]
            ];
        }

        return $this->render('calendar', [
            'eventsKalender' => $tasks,
            'listdataProviderKalender' => $today,
        ]);
    }
    // Menampilkan menu agenda
    public function actionIndex($owner, $year, $nopage)
    {
        $searchModel = new AgendaSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);
        if ($owner != '')
            $dataProvider->query->andWhere(['reporter' => $owner]);
        if ($year == date("Y"))
            $dataProvider->query->andWhere(['YEAR(waktuselesai)' => date("Y")]);
        elseif ($year != '')
            $dataProvider->query->andWhere(['YEAR(waktuselesai)' => $year]);
        if ($nopage == 1)
            $dataProvider->pagination =  false;

        $popups = Popups::find()->where('deleted = 0')->orderBy('id_popups desc')->all();
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'owner' => $owner,
            'year' => $year,
            'nopage' => $nopage,
            'popups' => $popups
        ]);
    }
    // Melihat rincian suatu agenda berdasarkan id
    public function actionView($id)
    {
        $model =  $this->findModel($id);
        if ($model->deleted == 1) {
            Yii::$app->session->setFlash('warning', "Data agenda ini sudah dihapus.");
            return $this->redirect(['index', 'owner' => '', 'year' => '', 'nopage' => 0]);
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
        $cek = Agenda::find()->select('*')
            ->where(['reporter' => Yii::$app->user->identity->username])
            ->andWhere([
                'or',
                ['>', 'DATEDIFF(NOW(), DATE(waktuselesai))', 7], // agenda direncanakan
                ['>', 'DATEDIFF(NOW(), DATE(waktuselesai_tunda))', 7], // agenda ditunda
            ])
            ->andWhere([
                'or',
                ['progress' => 0], //agenda direncanakan
                ['progress' => 2], //agenda ditunda
            ])
            ->andWhere(['deleted' => 0])
            ->count();
        if ($cek > 0) {
            Yii::$app->session->setFlash('warning', "Maaf. Anda masih memiliki agenda dengan status <span title='Rencana' class='badge bg-primary rounded-pill'><i class='fas fa-plus-square'></i> Rencana</span> dan/atau <span title='Rencana' class='badge bg-danger rounded-pill'><i class='fas fa-strikethrough'></i> Tunda</span>
            <br/>Mohon berikan mark yang sesuai (selesai atau batal).");
            return $this->redirect(['index', 'owner' => '', 'year' => '', 'nopage' => 0]);
        }

        $agendas = Agenda::find()
            ->select('agenda.*, laporan.id_laporan as approval') // Select agenda fields
            ->leftJoin('laporan', 'agenda.id_agenda = laporan.id_laporan') // LEFT JOIN to include all agendas
            ->where(['agenda.reporter' => Yii::$app->user->identity->username]) // Filter by reporter
            ->andWhere(['>', 'agenda.id_agenda', 340]) // Only agendas with id > 340
            ->andWhere([
                'or',
                // Case 1: No laporan exists for the agenda
                [
                    'and',
                    ['laporan.id_laporan' => null], // No paired laporan
                    ['agenda.deleted' => 0], // Not deleted
                    ['agenda.progress' => 1], // Progress = 1
                    ['<', 'DATEDIFF(NOW(), DATE(agenda.timestamp_lastupdate))', 3], // Last update > 3 days ago
                ],
                // Case 2: A laporan exists, but its approval is not yet true
                [
                    'and',
                    ['>', 'agenda.waktumulai', '2024-10-01 00:00:00'],
                    ['>', 'DATEDIFF(NOW(), DATE(agenda.timestamp_lastupdate))', 30], // Last update > 30 days ago
                    ['laporan.approval' => 0], // Laporan approval is false
                    ['agenda.deleted' => 0], // Not deleted
                    ['agenda.progress' => 1], // Progress = 1
                ]
            ])
            ->all();

        if (count($agendas) > 0) {
            // var_dump($agendas);
            $teks = '<ol>';
            foreach ($agendas as $agenda) {
                $kegiatan = $agenda->kegiatan;
                $agendaId = $agenda->id_agenda;

                $formatter = Yii::$app->formatter;
                $formatter->locale = 'id-ID'; // set the locale to Indonesian
                $timezone = new \DateTimeZone('Asia/Jakarta'); // create a timezone object for WIB
                $waktumulai = new \DateTime($agenda->waktumulai, new \DateTimeZone('UTC')); // create a datetime object for waktumulai with UTC timezone
                $waktumulai->setTimeZone($timezone); // set the timezone to WIB
                $waktumulaiFormatted = $formatter->asDatetime($waktumulai, 'd MMMM Y, H:mm'); // format the waktumulai datetime value
                $waktuselesai = new \DateTime($agenda->waktuselesai, new \DateTimeZone('UTC')); // create a datetime object for waktuselesai with UTC timezone
                $waktuselesai->setTimeZone($timezone); // set the timezone to WIB
                $waktuselesaiFormatted = $formatter->asDatetime($waktuselesai, 'H:mm'); // format the waktuselesai time value only
                if ($waktumulai->format('Y-m-d') === $waktuselesai->format('Y-m-d')) {
                    // if waktumulai and waktuselesai are on the same day, format the time range differently
                    $waktumulaiFormatted = $formatter->asDatetime($waktumulai, 'd MMMM Y, H:mm'); // format the waktumulai datetime value with the year and time
                    $waktuFormatted = $waktumulaiFormatted . ' - ' . $waktuselesaiFormatted . ' WIB'; // concatenate the formatted dates
                } else {
                    // if waktumulai and waktuselesai are on different days, format the date range normally
                    $waktuselesaiFormatted = $formatter->asDatetime($waktuselesai, 'd MMMM Y, H:mm'); // format the waktuselesai datetime value
                    $waktuFormatted = $waktumulaiFormatted . ' WIB <br/>s.d ' . $waktuselesaiFormatted . ' WIB'; // concatenate the formatted dates
                }

                if ($agenda->approval != null)
                    $teks .= '<li>' . $kegiatan . ' - ' . $waktuFormatted . ' | ' . Html::a(' <i class="fas fa-eye"></i> LIHAT', ['laporan/' . $agendaId], []) . '</li>';
                else
                    $teks .= '<li>' . $kegiatan . ' - ' . $waktuFormatted . ' | ' . Html::a(' <i class="fas fa-upload"></i> BUAT LAPORAN', ['laporan/create?agenda=' . $agendaId], []) . '</li>';
            }
            $teks .= '</ol>';
            Yii::$app->session->setFlash('warning', "Maaf. Sejak 1 Oktober 2024, ada agenda yang telah selesai lebih dari 3 hari, namun belum diberikan laporan. Atau, agenda yang telah selesai lebih dari 1 bulan, namun laporannya belum disetujui.
                <br/>" . $teks);
            return $this->redirect(['index', 'owner' => '', 'year' => '', 'nopage' => 0]);
        }

        $model = new Agenda();
        $searchModel = new AgendaSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);
        $dataProvider->query
            ->andWhere(['>=', 'waktumulai', date('Y-m-d H:i:s')])
            ->andWhere(['<=', 'waktumulai', date('Y-m-d H:i:s', strtotime('+2 weeks'))])
            ->andWhere(['progress' => '0']);
        $dataProvider->pagination = false;
        if ($this->request->isPost) {
            // die($_POST['Agenda']['pelaksana']);
            $model->load($this->request->post());
            $model->reporter = Yii::$app->user->identity->username;
            $peserta = $model->peserta;
            $cek = implode("@bps.go.id, ", $peserta) . "@bps.go.id";
            $model->peserta = $cek;
            if (str_contains($_POST['Agenda']['waktumulai'], 'WIB')) {
                /* WAKTU SELESAI */
                $datetimeStr = $_POST['Agenda']['waktumulai'];
                $datetime = DateTime::createFromFormat('Y-m-d H:i T', $datetimeStr);
                $formattedDatetime = $datetime->format('Y-m-d H:i:s');
                $model->waktumulai = $formattedDatetime;
            } else {
                $model->waktumulai = date("Y-m-d H:i:s", strtotime($_POST['Agenda']['waktumulai']));
            }
            if (str_contains($_POST['Agenda']['waktuselesai'], 'WIB')) {
                /* WAKTU SELESAI */
                $datetimeStr = $_POST['Agenda']['waktuselesai'];
                $datetime = DateTime::createFromFormat('Y-m-d H:i T', $datetimeStr);
                $formattedDatetime = $datetime->format('Y-m-d H:i:s');
                $model->waktuselesai = $formattedDatetime;
            } else {
                $model->waktuselesai = date("Y-m-d H:i:s", strtotime($_POST['Agenda']['waktuselesai']));
            }
            if ($model->validate()) {
                if ($_POST['Agenda']['pilihpelaksana'] == 1) { //jika pelaksana eksternal
                    if ($_POST['Agenda']['pelaksanatext'] != "") { //pelaksana eksternal ada
                        $model->pelaksana = $_POST['Agenda']['pelaksanatext'];
                    } else {
                        Yii::$app->session->setFlash('warning', "Detail Pelaksana Eksternal harus terisi jika Anda memilih Cakupan Eksternal. Terima kasih.");
                        return $this->render('create', [
                            'model' => $model,
                            'searchModel' => $searchModel,
                            'dataProvider' => $dataProvider,
                        ]);
                    }
                } else {
                    $model->surat_lanjutan = $_POST['Agenda']['surat_lanjutan'];
                    // die ($model->surat_lanjutan);
                    if ($_POST['Agenda']['pelaksana'] != "") { //pelaksana internal ada
                        $model->pelaksana = $_POST['Agenda']['pelaksana'];
                    } else { //pelaksana internal tidak ada
                        Yii::$app->session->setFlash('warning', "Detail Pelaksana Internal (Project) harus terisi jika Anda memilih Cakupan Internal. Terima kasih.");
                        return $this->render('create', [
                            'model' => $model,
                            'searchModel' => $searchModel,
                            'dataProvider' => $dataProvider,
                        ]);
                    }
                }
                if ($_POST['Agenda']['pilihtempat'] == 1) {
                    if ($_POST['Agenda']['tempattext'] != "") {
                        $model->tempat = $_POST['Agenda']['tempattext'];
                    } else {
                        Yii::$app->session->setFlash('warning', "Detail Tempat Luar Kantor harus terisi jika Anda memilih Lokasi Eksternal. Terima kasih.");
                        return $this->render('create', [
                            'model' => $model,
                            'searchModel' => $searchModel,
                            'dataProvider' => $dataProvider,
                        ]);
                    }
                } else {
                    if ($_POST['Agenda']['tempat'] != "") {
                        $model->tempat = $_POST['Agenda']['tempat'];
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
                    if ($model->progress == 0) {
                        //SIMPAN NOTIFIKASI SISTEM
                        $pelaksana = \app\models\Project::findOne($model->pelaksana);
                        if (!empty($pelaksana))
                            $pelaksana = $pelaksana->nama_project;
                        else
                            $pelaksana = $model->pelaksana;
                        $agendaId = Agenda::find()->max('id_agenda');
                        $formatter = Yii::$app->formatter;
                        $formatter->locale = 'id-ID';
                        $waktumulai = new \DateTime($model->waktumulai, new \DateTimeZone('Asia/Jakarta')); // create a datetime object for waktumulai with UTC timezone
                        $waktumulaiFormatted = $formatter->asDatetime($waktumulai, 'd MMMM Y, H:mm'); // format the waktumulai datetime value
                        $waktuselesai = new \DateTime($model->waktuselesai, new \DateTimeZone('Asia/Jakarta')); // create a datetime object for waktuselesai with UTC timezone
                        $waktuselesaiFormatted = $formatter->asDatetime($waktuselesai, 'H:mm'); // format the waktuselesai time value only
                        if ($waktumulai->format('Y-m-d') === $waktuselesai->format('Y-m-d')) {
                            // if waktumulai and waktuselesai are on the same day, format the time range differently
                            $waktumulaiFormatted = $formatter->asDatetime($waktumulai, 'd MMMM Y, H:mm'); // format the waktumulai datetime value with the year and time
                            $waktuFormatted = $waktumulaiFormatted . ' - ' . $waktuselesaiFormatted . ' WIB'; // concatenate the formatted dates
                        } else {
                            // if waktumulai and waktuselesai are on different days, format the date range normally
                            $waktuselesaiFormatted = $formatter->asDatetime($waktuselesai, 'd MMMM Y, H:mm'); // format the waktuselesai datetime value
                            $waktuFormatted = $waktumulaiFormatted . ' WIB s.d ' . $waktuselesaiFormatted . ' WIB'; // concatenate the formatted dates
                        }
                        foreach ($peserta as $userId) {
                            \app\models\Notification::createNotification($userId, 'Anda diundang dalam Kegiatan <strong>' . $model->kegiatan . '</strong> dari Project/Tim <strong>' . $pelaksana . '</strong> yang akan dilaksanakan pada <strong>' . $waktuFormatted . '</strong>', Yii::$app->controller->id, $agendaId);
                        }
                        //KIRIM NOTIFIKASI UNTUK CC dan EVENT TEAM
                        if ($model->by_event_team == true) {
                            // NOTIFIKASI UNTUK KETUA EVENT TEAM
                            // die($model->event_team_leader);
                            $event_team_leader = Pengguna::findOne($model->event_team_leader);
                            $nomor_tujuan_event_team_leader = $event_team_leader->nomor_hp;
                            $nama_event_team_leader = $event_team_leader->nama;
                            \app\models\Notification::createNotification($event_team_leader->username, 'Terdapat permohonan support Kegiatan <strong>' . $model->kegiatan . '</strong> dari Project/Tim <strong>' . $pelaksana . '</strong> yang akan dilaksanakan pada <strong>' . $waktuFormatted . '</strong>', Yii::$app->controller->id, $agendaId);
                            // Convert the list of names to a string in the format that can be used for autofill
                            $isi_notif_event_team_leader = '*Portal Pintar - WhatsApp Notification Blast*

Bapak/Ibu Ketua Event Team ' . $nama_event_team_leader . ', terdapat permohonan support Kegiatan *' . $model->kegiatan . '* dari Project/Tim *' . $pelaksana . '* yang akan dilaksanakan pada:
Jadwal : *' . $waktuFormatted . '*
Tempat : *' . $model->getTempate() . '*
            
_#pesan ini dikirim oleh Portal Pintar dan tidak perlu dibalas_';
                            $response = $this->wa_engine($nomor_tujuan_event_team_leader, $isi_notif_event_team_leader);

                            // NOTIFIKASI UNTUK CHANGE CHAMPION
                            $change_champion = Projectmember::find()->joinWith('projecte')->where(
                                [
                                    'tahun' => date("Y"),
                                    'nama_project' => 'Change Agent Network',
                                    'member_status' => 2
                                ]
                            )->one();
                            $change_champion_data = Pengguna::findOne($change_champion->pegawai);
                            $nomor_tujuan_change_champion = $change_champion_data->nomor_hp;
                            $nama_change_champion = $change_champion_data->nama;
                            \app\models\Notification::createNotification($change_champion->pegawai, 'Terdapat usulan support Event Team untuk Kegiatan <strong>' . $model->kegiatan . '</strong> dari Project/Tim <strong>' . $pelaksana . '</strong> yang akan dilaksanakan pada <strong>' . $waktuFormatted . '</strong>', Yii::$app->controller->id, $agendaId);
                            // Convert the list of names to a string in the format that can be used for autofill
                            $isi_notif_change_champion = '*Portal Pintar - WhatsApp Notification Blast*

Bapak/Ibu Change Champion, ' . $nama_change_champion . ', terdapat usulan support Event Team untuk Kegiatan *' . $model->kegiatan . '* dari Project/Tim *' . $pelaksana . '* yang akan dilaksanakan pada:
Jadwal : *' . $waktuFormatted . '*
Tempat : *' . $model->getTempate() . '*
            
_#pesan ini dikirim oleh Portal Pintar dan tidak perlu dibalas_';
                            $response = $this->wa_engine($nomor_tujuan_change_champion, $isi_notif_change_champion);
                        }
                    }
                    if (($model->pelaksana <= 65 && $model->peserta_lain == NULL && $model->surat_lanjutan == 1)) { //65: jumlah tim
                        Yii::$app->session->setFlash('success', "Agenda berhasil ditambahkan. Jika memerlukan, silahkan lanjutkan pengisian Surat Internal. Terima kasih.");
                        return $this->redirect(['suratrepo/create/', 'id' => $model->id_agenda]);
                    }

                    if (($model->metode == 0 || $model->tempat == 13) && $model->progress == 0 && $model->surat_lanjutan == 0) {
                        Yii::$app->session->setFlash('success', "Agenda berhasil ditambahkan. Jika memerlukan, silahkan lanjutkan pengisian Permohonan Zoom. Terima kasih.");
                        return $this->redirect(['zooms/create', 'fk_agenda' => $model->id_agenda]);
                    } else {
                        Yii::$app->session->setFlash('success', "Agenda berhasil ditambahkan. Terima kasih.");
                        return $this->redirect(['view', 'id' => $model->id_agenda]);
                    }
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
        if ($model->reporter != Yii::$app->user->identity->username) {
            Yii::$app->session->setFlash('warning', "Agenda hanya dapat diubah oleh pegawai yang menginput. Terima kasih.");
            return $this->redirect(['index', 'owner' => '', 'year' => '', 'nopage' => 0]);
        }
        if ($model->progress == 1 || $model->progress == 3 || $model->deleted == 1) {
            Yii::$app->session->setFlash('warning', "Agenda yang sudah selesai, batal atau sudah dihapus tidak dapat diubah kembali. Terima kasih.");
            return $this->redirect(['index', 'owner' => '', 'year' => '', 'nopage' => 0]);
        }
        $searchModel = new AgendaSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);
        $dataProvider->query
            ->andWhere(['>=', 'waktumulai', date('Y-m-d H:i:s')])
            ->andWhere(['<=', 'waktumulai', date('Y-m-d H:i:s', strtotime('+2 weeks'))])
            ->andWhere(['progress' => '0']);
        $dataProvider->pagination = false;

        if ($this->request->isPost) {
            $model->load($this->request->post());
            date_default_timezone_set('Asia/Jakarta');
            $model->timestamp_lastupdate = date('Y-m-d H:i:s');
            $peserta = $_POST['Agenda']['peserta'];
            $cek = implode("@bps.go.id, ", $peserta) . "@bps.go.id";
            $model->peserta = $cek;
            // die(var_dump($_POST['Agenda']['peserta']));
            if (str_contains($_POST['Agenda']['waktumulai'], 'WIB')) {
                /* WAKTU SELESAI */
                // Get the datetime string from the $_POST variable
                $datetimeStr = $_POST['Agenda']['waktumulai'];
                // Parse the datetime string and convert it to a DateTime object
                $datetime = DateTime::createFromFormat('Y-m-d H:i T', $datetimeStr);
                // Convert the DateTime object to a string in the desired format
                $formattedDatetime = $datetime->format('Y-m-d H:i:s');
                $model->waktumulai = $formattedDatetime;
            } else {
                $model->waktumulai = date("Y-m-d H:i:s", strtotime($_POST['Agenda']['waktumulai']));
            }
            if (str_contains($_POST['Agenda']['waktuselesai'], 'WIB')) {
                /* WAKTU SELESAI */
                // Get the datetime string from the $_POST variable
                $datetimeStr = $_POST['Agenda']['waktuselesai'];
                // Parse the datetime string and convert it to a DateTime object
                $datetime = DateTime::createFromFormat('Y-m-d H:i T', $datetimeStr);
                // Convert the DateTime object to a string in the desired format
                $formattedDatetime = $datetime->format('Y-m-d H:i:s');
                $model->waktuselesai = $formattedDatetime;
            } else {
                $model->waktuselesai = date("Y-m-d H:i:s", strtotime($_POST['Agenda']['waktuselesai']));
            }
            if ($model->validate()) {
                if ($_POST['Agenda']['pilihpelaksana'] == 1) {
                    if ($_POST['Agenda']['pelaksanatext'] != "") {
                        $model->pelaksana = $_POST['Agenda']['pelaksanatext'];
                    } else {
                        Yii::$app->session->setFlash('warning', "Detail Pelaksana Eksternal harus terisi jika Anda memilih Cakupan Eksternal. Terima kasih.");
                        return $this->render('update', [
                            'model' => $model,
                            'searchModel' => $searchModel,
                            'dataProvider' => $dataProvider,
                        ]);
                    }
                } else {
                    if ($_POST['Agenda']['pelaksana'] != "") {
                        $model->pelaksana = $_POST['Agenda']['pelaksana'];
                    } else {
                        Yii::$app->session->setFlash('warning', "Detail Pelaksana Internal (Project) harus terisi jika Anda memilih Cakupan Internal. Terima kasih.");
                        return $this->render('update', [
                            'model' => $model,
                            'searchModel' => $searchModel,
                            'dataProvider' => $dataProvider,
                        ]);
                    }
                }
                if ($_POST['Agenda']['pilihtempat'] == 1) {
                    if ($_POST['Agenda']['tempattext'] != "") {
                        $model->tempat = $_POST['Agenda']['tempattext'];
                    } else {
                        Yii::$app->session->setFlash('warning', "Detail Tempat Luar Kantor harus terisi jika Anda memilih Lokasi Eksternal. Terima kasih.");
                        return $this->render('update', [
                            'model' => $model,
                            'searchModel' => $searchModel,
                            'dataProvider' => $dataProvider,
                        ]);
                    }
                } else {
                    if ($_POST['Agenda']['tempat'] != "") {
                        $model->tempat = $_POST['Agenda']['tempat'];
                    } else {
                        Yii::$app->session->setFlash('warning', "Detail Lokasi Kantor harus terisi jika Anda memilih Lokasi Internal. Terima kasih.");
                        return $this->render('update', [
                            'model' => $model,
                            'searchModel' => $searchModel,
                            'dataProvider' => $dataProvider,
                        ]);
                    }
                }
                date_default_timezone_set('Asia/Jakarta');
                $model->timestamp_lastupdate = date('Y-m-d H:i:s');
                $zoom = Zooms::find()->select('*')->where('fk_agenda = ' . $model->id_agenda)->andWhere('deleted = 0')->count();
                if ($model->save()) {
                    //SIMPAN NOTIFIKASI SISTEM
                    $pelaksana = \app\models\Project::findOne($model->pelaksana);
                    if (!empty($pelaksana))
                        $pelaksana = $pelaksana->nama_project;
                    else
                        $pelaksana = $model->pelaksana;
                    $agendaId = Agenda::find()->max('id_agenda');
                    $formatter = Yii::$app->formatter;
                    $formatter->locale = 'id-ID';
                    $waktumulai = new \DateTime($model->waktumulai, new \DateTimeZone('Asia/Jakarta')); // create a datetime object for waktumulai with UTC timezone
                    $waktumulaiFormatted = $formatter->asDatetime($waktumulai, 'd MMMM Y, H:mm'); // format the waktumulai datetime value
                    $waktuselesai = new \DateTime($model->waktuselesai, new \DateTimeZone('Asia/Jakarta')); // create a datetime object for waktuselesai with UTC timezone
                    $waktuselesaiFormatted = $formatter->asDatetime($waktuselesai, 'H:mm'); // format the waktuselesai time value only
                    if ($waktumulai->format('Y-m-d') === $waktuselesai->format('Y-m-d')) {
                        // if waktumulai and waktuselesai are on the same day, format the time range differently
                        $waktumulaiFormatted = $formatter->asDatetime($waktumulai, 'd MMMM Y, H:mm'); // format the waktumulai datetime value with the year and time
                        $waktuFormatted = $waktumulaiFormatted . ' - ' . $waktuselesaiFormatted . ' WIB'; // concatenate the formatted dates
                    } else {
                        // if waktumulai and waktuselesai are on different days, format the date range normally
                        $waktuselesaiFormatted = $formatter->asDatetime($waktuselesai, 'd MMMM Y, H:mm'); // format the waktuselesai datetime value
                        $waktuFormatted = $waktumulaiFormatted . ' WIB s.d ' . $waktuselesaiFormatted . ' WIB'; // concatenate the formatted dates
                    }
                    if ($model->waktumulai != $_POST['Agenda']['waktumulai'] || $model->waktuselesai != $_POST['Agenda']['waktuselesai']) {
                        foreach ($peserta as $userId) {
                            \app\models\Notification::createNotification($userId, 'Jadwal kegiatan <strong>' . $model->kegiatan . '</strong> dari Project/Tim <strong>' . $pelaksana . '</strong> dipindahkan ke <strong>' . $waktuFormatted . '</strong>', Yii::$app->controller->id, $agendaId);
                        }
                    }

                    if ($model->by_event_team == true) {
                        // NOTIFIKASI UNTUK KETUA EVENT TEAM
                        // die($model->event_team_leader);
                        $event_team_leader = Pengguna::findOne($model->event_team_leader);
                        $nomor_tujuan_event_team_leader = $event_team_leader->nomor_hp;
                        $nama_event_team_leader = $event_team_leader->nama;
                        \app\models\Notification::createNotification($event_team_leader->username, 'Terdapat permohonan support Kegiatan <strong>' . $model->kegiatan . '</strong> dari Project/Tim <strong>' . $pelaksana . '</strong> yang akan dilaksanakan pada <strong>' . $waktuFormatted . '</strong>', Yii::$app->controller->id, $agendaId);
                        // Convert the list of names to a string in the format that can be used for autofill
                        $isi_notif_event_team_leader = '*Portal Pintar - WhatsApp Notification Blast*

Bapak/Ibu Ketua Event Team ' . $nama_event_team_leader . ', terdapat permohonan support Kegiatan *' . $model->kegiatan . '* dari Project/Tim *' . $pelaksana . '* yang akan dilaksanakan pada:
Jadwal : *' . $waktuFormatted . '*
Tempat : *' . $model->getTempate() . '*
        
_#pesan ini dikirim oleh Portal Pintar dan tidak perlu dibalas_';
                        $response = $this->wa_engine($nomor_tujuan_event_team_leader, $isi_notif_event_team_leader);

                        // NOTIFIKASI UNTUK CHANGE CHAMPION
                        $change_champion = Projectmember::find()->joinWith('projecte')->where(
                            [
                                'tahun' => date("Y"),
                                'nama_project' => 'Change Agent Network',
                                'member_status' => 2
                            ]
                        )->one();
                        $change_champion_data = Pengguna::findOne($change_champion->pegawai);
                        $nomor_tujuan_change_champion = $change_champion_data->nomor_hp;
                        $nama_change_champion = $change_champion_data->nama;
                        \app\models\Notification::createNotification($change_champion->pegawai, 'Terdapat usulan support Event Team untuk Kegiatan <strong>' . $model->kegiatan . '</strong> dari Project/Tim <strong>' . $pelaksana . '</strong> yang akan dilaksanakan pada <strong>' . $waktuFormatted . '</strong>', Yii::$app->controller->id, $agendaId);
                        // Convert the list of names to a string in the format that can be used for autofill
                        $isi_notif_change_champion = '*Portal Pintar - WhatsApp Notification Blast*

Bapak/Ibu Change Champion, ' . $nama_change_champion . ', terdapat usulan support Event Team untuk Kegiatan *' . $model->kegiatan . '* dari Project/Tim *' . $pelaksana . '* yang akan dilaksanakan pada:
Jadwal : *' . $waktuFormatted . '*
Tempat : *' . $model->getTempate() . '*
        
_#pesan ini dikirim oleh Portal Pintar dan tidak perlu dibalas_';
                        $response = $this->wa_engine($nomor_tujuan_change_champion, $isi_notif_change_champion);
                    }
                    if (($model->pelaksana <= 65 && $model->peserta_lain == NULL && $model->surat_lanjutan == 1)) {
                        Yii::$app->session->setFlash('success', "Agenda berhasil ditambahkan. Jika memerlukan, silahkan lanjutkan pengisian Surat Internal. Terima kasih.");
                        return $this->redirect(['suratrepo/create/0']);
                    } else {
                        Yii::$app->session->setFlash('success', "Agenda berhasil dimutakhirkan. Terima kasih.");
                        return $this->redirect(['view', 'id' => $model->id_agenda]);
                    }
                }
            }
        }
        return $this->render('update', [
            'model' => $model,
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }
    public function actionTunda($id)
    {
        $model = $this->findModel($id);
        if ($model->progress != 0) {
            Yii::$app->session->setFlash('warning', "Agenda hanya dapat ditunda satu kali. Terima kasih.");
            return $this->redirect(['view', 'id' => $model->id_agenda]);
        }
        if ($model->reporter != Yii::$app->user->identity->username) {
            Yii::$app->session->setFlash('warning', "Agenda hanya dapat diubah oleh pegawai yang menginput. Terima kasih.");
            return $this->redirect(['index', 'owner' => '', 'year' => '', 'nopage' => 0]);
        }
        $searchModel = new AgendaSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);
        $dataProvider->query
            ->andWhere(['>=', 'waktumulai', date('Y-m-d H:i:s')])
            ->andWhere(['<=', 'waktumulai', date('Y-m-d H:i:s', strtotime('+2 weeks'))])
            ->andWhere(['progress' => '0']);
        $dataProvider->pagination = false;
        if ($this->request->isPost) {
            $model->load($this->request->post());
            date_default_timezone_set('Asia/Jakarta');
            $model->timestamp_lastupdate = date('Y-m-d H:i:s');
            $model->progress = 2;
            if (str_contains($_POST['Agenda']['waktumulai_tunda'], 'WIB')) {
                /* WAKTU SELESAI */
                // Get the datetime string from the $_POST variable
                $datetimeStr = $_POST['Agenda']['waktumulai_tunda'];
                // Parse the datetime string and convert it to a DateTime object
                $datetime = DateTime::createFromFormat('Y-m-d H:i T', $datetimeStr);
                // Convert the DateTime object to a string in the desired format
                $formattedDatetime = $datetime->format('Y-m-d H:i:s');
                $model->waktumulai_tunda = $formattedDatetime;
            } else {
                $model->waktumulai_tunda = date("Y-m-d H:i:s", strtotime($_POST['Agenda']['waktumulai_tunda']));
            }
            if (str_contains($_POST['Agenda']['waktuselesai_tunda'], 'WIB')) {
                /* WAKTU SELESAI */
                // Get the datetime string from the $_POST variable
                $datetimeStr = $_POST['Agenda']['waktuselesai_tunda'];
                // Parse the datetime string and convert it to a DateTime object
                $datetime = DateTime::createFromFormat('Y-m-d H:i T', $datetimeStr);
                // Convert the DateTime object to a string in the desired format
                $formattedDatetime = $datetime->format('Y-m-d H:i:s');
                $model->waktuselesai_tunda = $formattedDatetime;
            } else {
                $model->waktuselesai_tunda = date("Y-m-d H:i:s", strtotime($_POST['Agenda']['waktuselesai_tunda']));
            }
            if ($model->validate() && $model->save()) {
                Yii::$app->session->setFlash('success', "Agenda berhasil ditunda. Terima kasih.");
                return $this->redirect(['view', 'id' => $model->id_agenda]);
            }
        }
        return $this->render('tunda', [
            'model' => $model,
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }
    public function actionDelete($id)
    {
        date_default_timezone_set('Asia/Jakarta');
        $affected_rows = Agenda::updateAll(['deleted' => 1, 'timestamp_lastupdate' => date('Y-m-d H:i:s')], 'id_agenda = "' . $id . '"');
        if ($affected_rows == 0) {
            Yii::$app->session->setFlash('warning', "Gagal. Mohon hubungi Admin.");
            return $this->redirect(['index', 'owner' => '', 'year' => '', 'nopage' => 0]);
        } else {
            Yii::$app->session->setFlash('success', "Agenda berhasil dihapus. Terima kasih.");
            return $this->redirect(['index', 'owner' => '', 'year' => '', 'nopage' => 0]);
        }
    }
    protected function findModel($id_agenda)
    {
        if (($model = Agenda::findOne(['id_agenda' => $id_agenda])) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('The requested page does not exist.');
    }
    public function actionBatal($id)
    {
        $model = $this->findModel($id);
        date_default_timezone_set('Asia/Jakarta');
        $affected_rows = Agenda::updateAll(['progress' => 3, 'timestamp_lastupdate' => date('Y-m-d H:i:s')], 'id_agenda = "' . $id . '"');
        if ($affected_rows == 0) {
            Yii::$app->session->setFlash('warning', "Gagal. Mohon hubungi Admin.");
            return $this->redirect(['index', 'owner' => '', 'year' => '', 'nopage' => 0]);
        } else {
            Yii::$app->session->setFlash('success', "Agenda berhasil dibatalkan. Terima kasih.");
            return $this->redirect(['index', 'owner' => '', 'year' => '', 'nopage' => 0]);
        }
    }
    public function actionSelesai($id)
    {
        $model = $this->findModel($id);
        if ($model->reporter != Yii::$app->user->identity->username) {
            Yii::$app->session->setFlash('warning', "Agenda hanya dapat diubah oleh pegawai yang menginput. Terima kasih.");
            return $this->redirect(['index', 'owner' => '', 'year' => '', 'nopage' => 0]);
        }
        if ($model->progress == 1) {
            Yii::$app->session->setFlash('warning', "Agenda yang sudah selesai tidak dapat diubah kembali. Terima kasih.");
            return $this->redirect(['index', 'owner' => '', 'year' => '', 'nopage' => 0]);
        }
        date_default_timezone_set('Asia/Jakarta');
        $affected_rows = Agenda::updateAll(['progress' => 1, 'timestamp_lastupdate' => date('Y-m-d H:i:s')], 'id_agenda = "' . $id . '"');
        if ($affected_rows == 0) {
            Yii::$app->session->setFlash('warning', "Gagal. Mohon hubungi Admin.");
            return $this->redirect(['index', 'owner' => '', 'year' => '', 'nopage' => 0]);
        } else {
            Yii::$app->session->setFlash('success', "Agenda berhasil ditandai selesai. Silahkan tambahkan Laporan. Terima kasih. ");
            return $this->redirect(['index', 'owner' => '', 'year' => '', 'nopage' => 0]);
        }
    }
    public static function findHeader($id_laporan)
    {
        $dataagenda = Agenda::findOne(['id_agenda' => $id_laporan]);
        $waktutampil = LaporanController::findWaktutampil($id_laporan);
        $pemimpin = Pengguna::findOne($dataagenda->pemimpin);
        $nama_pimpinan = $pemimpin->nama;
        $header = '
            <div class="row" style="margin-left:38pt;">
                <div class="col-sm-12 d-flex">
                    <div class="table-responsive">
                        <table class="table table-sm align-self-end">
                            <tbody>
                                <tr>
                                    <td class="col-sm-2" style="vertical-align: top">Waktu</td>
                                    <td style="vertical-align: top">: </td>
                                    <td style="vertical-align: top">' . $waktutampil . '</td>
                                </tr>                            
                                <tr>
                                    <td style="vertical-align: top">Tempat</td>
                                    <td style="vertical-align: top">: </td>
                                    <td style="vertical-align: top"> ' . $dataagenda->tempate . '</td>
                                </tr>
                                <tr>
                                    <td style="vertical-align: top">Agenda</td>
                                    <td style="vertical-align: top">: </td>
                                    <td style="vertical-align: top"> ' . $dataagenda->kegiatan . '</td>
                                </tr>
                                <tr>
                                    <td class="col-sm-2" style="vertical-align: top">Pemimpin Agenda</td>
                                    <td style="vertical-align: top">: </td>
                                    <td style="vertical-align: top">' . $nama_pimpinan . '</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            ';
        return $header;
    }
    public function actionEmailblast($id)
    {
        $model = new EmailblastForm();
        $dataagenda = $this->findModel($id);
        if ($dataagenda->progress != 0) {
            Yii::$app->session->setFlash('warning', "Email Blast hanya disediakan untuk Agenda berstatus -direncanakan-.");
            return $this->redirect(['index', 'owner' => '', 'year' => '', 'nopage' => 0]);
        }
        $header = AgendaController::findHeader($id);
        $pelaksana = $dataagenda->getPelaksanalengkape();
        $surat = Suratrepo::findOne(['fk_agenda' => $id, 'is_undangan' => 1]);
        $waktutampil = new \DateTime(date("Y-m-d"), new \DateTimeZone('UTC'));;
        $formatter = Yii::$app->formatter;
        $formatter->locale = 'id-ID'; // set the locale to Indonesian
        $timezone = new \DateTimeZone('Asia/Jakarta'); // create a timezone object for WIB
        if (!empty($surat->tanggal_suratrepo)) {
            $waktutampil = new \DateTime($surat->tanggal_suratrepo, new \DateTimeZone('UTC')); // create a datetime object for waktumulai with UTC timezone            
        }
        $waktutampil->setTimeZone($timezone); // set the timezone to WIB
        $waktutampil = $formatter->asDatetime($waktutampil, 'd MMMM Y'); // format the waktumulai datetime value
        $autofillString =
            "
                        <p style=''>Kepada Yth.</p>
                        <p style='margin-left:20pt;'>(terlampir nama)</p>
                        <p style='margin-left:20pt;'>di tempat</p>
                        <p style='text-indent:.5in;text-align:justify'>Dalam rangka <b>" . $dataagenda->kegiatan . "</b>, 
                        bersama ini kami mengundang Bapak/Ibu untuk hadir pada:</p>
                        " . $header . "                        
                        <p style='text-indent:.5in;text-align:justify'>Demikian disampaikan, atas perhatian dan kehadiran Bapak/Ibu diucapkan terima kasih.</p>
                        <br/>
                        <table width='500' border='0' bordercolor='33FFFF' align='center' cellpadding='0' cellspacing='0'>
                            <tr>
                                <td width='250'></td>
                                <td></td>
                                <td></td>
                                <td>
                                    <center>
                                        " . Yii::$app->params['ibukotaSatker'] . ", " . $waktutampil . "
                                        <br />
                                        <br />
                                        <br />
                                        <br />
                                        <b>" . $pelaksana . "</b>
                                        <center>
                                </td>
                            </tr>
                        </table>                                              
                        <br/>
                        <br/>
                        " . (!empty($surat->tembusan) ? "Tembusan: " . $surat->tembusan : "") . "<br/>";
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
        $autofillString2 = '<b>Peserta Kegiatan :</b> <ol>' . $listItems . '</ol>';
        $autofillString2 =  $autofillString2 . (($dataagenda->peserta_lain != null) ? '<b>Peserta Tambahan : </b><br/>' . $dataagenda->peserta_lain : '');

        $model->body = $autofillString . $autofillString2;

        if ($model->load(Yii::$app->request->post())) {
            $recipients = [];
            $recipients = array_merge($recipients, explode(', ', $dataagenda->peserta));
            $peserta_lain_emails = explode(', ', $dataagenda->peserta_lain);
            foreach ($peserta_lain_emails as $email) {
                if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $recipients[] = $email;
                }
            }
            $recipients = array_merge(['rb1700@bps.go.id'], $recipients);
            $recipients = array_merge([$dataagenda->pemimpin . '@bps.go.id'], $recipients);
            $subject = 'Undangan Digital Portal Pintar';
            $message = $this->renderPartial('_email_blast_template', ['message' => $model->body]);
            // die(var_dump($recipients));

            $result = true;
            foreach ($recipients as $chunk) {
                $result = \app\components\Emailer::sendEmail($chunk, $subject, $message);
                // die(var_dump($result));
                if ($result != true) {
                    Yii::$app->session->setFlash('error', "There was an issue sending emails to some recipients.");
                    break;
                }
            }

            if ($result = true) {
                Yii::$app->session->setFlash('contactFormSubmitted');
                return $this->refresh();
            }
        }
        return $this->render('emailblast', [
            'model' => $model,
            'dataagenda' => $dataagenda,
            'header' => $header
        ]);
    }
    public function actionEditpeserta($id)
    {
        $model = $this->findModel($id);
        if ($model->reporter != Yii::$app->user->identity->username) {
            Yii::$app->session->setFlash('warning', "Agenda hanya dapat diubah oleh pegawai yang menginput. Terima kasih.");
            return $this->redirect(['index', 'owner' => '', 'year' => '', 'nopage' => 0]);
        }
        if ($model->progress == 3) {
            Yii::$app->session->setFlash('warning', "Peserta agenda yang sudah dibatalkan tidak dapat diubah kembali. Terima kasih.");
            return $this->redirect(['index', 'owner' => '', 'year' => '', 'nopage' => 0]);
        }
        if ($this->request->isPost) {
            $model->load($this->request->post());
            date_default_timezone_set('Asia/Jakarta');
            $model->timestamp_lastupdate = date('Y-m-d H:i:s');
            $peserta = $model->peserta;
            $cek = implode("@bps.go.id, ", $peserta) . "@bps.go.id";
            $model->peserta = $cek;
            if ($model->validate()) {
                date_default_timezone_set('Asia/Jakarta');
                $model->timestamp_lastupdate = date('Y-m-d H:i:s');
                if ($model->save()) {
                    Yii::$app->session->setFlash('success', "Data Peserta berhasil dimutakhirkan. Terima kasih.");
                    return $this->redirect(['view', 'id' => $model->id_agenda]);
                }
            }
        }
        return $this->render('editpeserta', [
            'model' => $model,
        ]);
    }
    public function actionGetlistpeserta()
    {
        $selectedTeams = Yii::$app->request->post('teams', []);

        if (in_array('all', $selectedTeams)) {
            // Get all members if "all" is selected
            $members = Projectmember::find()
                ->select('pegawai')
                ->where('member_status <> 0')
                ->asArray()
                ->column();
        } else {
            // Get members for selected teams only
            $members = Projectmember::find()
                ->select('pegawai')
                ->where(['in', 'fk_project', $selectedTeams])
                ->andWhere('member_status <> 0')
                ->asArray()
                ->column();
        }

        $response = ['members' => $members];
        return Json::encode($response);
    }
    public static function wa_engine($nomor_tujuan, $isi_notif)
    {
        // URL tujuan
        $url = 'https://dialogwa.web.id/api/send-text';
        $token = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpZCI6IjY2NmNjNjA4ZGM2NDQyMmVkOTE1MzdjMiIsInVzZXJuYW1lIjoibm9mcmlhbmkiLCJpYXQiOjE3MjE3OTg3NDgsImV4cCI6NDg3NzU1ODc0OH0.5dnMRRM-G0bktsNFvArEnBkH1Qr5Snn8LvEBpswVhtw';

        // Data yang akan dikirim dalam body request
        $data = array(
            'session' => 'portalpintar',
            'target' => $nomor_tujuan . '@s.whatsapp.net', //format nomor tujuan harus menggunakan kode negara contoh : 628......@s.whatsapp.net
            'message' => $isi_notif
        );

        // Mengencode data menjadi JSON
        $jsonData = json_encode($data);

        // Inisialisasi cURL
        $ch = curl_init($url);

        // Mengatur opsi-opsi cURL
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Authorization: Bearer ' . $token
        ));
        // Disable SSL verification (not recommended for production)
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

        // Eksekusi request dan simpan responsenya
        $response = curl_exec($ch);

        // Periksa apakah terjadi kesalahan saat melakukan request
        if (curl_errno($ch)) {
            $error_msg = curl_error($ch);
            curl_close($ch);
            return 'Error: ' . $error_msg;
        } else {
            // Tutup cURL
            curl_close($ch);
            return $response;
        }
    }
    public static function wa_engine_fonnte($nomor_tujuan, $isi_notif)
    {
        // URL tujuan
        $url = 'https://api.fonnte.com/send';
        $token = 'GmcguyaWvLMQmv7hNGES';
        $ch = curl_init();

        curl_setopt_array($ch, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => array(
                'target' => $nomor_tujuan,
                'message' => $isi_notif,
                // 'countryCode' => '62', //optional
            ),
            CURLOPT_HTTPHEADER => array(
                'Authorization: ' .  $token //change TOKEN to your actual token
            ),
        ));


        // Eksekusi request dan simpan responsenya
        $response = curl_exec($ch);

        // Periksa apakah terjadi kesalahan saat melakukan request
        if (curl_errno($ch)) {
            $error_msg = curl_error($ch);
            curl_close($ch);
            return 'Error: ' . $error_msg;
        } else {
            // Tutup cURL
            curl_close($ch);
            return $response;
        }
    }
    public function actionWa_blast($id)
    {
        $dataagenda = $this->findModel($id);

        if ($dataagenda->progress != 0 && $dataagenda->progress != 2) {
            Yii::$app->session->setFlash('warning', "WA Blast hanya disediakan untuk Agenda berstatus -direncanakan- atau -ditunda-.");
            return $this->redirect(['index', 'owner' => '', 'year' => '', 'nopage' => 0]);
        }

        $pelaksana = \app\models\Project::findOne($dataagenda->pelaksana);
        if (!empty($pelaksana))
            $pelaksana = $pelaksana->nama_project;
        else
            $pelaksana = $dataagenda->pelaksana;

        $formatter = Yii::$app->formatter;
        $formatter->locale = 'id-ID'; // set the locale to Indonesian
        $timezone = new \DateTimeZone('Asia/Jakarta'); // create a timezone object for WIB
        if ($dataagenda->progress == 0) {
            $waktumulai = new \DateTime($dataagenda->waktumulai, new \DateTimeZone('UTC')); // create a datetime object for waktumulai with UTC timezone
            $waktumulai->setTimeZone($timezone); // set the timezone to WIB
            $waktumulaiFormatted = $formatter->asDatetime($waktumulai, 'd MMMM Y, H:mm'); // format the waktumulai datetime value
            $waktuselesai = new \DateTime($dataagenda->waktuselesai, new \DateTimeZone('UTC')); // create a datetime object for waktuselesai with UTC timezone
            $waktuselesai->setTimeZone($timezone); // set the timezone to WIB
            $waktuselesaiFormatted = $formatter->asDatetime($waktuselesai, 'H:mm'); // format the waktuselesai time value only            
        } elseif ($dataagenda->progress == 2) {
            $waktumulai = new \DateTime($dataagenda->waktumulai_tunda, new \DateTimeZone('UTC')); // create a datetime object for waktumulai with UTC timezone
            $waktumulai->setTimeZone($timezone); // set the timezone to WIB
            $waktumulaiFormatted = $formatter->asDatetime($waktumulai, 'd MMMM Y, H:mm'); // format the waktumulai datetime value
            $waktuselesai = new \DateTime($dataagenda->waktuselesai_tunda, new \DateTimeZone('UTC')); // create a datetime object for waktuselesai with UTC timezone
            $waktuselesai->setTimeZone($timezone); // set the timezone to WIB
            $waktuselesaiFormatted = $formatter->asDatetime($waktuselesai, 'H:mm'); // format the waktuselesai time value only           
        }
        if ($waktumulai->format('Y-m-d') === $waktuselesai->format('Y-m-d')) {
            // if waktumulai and waktuselesai are on the same day, format the time range differently
            $waktumulaiFormatted = $formatter->asDatetime($waktumulai, 'd MMMM Y, H:mm'); // format the waktumulai datetime value with the year and time
            $waktuFormatted = $waktumulaiFormatted . ' - ' . $waktuselesaiFormatted . ' WIB'; // concatenate the formatted dates
        } else {
            // if waktumulai and waktuselesai are on different days, format the date range normally
            $waktuselesaiFormatted = $formatter->asDatetime($waktuselesai, 'd MMMM Y, H:mm'); // format the waktuselesai datetime value
            $waktuFormatted = $waktumulaiFormatted . ' WIB s.d ' . $waktuselesaiFormatted . ' WIB'; // concatenate the formatted dates
        }

        // Get the list of phone numbers from the peserta attribute in the agenda table
        $waList = explode(', ', $dataagenda->peserta);
        // Extract the username (without "@bps.go.id") from each email address
        $usernames = [];
        foreach ($waList as $wa) {
            $username = substr($wa, 0, strpos($wa, '@'));
            $usernames[] = $username;
        }
        // Query the pengguna table for the list of names that correspond to the extracted usernames
        $penggunas = Pengguna::find()
            ->select(['nama', 'nomor_hp'])
            ->where(['in', 'username', $usernames])
            ->all();
        // Convert the list of names to a string in the format that can be used for autofill
        foreach ($penggunas as $name) {
            $nomor_tujuan = $name->nomor_hp;
            $nama_peserta = $name->nama;
            $isi_notif = '*Portal Pintar - WhatsApp Notification Blast*

Bapak/Ibu ' . $nama_peserta . ', Anda diundang dalam Kegiatan *' . $dataagenda->kegiatan . '* dari Project/Tim *' . $pelaksana . '* yang akan dilaksanakan pada:
Jadwal : *' . $waktuFormatted . '*
Tempat : *' . $dataagenda->getTempate() . '*
            
_#pesan ini dikirim oleh Portal Pintar dan tidak perlu dibalas_';
            $response = $this->wa_engine($nomor_tujuan, $isi_notif);
        }

        $pemimpin = Pengguna::findOne($dataagenda->pemimpin);
        $nomor_tujuan_pimpinan = $pemimpin->nomor_hp;
        $nama_pimpinan = $pemimpin->nama;
        $isi_notif_pimpinan = '*Portal Pintar - WhatsApp Notification Blast*

Bapak/Ibu ' . $nama_pimpinan . ', Anda diundang untuk memimpin agenda dalam Kegiatan *' . $dataagenda->kegiatan . '* dari Project/Tim *' . $pelaksana . '* yang akan dilaksanakan pada:
Jadwal : *' . $waktuFormatted . '*
Tempat : *' . $dataagenda->getTempate() . '*
        
_#pesan ini dikirim oleh Portal Pintar dan tidak perlu dibalas_';
        $response = $this->wa_engine($nomor_tujuan_pimpinan, $isi_notif_pimpinan);

        Yii::info($response, 'wa_blast'); // Log the response instead of outputting it

        if (strpos($response, 'Error:') !== false) {
            Yii::$app->session->setFlash('error', "Failed to send WA Blast. Error: " . $response);
        } else {
            Yii::$app->session->setFlash('success', "WA Blast berhasil dikirim. Terima kasih.");
        }

        return $this->redirect(['index', 'owner' => '', 'year' => '', 'nopage' => 0]);
    }

    public function actionCeksurat($id)
    {
        $model = new Suratrepo();
        $surats = Suratrepo::find()
            ->select('*')
            ->where(['owner' => Yii::$app->user->identity->username])
            ->andWhere(['deleted' => 0])
            ->andWhere(
                ['>', 'DATEDIFF(NOW(), DATE(timestamp_suratrepo_lastupdate))', 3]
            ) // diinput dalam span 3 hari
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
    }
}
