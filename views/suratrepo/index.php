<?php

use yii\helpers\Html;
use kartik\grid\SerialColumn;
use kartik\grid\ActionColumn;
use kartik\grid\GridView;
use yii\web\View;

$this->title = 'Surat-surat';

$this->registerCssFile(Yii::$app->request->baseUrl . '/library/css/fi-agenda-index.css', ['position' => View::POS_HEAD, 'depends' => [\yii\web\JqueryAsset::class]]);

?>
<style>
    .kv-table-header {
        background: transparent !important;
    }
</style>
<div class="container-fluid" data-aos="fade-up">
    <h1 class="text-center"><?= Html::encode($this->title) ?> <span class="<?= ((!Yii::$app->user->isGuest && Yii::$app->user->identity->theme == 0) ? 'text-light bg-dark' : 'text-dark bg-light') ?>">&nbsp;Internal&nbsp;</span> </h1>
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
            |
            <?= Html::a('<i class="fas fa-file-archive"></i> Arsip Surat Internal', ['suratrepo/index?owner=&year='], ['class' => 'btn btn btn-outline-warning btn-sm']) ?>
            |
            <?= Html::a('<i class="fas fa-plus-square"></i> Tambah Surat Baru', ['suratrepo/create/0'], ['class' => 'btn btn btn-outline-warning btn-sm']) ?>
        </div>
    </div>
    </p>
    <?php
    $ada = $dataProvider->getModels();
    ?>
    <?php if ($ada == NULL) : ?>
        <div class="card text-center <?= ((!Yii::$app->user->isGuest && Yii::$app->user->identity->theme == 0) ? '' : 'bg-dark') ?>">
            <div class="card-body">
                <h2><em>Belum Ada Surat Internal di Tahun <?php echo date("Y") ?> <br /> atau di Pencarian yang Anda Maksud</em></h2>
                <hr />
                <?= Html::a('<i class="fas fa-file-archive"></i> Klik untuk Lihat Arsip Surat Internal', ['suratrepo/index?owner=&year='], ['class' => 'btn btn ' . ((!Yii::$app->user->isGuest && Yii::$app->user->identity->theme == 0) ? 'btn-outline-dark' : 'btn-outline-light') . ' btn-lg']) ?>
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
                        // 'id_suratrepo',
                        [
                            'attribute' => 'fk_agenda',
                            'label' => 'Agenda',
                            'value' => function ($model) {
                                return $model->agendae->kegiatan ?? '-';
                            },
                        ],
                        'penerima_suratrepo',
                        [
                            'attribute' => 'tanggal_suratrepo',
                            'value' => function ($model) {
                                return \Yii::$app->formatter->asDatetime(strtotime($model->tanggal_suratrepo), "d MMMM y");
                            },
                        ],
                        'perihal_suratrepo:ntext',
                        'nomor_suratrepo',
                        [
                            'attribute' => 'jenis',
                            'value' => function ($data) {
                                if ($data->jenis == 0)
                                    return '<center><span class="badge bg-primary rounded-pill"><i class="fas fa-scroll"></i> Surat Biasa</span></center>';
                                elseif ($data->jenis == 1)
                                    return '<center><span class="badge bg-success rounded-pill"><i class="fas fa-scroll"></i> Nota Dinas</span></center>';
                                elseif ($data->jenis == 2)
                                    return '<center><span class="badge bg-secondary rounded-pill"><i class="fas fa-scroll"></i> Surat Keterangan</span></center>';
                                elseif ($data->jenis == 3)
                                    return '<center><span  class="badge bg-info rounded-pill"><i class="fas fa-scroll"></i> Berita Acara</span></center>';
                                elseif ($data->jenis == 4)
                                    return '<center><span  class="badge bg-warning rounded-pill"><i class="fas fa-scroll"></i> Lainnya</span></center>';
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
                            'value' => 'ownere.nama',
                        ],
                        [
                            'class' => ActionColumn::class,
                            'header' => 'Aksi',
                            'template' => '{update}{view}{agenda}',
                            'visibleButtons' => [
                                'update' => function ($model, $key, $index) {
                                    if (!file_exists(Yii::getAlias('@webroot/surat/internal/pdf/' . $model->id_suratrepo . '.pdf'))) {
                                        return (!Yii::$app->user->isGuest && Yii::$app->user->identity->username === $model['owner'])
                                            ? true : false;
                                    } else
                                        return false;
                                },
                                'agenda' => function ($model) {
                                    return $model->fk_agenda ? true : false;
                                },
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
                            'class' => ActionColumn::class,
                            'header' => 'Draft/Word',
                            'template' => '{uploadword}{lihatword}',
                            'visibleButtons' => [
                                'uploadword' => function ($model) {
                                    return (!Yii::$app->user->isGuest && Yii::$app->user->identity->username === $model['owner'] //datanya sendiri                               
                                    ) ? true : false;
                                },
                                'lihatword' => function ($model) {
                                    if (
                                        Yii::$app->user->identity->username === $model['owner'] //datanya sendiri   
                                        || Yii::$app->user->identity->issekretaris
                                    ) {
                                        if (file_exists(Yii::getAlias('@webroot/surat/internal/word/' . $model->id_suratrepo . '.doc')))
                                        return true;
                                    elseif (file_exists(Yii::getAlias('@webroot/surat/internal/word/' . $model->id_suratrepo . '.docx')))
                                        return true;
                                    else
                                        return false;
                                    }
                                },
                            ],
                            'buttons'  => [
                                'uploadword' => function ($url, $model, $key) {
                                    return Html::a('<i class="fas fa-cloud-upload-alt"></i> ',  ['suratrepo/uploadword/' . $model->id_suratrepo], ['title' => 'Upload draf surat ini', 'target' => '_blank']);
                                },
                                'lihatword' => function ($url, $model, $key) {
                                    if (file_exists(Yii::getAlias('@webroot/surat/internal/word/' . $model->id_suratrepo . '.doc')))
                                        return Html::a('<i class="fas fa-file-word"></i> ', ['surat/internal/word/' . $model->id_suratrepo . '.doc'], [
                                            'title' => 'Unduh draft surat ini',
                                        ]);
                                    elseif (file_exists(Yii::getAlias('@webroot/surat/internal/word/' . $model->id_suratrepo . '.docx')))
                                        return Html::a('<i class="fas fa-file-word"></i> ', ['surat/internal/word/' . $model->id_suratrepo . '.docx'], [
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
                            'template' => '{uploadscan}{lihatscan}',
                            'visibleButtons' => [
                                'uploadscan' => function ($model) {
                                    return (!Yii::$app->user->isGuest && Yii::$app->user->identity->username === $model['owner'] //datanya sendiri                               
                                    ) ? true : false;
                                },
                                'lihatscan' => function ($model) {
                                    if (file_exists(Yii::getAlias('@webroot/surat/internal/pdf/' . $model->id_suratrepo . '.pdf')))
                                        return true;
                                    else
                                        return false;
                                },
                            ],
                            'buttons'  => [
                                'uploadscan' => function ($url, $model, $key) {
                                    return Html::a('<i class="fas fa-upload"></i> ',  ['suratrepo/uploadscan/' . $model->id_suratrepo], ['title' => 'Upload scan surat ini', 'target' => '_blank']);
                                },
                                'lihatscan' => function ($url, $model, $key) {
                                    return Html::a('<i class="fas fa-book-reader"></i> ', ['suratrepo/lihatscan/' . $model->id_suratrepo], [
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
                            return '
                        <div class="btn-group">
                        ' .
                                Html::a('<span class="btn ' . (!Yii::$app->user->isGuest && Yii::$app->user->identity->theme == 0 ? 'btn-dark' : 'btn-light') . ' me-1"> Surat Saya (' . date("Y") . ')</span>', 'index?owner=' . Yii::$app->user->identity->username . '&year=' . date("Y"), ['title' => 'Tampikan Surat Anda', 'data-pjax' => 0])
                                .
                                Html::a('<span class="btn ' . (!Yii::$app->user->isGuest && Yii::$app->user->identity->theme == 0 ? 'btn-outline-dark' : 'btn-outline-light') . ' me-1"> Surat Saya (Since 2023)</span>', 'index?owner=' . Yii::$app->user->identity->username . '&year=', ['title' => 'Tampikan Surat Anda', 'data-pjax' => 0])
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