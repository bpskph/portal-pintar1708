<?php

use yii\helpers\Html;
use kartik\grid\SerialColumn;
use kartik\grid\ActionColumn;
use kartik\grid\GridView;
use yii\web\View;

$this->title = 'Petugas Apel dan Upacara';
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
            <?php if (!Yii::$app->user->isGuest && (Yii::$app->user->identity->level == 0 || Yii::$app->user->identity->issdmmember)) : ?>
                |
                <?= Html::a('<i class="fas fa-folder-plus"></i> Tambah Data Baru', ['create'], ['class' => 'btn btn btn-outline-warning btn-sm']) ?>
            <?php endif; ?>
        </div>
    </div>
    </p>

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
                        'attribute' => 'jenis_apel',
                        'value' => function ($data) {
                            if ($data->jenis_apel == 0)
                                return '<center><span title="Apel" class="badge bg-primary rounded-pill"><i class="far fa-flag"></i> Apel</span></center>';
                            elseif ($data->jenis_apel == 1)
                                return '<center><span title="Upacara" class="badge bg-success rounded-pill"><i class="fas fa-flag"></i> Upacara</span></center>';
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
                        'attribute' => 'tanggal_apel',
                        'value' => function ($model) {
                            return \Yii::$app->formatter->asDatetime(strtotime($model->tanggal_apel), "d MMMM y");
                        },
                        'hAlign' => 'center'
                    ],
                    [
                        'attribute' => 'pembina_inspektur',
                        'value' => function ($model) {
                            return $model->getPetugase($model->pembina_inspektur);
                        },
                    ],
                    [
                        'attribute' => 'pemimpin_komandan',
                        'value' => function ($model) {
                            return $model->getPetugase($model->pemimpin_komandan);
                        },
                    ],
                    [
                        'attribute' => 'mc',
                        'value' => function ($model) {
                            return $model->getPetugase($model->mc);
                        },
                    ],
                    [
                        'attribute' => 'reporter',
                        'value' => 'reportere.nama'
                    ],
                    [
                        'class' => ActionColumn::class,
                        'header' => 'Aksi',
                        'template' => '{update}{view}{delete}',
                        'visibleButtons' => [
                            'delete' => function ($model, $key, $index) {
                                return (!Yii::$app->user->isGuest && Yii::$app->user->identity->username === $model['reporter'] //datanya sendiri
                                ) ? true : false;
                            },
                            'update' => function ($model, $key, $index) {
                                return (!Yii::$app->user->isGuest && Yii::$app->user->identity->username === $model['reporter'] //datanya sendiri
                                ) ? true : false;
                            },
                        ],
                        'buttons'  => [
                            'delete' => function ($url, $model, $key) {
                                return Html::a('<i class="fas text-danger fa-trash-alt"></i> ', $url, [
                                    'title' => 'Hapus data apel ini',
                                    'data-method' => 'post',
                                    'data-pjax' => 0,
                                    'data-confirm' => 'Anda yakin ingin menghapus data apel ini? <br/><strong>' . \Yii::$app->formatter->asDatetime(strtotime($model['tanggal_apel']), "d MMMM y") . '</strong>'
                                ]);
                            },
                            'update' => function ($key, $client) {
                                return Html::a('<i class="fa">&#xf044;</i> ', $key, ['title' => 'Update rincian data apel ini']);
                            },
                            'view' => function ($key, $client) {
                                return Html::a('<i class="fas fa-eye"></i> ', $key, [
                                    'title' => 'Lihat rincian data apel/upacara ini',
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
                'headerRowOptions' => ['class' => 'kartik-sheet-style'],
                'filterRowOptions' => ['class' => 'kartik-sheet-style'],
                'export' => [
                    'fontAwesome' => true,
                    'label' => '<i class="fa">&#xf56d;</i>',
                    'pjax' => false,
                ],
                'exportConfig' => [
                    GridView::CSV => ['label' => 'CSV', 'filename' => 'Jadwal Apel/Upacara dari Portal Pintar - ' . date('d-M-Y')],
                    GridView::HTML => ['label' => 'HTML', 'filename' => 'Jadwal Apel/Upacara dari Portal Pintar - ' . date('d-M-Y')],
                    GridView::EXCEL => ['label' => 'EXCEL', 'filename' => 'Jadwal Apel/Upacara dari Portal Pintar - ' . date('d-M-Y')],
                    GridView::TEXT => ['label' => 'TEXT', 'filename' => 'Jadwal Apel/Upacara dari Portal Pintar - ' . date('d-M-Y')],
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
</div>