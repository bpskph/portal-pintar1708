<?php

namespace app\controllers;

use app\models\Mobildinas;
use app\models\MobildinasSearch;
use DateTime;
use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * MobildinasController implements the CRUD actions for Mobildinas model.
 */
class MobildinasController extends BaseController
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
                            'actions' => ['setujui', 'tolak', 'batal'],
                            'allow' => true,
                            'matchCallback' => function ($rule, $action) {
                                return !\Yii::$app->user->isGuest && (\Yii::$app->user->identity->level === 0 || \Yii::$app->user->identity->approver_mobildinas === 1);
                            },
                        ],
                        [
                            'actions' => ['create', 'update', 'delete'], // add all actions to take guest to login page
                            'allow' => true,
                            'roles' => ['@'],
                        ],
                    ],
                ],
            ]
        );
    }

    /**
     * Lists all Mobildinas models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new MobildinasSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Mobildinas model.
     * @param int $id_mobildinas Id Mobildinas
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
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

    /**
     * Creates a new Mobildinas model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new Mobildinas();

        $searchModel = new MobildinasSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);
        $dataProvider->query
            ->andWhere(['>=', 'mulai', date('Y-m-d H:i:s')])
            ->andWhere(['<=', 'mulai', date('Y-m-d H:i:s', strtotime('+2 weeks'))])
            ->andWhere(['deleted' => '0']);
        $dataProvider->pagination = false;

        if ($this->request->isPost) {
            $model->load($this->request->post());
            $model->borrower = Yii::$app->user->identity->username;
            if (str_contains($_POST['Mobildinas']['mulai'], 'WIB')) {
                /* WAKTU SELESAI */
                // Get the datetime string from the $_POST variable
                $datetimeStr = $_POST['Mobildinas']['mulai'];
                // Parse the datetime string and convert it to a DateTime object
                $datetime = DateTime::createFromFormat('Y-m-d H:i T', $datetimeStr);
                // Convert the DateTime object to a string in the desired format
                $formattedDatetime = $datetime->format('Y-m-d H:i:s');
                $model->mulai = $formattedDatetime;
            } else {
                $model->mulai = date("Y-m-d H:i:s", strtotime($_POST['Mobildinas']['mulai']));
            }
            if (str_contains($_POST['Mobildinas']['selesai'], 'WIB')) {
                /* WAKTU SELESAI */
                // Get the datetime string from the $_POST variable
                $datetimeStr = $_POST['Mobildinas']['selesai'];
                // Parse the datetime string and convert it to a DateTime object
                $datetime = DateTime::createFromFormat('Y-m-d H:i T', $datetimeStr);
                // Convert the DateTime object to a string in the desired format
                $formattedDatetime = $datetime->format('Y-m-d H:i:s');
                $model->selesai = $formattedDatetime;
            } else {
                $model->selesai = date("Y-m-d H:i:s", strtotime($_POST['Mobildinas']['selesai']));
            }
            if ($model->validate() && $model->save()) {
                $approvers = \app\models\Pengguna::find()->where(['approver_mobildinas' => 1])->all();
                $borrower = \app\models\Pengguna::findOne($model->borrower);
                $formatter = Yii::$app->formatter;
                $formatter->locale = 'id-ID'; // set the locale to Indonesian
                $timezone = new \DateTimeZone('Asia/Jakarta'); // create a timezone object for WIB
                $waktumulai = new \DateTime($model->mulai, new \DateTimeZone('UTC')); // create a datetime object for waktumulai with UTC timezone
                $waktumulai->setTimeZone($timezone); // set the timezone to WIB
                $waktumulaiFormatted = $formatter->asDatetime($waktumulai, 'd MMMM Y, H:mm'); // format the waktumulai datetime value
                $waktuselesai = new \DateTime($model->selesai, new \DateTimeZone('UTC')); // create a datetime object for waktuselesai with UTC timezone
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
                foreach ($approvers as $approver) {
                    $userId = $approver->username;
                    /* PENGIRIMAN WHATSAPP BLAST */
                    $pengguna = \app\models\Pengguna::findOne($userId);

                    $isi_notif_wa = '*Portal Pintar 2.0 - WhatsApp Notification Blast*

Bapak/Ibu ' . $pengguna->nama . ', Terdapat usulan peminjaman mobil dinas untuk *' . $waktuFormatted  . '* dari * ' . $borrower->nama . '*.

_#pesan ini dikirim oleh Portal Pintar dan tidak perlu dibalas_';

                    $response = AgendaController::wa_engine($pengguna->nomor_hp, $isi_notif_wa);

                    \app\models\Notification::createNotification($userId, 'Terdapat usulan peminjaman mobil dinas untuk <strong>' . $waktuFormatted . '</strong> dari <strong>' . $borrower->nama . '</strong>', Yii::$app->controller->id, $model->id_mobildinas);
                }
                Yii::$app->session->setFlash('success', "Usulan peminjaman mobil dinas sudah ditambahkan dan notifikasi WA sudah dikirimkan. Terima kasih.");
                return $this->redirect(['view', 'id' => $model->id_mobildinas]);
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

    /**
     * Updates an existing Mobildinas model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id_mobildinas Id Mobildinas
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->borrower != Yii::$app->user->identity->username) {
            Yii::$app->session->setFlash('warning', "Maaf. Hanya pengusul peminjaman terkait yang dapat mengubah Permohonan Zoom Meeting.");
            return $this->redirect(['index']);
        }
        if ($model->deleted != 0) {
            Yii::$app->session->setFlash('warning', "Maaf. Usulan yang telah dihapus tidak dapat diubah kembali.");
            return $this->redirect(['index']);
        }
        if ($model->approval != 0) {
            Yii::$app->session->setFlash('warning', "Maaf. Usulan yang telah disetujui/ditolak/dibatalkan persetujuannya tidak dapat diubah kembali.");
            return $this->redirect(['index']);
        }

        $searchModel = new MobildinasSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);
        $dataProvider->query
            ->andWhere(['>=', 'mulai', date('Y-m-d H:i:s')])
            ->andWhere(['<=', 'mulai', date('Y-m-d H:i:s', strtotime('+2 weeks'))])
            ->andWhere(['deleted' => '0']);
        $dataProvider->pagination = false;

        if ($this->request->isPost) {
            $model->load($this->request->post());
            $model->timestamp_lastupdate = date('Y-m-d H:i:s');
            if (str_contains($_POST['Mobildinas']['mulai'], 'WIB')) {
                /* WAKTU SELESAI */
                // Get the datetime string from the $_POST variable
                $datetimeStr = $_POST['Mobildinas']['mulai'];
                // Parse the datetime string and convert it to a DateTime object
                $datetime = DateTime::createFromFormat('Y-m-d H:i T', $datetimeStr);
                // Convert the DateTime object to a string in the desired format
                $formattedDatetime = $datetime->format('Y-m-d H:i:s');
                $model->mulai = $formattedDatetime;
            } else {
                $model->mulai = date("Y-m-d H:i:s", strtotime($_POST['Mobildinas']['mulai']));
            }
            if (str_contains($_POST['Mobildinas']['selesai'], 'WIB')) {
                /* WAKTU SELESAI */
                // Get the datetime string from the $_POST variable
                $datetimeStr = $_POST['Mobildinas']['selesai'];
                // Parse the datetime string and convert it to a DateTime object
                $datetime = DateTime::createFromFormat('Y-m-d H:i T', $datetimeStr);
                // Convert the DateTime object to a string in the desired format
                $formattedDatetime = $datetime->format('Y-m-d H:i:s');
                $model->selesai = $formattedDatetime;
            } else {
                $model->selesai = date("Y-m-d H:i:s", strtotime($_POST['Mobildinas']['selesai']));
            }
            if ($model->validate()) {
                $model->timestamp_lastupdate = date('Y-m-d H:i:s');
                // die ($_POST['Mobildinas']['keperluan']);
                if ($_POST['Mobildinas']['keperluan'] == 6) {
                    if ($_POST['Mobildinas']['keperluan_lainnya'] == "") {
                        Yii::$app->session->setFlash('warning', "Mohon isikan keperluan lainnya yang dimaksud.");
                        return $this->render('update', [
                            'model' => $model,
                            'searchModel' => $searchModel,
                            'dataProvider' => $dataProvider,
                        ]);
                    }
                }
                if ($model->save()) {
                    Yii::$app->session->setFlash('success', "Usulan peminjaman mobil dinas berhasil diperbaiki. Terima kasih.");
                    return $this->redirect(['view', 'id' => $model->id_mobildinas]);
                }
            }
        }
        return $this->render('update', [
            'model' => $model,
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Deletes an existing Mobildinas model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $id_mobildinas Id Mobildinas
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id_mobildinas)
    {
        $affected_rows = Mobildinas::updateAll(['deleted' => 1, 'timestamp_lastupdate' => date('Y-m-d H:i:s')], 'id_mobildinas = "' . $id_mobildinas . '"');
        if ($affected_rows == 0) {
            Yii::$app->session->setFlash('warning', "Gagal. Mohon hubungi Admin.");
            return $this->redirect(['index']);
        } else {
            Yii::$app->session->setFlash('success', "Usulan pemijaman mobil dinas berhasil dihapus. Terima kasih.");
            return $this->redirect(['index']);
        }
    }

    /**
     * Finds the Mobildinas model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id_mobildinas Id Mobildinas
     * @return Mobildinas the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id_mobildinas)
    {
        if (($model = Mobildinas::findOne(['id_mobildinas' => $id_mobildinas])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    public function actionSetujui($id)
    {
        $model = $this->findModel($id);
        $affected_rows = Mobildinas::updateAll(['approval' => 1, 'timestamp_lastupdate' => date('Y-m-d H:i:s')], 'id_mobildinas = "' . $id . '"');
        if ($affected_rows == 0) {
            Yii::$app->session->setFlash('warning', "Gagal. Mohon hubungi Admin.");
            return $this->redirect(['view', 'id' => $model->id_mobildinas]);
        } else {
            $formatter = Yii::$app->formatter;
            $formatter->locale = 'id-ID'; // set the locale to Indonesian
            $timezone = new \DateTimeZone('Asia/Jakarta'); // create a timezone object for WIB
            $waktumulai = new \DateTime($model->mulai, new \DateTimeZone('UTC')); // create a datetime object for waktumulai with UTC timezone
            $waktumulai->setTimeZone($timezone); // set the timezone to WIB
            $waktumulaiFormatted = $formatter->asDatetime($waktumulai, 'd MMMM Y, H:mm'); // format the waktumulai datetime value
            $waktuselesai = new \DateTime($model->selesai, new \DateTimeZone('UTC')); // create a datetime object for waktuselesai with UTC timezone
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

            /* PENGIRIMAN WHATSAPP BLAST */
            $pengguna = \app\models\Pengguna::findOne($model->borrower);

            $isi_notif_wa = '*Portal Pintar 2.0 - WhatsApp Notification Blast*

Bapak/Ibu ' . $pengguna->nama . ', Pengajuan Anda untuk peminjaman mobil dinas untuk *' . $waktuFormatted  . '* sudah disetujui.

_#pesan ini dikirim oleh Portal Pintar dan tidak perlu dibalas_';

            $response = AgendaController::wa_engine($pengguna->nomor_hp, $isi_notif_wa);

            \app\models\Notification::createNotification($model->borrower, 'Pengajuan Anda untuk peminjaman mobil dinas untuk <strong>' . $waktuFormatted . '</strong> sudah disetujui.', Yii::$app->controller->id, $model->id_mobildinas);

            Yii::$app->session->setFlash('success', "Usulan peminjaman mobil dinas berhasil disetujui dan notifikasi WA sudah dikirimkan. Terima kasih.");
            return $this->redirect(['view', 'id' => $model->id_mobildinas]);
        }
    }

    public function actionTolak($id)
    {
        $model = $this->findModel($id);

        if (1 !== Yii::$app->user->identity->approver_mobildinas) {
            Yii::$app->session->setFlash('warning', "Maaf. Anda tidak memiliki hak akses dalam penolakan usulan peminjaman mobil dinas.");
            return $this->redirect(['index']);
        }
        if ($model->deleted != 0) {
            Yii::$app->session->setFlash('warning', "Maaf. Usulan yang telah dihapus tidak dapat diubah kembali.");
            return $this->redirect(['index']);
        }
        if ($model->approval != 0) {
            Yii::$app->session->setFlash('warning', "Maaf. Usulan yang telah disetujui/ditolak/dibatalkan persetujuannya tidak dapat diubah kembali.");
            return $this->redirect(['index']);
        }

        if ($this->request->isPost) {
            $model->load($this->request->post());
            if ($model->validate()) {
                $model->timestamp_lastupdate = date('Y-m-d H:i:s');
                $model->approval = 2;
                if ($model->save()) {
                    $formatter = Yii::$app->formatter;
                    $formatter->locale = 'id-ID'; // set the locale to Indonesian
                    $timezone = new \DateTimeZone('Asia/Jakarta'); // create a timezone object for WIB
                    $waktumulai = new \DateTime($model->mulai, new \DateTimeZone('UTC')); // create a datetime object for waktumulai with UTC timezone
                    $waktumulai->setTimeZone($timezone); // set the timezone to WIB
                    $waktumulaiFormatted = $formatter->asDatetime($waktumulai, 'd MMMM Y, H:mm'); // format the waktumulai datetime value
                    $waktuselesai = new \DateTime($model->selesai, new \DateTimeZone('UTC')); // create a datetime object for waktuselesai with UTC timezone
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

                    /* PENGIRIMAN WHATSAPP BLAST */
                    $pengguna = \app\models\Pengguna::findOne($model->borrower);

                    $isi_notif_wa = '*Portal Pintar 2.0 - WhatsApp Notification Blast*

Bapak/Ibu ' . $pengguna->nama . ', Pengajuan Anda untuk peminjaman mobil dinas untuk *' . $waktuFormatted  . '* ditolak.
            
_#pesan ini dikirim oleh Portal Pintar dan tidak perlu dibalas_';

                    $response = AgendaController::wa_engine($pengguna->nomor_hp, $isi_notif_wa);

                    \app\models\Notification::createNotification($model->borrower, 'Pengajuan Anda untuk peminjaman mobil dinas untuk <strong>' . $waktuFormatted . '</strong> ditolak.', Yii::$app->controller->id, $model->id_mobildinas);

                    Yii::$app->session->setFlash('success', "Usulan peminjaman mobil dinas berhasil ditolak dan notifikasi WA sudah dikirimkan. Terima kasih.");
                    return $this->redirect(['view', 'id' => $model->id_mobildinas]);
                }
            }
        }
        if (Yii::$app->request->isAjax) {
            return $this->renderAjax('tolak', [
                'model' => $this->findModel($id),
            ]);
        } else {
            return $this->render('tolak', [
                'model' => $this->findModel($id),
            ]);
        }
    }
    public function actionBatal($id)
    {
        $model = $this->findModel($id);

        if (1 !== Yii::$app->user->identity->approver_mobildinas) {
            Yii::$app->session->setFlash('warning', "Maaf. Anda tidak memiliki hak akses dalam pembatalan persetujuan peminjaman mobil dinas.");
            return $this->redirect(['index']);
        }
        if ($model->deleted != 0) {
            Yii::$app->session->setFlash('warning', "Maaf. Usulan yang telah dihapus tidak dapat diubah kembali.");
            return $this->redirect(['index']);
        }
        if ($model->approval == 0 || $model->approval == 2) {
            Yii::$app->session->setFlash('warning', "Maaf. Usulan yang telah belum disetujui atau sudah ditolak tidak dapat diubah kembali.");
            return $this->redirect(['index']);
        }

        if ($this->request->isPost) {
            $model->load($this->request->post());
            if ($model->validate()) {
                $model->timestamp_lastupdate = date('Y-m-d H:i:s');
                $model->approval = 3;
                if ($model->save()) {
                    $formatter = Yii::$app->formatter;
                    $formatter->locale = 'id-ID'; // set the locale to Indonesian
                    $timezone = new \DateTimeZone('Asia/Jakarta'); // create a timezone object for WIB
                    $waktumulai = new \DateTime($model->mulai, new \DateTimeZone('UTC')); // create a datetime object for waktumulai with UTC timezone
                    $waktumulai->setTimeZone($timezone); // set the timezone to WIB
                    $waktumulaiFormatted = $formatter->asDatetime($waktumulai, 'd MMMM Y, H:mm'); // format the waktumulai datetime value
                    $waktuselesai = new \DateTime($model->selesai, new \DateTimeZone('UTC')); // create a datetime object for waktuselesai with UTC timezone
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

                    /* PENGIRIMAN WHATSAPP BLAST */
                    $pengguna = \app\models\Pengguna::findOne($model->borrower);

                    $isi_notif_wa = '*Portal Pintar 2.0 - WhatsApp Notification Blast*
 
Bapak/Ibu ' . $pengguna->nama . ', Pengajuan Anda untuk peminjaman mobil dinas untuk *' . $waktuFormatted  . '* dibatalkan.
             
_#pesan ini dikirim oleh Portal Pintar dan tidak perlu dibalas_';

                    $response = AgendaController::wa_engine($pengguna->nomor_hp, $isi_notif_wa);

                    \app\models\Notification::createNotification($model->borrower, 'Pengajuan Anda untuk peminjaman mobil dinas untuk <strong>' . $waktuFormatted . '</strong> dibatalkan.', Yii::$app->controller->id, $model->id_mobildinas);

                    Yii::$app->session->setFlash('success', "Usulan peminjaman mobil dinas berhasil dibatalkan dan notifikasi WA sudah dikirimkan. Terima kasih.");
                    return $this->redirect(['view', 'id' => $model->id_mobildinas]);
                }
            }
        }
        if (Yii::$app->request->isAjax) {
            return $this->renderAjax('tolak', [
                'model' => $this->findModel($id),
            ]);
        } else {
            return $this->render('tolak', [
                'model' => $this->findModel($id),
            ]);
        }
    }
}
