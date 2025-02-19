<?php

use app\models\Suratmasukdisposisi;
use yii\helpers\Html;
use yii\web\View;
use kartik\grid\SerialColumn;
use kartik\grid\ActionColumn;
use kartik\grid\GridView;

$this->title = 'Surat Masuk dan Disposisi';

$this->registerCssFile(Yii::$app->request->baseUrl . '/library/css/fi-agenda-index.css', ['position' => View::POS_HEAD, 'depends' => [\yii\web\JqueryAsset::class]]);
?>
<?php
$ada = $dataProvider->getModels();
?>
<div class="container-fluid" data-aos="fade-up">
    <h1 class="text-center"><?= Html::encode($this->title) ?></h1>
    <hr class="bps" />
    <p>
    <div class="d-flex justify-content-between" style="margin-bottom: -0.8rem;">
        <div class="p-2">
        </div>
        <div class="p-2">
        </div>
        <div class="p-2">
            <?php
            $homeUrl = ['agenda/index?owner=&year=' . date("Y") . '&nopage=0'];
            echo Html::a('<i class="fas fa-home"></i> Agenda Utama', $homeUrl, ['class' => 'btn btn btn-outline-warning btn-sm']);
            ?>
            <?php if (!Yii::$app->user->isGuest && (Yii::$app->user->identity->level == 0 || Yii::$app->user->identity->approver_mobildinas == 1 || Yii::$app->user->identity->issekretaris)) : ?>
                |
                <?= Html::a('<i class="fas fa-folder-plus"></i> Tambah Data Baru', ['create'], ['class' => 'btn btn btn-outline-warning btn-sm']) ?>
            <?php endif; ?>
        </div>
    </div>
    </p>
    <?php if ($ada == NULL) : ?>
        <div class="card text-center <?= ((!Yii::$app->user->isGuest && Yii::$app->user->identity->theme == 0) ? '' : 'bg-dark') ?>">
            <div class="card-body">
                <h2><em>Belum Ada Agenda di Tahun <?php echo date("Y") ?> <br /> atau di Pencarian yang Anda Maksud</em></h2>
                <hr />
                <?= Html::a('<i class="fas fa-file-archive"></i> Klik untuk Lihat Arsip Surat Masuk', ['suratmasuk/index?year=&from=&for='], ['class' => 'btn btn ' . ((!Yii::$app->user->isGuest && Yii::$app->user->identity->theme == 0) ? 'btn-outline-dark' : 'btn-outline-light') . ' btn-lg']) ?>
            </div>
        </div>
    <?php else : ?>
        <?php echo $this->render('_search', ['model' => $searchModel]);
        ?>
        <?php
        function checkVisibility($model)
        {
            $penerima_disposisi =  Suratmasukdisposisi::find()->select(['tujuan_disposisi_pegawai'])->where(['fk_suratmasuk' => $model['id_suratmasuk']])->column();
            if (
                !Yii::$app->user->isGuest && (
                    $model['sifat'] == 0
                    || (
                        (
                            Yii::$app->user->identity->issuratmasukpejabat
                            || in_array(Yii::$app->user->identity->username, $penerima_disposisi)
                            || Yii::$app->user->identity->username === $model['reporter']
                        )
                        && $model['sifat'] !== 0))
            )
                return true;
            else
                return false;
        }

        ?>
        <div class="card <?= ((!Yii::$app->user->isGuest && Yii::$app->user->identity->theme == 0) ? '' : 'bg-dark') ?>">
            <div class="card-body table-responsive p-0">
                <?php
                $layout = '
                        <div class="card-header ' . (!Yii::$app->user->isGuest ? Yii::$app->user->identity->themechoice : '') . '">
                            <div class="d-flex justify-content-between" style="margin-bottom: -0.8rem;">
                                <div class="p-2">
                                {toolbar}
                                {custom}
                                </div>
                                <div class="p-2" style="margin-top:0.5rem;">
                                {summary}
                                </div>
                                <div class="p-2">
                                {pager}
                                </div>
                            </div>                            
                        </div>  
                        {items}
                    ';
                ?>
                <?= GridView::widget([
                    'dataProvider' => $dataProvider,
                    'tableOptions' => ['class' => 'table table-condensed ' . ((!Yii::$app->user->isGuest && Yii::$app->user->identity->theme == 0) ? '' : 'table-dark')],
                    'columns' => [
                        [
                            'class' => SerialColumn::class,
                        ],
                        [
                            'attribute' => 'tanggal_diterima',
                            'value' => function ($model) {
                                $checkVisibility = checkVisibility($model);
                                if ($checkVisibility == true)
                                    return \Yii::$app->formatter->asDatetime(strtotime($model->tanggal_diterima), "d MMMM y");
                                else
                                    return '';
                            },
                            'vAlign' => 'middle',
                        ],
                        [
                            'attribute' => 'pengirim_suratmasuk',
                            'value' => function ($model) {
                                $checkVisibility = checkVisibility($model);
                                if ($checkVisibility == true)
                                    return $model->pengirim_suratmasuk;
                                else
                                    return '';
                            },
                            'vAlign' => 'middle',
                        ],
                        [
                            'attribute' => 'tanggal_suratmasuk',
                            'value' => function ($model) {
                                $checkVisibility = checkVisibility($model);
                                if ($checkVisibility == true)
                                    return \Yii::$app->formatter->asDatetime(strtotime($model->tanggal_suratmasuk), "d MMMM y");
                                else
                                    return '';
                            },
                            'vAlign' => 'middle',
                        ],
                        [
                            'attribute' => 'nomor_suratmasuk',
                            'value' => function ($model) {
                                $checkVisibility = checkVisibility($model);
                                if ($checkVisibility == true)
                                    return $model->nomor_suratmasuk;
                                else
                                    return '';
                            },
                            'vAlign' => 'middle',
                        ],
                        [
                            'attribute' => 'sifat',
                            'value' => function ($data) {
                                $checkVisibility = checkVisibility($data);
                                if ($checkVisibility == true) {
                                    if ($data->sifat == 0)
                                        return '<center><span title="Biasa" class="badge bg-primary rounded-pill"><i class="fas fa-scroll"></i> Biasa</span></center>';
                                    elseif ($data->sifat == 1)
                                        return '<center><span title="Terbatas" class="badge bg-success rounded-pill"><i class="fas fa-star"></i> Terbatas</span></center>';
                                    elseif ($data->sifat == 2)
                                        return '<center><span title="Rahasia" class="badge bg-danger rounded-pill"><i class="fas fa-key"></i> Rahasia</span></center>';
                                    else
                                        return '';
                                } else
                                    return '';
                            },
                            'header' => 'Sifat',
                            'enableSorting' => false,
                            'format' => 'html',
                            'vAlign' => 'middle',
                            'hAlign' => 'center'
                        ],
                        [
                            'attribute' => 'perihal_suratmasuk',
                            'value' => function ($model) {
                                $checkVisibility = checkVisibility($model);
                                if ($checkVisibility == true)
                                    return $model->perihal_suratmasuk;
                                else
                                    return '';
                            },
                            'vAlign' => 'middle',
                        ],
                        [
                            'attribute' => 'fk_suratmasukpejabat',
                            'value' => function ($model) {
                                $checkVisibility = checkVisibility($model);
                                if ($checkVisibility == true)
                                    return $model->pejabate->nama;
                                else
                                    return '';
                            },
                            'vAlign' => 'middle',
                        ],
                        [
                            'label' => 'Tujuan Disposisi',
                            'value' => function ($model) {
                                $checkVisibility = checkVisibility($model);
                                if ($checkVisibility == true) {
                                    // Initialize arrays with required keys
                                    $level1a = ['team' => [], 'pegawai' => []];
                                    $level2a = ['team' => [], 'pegawai' => []];
                                    $level1b = ['team' => [], 'pegawai' => []];
                                    $level2b = ['team' => [], 'pegawai' => []];

                                    // Categorize dispositions
                                    foreach ($model->suratmasukdisposisie as $disposisi) {
                                        if ($disposisi->deleted === 0) { // Only include non-deleted records
                                            if ($disposisi->level_disposisi == '1a') {
                                                $level1a['team'][] = $disposisi->teame->panggilan_team ?? '';
                                                $level1a['pegawai'][] = $disposisi->pegawaie->nama ?? '';
                                            } elseif ($disposisi->level_disposisi == '2a') {
                                                $level2a['team'][] = $disposisi->teame->panggilan_team ?? '';
                                                $level2a['pegawai'][] = $disposisi->pegawaie->nama ?? '';
                                            }
                                            if ($disposisi->level_disposisi == '1b') {
                                                $level1b['team'][] = $disposisi->teame->panggilan_team ?? '';
                                                $level1b['pegawai'][] = $disposisi->pegawaie->nama ?? '';
                                            } elseif ($disposisi->level_disposisi == '2b') {
                                                $level2b['team'][] = $disposisi->teame->panggilan_team ?? '';
                                                $level2b['pegawai'][] = $disposisi->pegawaie->nama ?? '';
                                            }
                                        }
                                    }

                                    $output = '';
                                    if (!empty($model['suratmasukdisposisie'])) {
                                        // Format the output with grouping
                                        $pemberi_disposisi =  app\models\Pengguna::findOne(['username' => $model['suratmasukdisposisie'][0]['pemberi_disposisi']]);

                                        $output = 'Pemberi Disposisi: <br/><strong>' . $pemberi_disposisi->nama . '</strong><br/><br/>';
                                        if (!empty($level1a['team'])) {
                                            if ($level1a['pegawai'] == $level2a['pegawai']) //disposisi di ketua tim saja
                                            {
                                                $output .= "Disposisi Utama: <br/>Tim <strong>" . implode("<br/>+", $level1a['team']) . "</strong><br/> ";
                                                if (!empty($level1a['pegawai'])) {
                                                    $output .= "+ " . implode("<br/>+ ", $level1a['pegawai']) . "<br/>";
                                                }
                                            } else {
                                                $output .= "Disposisi Utama: <br/>Tim <strong>" . implode("<br/>+", $level1a['team']) . "</strong><br/> ";
                                                if (!empty($level1a['pegawai'])) {
                                                    $output .= "+ " . implode("<br/>+ ", $level1a['pegawai']) . "<br/>";
                                                }
                                                if (!empty($level2a['pegawai'])) {
                                                    $output .= "+ " . implode("<br/>+ ", $level2a['pegawai']) . "<br/>";
                                                }
                                            }
                                        }

                                        if (!empty($level1b['team'])) {
                                            $output .= "<span class='small'><br/>Disposisi Lainnya:<br/>";

                                            // Group teams and members
                                            $teamMembers = [];
                                            if ($level1b['pegawai'] == $level2b['pegawai']) //disposisi di ketua tim saja
                                            {
                                                foreach ($model->suratmasukdisposisie as $disposisi) {
                                                    if ($disposisi->deleted === 0) { // Only include non-deleted records
                                                        if ($disposisi->level_disposisi === '2b') {
                                                            $teamName = $disposisi->teame->panggilan_team ?? '[Tim Tidak Ada]';
                                                            $pegawaiName = $disposisi->pegawaie->nama ?? '[Nama Tidak Ada]';

                                                            // Group members under their respective teams
                                                            $teamMembers[$teamName][] = $pegawaiName;
                                                        }
                                                    }
                                                }
                                            } else {
                                                foreach ($model->suratmasukdisposisie as $disposisi) {
                                                    if ($disposisi->deleted === 0) { // Only include non-deleted records
                                                        if ($disposisi->level_disposisi === '1b' || $disposisi->level_disposisi === '2b') {
                                                            $teamName = $disposisi->teame->panggilan_team ?? '[Tim Tidak Ada]';
                                                            $pegawaiName = $disposisi->pegawaie->nama ?? '[Nama Tidak Ada]';

                                                            // Group members under their respective teams
                                                            $teamMembers[$teamName][] = $pegawaiName;
                                                        }
                                                    }
                                                }
                                            }


                                            // Build output for each team
                                            foreach ($teamMembers as $teamName => $members) {
                                                $output .= "Tim <strong>{$teamName}</strong><br/>";
                                                $output .= "+ " . implode("<br/>+ ", $members) . "<br/><br/>";
                                            }

                                            $output .= "</span>";
                                        }
                                    }
                                    return $output ?: '[belum didisposisikan]';
                                } else {
                                    return '';
                                }
                            },
                            'format' => 'html',
                            'vAlign' => 'middle',
                        ],
                        [
                            'class' => ActionColumn::class,
                            'header' => 'Aksi',
                            'template' => '{update}{view}{delete}',
                            'visibleButtons' => [
                                'delete' => function ($model, $key, $index) {
                                    $disposisi = Suratmasukdisposisi::findOne(['fk_suratmasuk' => $model['id_suratmasuk']]);
                                    return (!Yii::$app->user->isGuest && Yii::$app->user->identity->username === $model['reporter'] && empty($disposisi)) ? true : false;
                                },
                                'update' => function ($model, $key, $index) {
                                    $disposisi = Suratmasukdisposisi::findOne(['fk_suratmasuk' => $model['id_suratmasuk']]);
                                    return (!Yii::$app->user->isGuest && Yii::$app->user->identity->username === $model['reporter'] && empty($disposisi)) ? true : false;
                                },
                                'view' => function ($model, $key, $index) {
                                    $penerima_disposisi =  Suratmasukdisposisi::find()->select(['tujuan_disposisi_pegawai'])->where(['fk_suratmasuk' => $model['id_suratmasuk']])->column();
                                    return (
                                        !Yii::$app->user->isGuest && (
                                            $model['sifat'] == 0
                                            || (
                                                (
                                                    Yii::$app->user->identity->issuratmasukpejabat
                                                    || in_array(Yii::$app->user->identity->username, $penerima_disposisi)
                                                    || Yii::$app->user->identity->username === $model['reporter']
                                                )
                                                && $model['sifat'] !== 0)
                                        )) ? true : false;
                                },
                            ],
                            'buttons'  => [
                                'delete' => function ($url, $model, $key) {
                                    return Html::a('<i class="fas text-danger fa-trash-alt"></i> ', $url, [
                                        'title' => 'Hapus data surat ini',
                                        'data-method' => 'post',
                                        'data-pjax' => 0,
                                        'data-confirm' => 'Anda yakin ingin menghapus data surat ini? <br/><strong>' . $model['nomor_suratmasuk'] . ' dari ' . $model['pengirim_suratmasuk'] . '</strong>'
                                    ]);
                                },
                                'update' => function ($key, $client) {
                                    return Html::a('<i class="fa">&#xf044;</i> ', $key, ['title' => 'Update rincian menghapus data surat ini']);
                                },
                                'view' => function ($key, $client) {
                                    return Html::a('<i class="fas fa-eye"></i> ', $key, [
                                        'title' => 'Lihat rincian data surat ini',
                                        'data-bs-toggle' => 'modal',
                                        'data-bs-target' => '#exampleModal',
                                        'class' => 'modal-link',
                                    ]);
                                },
                            ],
                        ],
                        [
                            'class' => ActionColumn::class,
                            'header' => 'Kelola Disposisi',
                            'template' => '{kelola-disposisi} {lapor-disposisi}',
                            'visibleButtons' => [
                                'kelola-disposisi' => function ($model, $key, $index) {
                                    $user = Yii::$app->user->identity;

                                    // Fetch disposisi data
                                    $disposisisatu_a = Suratmasukdisposisi::find()->where([
                                        'fk_suratmasuk' => $model['id_suratmasuk'],
                                        'level_disposisi' => '1a',
                                        'tujuan_disposisi_pegawai' => $user->username,
                                        'deleted' => 0
                                    ])->count();
                                    // die(var_dump($disposisisatu_a));

                                    $disposisisatu_b = Suratmasukdisposisi::find()->where([
                                        'fk_suratmasuk' => $model['id_suratmasuk'],
                                        'level_disposisi' => '1b',
                                        'tujuan_disposisi_pegawai' => $user->username,
                                        'deleted' => 0
                                    ])->count();
                                    // die(var_dump($disposisisatu_b));

                                    $disposisidua_a = Suratmasukdisposisi::find()->where([
                                        'fk_suratmasuk' => $model['id_suratmasuk'],
                                        'level_disposisi' => '2a',
                                        'deleted' => 0
                                    ])->count();

                                    $disposisidua_b = Suratmasukdisposisi::find()->where([
                                        'fk_suratmasuk' => $model['id_suratmasuk'],
                                        'level_disposisi' => '2b',
                                        'deleted' => 0
                                    ])->count();

                                    $status_penyelesaian = Suratmasukdisposisi::find()->where(['fk_suratmasuk' =>  $model['id_suratmasuk'], 'status_penyelesaian' => 1, 'deleted' => 0])->count();

                                    if (
                                        !Yii::$app->user->isGuest
                                        && $user->isteamleader
                                        && ($disposisisatu_a > 0 || $disposisisatu_b > 0) // Disposisi level 2 is allowed if disposisi level 1 exists
                                    ) {
                                        return true;
                                    } elseif (
                                        !Yii::$app->user->isGuest
                                        && $user->issuratmasukpejabat
                                        && ($disposisidua_a == 0 || $disposisidua_b == 0) // Disposisi level 1 is allowed if disposisi level 2 does not exist
                                    ) {
                                        return true;
                                    } elseif ($status_penyelesaian > 0 || $model['fk_suratmasukpejabat'] != Yii::$app->user->identity->username) {
                                        return false;
                                    } else {
                                        return false;
                                    }
                                },
                                'lapor-disposisi' => function ($model, $key, $index) {
                                    $user = Yii::$app->user->identity;

                                    $disposisidua_a = Suratmasukdisposisi::find()->where([
                                        'fk_suratmasuk' => $model['id_suratmasuk'],
                                        'tujuan_disposisi_pegawai' => $user->username,
                                        'status_penyelesaian' => 0,
                                        'level_disposisi' => '2a',
                                        'deleted' => 0
                                    ])->count();

                                    if ($disposisidua_a > 0) {
                                        return true;
                                    } else {
                                        return false;
                                    }
                                },
                            ],
                            'buttons' => [
                                'kelola-disposisi' => function ($url, $model, $key) {
                                    // Determine the level based on the user's identity
                                    $level = 1; // Default to level 1
                                    $actionUrl = ''; // Default URL to index
                                    if (Yii::$app->user->identity->issuratmasukpejabat && $model->fk_suratmasukpejabat == Yii::$app->user->identity->username) {
                                        $disposisisatu_semua = Suratmasukdisposisi::find()
                                            ->where([
                                                'fk_suratmasuk' => $model['id_suratmasuk'],
                                                'deleted' => 0
                                            ])
                                            ->andWhere([
                                                'or',
                                                ['level_disposisi' => '1a'],
                                                ['level_disposisi' => '1b']
                                            ])
                                            ->all();
                                        $disposisisatu_pemilik = Suratmasukdisposisi::find()
                                            ->where([
                                                'fk_suratmasuk' => $model['id_suratmasuk'],
                                                'deleted' => 0
                                            ])
                                            ->andWhere([
                                                'or',
                                                ['level_disposisi' => '1a'],
                                                ['level_disposisi' => '1b']
                                            ])
                                            ->andWhere([
                                                'pemberi_disposisi' => Yii::$app->user->identity->username,
                                            ])
                                            ->all();
                                        $disposisisatu_kabagumum = Suratmasukdisposisi::find()
                                            ->where([
                                                'fk_suratmasuk' => $model['id_suratmasuk'],
                                                'deleted' => 0
                                            ])
                                            ->andWhere([
                                                'or',
                                                ['level_disposisi' => '1a'],
                                                ['level_disposisi' => '1b']
                                            ])
                                            ->andWhere([
                                                'pemberi_disposisi' => Yii::$app->user->identity->username,
                                            ])
                                            ->andWhere([
                                                'tujuan_disposisi_pegawai' => Yii::$app->user->identity->username,
                                            ])
                                            ->count();
                                        $disposisidua = Suratmasukdisposisi::find()
                                            ->where([
                                                'fk_suratmasuk' => $model['id_suratmasuk'],
                                                'deleted' => 0
                                            ])
                                            ->andWhere([
                                                'or',
                                                ['level_disposisi' => '2a'],
                                                ['level_disposisi' => '2b']
                                            ])
                                            ->all();
                                        $level = 1;
                                        // var_dump($disposisisatu_kabagumum);
                                        if (count($disposisisatu_pemilik) > 0 && count($disposisidua) < 1 && $disposisisatu_pemilik[0]['tujuan_disposisi_pegawai'] != Yii::$app->user->identity->username) // sudah beri disposisi 1 dan belum ada disposisi 2
                                            $actionUrl = Yii::$app->urlManager->createUrl([
                                                'suratmasuk/edit-disposisi',
                                                'id' => $model->id_suratmasuk,
                                            ]);
                                        elseif (count($disposisisatu_semua) > 0 && $disposisisatu_kabagumum > 0)
                                            $actionUrl = Yii::$app->urlManager->createUrl([
                                                'suratmasuk/beri-disposisi',
                                                'id' => $model->id_suratmasuk,
                                                'level' => '2a',
                                            ]);
                                        elseif (count($disposisisatu_semua) < 1)
                                            $actionUrl = Yii::$app->urlManager->createUrl([
                                                'suratmasuk/beri-disposisi',
                                                'id' => $model->id_suratmasuk,
                                                'level' => $level,
                                            ]);
                                    } elseif (Yii::$app->user->identity->isteamleader) {
                                        $disposisisatu_penerima = Suratmasukdisposisi::find()
                                            ->where([
                                                'fk_suratmasuk' => $model['id_suratmasuk'],
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
                                            ->all();
                                        $disposisidua_pemberi = Suratmasukdisposisi::find()
                                            ->where([
                                                'fk_suratmasuk' => $model['id_suratmasuk'],
                                                'deleted' => 0
                                            ])
                                            ->andWhere([
                                                'or',
                                                ['level_disposisi' => '2a'],
                                                ['level_disposisi' => '2b']
                                            ])
                                            ->andWhere([
                                                'pemberi_disposisi' => Yii::$app->user->identity->username,
                                            ])
                                            ->all();
                                        $level = 2;
                                        if (count($disposisidua_pemberi) > 0)
                                            $actionUrl = Yii::$app->urlManager->createUrl([
                                                'suratmasuk/edit-disposisi',
                                                'id' => $model->id_suratmasuk, // Ensure $model contains the necessary ID
                                            ]);
                                        elseif (count($disposisisatu_penerima) > 0) {
                                            $level_disposisi = $disposisisatu_penerima[0]['level_disposisi'];

                                            $actionUrl = Yii::$app->urlManager->createUrl([
                                                'suratmasuk/beri-disposisi',
                                                'id' => $model->id_suratmasuk, // Ensure $model contains the necessary ID
                                                'level' => $level . substr($level_disposisi, -1),
                                            ]);
                                        }
                                    }
                                    // die(var_dump($actionUrl));
                                    // Return the button
                                    if ($actionUrl != '')
                                        return Html::a('<i class="fas fa-user-md"></i>', $actionUrl, [
                                            'title' => 'Lakukan disposisi pada surat ini',
                                        ]);
                                },
                                'lapor-disposisi' => function ($key, $client) {
                                    return Html::a('<i class="fas fa-user-check text-success"></i> ', $key, [
                                        'title' => 'Laporkan pengerjaan disposisi ini',
                                        'data-bs-toggle' => 'modal',
                                        'data-bs-target' => '#exampleModal',
                                        'class' => 'modal-link',
                                    ]);
                                },
                            ],
                        ],
                        [
                            'header' => 'Progress',
                            'value' => function ($model) {
                                $penerima_disposisi = Suratmasukdisposisi::find()->select(['tujuan_disposisi_pegawai'])->where(['fk_suratmasuk' => $model['id_suratmasuk']])->column();
                                $level_disposisi = Suratmasukdisposisi::find()
                                    ->select(['status_penyelesaian'])
                                    ->where(['fk_suratmasuk' => $model['id_suratmasuk']])
                                    ->andWhere(['level_disposisi' => '2a'])
                                    ->andWhere(['deleted' => '0'])
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

                                    return '<span title="Selesai Dilaksanakan" class="badge bg-primary rounded-pill"><i class="fas fa-check"></i> Selesai Dilaksanakan</span> <br/> <small>oleh ' . $penerima_akhir_disposisi_utama->pegawaie->nama . '</small>';
                                } else {
                                    return '<span title="Belum Selesai" class="badge bg-danger rounded-pill"><i class="fas fa-times"></i> Belum Selesai</span>';
                                }
                            },
                            'enableSorting' => false,
                            'format' => 'html',
                            'vAlign' => 'middle',
                            'hAlign' => 'center'
                        ],
                    ],
                    'layout' => $layout,
                    'bordered' => false,
                    'striped' => false,
                    'condensed' => false,
                    'hover' => true,
                    'headerRowOptions' => ['class' => 'kartik-sheet-style'],
                    'filterRowOptions' => ['class' => 'kartik-sheet-style'],
                    'export' => [
                        'fontAwesome' => true,
                        'label' => '<i class="fa">&#xf56d;</i>',
                        'pjax' => false,
                    ],
                    'exportConfig' => [
                        GridView::CSV => ['label' => 'CSV', 'filename' => 'Surat Masuk dan Disposisi di Portal Pintar - ' . date('d-M-Y')],
                        GridView::HTML => ['label' => 'HTML', 'filename' => 'Surat Masuk dan Disposisi di Portal Pintar - ' . date('d-M-Y')],
                        GridView::EXCEL => ['label' => 'EXCEL', 'filename' => 'Surat Masuk dan Disposisi di Portal Pintar - ' . date('d-M-Y')],
                        GridView::TEXT => ['label' => 'TEXT', 'filename' => 'Surat Masuk dan Disposisi di Portal Pintar - ' . date('d-M-Y')],
                    ],
                    'pjax' => false,
                    'pjaxSettings' => [
                        'neverTimeout' => true,
                        'options' => ['id' => 'some_pjax_id'],
                    ],
                    'pager' => [
                        'firstPageLabel' => '<i class="fas fa-angle-double-left"></i>',
                        'lastPageLabel' => '<i class="fas fa-angle-double-right"></i>',
                        'prevPageLabel' => '<i class="fas fa-angle-left"></i>',   // Set the label for the "previous" page button
                        'nextPageLabel' => '<i class="fas fa-angle-right"></i>',
                        'maxButtonCount' => 10,
                    ],
                    'toggleDataOptions' => ['minCount' => 10],
                    'floatOverflowContainer' => true,
                    'floatHeader' => true,
                    'floatHeaderOptions' => [
                        'scrollingTop' => '0',
                        'position' => 'absolute',
                        'top' => 50
                    ],
                    'replaceTags' => [
                        '{custom}' => function () {
                            // you could call other widgets/custom code here
                            return '
                        <div class="btn-group">
                        ' .
                                Html::a('<span class="btn btn-success me-1"> Disposisi dari Saya</span>', 'index?from=' . Yii::$app->user->identity->username . '&for=&year=' . date("Y"), ['title' => 'Tampikan Disposisi untuk Anda', 'data-pjax' => 0])
                                .
                                Html::a('<span class="btn btn-outline-success me-1"> Disposisi untuk Saya</span>', 'index?for=' . Yii::$app->user->identity->username . '&from&year=' . date("Y"), ['title' => 'Tampikan Disposisi dari Anda', 'data-pjax' => 0])
                                .
                                Html::a('<span class="btn btn-warning me-1"> Semua', 'index?from=&for=&year=' . date("Y"), ['title' => 'Tampikan Semua Disposisi Semua', 'data-pjax' => 0])
                                .
                                '
                        </div>
                        ';
                        }
                    ]
                ]); ?>
            </div>
        </div>
    <?php endif; ?>

</div>