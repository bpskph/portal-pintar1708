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
                <?= Html::a('<i class="fas fa-file-archive"></i> Klik untuk Lihat Arsip Surat Masuk', ['suratmasuk/index?year='], ['class' => 'btn btn ' . ((!Yii::$app->user->isGuest && Yii::$app->user->identity->theme == 0) ? 'btn-outline-dark' : 'btn-outline-light') . ' btn-lg']) ?>
            </div>
        </div>
    <?php else : ?>
        <?php echo $this->render('_search', ['model' => $searchModel]);
        ?>
        <div class="card <?= ((!Yii::$app->user->isGuest && Yii::$app->user->identity->theme == 0) ? '' : 'bg-dark') ?>">
            <div class="card-body table-responsive p-0">
                <?php
                $layout = '
                        <div class="card-header ' . (!Yii::$app->user->isGuest ? Yii::$app->user->identity->themechoice : '') . '">
                            <div class="d-flex justify-content-between" style="margin-bottom: -0.8rem;">
                                <div class="p-2">
                                {toolbar}
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
                                return \Yii::$app->formatter->asDatetime(strtotime($model->tanggal_diterima), "d MMMM y");
                            },
                            'vAlign' => 'middle',
                        ],
                        [
                            'attribute' => 'pengirim_suratmasuk',
                            'vAlign' => 'middle',
                        ],
                        [
                            'attribute' => 'tanggal_suratmasuk',
                            'value' => function ($model) {
                                return \Yii::$app->formatter->asDatetime(strtotime($model->tanggal_suratmasuk), "d MMMM y");
                            },
                            'vAlign' => 'middle',
                        ],
                        [
                            'attribute' => 'nomor_suratmasuk',
                            'vAlign' => 'middle',
                        ],
                        [
                            'attribute' => 'sifat',
                            'value' => function ($data) {
                                if ($data->sifat == 0)
                                    return '<center><span title="Biasa" class="badge bg-primary rounded-pill"><i class="fas fa-scroll"></i> Biasa</span></center>';
                                elseif ($data->sifat == 1)
                                    return '<center><span title="Terbatas" class="badge bg-success rounded-pill"><i class="fas fa-star"></i> Terbatas</span></center>';
                                elseif ($data->sifat == 2)
                                    return '<center><span title="Rahasia" class="badge bg-danger rounded-pill"><i class="fas fa-key"></i> Rahasia</span></center>';
                                else
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
                                ) {
                                    return $model->perihal_suratmasuk;
                                } else {
                                    return '';
                                }
                            },
                            'vAlign' => 'middle',
                        ],
                        [
                            'label' => 'Tujuan Disposisi',
                            'value' => function ($model) {
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
                                ) {
                                    // Initialize arrays with required keys
                                    $level1a = ['team' => [], 'pegawai' => []];
                                    $level2a = ['team' => [], 'pegawai' => []];
                                    $level1b = ['team' => [], 'pegawai' => []];
                                    $level2b = ['team' => [], 'pegawai' => []];

                                    // Categorize dispositions
                                    foreach ($model->suratmasukdisposisie as $disposisi) {
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

                                    // Format the output with grouping
                                    $output = '';
                                    if (!empty($level1a['team'])) {
                                        $output .= "Disposisi Utama: <br/>Tim <strong>" . implode("<br/>+", $level1a['team']) . "</strong><br/> ";
                                        if (!empty($level1a['pegawai'])) {
                                            $output .= "+ " . implode("<br/>+ ", $level1a['pegawai']) . "<br/>";
                                        }
                                        if (!empty($level2a['pegawai'])) {
                                            $output .= "+ " . implode("<br/>+ ", $level2a['pegawai']) . "<br/>";
                                        }
                                    }

                                    if (!empty($level1b['team'])) {
                                        $output .= "<span class='small'><br/>Disposisi Lainnya:<br/>";

                                        // Group teams and members
                                        $teamMembers = [];
                                        foreach ($model->suratmasukdisposisie as $disposisi) {
                                            if ($disposisi->level_disposisi === '1b' || $disposisi->level_disposisi === '2b') {
                                                $teamName = $disposisi->teame->panggilan_team ?? '[Tim Tidak Ada]';
                                                $pegawaiName = $disposisi->pegawaie->nama ?? '[Nama Tidak Ada]';

                                                // Group members under their respective teams
                                                $teamMembers[$teamName][] = $pegawaiName;
                                            }
                                        }

                                        // Build output for each team
                                        foreach ($teamMembers as $teamName => $members) {
                                            $output .= "Tim <strong>{$teamName}</strong><br/>";
                                            $output .= "+ " . implode("<br/>+ ", $members) . "<br/><br/>";
                                        }

                                        $output .= "</span>";
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
                            'template' => '{beri-disposisi}',
                            'visibleButtons' => [
                                'beri-disposisi' => function ($model, $key, $index) {
                                    $user = Yii::$app->user->identity;

                                    // Fetch disposisi data
                                    $disposisisatu_a = Suratmasukdisposisi::find()->where([
                                        'fk_suratmasuk' => $model['id_suratmasuk'],
                                        'level_disposisi' => '1a',
                                        'tujuan_disposisi_pegawai' => $user->username,
                                        'deleted' => 0
                                    ])->count();

                                    $disposisisatu_b = Suratmasukdisposisi::find()->where([
                                        'fk_suratmasuk' => $model['id_suratmasuk'],
                                        'level_disposisi' => '1b',
                                        'tujuan_disposisi_pegawai' => $user->username,
                                        'deleted' => 0
                                    ])->count();

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

                                    if ($status_penyelesaian > 0) {
                                        return false;
                                    } elseif (
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
                                    } else {
                                        return false;
                                    }
                                },
                            ],
                            'buttons' => [
                                'beri-disposisi' => function ($url, $model, $key) {
                                    // Determine the level based on the user's identity
                                    $level = 1; // Default to level 1
                                    if (Yii::$app->user->identity->issuratmasukpejabat) {
                                        $disposisisatu = Suratmasukdisposisi::find()
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
                                        $level = 1;
                                        if (count($disposisisatu) > 0 && Yii::$app->user->identity->username == $disposisisatu[0]['pemberi_disposisi'])
                                            $actionUrl = Yii::$app->urlManager->createUrl([
                                                'suratmasuk/edit-disposisi',
                                                'id' => $model->id_suratmasuk, // Ensure $model contains the necessary ID
                                            ]);
                                        else
                                            $actionUrl = Yii::$app->urlManager->createUrl([
                                                'suratmasuk/beri-disposisi',
                                                'id' => $model->id_suratmasuk, // Ensure $model contains the necessary ID
                                                'level' => $level,
                                            ]);
                                    } elseif (Yii::$app->user->identity->isteamleader) {
                                        $disposisidua = Suratmasukdisposisi::find()
                                            ->where([
                                                'fk_suratmasuk' => $model['id_suratmasuk'],
                                                'deleted' => 0
                                            ])
                                            ->andWhere([
                                                'or',
                                                ['level_disposisi' => '2a'],
                                                ['level_disposisi' => '2ab']
                                            ])
                                            ->all();
                                        $level = 2;
                                        if (count($disposisidua) > 0 && Yii::$app->user->identity->username == $disposisidua[0]['pemberi_disposisi'])
                                            $actionUrl = Yii::$app->urlManager->createUrl([
                                                'suratmasuk/edit-disposisi',
                                                'id' => $model->id_suratmasuk, // Ensure $model contains the necessary ID
                                            ]);
                                        else
                                            $actionUrl = Yii::$app->urlManager->createUrl([
                                                'suratmasuk/beri-disposisi',
                                                'id' => $model->id_suratmasuk, // Ensure $model contains the necessary ID
                                                'level' => $level,
                                            ]);
                                    }

                                    // Return the button
                                    return Html::a('<i class="fas fa-user-md"></i>', $actionUrl, [
                                        'title' => 'Lakukan disposisi pada surat ini',
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
                                    return '<span title="Selesai Dilaksanakan" class="badge bg-primary rounded-pill"><i class="fas fa-check"></i> Selesai Dilaksanakan</span>';
                                } elseif (!empty($level_disposisi) && $level_disposisi->status_penyelesaian === 0) {
                                    return '<span title="Belum Selesai" class="badge bg-danger rounded-pill"><i class="fas fa-times"></i> Belum Selesai</span>';
                                } elseif (!empty($level_disposisi) && $level_disposisi->status_penyelesaian === null) {
                                    return '-';
                                } else {
                                    return '';
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
                ]); ?>
            </div>
        </div>
    <?php endif; ?>

</div>