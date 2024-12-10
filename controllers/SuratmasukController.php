<?php

namespace app\controllers;

use app\models\Suratmasuk;
use app\models\Suratmasukdisposisi;
use app\models\SuratmasukSearch;
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
                            'actions' => ['error', 'view', 'index'],
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
                            'actions' => [''], // add all actions to take guest to login page
                            'allow' => true,
                            'roles' => ['@'],
                        ],
                    ],
                ],
            ]
        );
    }
    public function actionIndex($year)
    {
        $searchModel = new SuratmasukSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

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
            $teamleader = Teamleader::findOne(['fk_team' => $model->tujuan_disposisi_team, 'leader_status' => 0]);
            $model->tujuan_disposisi_pegawai = $teamleader->nama_teamleader;

            if ($model->validate() && $model->save()) {
                $model->filepdf = UploadedFile::getInstance($model, 'filepdf');

                if ($model->filepdf && $model->id_suratmasuk && $model->filepdf->extension === 'pdf') {
                    if (file_exists(Yii::getAlias('@webroot/surat/masuk/' . $model->id_suratmasuk . '.pdf'))) {
                        unlink(Yii::getAlias('@webroot/surat/masuk/') . $model->id_suratmasuk . '.pdf');
                    }
                    if ($model->upload()) {
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
            return $this->redirect(['index?year=']);
        }
        if ($model->deleted != 0) {
            Yii::$app->session->setFlash('warning', "Maaf. Data surat yang telah dihapus tidak dapat diubah kembali.");
            return $this->redirect(['index?year=']);
        }
        $disposisi = Suratmasukdisposisi::findAll(['fk_suratmasuk' => $id]);
        if (!empty($disposisi)) {
            Yii::$app->session->setFlash('warning', "Maaf. Surat masuk yang telah melalui proses disposisi tidak dapat diubah kembali.");
            return $this->redirect(['index?year=']);
        }

        if ($this->request->isPost) {
            $model->load($this->request->post());
            date_default_timezone_set('Asia/Jakarta');
            $model->timestamp_lastupdate = date('Y-m-d H:i:s');

            if ($model->validate() && $model->save()) {
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
            return $this->redirect(['index?year=']);
        }
        if ($model->deleted != 0) {
            Yii::$app->session->setFlash('warning', "Maaf. Data surat yang telah dihapus.");
            return $this->redirect(['index?year=']);
        }
        $disposisi = Suratmasukdisposisi::findAll(['fk_suratmasuk' => $id]);
        if (!empty($disposisi)) {
            Yii::$app->session->setFlash('warning', "Maaf. Surat masuk yang telah melalui proses disposisi tidak dapat dihapus.");
            return $this->redirect(['index?year=']);
        }

        date_default_timezone_set('Asia/Jakarta');
        $affected_rows = Suratmasuk::updateAll(['deleted' => 1, 'timestamp_lastupdate' => date('Y-m-d H:i:s')], 'id_suratmasuk = "' . $id . '"');
        if ($affected_rows == 0) {
            Yii::$app->session->setFlash('warning', "Gagal. Mohon hubungi Admin.");
            return $this->redirect(['index?year=']);
        } else {
            Yii::$app->session->setFlash('success', "Data Surat Masuk berhasil dihapus. Terima kasih.");
            return $this->redirect(['index?year=']);
        }
    }
    protected function findModel($id_suratmasuk)
    {
        if (($model = Suratmasuk::findOne(['id_suratmasuk' => $id_suratmasuk])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    public function actionBeriDisposisi($id, $level)
    {
        $model = new Suratmasukdisposisi();
        $suratmasuk = $this->findModel($id);

        if ($level == 1 && !Yii::$app->user->identity->issuratmasukpejabat) {
            Yii::$app->session->setFlash('warning', "Maaf. Anda tidak dapat memberikan disposisi level ini.");
            return $this->redirect(['index?year=']);
        }
        if ($level == 2 && !Yii::$app->user->identity->isteamleader) {
            Yii::$app->session->setFlash('warning', "Maaf. Anda tidak dapat memberikan disposisi level ini.");
            return $this->redirect(['index?year=']);
        }
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
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            ';

        $disposisisatu = Suratmasukdisposisi::findOne(['fk_suratmasuk' => $id, 'level_disposisi' => 1, 'deleted' => 0]);
        if ($this->request->isPost) {
            $model->load($this->request->post());
            $model->pemberi_disposisi = Yii::$app->user->identity->username;
            $teamleader = Teamleader::findOne(['fk_team' => $model->tujuan_disposisi_team, 'leader_status' => 1]);
            $model->tujuan_disposisi_pegawai = $teamleader->nama_teamleader;
            $model->level_disposisi = $level . 'a';
            $model->status_penyelesaian = 0;
            $model->fk_suratmasuk = $id;

            if ($model->validate() && $model->save()) {
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

        return $this->render('beri-disposisi', [
            'model' => $model,
            'suratmasuk' => $suratmasuk,
            'header' => $header,
            'level' => $level,
            'disposisisatu' => $disposisisatu
        ]);
    }

    public function actionEditDisposisi($id)
    {
        $model = Suratmasukdisposisi::find()
            ->where(['fk_suratmasuk' => $id, 'pemberi_disposisi' => Yii::$app->user->identity->username, 'deleted' => 0])
            ->andWhere([
                'or',
                ['level_disposisi' => '1a'],
                ['level_disposisi' => '2a']
            ])
            ->asArray();

        $pemberi_disposisi = Suratmasukdisposisi::find()
            ->where(['fk_suratmasuk' => $id, 'pemberi_disposisi' => Yii::$app->user->identity->username, 'deleted' => 0])
            ->andWhere([
                'or',
                ['level_disposisi' => '1a'],
                ['level_disposisi' => '2a']
            ])
            ->count();

        $disposisidua = Suratmasukdisposisi::find()
            ->where(['fk_suratmasuk' => $id, 'deleted' => 0])
            ->andWhere([
                'or',
                ['level_disposisi' => '2a'],
                ['level_disposisi' => '2b']
            ])
            ->all();
        $status_penyelesaian = Suratmasukdisposisi::find()
            ->where(['fk_suratmasuk' => $id, 'status_penyelesaian' => 1, 'deleted' => 0])
            ->all();

        $suratmasuk = $this->findModel($id);

        if ($pemberi_disposisi < 1) {
            Yii::$app->session->setFlash('warning', "Maaf. Hanya pemberi disposisi terkait yang dapat mengubah disposisinya.");
            return $this->redirect(['index?year=']);
        }
        if (count($status_penyelesaian) > 0) {
            Yii::$app->session->setFlash('warning', "Maaf. Disposisi ini telah selesai dilaksanakan oleh pelaksana terkait.");
            return $this->redirect(['index?year=']);
        }
        if (count($disposisidua) > 0 && Yii::$app->user->identity->issuratmasukpejabat) {
            Yii::$app->session->setFlash('warning', "Maaf. Disposisi surat ini sudah didisposisi di level Ketua Tim dan tidak dapat diubah kembali.");
            return $this->redirect(['index?year=']);
        }

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
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            ';

        $disposisisatu = Suratmasukdisposisi::findOne(['fk_suratmasuk' => $id, 'level_disposisi' => '1a', 'deleted' => 0]);
        if ($this->request->isPost) {
            $model->load($this->request->post());
            date_default_timezone_set('Asia/Jakarta');
            $suratmasuk->timestamp_lastupdate = date('Y-m-d H:i:s');

            if ($suratmasuk->validate() && $suratmasuk->save()) {
                Yii::$app->session->setFlash('success', "Disposisi berhasil ditambahkan. Terima kasih.");
                return $this->redirect(['view', 'id' => $suratmasuk->id_suratmasuk]);
            } else {
                // Check for errors
                // print_r($model->errors);
            }
        } else {
            $suratmasuk->loadDefaultValues();
        }

        return $this->render('beri-disposisi', [
            'model' => $model,
            'suratmasuk' => $suratmasuk,
            'header' => $header,
            // 'level' => $level,
            'disposisisatu' => $disposisisatu
        ]);
    }
}
