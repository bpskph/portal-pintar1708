<?php

namespace app\controllers;

use app\models\Pengguna;
use app\models\Suratmasuk;
use app\models\Suratmasukdisposisi;
use app\models\Suratmasukpejabat;
use app\models\SuratmasukSearch;
use app\models\Team;
use app\models\Teamleader;
use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\UploadedFile;

class SuratmasukController extends BaseController
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
                            'actions' => ['create', 'update', 'delete'],
                            'allow' => true,
                            'matchCallback' => function ($rule, $action) {
                                return !\Yii::$app->user->isGuest &&
                                    (\Yii::$app->user->identity->approver_mobildinas === 1 || \Yii::$app->user->identity->issekretaris || \Yii::$app->user->identity->level === 0);
                            },
                        ],
                        [
                            'actions' => ['beri-disposisi', 'edit-disposisi'],
                            'allow' => true,
                            'matchCallback' => function ($rule, $action) {
                                return !\Yii::$app->user->isGuest &&
                                    (\Yii::$app->user->identity->issuratmasukpejabat || \Yii::$app->user->identity->isteamleader);
                            },
                        ],
                        [
                            'actions' => ['index', 'view', 'lapor-disposisi'], // add all actions to take guest to login page
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
        if ($action->id === 'lapor-disposisi') {
            $this->enableCsrfValidation = false; // Disable CSRF validation for the action
        }
        return parent::beforeAction($action);
    }
    
    public function actionIndex($year, $for, $from)
    {
        $searchModel = new SuratmasukSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        if ($for != '')
            $dataProvider->query->andWhere(['suratmasukdisposisi.tujuan_disposisi_pegawai' => $for]);
        if ($from != '')
            $dataProvider->query->andWhere(['suratmasukdisposisi.pemberi_disposisi' => $from]);
        if ($year == date("Y"))
            $dataProvider->query->andWhere(['YEAR(tanggal_diterima)' => date("Y")]);
        elseif ($year != '')
            $dataProvider->query->andWhere(['YEAR(tanggal_diterima)' => $year]);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionView($id)
    {
        $model =  $this->findModel($id);

        if ($model->deleted == 1) {
            Yii::$app->session->setFlash('warning', "Data surat ini sudah dihapus.");
            return $this->redirect(['index', 'year' => '']);
        }

        $penerima_disposisi =  Suratmasukdisposisi::find()->select(['tujuan_disposisi_pegawai'])->where(['fk_suratmasuk' => $model['id_suratmasuk']])->column();
        if (
            !Yii::$app->user->isGuest
            && !Yii::$app->user->identity->issekretaris
            && !Yii::$app->user->identity->issuratmasukpejabat
            && !in_array(Yii::$app->user->identity->username, $penerima_disposisi)
            && $model->sifat !== 0
        ) {
            Yii::$app->session->setFlash('warning', "Akses surat ini terbatas.");
            return $this->redirect(['index', 'year' => '']);
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
        $model = new Suratmasuk();

        if ($this->request->isPost) {
            $model->load($this->request->post());
            $model->reporter = Yii::$app->user->identity->username;
            $pejabat = Suratmasukpejabat::findOne($model->fk_suratmasukpejabat);
            $model->fk_suratmasukpejabat = $pejabat->pegawai;
            $model->filepdf = UploadedFile::getInstance($model, 'filepdf'); // Move this up
            if ($model->validate() && $model->save()) {
                if ($model->filepdf && $model->id_suratmasuk && $model->filepdf->extension === 'pdf') {
                    if (file_exists(Yii::getAlias('@webroot/surat/masuk/' . $model->id_suratmasuk . '.pdf'))) {
                        unlink(Yii::getAlias('@webroot/surat/masuk/') . $model->id_suratmasuk . '.pdf');
                    }
                    if ($model->upload()) {
                        /* PENGIRIMAN NOTIFIKASI WA BLAST UNTUK PEJABAT PEMBERI DISPOSISI */
                        $pejabat_pemberi_disposisi = Pengguna::findOne(['username' => $model->fk_suratmasukpejabat]);
                        $nomor_tujuan_pejabat = $pejabat_pemberi_disposisi->nomor_hp;
                        $nama_pejabat_pemberi_disposisi = $pejabat_pemberi_disposisi->nama;
                        $isi_notif_pejabat = '*Portal Pintar - WhatsApp Notification Blast*

Bapak/Ibu Pimpinan *' . $nama_pejabat_pemberi_disposisi . '*, terdapat data surat masuk dari *' . $model->pengirim_suratmasuk . '* nomor *' . $model->nomor_suratmasuk .  '* untuk didisposisikan pada Sistem Portal Pintar di Sistem Portal Pintar, ' . Yii::$app->params['webhostingSatker'] . 'portalpintar/suratmasuk/' . $model->id_suratmasuk . '
Terima kasih.

_#pesan ini dikirim oleh Portal Pintar dan tidak perlu dibalas_';

                        $response = AgendaController::wa_engine($nomor_tujuan_pejabat, $isi_notif_pejabat);

                        /* NOTIFIKASI SISTEM UNTUK PENERIMA DISPOSISI UTAMA YANG BARU*/
                        \app\models\Notification::createNotification(
                            $model->fk_suratmasukpejabat,
                            'Terdapat data surat masuk dari <strong>' . $model->pengirim_suratmasuk . '</strong> nomor <strong>' . $model->nomor_suratmasuk . '</strong> 
                             untuk didisposisikan. Terima kasih.',
                            Yii::$app->controller->id,
                            $model->id_suratmasuk
                        );
                        Yii::$app->session->setFlash('success', "Data dan berkas Surat Masuk berhasil ditambahkan. Terima kasih.");
                        return $this->redirect(['view', 'id' => $model->id_suratmasuk]);
                    }
                }

                Yii::$app->session->setFlash('success', "Data Surat Masuk berhasil ditambahkan. Terima kasih.");
                return $this->redirect(['view', 'id' => $model->id_suratmasuk]);
            } else {
                // Check for errors
                // print_r($model->errors);
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

        if ($model->reporter != Yii::$app->user->identity->username) {
            Yii::$app->session->setFlash('warning', "Maaf. Hanya penginput data surat masuk terkait yang dapat mengubah datanya.");
            return $this->redirect(['index?year=&from=&for=']);
        }
        if ($model->deleted != 0) {
            Yii::$app->session->setFlash('warning', "Maaf. Data surat yang telah dihapus tidak dapat diubah kembali.");
            return $this->redirect(['index?year=&from=&for=']);
        }
        $disposisi = Suratmasukdisposisi::findAll(['fk_suratmasuk' => $id]);
        if (!empty($disposisi)) {
            Yii::$app->session->setFlash('warning', "Maaf. Surat masuk yang telah melalui proses disposisi tidak dapat diubah kembali.");
            return $this->redirect(['index?year=&from=&for=']);
        }

        if ($this->request->isPost) {
            $postedData = $this->request->post();
            $model->load($postedData);

            // Check if the user has changed the 'fk_suratmasukpejabat'
            $posted = $postedData['Suratmasuk']['fk_suratmasukpejabat'] ?? null;
            $pejabat = Suratmasukpejabat::findOne($posted);
            $postedPejabat = $pejabat->pegawai;
            $currentPejabat = $model->getOldAttribute('fk_suratmasukpejabat'); // Fetch the original value from the database

            $pejabat = Suratmasukpejabat::findOne($model->fk_suratmasukpejabat);
            $model->fk_suratmasukpejabat = $pejabat->pegawai;
            date_default_timezone_set('Asia/Jakarta');
            $model->timestamp_lastupdate = date('Y-m-d H:i:s');

            if ($model->validate() && $model->save()) {
                if ($postedPejabat != $currentPejabat) {
                    /* PENGIRIMAN NOTIFIKASI WA BLAST UNTUK PEJABAT PEMBERI DISPOSISI BARU*/
                    $pejabat_pemberi_disposisi = Pengguna::findOne(['username' => $postedPejabat]);
                    $nomor_tujuan_pejabat = $pejabat_pemberi_disposisi->nomor_hp;
                    $nama_pejabat_pemberi_disposisi = $pejabat_pemberi_disposisi->nama;
                    $isi_notif_pejabat = '*Portal Pintar - WhatsApp Notification Blast*

Bapak/Ibu Pimpinan *' . $nama_pejabat_pemberi_disposisi . '*, terdapat data surat masuk dari *' . $model->pengirim_suratmasuk . '* nomor *' . $model->nomor_suratmasuk .  '* untuk didisposisikan di Sistem Portal Pintar, ' . Yii::$app->params['webhostingSatker'] . 'portalpintar/suratmasuk/' . $model->id_suratmasuk . '
Terima kasih.

_#pesan ini dikirim oleh Portal Pintar dan tidak perlu dibalas_';

                    $response = AgendaController::wa_engine($nomor_tujuan_pejabat, $isi_notif_pejabat);

                    /* PENGIRIMAN NOTIFIKASI WA BLAST UNTUK PEJABAT PEMBERI DISPOSISI LAMA*/
                    $pejabat_pemberi_disposisi_lama = Pengguna::findOne(['username' => $currentPejabat]);
                    $nomor_tujuan_pejabat_lama = $pejabat_pemberi_disposisi_lama->nomor_hp;
                    $nama_pejabat_pemberi_disposisi_lama = $pejabat_pemberi_disposisi_lama->nama;
                    $isi_notif_pejabat_lama = '*Portal Pintar - WhatsApp Notification Blast*

Bapak/Ibu Pimpinan *' . $nama_pejabat_pemberi_disposisi_lama . '*, wewenang disposisi terhadap surat masuk dari *' . $model->pengirim_suratmasuk . '* nomor *' . $model->nomor_suratmasuk .  '* dialihkan ke Bapak/Ibu Pimpinan *' . $nama_pejabat_pemberi_disposisi . '* di Sistem Portal Pintar, ' . Yii::$app->params['webhostingSatker'] . 'portalpintar/suratmasuk/' . $model->id_suratmasuk . '
Terima kasih.

_#pesan ini dikirim oleh Portal Pintar dan tidak perlu dibalas_';

                    $response = AgendaController::wa_engine($nomor_tujuan_pejabat_lama, $isi_notif_pejabat_lama);

                    /* NOTIFIKASI SISTEM UNTUK PENERIMA DISPOSISI UTAMA YANG BARU*/
                    \app\models\Notification::createNotification(
                        $postedPejabat,
                        'Terdapat data surat masuk dari <strong>' . $model->pengirim_suratmasuk . '</strong> nomor <strong>' . $model->nomor_suratmasuk . '</strong> 
                         untuk didisposisikan. Terima kasih.',
                        Yii::$app->controller->id,
                        $model->id_suratmasuk
                    );

                    /* NOTIFIKASI SISTEM UNTUK PENERIMA DISPOSISI UTAMA YANG LAMA*/
                    \app\models\Notification::createNotification(
                        $currentPejabat,
                        'Wewenang disposisi terhadap surat masuk dari <strong>' . $model->pengirim_suratmasuk . '</strong> nomor <strong>' . $model->nomor_suratmasuk . '</strong> 
                         dialihkan ke Bapak/Ibu Pimpinan <strong>' . $nama_pejabat_pemberi_disposisi_lama . '<strong>. Terima kasih.',
                        Yii::$app->controller->id,
                        $model->id_suratmasuk
                    );
                }

                $model->filepdf = UploadedFile::getInstance($model, 'filepdf');
                // Check if there's an existing file and delete it
                if ($model->filepdf && $model->id_suratmasuk && $model->filepdf->extension === 'pdf') {
                    if (file_exists(Yii::getAlias('@webroot/surat/masuk/' . $model->id_suratmasuk . '.pdf'))) {
                        unlink(Yii::getAlias('@webroot/surat/masuk/') . $model->id_suratmasuk . '.pdf');
                    }
                    if ($model->upload()) {
                        Yii::$app->session->setFlash('success', "Data dan berkas Surat Masuk berhasil dimutakhirkan. Terima kasih.");
                        return $this->redirect(['view', 'id' => $model->id_suratmasuk]);
                    }
                }

                Yii::$app->session->setFlash('success', "Data Surat Masuk berhasil dimutakhirkan. Terima kasih.");
                return $this->redirect(['view', 'id' => $model->id_suratmasuk]);
            } else {
                // Check for errors
                // print_r($model->errors);
            }
        } else {
            $model->loadDefaultValues();
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        if ($model->reporter != Yii::$app->user->identity->username) {
            Yii::$app->session->setFlash('warning', "Maaf. Hanya penginput data surat masuk terkait yang dapat menghapus datanya.");
            return $this->redirect(['index?year=&from=&for=']);
        }
        if ($model->deleted != 0) {
            Yii::$app->session->setFlash('warning', "Maaf. Data surat yang telah dihapus.");
            return $this->redirect(['index?year=&from=&for=']);
        }
        $disposisi = Suratmasukdisposisi::findAll(['fk_suratmasuk' => $id]);
        if (!empty($disposisi)) {
            Yii::$app->session->setFlash('warning', "Maaf. Surat masuk yang telah melalui proses disposisi tidak dapat dihapus.");
            return $this->redirect(['index?year=&from=&for=']);
        }

        date_default_timezone_set('Asia/Jakarta');
        $affected_rows = Suratmasuk::updateAll(['deleted' => 1, 'timestamp_lastupdate' => date('Y-m-d H:i:s')], 'id_suratmasuk = "' . $id . '"');
        if ($affected_rows == 0) {
            Yii::$app->session->setFlash('warning', "Gagal. Mohon hubungi Admin.");
            return $this->redirect(['index?year=&from=&for=']);
        } else {
            Yii::$app->session->setFlash('success', "Data Surat Masuk berhasil dihapus. Terima kasih.");
            return $this->redirect(['index?year=&from=&for=']);
        }
    }
    protected function findModel($id_suratmasuk)
    {
        if (($model = Suratmasuk::findOne(['id_suratmasuk' => $id_suratmasuk])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
    protected function getHeaderdisposisi($id)
    {
        $suratmasuk = $this->findModel($id);
        $disposisi_satu = Suratmasukdisposisi::find()
            ->joinWith(['teame', 'pegawaie'])
            ->where(['fk_suratmasuk' => $id, 'deleted' => 0])
            ->andWhere([
                'or',
                ['level_disposisi' => '1a'],
                ['level_disposisi' => '1b']
            ])
            ->all();
        $level1a = ['team' => [], 'pegawai' => []];
        $level1b = ['team' => [], 'pegawai' => []];

        // Categorize dispositions
        foreach ($disposisi_satu as $disposisi) {
            if ($disposisi->level_disposisi == '1a') {
                $level1a['team'][] = $disposisi->teame->panggilan_team ?? '';
                $level1a['pegawai'][] = $disposisi->pegawaie->nama ?? '';
            } elseif ($disposisi->level_disposisi == '1b') {
                $level1b['team'][] = $disposisi->teame->panggilan_team ?? '';
                $level1b['pegawai'][] = $disposisi->pegawaie->nama ?? '';
            }
        }

        // Format the output with grouping
        $output = '';
        if (!empty($level1a['team'])) {
            $output .= "Disposisi Utama: <br/>Tim <strong>" . implode("<br/>+", $level1a['team']) . "</strong><br/> ";
            if (!empty($level1a['pegawai'])) {
                $output .= "+ " . implode("<br/>+ ", $level1a['pegawai']) . "<br/>";
            }
        }

        if (!empty($level1b['team'])) {
            $output .= "<span class='small'><br/>Disposisi Lainnya:<br/>";

            // Group teams and members
            $teamMembers = [];
            foreach ($disposisi_satu as $disposisi) {
                if ($disposisi->level_disposisi === '1b') {
                    $teamName = $disposisi->teame->panggilan_team ?? '[Tim Tidak Ada]';
                    $pegawaiName = $disposisi->pegawaie->nama ?? '[Nama Tidak Ada]';
                    // Group members under their respective teams
                    $teamMembers[$teamName][] = $pegawaiName;
                }
            }

            // Build output for each team
            foreach ($teamMembers as $teamName => $members) {
                $output .= "Tim <strong>{$teamName}</strong><br/>";
                $output .= "+ " . implode("<br/>+ ", $members) . "<br/>";
            }

            $output .= "</span>";
        }

        $rincian_disposisi = $output ?: '[belum didisposisikan]';
        $header = '
            <div class="row">
                <div class="col-sm-12">
                    <div class="table-responsive">
                        <table class="table table-sm align-self-end ' . ((!Yii::$app->user->isGuest && Yii::$app->user->identity->theme == 0) ? '' : 'table-dark') . '">
                            <tbody>
                                <tr>
                                    <td class="col-sm-3">Tanggal Terima Surat</td>
                                    <td>: ' . Yii::$app->formatter->asDatetime(strtotime($suratmasuk->tanggal_diterima), "d MMMM y") . '</td>
                                </tr>                            
                                <tr>
                                    <td>Dari</td>
                                    <td>: ' . $suratmasuk->pengirim_suratmasuk . '</td>
                                </tr>
                                <tr>
                                    <td>Nomor</td>
                                    <td>: ' . $suratmasuk->nomor_suratmasuk .  '</td>
                                </tr>                                
                                <tr>
                                    <td>Sifat Surat</td>
                                    <td>: ' . (($suratmasuk->sifat == 0) ? '<span title="Biasa" class="badge bg-primary rounded-pill"><i class="fas fa-scroll"></i> Biasa</span>' : (($suratmasuk->sifat == 1) ? '<span title="Terbatas" class="badge bg-success rounded-pill"><i class="fas fa-star"></i> Terbatas</span>' :
            '<span title="Rahasia" class="badge bg-danger rounded-pill"><i class="fas fa-key"></i> Rahasia</span>'
        )) .  '</td>
                                </tr>
                                <tr>
                                    <td>Tanggal pada Surat</td>
                                    <td>: ' . Yii::$app->formatter->asDatetime(strtotime($suratmasuk->tanggal_suratmasuk), "d MMMM y") . '</td>
                                </tr>
                                <tr>
                                    <td>Perihal Surat</td>
                                    <td>: ' . $suratmasuk->perihal_suratmasuk . '</td>
                                </tr>
                                <tr>
                                    <td></td>
                                    <td>' . $rincian_disposisi . '</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            ';

        return $header;
    }

    public function actionBeriDisposisi($id, $level)
    {
        $model = new Suratmasukdisposisi();
        $suratmasuk = $this->findModel($id);
        $header = $this->getHeaderdisposisi($id);
        if ($level == 1 && (!Yii::$app->user->identity->issuratmasukpejabat || $suratmasuk->fk_suratmasukpejabat != Yii::$app->user->identity->username)) {
            Yii::$app->session->setFlash('warning', "Maaf. Anda tidak dapat memberikan disposisi level ini.");
            return $this->redirect(['index?year=&from=&for=']);
        }
        $disposisisatu_penerima = Suratmasukdisposisi::find()
            ->where([
                'fk_suratmasuk' => $id,
                'deleted' => 0
            ])
            ->andWhere([
                'or',
                ['level_disposisi' => '1a'],
                ['level_disposisi' => '1b']
            ])
            ->andWhere([
                'tujuan_disposisi_pegawai' => Yii::$app->user->identity->username,
            ])
            ->one();
        // die(var_dump($disposisisatu_penerima));
        if (
            ($level == '2a' ||  $level == '2b')
            && Yii::$app->user->identity->isteamleader
            && (substr($disposisisatu_penerima->level_disposisi, -1) != substr($level, -1) || empty($disposisisatu_penerima))
        ) { //ketua tim bukan penerima disposisi pertama
            Yii::$app->session->setFlash('warning', "Maaf. Anda tidak dapat memberikan disposisi level ini.");
            return $this->redirect(['index?year=&from=&for=']);
        }

        $disposisisatu = Suratmasukdisposisi::find()
            ->joinWith('pemberie')
            ->where(['fk_suratmasuk' => $id, 'deleted' => 0])
            ->andWhere([
                'or',
                ['level_disposisi' => '1a'],
                ['level_disposisi' => '1b']
            ])->one();
        if ($this->request->isPost) {
            $model->load($this->request->post());
            $model->pemberi_disposisi = Yii::$app->user->identity->username;

            if (Yii::$app->user->identity->issuratmasukpejabat && empty($disposisisatu_penerima)) {
                $model->level_disposisi = $level . 'a';
                $teamleader = Teamleader::findOne(['fk_team' => $model->tujuan_disposisi_team, 'leader_status' => 1]);
                $model->tujuan_disposisi_pegawai = $teamleader->nama_teamleader;
            } else {
                $model->level_disposisi = $level;
            }

            if (substr($model->level_disposisi, -1) == 'a') //disposisi lainnya tidak perlu status penyelesaian
                $model->status_penyelesaian = 0;

            $model->fk_suratmasuk = $id;

            if ($model->validate() && $model->save()) {
                /* PENGIRIMAN NOTIFIKASI WA BLAST UNTUK PENERIMA DISPOSISI UTAMA */
                if ($model->tujuan_disposisi_pegawai != Yii::$app->user->identity->username) {//tidak ada notifikasi untuk diri sendiri (ketua tim)
                    $penerima_disposisi = Pengguna::findOne(['username' => $model->tujuan_disposisi_pegawai]);
                    $pemberi_disposisi = Pengguna::findOne(['username' => $model->pemberi_disposisi]);
                    $nomor_tujuan = $penerima_disposisi->nomor_hp;
                    $nama_penerima_disposisi = $penerima_disposisi->nama;
                    $isi_notif = '*Portal Pintar - WhatsApp Notification Blast*
    
    Bapak/Ibu ' . $nama_penerima_disposisi . ', dari Tim *' . $model->teame->nama_team .  '*, Anda menerima ' . (substr($model->level_disposisi, -1) == 'a' ? '*disposisi utama*' : '*tembusan disposisi*') . ' pada surat masuk nomor *' . $suratmasuk->nomor_suratmasuk . '* dari Bapak/Ibu *' . $pemberi_disposisi->nama . '* di Sistem Portal Pintar, ' . Yii::$app->params['webhostingSatker'] . 'portalpintar/suratmasuk/' . $id . '
    Terima kasih.
                
    _#pesan ini dikirim oleh Portal Pintar dan tidak perlu dibalas_';

                    $response = AgendaController::wa_engine($nomor_tujuan, $isi_notif);

                    /* NOTIFIKASI SISTEM UNTUK PENERIMA DISPOSISI UTAMA */
                    \app\models\Notification::createNotification(
                        $model->tujuan_disposisi_pegawai,
                        'Anda menerima <strong>disposisi utama</strong> pada surat masuk nomor <strong>' . $suratmasuk->nomor_suratmasuk . '</strong> 
                         dari Bapak/Ibu <strong> ' . $pemberi_disposisi->nama . '</strong>
                         pada Tim <strong>' . $model->teame->nama_team . '</strong>.',
                        Yii::$app->controller->id,
                        $id
                    );
                }

                /*PENYIMPANAN DATA DISPOSISI LAINNYA */
                $selectedTeams = $model->tujuan_disposisi_team_lain; // This is an array of selected `fk_team` values

                if (!empty($selectedTeams)) {
                    foreach ($selectedTeams as $teamId) {
                        $newModel = new Suratmasukdisposisi();
                        $newModel->pemberi_disposisi = Yii::$app->user->identity->username;

                        $teamleader = Teamleader::findOne(['fk_team' => $teamId, 'leader_status' => 1]);
                        if ($teamleader) {
                            $newModel->tujuan_disposisi_pegawai = $teamleader->nama_teamleader;
                        }

                        $newModel->level_disposisi = $level . 'b';
                        $newModel->fk_suratmasuk = $id;
                        $newModel->tanggal_disposisi = $model->tanggal_disposisi;
                        $newModel->tujuan_disposisi_team = $teamId;
                        $newModel->instruksi = $model->instruksi;
                        // die(var_dump($newModel));
                        // Save each new record
                        if (!$newModel->save()) {
                            Yii::$app->session->setFlash('error', 'Failed to save disposisi for team: ' . $teamleader->nama_teamleader);
                            break; // Stop the loop if saving fails
                        }

                        /* PENGIRIMAN NOTIFIKASI WA BLAST UNTUK PENERIMA DISPOSISI LAINNYA */
                        $penerima_disposisi_lain = Pengguna::findOne(['username' => $newModel->tujuan_disposisi_pegawai]);
                        $nomor_tujuan_lain = $penerima_disposisi_lain->nomor_hp;
                        $nama_penerima_disposisi_lain = $penerima_disposisi_lain->nama;
                        $isi_notif_lain = '*Portal Pintar - WhatsApp Notification Blast*

Bapak/Ibu ' . $nama_penerima_disposisi_lain . ', dari Tim *' . $model->teame->nama_team .  '*, Anda menerima *tembusan disposisi* pada surat masuk nomor *' . $suratmasuk->nomor_suratmasuk . '* dari Bapak/Ibu *' . $pemberi_disposisi->nama . '* di Sistem Portal Pintar, ' . Yii::$app->params['webhostingSatker'] . 'portalpintar/suratmasuk/' . $id . '
Terima kasih.
        
_#pesan ini dikirim oleh Portal Pintar dan tidak perlu dibalas_';

                        $response = AgendaController::wa_engine($nomor_tujuan_lain, $isi_notif_lain);

                        /* NOTIFIKASI SISTEM UNTUK PENERIMA DISPOSISI UTAMA */
                        \app\models\Notification::createNotification(
                            $newModel->tujuan_disposisi_pegawai,
                            'Anda menerima <strong>tembusan disposisi</strong> pada surat masuk nomor <strong>' . $suratmasuk->nomor_suratmasuk . '</strong> 
                             dari Bapak/Ibu <strong> ' . $pemberi_disposisi->nama . '</strong>
                             pada Tim <strong>' . $newModel->teame->nama_team . '</strong>.',
                            Yii::$app->controller->id,
                            $id
                        );
                    }
                    Yii::$app->session->setFlash('success', 'Disposisi berhasil ditambahkan. Terima kasih.');
                }
                return $this->redirect(['view', 'id' => $suratmasuk->id_suratmasuk]);
            } else {
                // Check for errors
                // print_r($model->errors);
            }
        } else {
            $model->loadDefaultValues();
        }

        return $this->render('kelola-disposisi', [
            'model' => $model,
            'suratmasuk' => $suratmasuk,
            'header' => $header,
            'level' => $level,
            'disposisisatu' => $disposisisatu,
            'disposisisatu_penerima' => $disposisisatu_penerima
        ]);
    }

    public function actionEditDisposisi($id)
    {
        $model = Suratmasukdisposisi::find()
            ->where(['fk_suratmasuk' => $id, 'pemberi_disposisi' => Yii::$app->user->identity->username, 'deleted' => 0])
            ->andWhere([
                'or',
                ['level_disposisi' => '1a'],
                ['level_disposisi' => '2a'],
                ['level_disposisi' => '2b']
            ])
            ->one();

        $disposisilain = Suratmasukdisposisi::find()
            ->where(['fk_suratmasuk' => $id, 'pemberi_disposisi' => Yii::$app->user->identity->username, 'deleted' => 0])
            ->andWhere([
                'or',
                ['level_disposisi' => '1b'],
                ['level_disposisi' => '2b']
            ])
            ->all();

        $pemberi_disposisi = Suratmasukdisposisi::find()
            ->where(['fk_suratmasuk' => $id, 'pemberi_disposisi' => Yii::$app->user->identity->username, 'deleted' => 0])
            ->andWhere([
                'or',
                ['level_disposisi' => '1a'],
                ['level_disposisi' => '2a'],
                ['level_disposisi' => '2b']
            ])
            ->count();

        $disposisidua = Suratmasukdisposisi::find()
            ->where(['fk_suratmasuk' => $id, 'pemberi_disposisi' => Yii::$app->user->identity->username, 'deleted' => 0])
            ->andWhere([
                'or',
                ['level_disposisi' => '2a'],
                ['level_disposisi' => '2b']
            ])
            ->all();

        $disposisisatu_penerima = Suratmasukdisposisi::find()
            ->where([
                'fk_suratmasuk' => $id,
                'deleted' => 0
            ])
            ->andWhere([
                'or',
                ['level_disposisi' => '1a'],
                ['level_disposisi' => '1b']
            ])
            ->andWhere([
                'tujuan_disposisi_pegawai' => Yii::$app->user->identity->username,
            ])
            ->one();
        //cek disposisi level 1a
        $level = Suratmasukdisposisi::find()
            ->where(['fk_suratmasuk' => $id, 'pemberi_disposisi' => Yii::$app->user->identity->username, 'deleted' => 0])
            ->andWhere([
                'or',
                ['level_disposisi' => '1a'],
            ])
            ->count();

        if ($level > 0)
            $level = 1;
        else
        if (count($disposisidua) > 0)
            $level = $disposisidua[0]['level_disposisi'];
        else {
            Yii::$app->session->setFlash('warning', "Maaf. Anda tidak dapat memberikan disposisi surat ini.");
            return $this->redirect(['index?year=&from=&for=']);
        }

        $status_penyelesaian = Suratmasukdisposisi::find()
            ->where(['fk_suratmasuk' => $id, 'status_penyelesaian' => 1, 'deleted' => 0])
            ->all();

        $suratmasuk = $this->findModel($id);

        if ($pemberi_disposisi < 1) {
            Yii::$app->session->setFlash('warning', "Maaf. Hanya pemberi disposisi terkait yang dapat mengubah disposisinya.");
            return $this->redirect(['index?year=&from=&for=']);
        }
        if (count($status_penyelesaian) > 0) {
            Yii::$app->session->setFlash('warning', "Maaf. Disposisi ini telah selesai dilaksanakan oleh pelaksana terkait.");
            return $this->redirect(['index?year=&from=&for=']);
        }
        if (count($disposisidua) > 0 && Yii::$app->user->identity->issuratmasukpejabat && empty($disposisisatu_penerima)) {
            Yii::$app->session->setFlash('warning', "Maaf. Disposisi surat ini sudah didisposisi di level Ketua Tim dan tidak dapat diubah kembali.");
            return $this->redirect(['index?year=&from=&for=']);
        }

        $header = $this->getHeaderdisposisi($id);

        $disposisisatu = Suratmasukdisposisi::findOne(['fk_suratmasuk' => $id, 'level_disposisi' => '1a', 'deleted' => 0]);

        if ($this->request->isPost) {
            $postedData = $this->request->post();
            $model->load($postedData);

            $model->pemberi_disposisi = Yii::$app->user->identity->username;
            $pemberi_disposisi = Pengguna::findOne(['username' => $model->pemberi_disposisi]);

            if (Yii::$app->user->identity->issuratmasukpejabat && empty($disposisisatu_penerima)) {
                $model->level_disposisi = $level . 'a';
                $teamleader = Teamleader::findOne(['fk_team' => $model->tujuan_disposisi_team, 'leader_status' => 1]);
                $model->tujuan_disposisi_pegawai = $teamleader->nama_teamleader;
            } else {
                $model->level_disposisi = $level;
            }

            date_default_timezone_set('Asia/Jakarta');
            $model->timestamp_lastupdate = date('Y-m-d H:i:s');

            // Check if the user has changed the 'tujuan_disposisi_team', saat user is pejabat
            $postedTeam = $postedData['Suratmasukdisposisi']['tujuan_disposisi_team'] ?? null;
            $currentTeam = $model->getOldAttribute('tujuan_disposisi_team'); // Fetch the original value from the database

            // saat user is ketua tim
            $postedPegawai = $postedData['Suratmasukdisposisi']['tujuan_disposisi_pegawai'] ?? null;
            $currentPegawai = $model->getOldAttribute('tujuan_disposisi_pegawai'); // Fetch the original value from the database

            if ($model->validate() && $model->save()) {
                if ($postedTeam != $currentTeam || $postedPegawai != $currentPegawai) {
                    /* PENGIRIMAN NOTIFIKASI WA BLAST UNTUK PENERIMA DISPOSISI UTAMA YANG BARU */
                    $penerima_disposisi_baru = Pengguna::findOne(['username' => $model->tujuan_disposisi_pegawai]);
                    $nomor_tujuan_baru = $penerima_disposisi_baru->nomor_hp;
                    $nama_penerima_disposisi_baru = $penerima_disposisi_baru->nama;
                    $isi_notif_baru = '*Portal Pintar - WhatsApp Notification Blast*

Bapak/Ibu ' . $nama_penerima_disposisi_baru . ', dari Tim *' . $model->teame->nama_team .  '*, Anda menerima *disposisi utama* pada surat masuk nomor *' . $suratmasuk->nomor_suratmasuk . '* dari Bapak/Ibu *' . $pemberi_disposisi->nama . '* di Sistem Portal Pintar, ' . Yii::$app->params['webhostingSatker'] . 'portalpintar/suratmasuk/' . $id . '
Terima kasih.

_#pesan ini dikirim oleh Portal Pintar dan tidak perlu dibalas_';

                    $response = AgendaController::wa_engine($nomor_tujuan_baru, $isi_notif_baru);

                    /* NOTIFIKASI SISTEM UNTUK PENERIMA DISPOSISI UTAMA YANG BARU*/
                    \app\models\Notification::createNotification(
                        $model->tujuan_disposisi_pegawai,
                        'Anda menerima <strong>disposisi utama</strong> pada surat masuk nomor <strong>' . $suratmasuk->nomor_suratmasuk . '</strong> 
                                         dari Bapak/Ibu <strong> ' . $pemberi_disposisi->nama . '</strong>
                                         pada Tim <strong>' . $model->teame->nama_team . '</strong>.',
                        Yii::$app->controller->id,
                        $id
                    );

                    /* PENGIRIMAN NOTIFIKASI WA BLAST UNTUK PEMBATALAN DISPOSISI YANG LAMA */
                    $pembatalan_disposisi_lama = Pengguna::findOne(['username' => $currentPegawai]);
                    $nomor_tujuan_batal_lama = $pembatalan_disposisi_lama->nomor_hp;
                    $nama_pembatalan_disposisi_lama = $pembatalan_disposisi_lama->nama;
                    $isi_notif_lama = '*Portal Pintar - WhatsApp Notification Blast*

Bapak/Ibu ' . $nama_pembatalan_disposisi_lama . ', dari Tim *' . $model->teame->nama_team .  '*, *disposisi utama* pada surat masuk nomor *' . $suratmasuk->nomor_suratmasuk . '* dari Bapak/Ibu *' . $pemberi_disposisi->nama . '* untuk Anda, *dibatalkan* di Sistem Portal Pintar, ' . Yii::$app->params['webhostingSatker'] . 'portalpintar/suratmasuk/' . $id . '
Terima kasih.

_#pesan ini dikirim oleh Portal Pintar dan tidak perlu dibalas_';

                    $response = AgendaController::wa_engine($nomor_tujuan_batal_lama, $isi_notif_lama);

                    /* NOTIFIKASI SISTEM UNTUK PEMBATALAN DISPOSISI YANG LAMA */
                    $oldTeam = Team::findOne(['id_team' => $currentTeam]);
                    \app\models\Notification::createNotification(
                        $model->getOldAttribute('tujuan_disposisi_pegawai'),
                        '<strong>Disposisi utama</strong> pada surat masuk nomor <strong>' . $suratmasuk->nomor_suratmasuk . '</strong> 
                     dari Bapak/Ibu <strong> ' . $pemberi_disposisi->nama . '</strong>
                     untuk Anda pada Tim <strong>' . $oldTeam->nama_team . ' dibatalkan</strong>.',
                        Yii::$app->controller->id,
                        $id
                    );
                }

                /*PENYIMPANAN DATA PENERIMA DISPOSISI LAINNYA */
                $selectedTeams = $model->tujuan_disposisi_team_lain; // This is an array of selected `fk_team` values

                // Fetch all existing records for this suratmasuk and level_disposisi
                $existingRecords = Suratmasukdisposisi::find()
                    ->where([
                        'fk_suratmasuk' => $id,
                        'level_disposisi' => $level . 'b',
                        'pemberi_disposisi' => Yii::$app->user->identity->username,
                        'deleted' => 0,
                    ])
                    ->all();

                // Handle updates and additions
                if (!empty($selectedTeams)) {
                    foreach ($selectedTeams as $teamId) {
                        // Check if a record already exists for the given team
                        $existingModels = Suratmasukdisposisi::find()
                            ->where([
                                'fk_suratmasuk' => $id,
                                'level_disposisi' => $level . 'b',
                                'tujuan_disposisi_team' => $teamId,
                                'pemberi_disposisi' => Yii::$app->user->identity->username,
                                'deleted' => 0,
                            ])
                            ->all();

                        if (!empty($existingModels)) {
                            // Update each matching record
                            foreach ($existingModels as $existingModel) {
                                $existingModel->instruksi = $model->instruksi;
                                $existingModel->tanggal_disposisi = $model->tanggal_disposisi;
                                $existingModel->timestamp_lastupdate = date('Y-m-d H:i:s');

                                if (!$existingModel->save()) {
                                    Yii::$app->session->setFlash('error', 'Failed to update disposisi for team: ' . $teamId);
                                    break 2; // Stop the loop if saving fails
                                }
                            }
                        } else {
                            // Create a new record if no matching records are found
                            $newModel = new Suratmasukdisposisi();
                            $newModel->pemberi_disposisi = Yii::$app->user->identity->username;
                            $newModel->level_disposisi = $level . 'b';
                            $newModel->fk_suratmasuk = $id;
                            $newModel->tanggal_disposisi = $model->tanggal_disposisi;
                            $newModel->tujuan_disposisi_team = $teamId;
                            $newModel->instruksi = $model->instruksi;
                            $newModel->timestamp_lastupdate = date('Y-m-d H:i:s');

                            // Get team leader details
                            $teamleader = Teamleader::findOne(['fk_team' => $teamId, 'leader_status' => 1]);
                            if ($teamleader) {
                                $newModel->tujuan_disposisi_pegawai = $teamleader->nama_teamleader;
                            }

                            if (!$newModel->save()) {
                                Yii::$app->session->setFlash('error', 'Failed to create disposisi for team: ' . $teamId);
                                break; // Stop the loop if saving fails
                            }

                            /* PENGIRIMAN NOTIFIKASI WA BLAST UNTUK PENERIMA DISPOSISI LAINNYA */
                            $penerima_disposisi_lain = Pengguna::findOne(['username' => $newModel->tujuan_disposisi_pegawai]);
                            $nomor_tujuan_lain = $penerima_disposisi_lain->nomor_hp;
                            $nama_penerima_disposisi_lain = $penerima_disposisi_lain->nama;
                            $isi_notif_lain = '*Portal Pintar - WhatsApp Notification Blast*

Bapak/Ibu ' . $nama_penerima_disposisi_lain . ', dari Tim *' . $model->teame->nama_team .  '*, Anda menerima *tembusan disposisi* pada surat masuk nomor *' . $suratmasuk->nomor_suratmasuk . '* dari Bapak/Ibu *' . $pemberi_disposisi->nama . '* di Sistem Portal Pintar, ' . Yii::$app->params['webhostingSatker'] . 'portalpintar/suratmasuk/' . $id . '
Terima kasih.
        
_#pesan ini dikirim oleh Portal Pintar dan tidak perlu dibalas_';

                            $response = AgendaController::wa_engine($nomor_tujuan_lain, $isi_notif_lain);

                            /* NOTIFIKASI SISTEM UNTUK PENERIMA DISPOSISI UTAMA */
                            \app\models\Notification::createNotification(
                                $newModel->tujuan_disposisi_pegawai,
                                'Anda menerima <strong>tembusan disposisi</strong> pada surat masuk nomor <strong>' . $suratmasuk->nomor_suratmasuk . '</strong> 
                             dari Bapak/Ibu <strong> ' . $pemberi_disposisi->nama . '</strong>
                             pada Tim <strong>' . $newModel->teame->nama_team . '</strong>.',
                                Yii::$app->controller->id,
                                $id
                            );
                        }
                    }
                }

                // Handle deletions for records not in $selectedTeams
                foreach ($existingRecords as $existingRecord) {
                    // die(var_dump($selectedTeams));
                    if ((!empty($selectedTeams) && !in_array($existingRecord->tujuan_disposisi_team, $selectedTeams)) || empty($selectedTeams)) {
                        // die(var_dump($selectedTeams));
                        $existingRecord->deleted = 1; // Mark as deleted (soft delete)
                        $existingRecord->timestamp_lastupdate = date('Y-m-d H:i:s');
                        if (!$existingRecord->save()) {
                            Yii::$app->session->setFlash('error', 'Failed to delete disposisi for team: ' . $existingRecord->tujuan_disposisi_team);
                        }

                        /* PENGIRIMAN NOTIFIKASI WA BLAST UNTUK PEMBATALAN DISPOSISI LAINNYA */
                        $pembatalan_disposisi_lain = Pengguna::findOne(['username' => $existingRecord->tujuan_disposisi_pegawai]);
                        $nomor_tujuan_batal_lain = $pembatalan_disposisi_lain->nomor_hp;
                        $nama_pembatalan_disposisi_lain = $pembatalan_disposisi_lain->nama;
                        $isi_notif_lain = '*Portal Pintar - WhatsApp Notification Blast*

Bapak/Ibu ' . $nama_pembatalan_disposisi_lain . ', dari Tim *' . $model->teame->nama_team .  '*, *tembusan disposisi* pada surat masuk nomor *' . $suratmasuk->nomor_suratmasuk . '* dari Bapak/Ibu *' . $pemberi_disposisi->nama . '* untuk Anda, *dibatalkan* di Sistem Portal Pintar, ' . Yii::$app->params['webhostingSatker'] . 'portalpintar/suratmasuk/' . $id . '
Terima kasih.
    
_#pesan ini dikirim oleh Portal Pintar dan tidak perlu dibalas_';

                        $response = AgendaController::wa_engine($nomor_tujuan_batal_lain, $isi_notif_lain);

                        /* NOTIFIKASI SISTEM UNTUK PEMBATALAN DISPOSISI UTAMA */
                        \app\models\Notification::createNotification(
                            $existingRecord->tujuan_disposisi_pegawai,
                            '<strong>Tembusan disposisi</strong> pada surat masuk nomor <strong>' . $suratmasuk->nomor_suratmasuk . '</strong> 
                         dari Bapak/Ibu <strong> ' . $pemberi_disposisi->nama . '</strong>
                         untuk Anda pada Tim <strong>' . $existingRecord->teame->nama_team . ' dibatalkan</strong>.',
                            Yii::$app->controller->id,
                            $id
                        );
                    }
                }

                Yii::$app->session->setFlash('success', 'Disposisi berhasil diperbaiki. Terima kasih.');
                return $this->redirect(['view', 'id' => $suratmasuk->id_suratmasuk]);
            } else {
                Yii::$app->session->setFlash('error', 'Gagal menyimpan perubahan. Silakan periksa kembali data yang dimasukkan.');
            }
        } else {
            $suratmasuk->loadDefaultValues();
        }

        return $this->render('kelola-disposisi', [
            'model' => $model,
            'suratmasuk' => $suratmasuk,
            'header' => $header,
            'level' => $level,
            'disposisisatu' => $disposisisatu,
            'disposisilain' => $disposisilain,
            'disposisisatu_penerima' => $disposisisatu_penerima
        ]);
    }

    public function actionLaporDisposisi($id)
    {
        $user = Yii::$app->user->identity;

        $model = Suratmasukdisposisi::find()
            ->joinWith(['suratmasuke', 'teame'])
            ->where([
                'fk_suratmasuk' => $id,
                'tujuan_disposisi_pegawai' => $user->username,
                'status_penyelesaian' => 0,
                'level_disposisi' => '2a',
                'suratmasukdisposisi.deleted' => 0
            ])->one();

        if (empty($model)) {
            Yii::$app->session->setFlash('warning', "Maaf. Disposisi telah selesai dilakukan. Jika belum, penyelesaiannya hanya dapat dilaporkan oleh penerima akhir dalam disposisi utama.");
            return $this->redirect(['index?year=&from=&for=']);
        }

        if ($this->request->isPost) {
            $model->load($this->request->post());
            if ($model->validate()) {
                $model->timestamp_lastupdate = date('Y-m-d H:i:s', strtotime('+7 hours'));
                $model->status_penyelesaian = 1;

                $disposisi_satu = Suratmasukdisposisi::find()
                    ->where([
                        'fk_suratmasuk' => $id,
                        'status_penyelesaian' => 0,
                        'level_disposisi' => '1a',
                        'suratmasukdisposisi.deleted' => 0
                    ])->one();

                $disposisi_satu->timestamp_lastupdate = date('Y-m-d H:i:s', strtotime('+7 hours'));
                $disposisi_satu->status_penyelesaian = 1;
                $disposisi_satu->laporan_penyelesaian = $model->laporan_penyelesaian;

                if ($model->save() && $disposisi_satu->save()) {
                    /* PENGIRIMAN WHATSAPP BLAST UNTUK PIMPINAN */
                    $reporter = \app\models\Pengguna::findOne($user->username);
                    $pemberi_disposisi_satu = Suratmasukdisposisi::find()
                        ->where([
                            'fk_suratmasuk' => $id,
                            'status_penyelesaian' => 1,
                            'level_disposisi' => '1a',
                            'suratmasukdisposisi.deleted' => 0
                        ])
                        ->one();

                    $pemberi_disposisi_satu_pengguna = \app\models\Pengguna::findOne($pemberi_disposisi_satu->pemberi_disposisi);
                    $penerima_disposisi_satu_pengguna = \app\models\Pengguna::findOne($pemberi_disposisi_satu->tujuan_disposisi_pegawai);

                    $isi_notif_disposisi_satu = '*Portal Pintar - WhatsApp Notification Blast*

Bapak/Ibu Pimpinan ' . $pemberi_disposisi_satu_pengguna->nama . ', disposisi surat masuk nomor *'  . $model->suratmasuke->nomor_suratmasuk  . '* yang Anda disposisikan ke *' . $penerima_disposisi_satu_pengguna->nama . '*, dari Tim *' . $model->teame->nama_team  . '* telah dilaporkan penyelesaiannya oleh anggota timnya, yaitu *' . $reporter->nama . '* di Sistem Informasi Portal Pintar, ' . Yii::$app->params['webhostingSatker'] . 'portalpintar/suratmasuk/' . $id .
                        '. Terima kasih.
            
_#pesan ini dikirim oleh Portal Pintar dan tidak perlu dibalas_';

                    $response = AgendaController::wa_engine($pemberi_disposisi_satu_pengguna->nomor_hp, $isi_notif_disposisi_satu);

                    /* NOTIFIKASI SISTEM UNTUK PIMPINAN */
                    \app\models\Notification::createNotification(
                        $pemberi_disposisi_satu->pemberi_disposisi,
                        'Disposisi surat masuk nomor <strong>' . $model->suratmasuke->nomor_suratmasuk . '</strong> 
                         yang Anda disposisikan ke <strong> ' . $penerima_disposisi_satu_pengguna->nama . '</strong>
                         dari Tim <strong>' . $model->teame->nama_team . '</strong>
                         telah dilaporkan penyelesaiannya oleh anggota timnya, yaitu <strong>' . $reporter->nama . '</strong>.',
                        Yii::$app->controller->id,
                        $id
                    );

                    /* PENGIRIMAN WHATSAPP BLAST UNTUK KETUA TIM */
                    $isi_notif_disposisi_dua = '*Portal Pintar - WhatsApp Notification Blast*

Bapak/Ibu ' . $penerima_disposisi_satu_pengguna->nama . ', Ketua Tim *' . $model->teame->nama_team  . '*, disposisi surat masuk nomor *'  . $model->suratmasuke->nomor_suratmasuk  . '* yang Anda terima dari *' . $pemberi_disposisi_satu_pengguna->nama . '* dan Anda disposisikan ke *' . $reporter->nama . '*, telah dilaporkan penyelesaiannya yang bersangkutan di Sistem Informasi Portal Pintar, ' . Yii::$app->params['webhostingSatker'] . 'portalpintar/suratmasuk/' . $id .
                        '. Terima kasih.
                                
_#pesan ini dikirim oleh Portal Pintar dan tidak perlu dibalas_';

                    $response = AgendaController::wa_engine($penerima_disposisi_satu_pengguna->nomor_hp, $isi_notif_disposisi_dua);

                    /* NOTIFIKASI SISTEM UNTUK KETUA TIM */
                    \app\models\Notification::createNotification(
                        $pemberi_disposisi_satu->tujuan_disposisi_pegawai,
                        'Disposisi surat masuk nomor <strong>' . $model->suratmasuke->nomor_suratmasuk . '</strong> 
                         yang Anda disposisikan ke <strong> ' . $reporter->nama . '</strong>
                         pada Tim <strong>' . $model->teame->nama_team . '</strong>
                         telah dilaporkan penyelesaiannya oleh yang bersangkutan.',
                        Yii::$app->controller->id,
                        $id
                    );

                    if (strpos($response, 'Error:') !== false) {
                        Yii::$app->session->setFlash('error', "Penyelesaian disposisi berhasil dilaporkan tapi notifikasi WA gagal dikirimkan. Error: " . $response);
                        return $this->redirect(['view', 'id' => $id]);
                    } else {
                        Yii::$app->session->setFlash('success', "Penyelesaian disposisi berhasil dilaporkan dan notifikasi WA sudah dikirimkan. Terima kasih.");
                        return $this->redirect(['view', 'id' => $id]);
                    }
                }
            }
        }
        if (Yii::$app->request->isAjax) {
            return $this->renderAjax('lapor-disposisi', [
                'model' => $model,
            ]);
        } else {
            return $this->render('lapor-disposisi', [
                'model' => $model,
            ]);
        }
    }
}
