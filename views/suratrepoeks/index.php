<?php

use app\models\Suratrepoeks;
use yii\helpers\Html;
use kartik\grid\SerialColumn;
use kartik\grid\ActionColumn;
use kartik\grid\GridView;
use yii\bootstrap5\Modal;
use yii\web\View;

$this->title = 'Surat-surat';

$this->registerCssFile(Yii::$app->request->baseUrl . '/library/css/fi-agenda-index.css', ['position' => View::POS_HEAD, 'depends' => [\yii\web\JqueryAsset::class]]);
?>
<?php
$surats = Suratrepoeks::find()->select('*')
    ->andWhere(['approver' => Yii::$app->user->identity->username])
    ->andWhere(['approval' => 0])
    ->andWhere(['deleted' => 0])
    ->orderBy(['tanggal_suratrepoeks' => SORT_ASC])
    ->all(); ?>
<?php if (count($surats) > 0) : ?>
    <div class="toast-container position-fixed p-3 bottom-0 end-0" data-aos="fade-left" id="toastSurat">
        <div class="toast show shadow-sm p-1 mb-2 rounded" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="toast-header bg-warning text-dark">
                <img src="<?php echo Yii::$app->request->baseUrl ?>/images/favicon.png" class="rounded me-2" width="20" height="20" alt="Persetujuan Surat">
                <strong class="me-auto">Surat yang Belum Disetujui</strong>
                <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
            <div class="toast-body bg-light text-dark">
                Berikut Daftar Surat yang Belum Anda Setujui:
                <table class="table table-sm">
                    <tbody>
                        <?php foreach ($surats as $surat) : ?>
                            <tr>
                                <th scope="row"><i class="fas fa-calendar-alt text-info me-2"></i><?= $surat->tanggal_suratrepoeks ?></th>
                                <td><?= $surat->perihal_suratrepoeks ?></td>
                                <td><a href="<?= Yii::$app->request->baseUrl . '/suratrepoeks/' . $surat->id_suratrepoeks ?>" target="_blank"><i class="fas fa-eye"></i> Lihat</a></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
<?php endif; ?>

<div class="container-fluid" data-aos="fade-up">
    <h1 class="text-center"><?= Html::encode($this->title) ?> <span class="text-white bg-success">&nbsp;Eksternal&nbsp;</span> </h1>
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
            echo Html::a('<i class="fas fa-home"></i> Beranda Agenda', $homeUrl, ['class' => 'btn btn btn-outline-warning btn-sm']);
            ?>
            |
            <?= Html::a('<i class="fas fa-file-archive"></i> Arsip Surat Eskternal', ['suratrepoeks/index?owner=&year='], ['class' => 'btn btn btn-outline-warning btn-sm']) ?>
            |
            <?= Html::a('<i class="fas fa-plus-square"></i> Tambah Surat Baru', ['suratrepoeks/create/0'], ['class' => 'btn btn btn-outline-warning btn-sm']) ?>
        </div>
    </div>
    </p>
    <?php
    $ada = $dataProvider->getModels();
    ?>
    <?php if ($ada == NULL) : ?>
        <div class="card text-center <?= ((!Yii::$app->user->isGuest && Yii::$app->user->identity->theme == 0) ? '' : 'bg-dark') ?>">
            <div class="card-body">
                <h2><em>Belum Ada Surat Eksternal di Tahun <?php echo date("Y") ?> <br /> atau di Pencarian yang Anda Maksud</em></h2>
                <hr />
                <?= Html::a('<i class="fas fa-file-archive"></i> Klik untuk Lihat Arsip Surat Eksternal', ['suratrepo/index?owner=&year='], ['class' => 'btn btn ' . ((!Yii::$app->user->isGuest && Yii::$app->user->identity->theme == 0) ? 'btn-outline-dark' : 'btn-outline-light') . ' btn-lg']) ?>
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
                        // 'id_suratrepoeks',
                        [
                            'attribute' => 'fk_agenda',
                            'label' => 'Agenda',
                            'value' => function ($model) {
                                if (!Yii::$app->user->isGuest && $model->sifat != 2 || ($model->sifat == 2 && Yii::$app->user->identity->username === $model['owner'])) //datanya sendiri                          
                                {
                                    return $model->agendae->kegiatan ?? '-';
                                } else {
                                    return '';
                                }
                            },
                        ],
                        [
                            'attribute' => 'penerima_suratrepoeks',
                            'value' => function ($model) {
                                if (!Yii::$app->user->isGuest && $model->sifat != 2 || ($model->sifat == 2 && Yii::$app->user->identity->username === $model['owner'])) //datanya sendiri                          
                                {
                                    return  $model->invisibility == 0 ? $model->penerima_suratrepoeks : '~';
                                } else {
                                    return '';
                                }
                            },
                        ],
                        [
                            'attribute' => 'tanggal_suratrepoeks',
                            'value' => function ($model) {
                                if (!Yii::$app->user->isGuest && $model->sifat != 2 || ($model->sifat == 2 && Yii::$app->user->identity->username === $model['owner'])) //datanya sendiri                          
                                {
                                    return $model->invisibility == 0 ? \Yii::$app->formatter->asDatetime(strtotime($model->tanggal_suratrepoeks), "d MMMM y") : '~';
                                } else {
                                    return '';
                                }
                            },
                        ],
                        [
                            'attribute' => 'perihal_suratrepoeks',
                            'value' => function ($model) {
                                if (!Yii::$app->user->isGuest && $model->sifat != 2 || ($model->sifat == 2 && Yii::$app->user->identity->username === $model['owner'])) //datanya sendiri                          
                                {
                                    return $model->invisibility == 0 ? $model->perihal_suratrepoeks : '~';
                                } else {
                                    return '';
                                }
                            },
                        ],
                        [
                            'attribute' => 'sifat',
                            'value' => function ($data) {
                                if ($data->sifat == 0)
                                    return '<center><span title="Biasa" class="badge bg-primary rounded-pill"><i class="fas fa-scroll"></i> Biasa</span></center>';
                                elseif ($data->sifat == 1)
                                    return '<center><span title="Penting" class="badge bg-success rounded-pill"><i class="fas fa-star"></i> Penting</span></center>';
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
                        'nomor_suratrepoeks',
                        [
                            'attribute' => 'jenis',
                            'value' => function ($data) {
                                if ($data->jenis == 0)
                                    return '<center><span class="badge bg-primary rounded-pill"><i class="fas fa-scroll"></i> Surat Biasa</span></center>';
                                elseif ($data->jenis == 1)
                                    return '<center><span class="badge bg-success rounded-pill"><i class="fas fa-scroll"></i> Surat Perintah Lembur</span></center>';
                                elseif ($data->jenis == 2)
                                    return '<center><span class="badge bg-secondary rounded-pill"><i class="fas fa-scroll"></i> Surat Keterangan</span></center>';
                                elseif ($data->jenis == 3)
                                    return '<center><span class="badge bg-warning rounded-pill"><i class="fas fa-scroll"></i> Berita Acara</span></center>';
                                else
                                    return '';
                            },
                            'header' => 'Jenis',
                            'enableSorting' => false,
                            'format' => 'html',
                            'vAlign' => 'middle',
                            'hAlign' => 'center'
                        ],
                        [
                            'attribute' => 'owner',
                            'value' => function ($model) {
                                if (!Yii::$app->user->isGuest && $model->sifat != 2 || ($model->sifat == 2 && Yii::$app->user->identity->username === $model['owner'])) //datanya sendiri                          
                                {
                                    return $model->ownere->nama;
                                } else {
                                    return '';
                                }
                            },
                        ],
                        [
                            'class' => ActionColumn::class,
                            'header' => 'Aksi',
                            'template' => (Yii::$app->user->isGuest || Yii::$app->user->identity->theme == 0)
                                ? '{update}{view}{agenda}'
                                : '{update}{view}{agenda}',
                            'visibleButtons' => [
                                'update' => function ($model, $key, $index) {
                                    return (!Yii::$app->user->isGuest &&
                                        (Yii::$app->user->identity->username === $model['owner'] //datanya sendiri   
                                            || Yii::$app->user->identity->issekretaris)
                                        && $model['approval'] == 0
                                    ) ? true : false;
                                },
                                'view' => function ($model, $key, $index) {
                                    // ($model->sifat == 2 || $model->invisibility == 1 || Yii::$app->user->identity->username === $model['owner'])
                                    if (
                                        Yii::$app->user->identity->username === $model['owner']
                                        || Yii::$app->user->identity->issekretaris
                                        || Yii::$app->user->identity->username === $model['approver']
                                        || $model->visibletome == true
                                    )
                                        return true;
                                    // elseif (($model->sifat == 0 || $model->sifat == 1) && $model->invisibility == 0)
                                    //     return true;
                                    else
                                        return false;
                                },
                                'agenda' => function ($model) {
                                    return $model->fk_agenda ? true : false;
                                }
                            ],
                            'buttons'  => [
                                'update' => function ($key, $client) {
                                    return Html::a('<i class="fa">&#xf044;</i> ', $key, ['title' => 'Update rincian surat ini']);
                                },
                                'view' => function ($key, $client) {
                                    return Html::a('<i class="fas fa-eye"></i> ', $key, [
                                        'title' => 'Lihat rincian surat ini',
                                        'data-bs-toggle' => 'modal',
                                        'data-bs-target' => '#exampleModal',
                                        'class' => 'modal-link',
                                    ]);
                                },
                                'agenda' => function ($url, $model, $key) {
                                    return Html::a('<i class="fas fa-calendar-alt"></i> ',  ['agenda/' . $model->fk_agenda], ['title' => 'Lihat rincian agenda ini', 'class' => 'modalButton', 'data-pjax' => '0']);
                                },
                            ],
                        ],
                        [
                            'attribute' => 'approval',
                            'value' => function ($data) {
                                if ($data->approval == 0)
                                    return '<center><span title="Belum Disetujui" class="badge bg-danger rounded-pill"><i class="fas fa-times"></i> Belum</span></center>';
                                elseif ($data->approval == 1)
                                    return '<center><span title="Disetujui" class="badge bg-success rounded-pill"><i class="fas fa-check"></i> Disetujui</span></center>';
                                else
                                    return '';
                            },
                            'header' => 'Disetujui',
                            'enableSorting' => false,
                            'format' => 'html',
                            'vAlign' => 'middle',
                            'hAlign' => 'center'
                        ],
                        [
                            'class' => ActionColumn::class,
                            'header' => 'Draft/Word dan Persetujuan',
                            'template' => (Yii::$app->user->isGuest || Yii::$app->user->identity->theme == 0)
                                ? '{setujui}{cetak}{komentar}{uploadword}{lihatword}'
                                : '{setujui}{cetak}{komentar}{uploadword}{lihatword}',
                            'visibleButtons' =>
                            [
                                'setujui' => function ($model, $key, $index) {
                                    return ($model->approval == 0 && !Yii::$app->user->isGuest && $model['approver'] === Yii::$app->user->identity->username //datanya sendiri                               
                                    ) ? true : false;
                                },
                                'komentar' => function ($model) {
                                    return ((Yii::$app->user->identity->username === $model['owner'] && $model['komentar'] != null)
                                        || ($model->approval == 0 && Yii::$app->user->identity->username === $model['approver'] && $model->jumlah_revisi < 2)
                                        || Yii::$app->user->identity->issekretaris) ? true : false;
                                },
                                'cetak' => function ($model) {
                                    return $model->isi_suratrepoeks != null &&
                                        (Yii::$app->user->identity->username === $model['owner']
                                            || Yii::$app->user->identity->username === $model['approver']
                                            || Yii::$app->user->identity->issekretaris) ? true : false;
                                },
                                'uploadword' => function ($model) {
                                    return (!Yii::$app->user->isGuest && Yii::$app->user->identity->username === $model['owner'] //datanya sendiri                               
                                    ) ? true : false;
                                },
                                'lihatword' => function ($model) {
                                    if (
                                        Yii::$app->user->identity->username === $model['owner'] //datanya sendiri   
                                        || Yii::$app->user->identity->issekretaris
                                        || $model['approver'] === Yii::$app->user->identity->username
                                    ) {
                                        if (file_exists(Yii::getAlias('@webroot/surat/eksternal/word/' . $model->id_suratrepoeks . '.doc')))
                                            return true;
                                        elseif (file_exists(Yii::getAlias('@webroot/surat/eksternal/word/' . $model->id_suratrepoeks . '.docx')))
                                            return true;
                                        else
                                            return false;
                                    }
                                },
                            ],
                            'buttons'  => [
                                'setujui' => function ($url, $model, $key) {
                                    return Html::a('<i class="fas text-primary fa-check"></i> ', $url, [
                                        'title' => 'Setujui surat ini',
                                        'data-method' => 'post',
                                        'data-pjax' => 0,
                                        'data-confirm' => 'Anda yakin ingin menyetujui surat ini? <br/><strong>' . $model['perihal_suratrepoeks'] . '</strong>'
                                    ]);
                                },
                                'komentar' => function ($url, $model, $key) {
                                    if (Yii::$app->user->identity->username == $model['approver'])
                                        return Html::a('<i class="fas fa-comment-alt"></i> ',  ['suratrepoeks/komentar/' . $model->id_suratrepoeks], ['title' => 'Beri koreksi untuk surat ini', 'class' => 'modalButton', 'data-pjax' => '0']);
                                    else
                                        return Html::a('<i class="fas fa-comment-alt"></i> ',  ['suratrepoeks/komentar/' . $model->id_suratrepoeks], ['title' => 'Beri koreksi untuk surat ini', 'class' => 'modalButton', 'data-pjax' => '0']);
                                },
                                'cetak' => function ($url, $model, $key) {
                                    return Html::a('<i class="fas fa-file-pdf"></i> ',  ['suratrepoeks/cetaksurat/' . $model->id_suratrepoeks], ['title' => 'Cetak surat ini', 'target' => '_blank']);
                                },
                                'uploadword' => function ($url, $model, $key) {
                                    return Html::a('<i class="fas fa-cloud-upload-alt"></i> ',  ['suratrepoeks/uploadword/' . $model->id_suratrepoeks], ['title' => 'Upload scan surat ini', 'target' => '_blank']);
                                },
                                'lihatword' => function ($url, $model, $key) {
                                    if (file_exists(Yii::getAlias('@webroot/surat/eksternal/word/' . $model->id_suratrepoeks . '.doc')))
                                        return Html::a('<i class="fas fa-file-word"></i> ', ['surat/eksternal/word/' . $model->id_suratrepoeks . '.doc'], [
                                            'title' => 'Unduh draft surat ini',
                                        ]);
                                    elseif (file_exists(Yii::getAlias('@webroot/surat/eksternal/word/' . $model->id_suratrepoeks . '.docx')))
                                        return Html::a('<i class="fas fa-file-word"></i> ', ['surat/eksternal/word/' . $model->id_suratrepoeks . '.docx'], [
                                            'title' => 'Unduh draft surat ini',
                                        ]);
                                    else
                                        return false;
                                },
                            ],
                        ],
                        [
                            'class' => ActionColumn::class,
                            'header' => 'Scan Surat',
                            'template' => (Yii::$app->user->isGuest || Yii::$app->user->identity->theme == 0)
                                ? '{uploadscan}{lihatscan}'
                                : '{uploadscan}{lihatscan}',
                            'visibleButtons' => [
                                'uploadscan' => function ($model) {
                                    return ((Yii::$app->user->identity->username === $model['owner'] || Yii::$app->user->identity->issekretaris) //data sendiri atau sekretaris
                                        && $model->approval == 1 //sudah disetujui                                
                                    ) ? true : false;
                                },
                                'lihatscan' => function ($model) {
                                    if (file_exists(Yii::getAlias('@webroot/surat/eksternal/pdf/' . $model->id_suratrepoeks . '.pdf'))) {
                                        return ((Yii::$app->user->identity->username === $model['owner']
                                            || Yii::$app->user->identity->username === $model['approver']
                                            || Yii::$app->user->identity->issekretaris) //data sendiri atau sekretaris  
                                            || $model->visibletome == true
                                            && $model->approval == 1 //sudah disetujui   
                                        ) ? true : false;
                                    } else
                                        return false;
                                },
                            ],
                            'buttons'  => [
                                'uploadscan' => function ($url, $model, $key) {
                                    return Html::a('<i class="fas fa-upload"></i> ',  ['suratrepoeks/uploadscan/' . $model->id_suratrepoeks], ['title' => 'Upload scan surat ini', 'target' => '_blank']);
                                },
                                'lihatscan' => function ($url, $model, $key) {
                                    return Html::a('<i class="fas fa-book-reader"></i> ', ['suratrepoeks/lihatscan/' . $model->id_suratrepoeks], [
                                        'title' => 'Lihat scan surat ini',
                                        'data-bs-toggle' => 'modal',
                                        'data-bs-target' => '#exampleModal',
                                        'class' => 'modal-link',
                                    ]);
                                },
                            ],
                        ],
                    ],
                    'layout' => $layout,
                    'bordered' => false,
                    'striped' => false,
                    'condensed' => false,
                    'hover' => true,
                    'headerRowOptions' => ['class' => 'kartik-sheet-style kv-align-middle'],
                    'filterRowOptions' => ['class' => 'kartik-sheet-style'],
                    'export' => [
                        'fontAwesome' => true,
                        'label' => '<i class="fa">&#xf56d;</i>',
                        'pjax' => false,
                    ],
                    'exportConfig' => [
                        GridView::CSV => ['label' => 'CSV', 'filename' => 'Link Materi Portal Pintar - ' . date('d-M-Y')],
                        GridView::HTML => ['label' => 'HTML', 'filename' => 'Link Materi Portal Pintar - ' . date('d-M-Y')],
                        GridView::EXCEL => ['label' => 'EXCEL', 'filename' => 'Link Materi Portal Pintar - ' . date('d-M-Y')],
                        GridView::TEXT => ['label' => 'TEXT', 'filename' => 'Link Materi Portal Pintar - ' . date('d-M-Y')],
                    ],
                    'pjax' => false,
                    'pjaxSettings' => [
                        'neverTimeout' => true,
                        // 'enablePushState' => false,
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
                                Html::a('<span class="btn btn-success me-1"> Surat Saya (' . date("Y") . ')</span>', 'index?owner=' . Yii::$app->user->identity->username . '&year=' . date("Y"), ['title' => 'Tampikan Surat Anda', 'data-pjax' => 0])
                                .
                                Html::a('<span class="btn btn-outline-success me-1"> Surat Saya (Since 2023)</span>', 'index?owner=' . Yii::$app->user->identity->username . '&year=', ['title' => 'Tampikan Surat Anda', 'data-pjax' => 0])
                                .
                                Html::a('<span class="btn btn-warning me-1"> Semua (' . date("Y") . ')</span>', 'index?owner=&year=' . date("Y"), ['title' => 'Tampikan Surat Semua', 'data-pjax' => 0])
                                .
                                Html::a('<span class="btn btn btn-outline-warning"> Semua (Since 2023)</span>', 'index?owner=&year=', ['title' => 'Tampikan Surat Semua', 'data-pjax' => 0])
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
<?php
Modal::begin([
    'title' => '',
    'id' => 'modal',
    'size' => 'modal-lg'
]);
echo '<div id="modalContent"></div>';
Modal::end();
?>
<script>
    $(function() {
        // changed id to class
        $('.modalButton').click(function() {
            $.get($(this).attr('href'), function(data) {
                $('#modal').modal('show').find('#modalContent').html(data)
            });
            return false;
        });
    });
</script>
<script>
    const button = document.getElementById('w2-button');
    const dropdown = document.getElementById('w3');
    button.addEventListener('click', () => {
        dropdown.classList.toggle('show');
    });
    document.addEventListener('click', (event) => {
        if (!event.target.matches('#w2-button, #w3')) {
            dropdown.classList.remove('show');
        }
    });
</script>