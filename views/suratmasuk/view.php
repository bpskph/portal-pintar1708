<?php

use app\models\Suratmasukdisposisi;
use yii\helpers\Html;
use kartik\detail\DetailView;

$this->title = "Detail Surat Masuk #" . $model->id_suratmasuk;
\yii\web\YiiAsset::register($this);
?>
<div class="container" data-aos="fade-up">
    <h1><?= Html::encode($this->title) ?></h1>

    <div class="d-flex justify-content-between">
        <div class="p-2">
            <?php $disposisi = Suratmasukdisposisi::findOne(['fk_suratmasuk' => $model['id_suratmasuk']]); ?>
            <?php if (Yii::$app->user->identity->approver_mobildinas === 1 || Yii::$app->user->identity->issekretaris || Yii::$app->user->identity->level == 0 && empty($disposisi)) : ?>
                <p>
                    <?= Html::a('<i class="fas fa-edit"></i> Update', ['update', 'id' => $model->id_suratmasuk], ['class' => 'btn btn-sm btn-warning']) ?>
                    <?= Html::a('Delete', ['delete', 'id' => $model->id_suratmasuk], [
                        'class' => 'btn btn-sm btn-danger',
                        'data' => [
                            'confirm' => 'Anda yakin akan menghapus data surat ini?',
                            'method' => 'post',
                        ],
                    ]) ?>
                </p>
            <?php endif; ?>
        </div>
        <div class="p-2">
            <?= Html::a('<i class="fas fa-car"></i> List Surat Masuk', ['index', 'year' => date("Y"), 'for' => '', 'from' => ''], ['class' => 'btn btn-outline-warning btn-sm']) ?>
        </div>
    </div>
    <?php
    function tujuanDisposisi($model)
    {
        // Initialize arrays with required keys
        $level1a = ['team' => [], 'pegawai' => [], 'instruksi' => []];
        $level2a = ['team' => [], 'pegawai' => [], 'instruksi' => []];
        $level1b = ['team' => [], 'pegawai' => [], 'instruksi' => []];
        $level2b = ['team' => [], 'pegawai' => [], 'instruksi' => []];

        // Categorize dispositions
        foreach ($model->suratmasukdisposisie as $disposisi) {
            if ($disposisi->deleted === 0) { // Only include non-deleted records
                if ($disposisi->level_disposisi == '1a') {
                    $level1a['team'][] = $disposisi->teame->panggilan_team ?? '';
                    $level1a['pegawai'][] = $disposisi->pegawaie->nama ?? '';
                    $level1a['instruksi'][] = $disposisi->instruksi ?? '';
                } elseif ($disposisi->level_disposisi == '2a') {
                    $level2a['team'][] = $disposisi->teame->panggilan_team ?? '';
                    $level2a['pegawai'][] = $disposisi->pegawaie->nama ?? '';
                    $level2a['instruksi'][] = $disposisi->instruksi ?? '';
                }
                if ($disposisi->level_disposisi == '1b') {
                    $level1b['team'][] = $disposisi->teame->panggilan_team ?? '';
                    $level1b['pegawai'][] = $disposisi->pegawaie->nama ?? '';
                    $level1b['instruksi'][] = $disposisi->instruksi ?? '';
                } elseif ($disposisi->level_disposisi == '2b') {
                    $level2b['team'][] = $disposisi->teame->panggilan_team ?? '';
                    $level2b['pegawai'][] = $disposisi->pegawaie->nama ?? '';
                    $level2b['instruksi'][] = $disposisi->instruksi ?? '';
                }
            }
        }
        // var_dump($model->suratmasukdisposisie);

        $output = '';
        if (!empty($model['suratmasukdisposisie'])) {
            $pemberi_disposisi =  app\models\Pengguna::findOne(['username' => $model['suratmasukdisposisie'][0]['pemberi_disposisi']]);
            // Format the output with grouping
            $output = "<h5><span class='badge bg-success'><strong>Instruksi Pimpinan: </strong><span></h5><i class='fas fa-user text-success'></i> <strong>" . $pemberi_disposisi->nama . "</strong> <br/> <i class='fas fa-arrow-circle-right text-success'></i> " . $model['suratmasukdisposisie'][0]['instruksi'] . "<br/><br/>";

            if (!empty($level1a['team'])) {
                // var_dump($level2a);
                if ($level1a['pegawai'] == $level2a['pegawai']) //disposisi di ketua tim saja
                {
                    $output .= "<h5><span class='badge bg-primary'>Disposisi Utama: </span></h5><span class='text-primary'><strong>Tim " . implode("<br/>+", $level1a['team']) . "</strong></span><br/> ";
                    if (!empty($level1a['pegawai'])) {
                        $output .= "<span class='badge bg-primary'>1 </span> " . implode("<br/>+ ", $level1a['pegawai']) . "<br/>";
                    }
                } else {
                    $output .= "<h5><span class='badge bg-primary'>Disposisi Utama: </span></h5><span class='text-primary'><strong>Tim " . implode("<br/>+", $level1a['team']) . "</strong></span><br/> ";
                    if (!empty($level1a['pegawai'])) {
                        $output .= "<span class='badge bg-primary'>1 </span> " . implode("<br/>+ ", $level1a['pegawai']) . "<br/>";
                    }
                    if (!empty($level2a['pegawai'])) {
                        $output .= "<span class='badge bg-primary'>2 </span>  " . implode("<br/>+ ", $level2a['pegawai']) . "<br/><span class='badge bg-primary'><i class='fas fa-arrow-circle-right'></i> </span> <small> Instruksi Ketua Tim: " . implode("<br/>+ ", $level2a['instruksi']) . "</small><br/>";
                    }
                }
            }

            if (!empty($level1b['team'])) {
                $output .= "<br/><h5><span class='badge bg-info'>Disposisi Lainnya: </span></h5>";

                // Group teams and members with their respective instructions
                $teamMembersWithInstruksi = [];

                if ($level1b['pegawai'] == $level2b['pegawai']) //disposisi di ketua tim saja
                {
                    foreach ($model->suratmasukdisposisie as $disposisi) {
                        if ($disposisi->deleted === 0) { // Only include non-deleted records
                            if ($disposisi->level_disposisi === '2b') {
                                $teamName = $disposisi->teame->panggilan_team ?? '[-]';
                                $pegawaiName = $disposisi->pegawaie->nama ?? '[-]';
                                $instruksi = $disposisi->instruksi ?? '[Belum Ada Instruksi]';

                                // Group members and their instructions under their respective teams
                                $teamMembersWithInstruksi[$teamName]['members'][] = $pegawaiName;

                                // Only set the instruction if it's a level2b disposition
                                if ($disposisi->level_disposisi === '2b') {
                                    $teamMembersWithInstruksi[$teamName]['instruksi'] = $instruksi;
                                }
                            }
                        }
                    }
                } else {
                    foreach ($model->suratmasukdisposisie as $disposisi) {
                        if ($disposisi->deleted === 0) { // Only include non-deleted records
                            if ($disposisi->level_disposisi === '1b' || $disposisi->level_disposisi === '2b') {
                                $teamName = $disposisi->teame->panggilan_team ?? '[-]';
                                $pegawaiName = $disposisi->pegawaie->nama ?? '[-]';
                                $instruksi = $disposisi->instruksi ?? '[Belum Ada Instruksi]';

                                // Group members and their instructions under their respective teams
                                $teamMembersWithInstruksi[$teamName]['members'][] = $pegawaiName;

                                // Only set the instruction if it's a level2b disposition
                                if ($disposisi->level_disposisi === '2b') {
                                    $teamMembersWithInstruksi[$teamName]['instruksi'] = $instruksi;
                                }
                            }
                        }
                    }
                }


                // Build output for each team
                foreach ($teamMembersWithInstruksi as $teamName => $details) {
                    $output .= "<span class='text-info'><strong>Tim {$teamName}</strong></span><br/>";
                    $output .= "<span class='badge bg-info'>1 </span> " . implode("<br/><span class='badge bg-info'>2 </span> ", $details['members']) . "<br/>";

                    // Display instruction if it exists for the team
                    if (!empty($details['instruksi'])) {
                        $output .= "<span class='badge bg-info'><i class='fas fa-arrow-circle-right'></i> </span> <small>Instruksi Ketua Tim: {$details['instruksi']}</small><br/><br/>";
                    } else {
                        $output .= "<span class='badge bg-info'><i class='fas fa-arrow-circle-right'></i> </span> <small>[Belum Ada Instruksi]</small><br/><br/>";
                    }
                }

                $output .= "</span>";
            }
        }
        return $output ?: '[belum didisposisikan]';
    }

    function statusDisposisi($model)
    {
        $penerima_disposisi = Suratmasukdisposisi::find()->select(['tujuan_disposisi_pegawai'])->where(['fk_suratmasuk' => $model['id_suratmasuk']])->column();
        $level_disposisi = Suratmasukdisposisi::find()
            ->select(['status_penyelesaian'])
            ->where(['fk_suratmasuk' => $model['id_suratmasuk']])
            ->andWhere([
                'or',
                ['level_disposisi' => '1a'],
                ['level_disposisi' => '2a']
            ])
            ->one();

        if (
            !Yii::$app->user->isGuest
            && !Yii::$app->user->identity->issekretaris
            && !Yii::$app->user->identity->issuratmasukpejabat
            && !in_array(Yii::$app->user->identity->username, $penerima_disposisi)
            && $model->sifat !== 0
        ) {
            return '';
        } elseif (!empty($level_disposisi) && $level_disposisi->status_penyelesaian === 1) {
            $penerima_akhir_disposisi_utama = Suratmasukdisposisi::find()
                ->joinWith('pegawaie')
                ->where(['fk_suratmasuk' => $model['id_suratmasuk'], 'deleted' => 0, 'level_disposisi' => '2a'])
                ->one();

            return '<span title="Selesai Dilaksanakan" class="badge bg-primary rounded-pill"><i class="fas fa-check"></i> Selesai Dilaksanakan</span><br/><small>oleh ' . $penerima_akhir_disposisi_utama->pegawaie->nama . '</small>'
                . '<br/><small><span class="text-primary fw-bold">Laporan: </span><br/>' . $penerima_akhir_disposisi_utama->laporan_penyelesaian . '</small>';
        } elseif (!empty($level_disposisi) && $level_disposisi->status_penyelesaian === 0) {
            return '<span title="Belum Selesai" class="badge bg-danger rounded-pill"><i class="fas fa-times"></i> Belum Selesai</span>';
        } elseif (!empty($level_disposisi) && $level_disposisi->status_penyelesaian === null) {
            return '-';
        } else {
            return '-';
        }
    }
    ?>
    <?= DetailView::widget([
        'model' => $model,
        'options' => ['class' => 'table ' . ((!Yii::$app->user->isGuest && Yii::$app->user->identity->theme == 0) ? '' : 'table-dark')],
        'condensed' => true,
        'striped' => (!Yii::$app->user->isGuest && Yii::$app->user->identity->theme == 0) ? true : false,
        'bordered' => false,
        'hover' => true,
        'hAlign' => 'left',
        'attributes' => [
            'id_suratmasuk',
            'pengirim_suratmasuk',
            'perihal_suratmasuk:ntext',
            [
                'attribute' => 'tanggal_diterima',
                'value' => \Yii::$app->formatter->asDatetime(strtotime($model->tanggal_diterima), "d MMMM y"),
            ],
            'nomor_suratmasuk',
            [
                'attribute' => 'tanggal_suratmasuk',
                'value' => \Yii::$app->formatter->asDatetime(strtotime($model->tanggal_suratmasuk), "d MMMM y"),
            ],
            [
                'attribute' => 'sifat',
                'value' => $model->sifat == 0 ? '<span title="Biasa" class="badge bg-primary rounded-pill"><i class="fas fa-scroll"></i> Biasa</span>' : ($model->sifat == 1 ? '<span title="Terbatas" class="badge bg-success rounded-pill"><i class="fas fa-warehouse"></i> Terbatas</span>' : '<span title="Rahasia" class="badge bg-danger rounded-pill"><i class="fas fa-key"></i> Rahasia</span>'),
                'format' => 'html',
                'label' => 'Sifat Surat',
            ],
            [
                'attribute' => 'fk_suratmasukpejabat',
                'value' => $model->pejabate->nama,
            ],
            [
                'label' => 'Tujuan Disposisi',
                'value' => tujuanDisposisi($model),
                'format' => 'html',
                'vAlign' => 'middle',
            ],
            [
                'label' => 'Status Penyelesaian',
                'value' => statusDisposisi($model),
                'format' => 'html',
                'vAlign' => 'middle',
            ],
            [
                'attribute' => 'reporter',
                'value' => $model->reportere->nama,
            ],
            [
                'attribute' => 'timestamp',
                'value' => \Yii::$app->formatter->asDatetime(strtotime($model->timestamp), "d MMMM y 'pada' H:mm a"),
            ],
            [
                'attribute' => 'timestamp_lastupdate',
                'value' => \Yii::$app->formatter->asDatetime(strtotime($model->timestamp_lastupdate), "d MMMM y 'pada' H:mm a"),
            ],
        ],
    ]) ?>
    <div class="card <?= ((!Yii::$app->user->isGuest && Yii::$app->user->identity->theme == 0) ? '' : 'bg-dark text-light') ?>">
        <div class="card-body">
            <h3>
                <span class="badge bg-primary">Berkas Surat</span>
            </h3>
            <?php if (file_exists(Yii::getAlias('@webroot/surat/masuk/' . $model->id_suratmasuk . '.pdf'))) : ?>
                <div class="text-center">
                    <div id="pdf-container container" data-aos="fade-up">
                        <h5 class="text-center mt-2 mb-2"><em>Jika tampilan file belum berubah (untuk upload ulang), <br /> lakukan clear cache pada browser Anda, atau lihat melalui Moda Privasi (Incognito). Terima kasih.</em></h5>
                        <iframe id="pdf-iframe" src="<?= Yii::getAlias('@web') ?>/surat/masuk/<?php echo $model->id_suratmasuk ?>.pdf" width="100%" height="700px"></iframe>
                    </div>
                </div>
            <?php else: ?>
                <div id="pdf-container container" data-aos="fade-up">
                    <h5 class="text-center mt-2 mb-2"><em>Berkas PDF belum tersedia (belum diunggah oleh penginput surat atau terhapus).<br />Jika berkas sudah diupload namun belum tampil, mohon lakukan clear cache pada browser Anda, atau lihat melalui Moda Privasi (Incognito). Terima kasih.</em></h5>
                </div>
            <?php endif; ?>
            <br />
        </div>
    </div>

</div>