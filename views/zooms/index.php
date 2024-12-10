<?php

use yii\helpers\Html;
use kartik\grid\SerialColumn;
use kartik\grid\ActionColumn;
use kartik\grid\GridView;

$this->title = 'Jadwal Request/Pemakaian Zoom BPS Kabupaten Bengkulu Selatan';
?>
<style>
    .modal-link .icon-wrapper {
        margin-right: 8px;
        display: flex;
        align-items: center;
    }

    .modal-link .text-wrapper {
        display: flex;
        align-items: center;
        white-space: nowrap;
    }
</style>
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
            <?php if (!Yii::$app->user->isGuest) : ?>
                |
                <?= Html::a('<i class="fas fa-folder-plus"></i> Tambah Data Baru', ['create?fk_agenda='], ['class' => 'btn btn btn-outline-warning btn-sm']) ?>
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
                        'class' => ActionColumn::class,
                        'header' => 'Agenda',
                        'template' => '{agenda}',
                        'buttons'  => [
                            'agenda' => function ($url, $model, $key) {
                                $formatter = Yii::$app->formatter;
                                $formatter->locale = 'id-ID'; // set the locale to Indonesian
                                $timezone = new \DateTimeZone('Asia/Jakarta'); // create a timezone object for WIB
                                $waktumulai = new \DateTime($model->agendae->waktumulai, new \DateTimeZone('UTC')); // create a datetime object for waktumulai with UTC timezone
                                $waktumulai->setTimeZone($timezone); // set the timezone to WIB
                                $waktumulaiFormatted = $formatter->asDatetime($waktumulai, 'd MMMM Y, H:mm'); // format the waktumulai datetime value
                                $waktuselesai = new \DateTime($model->agendae->waktuselesai, new \DateTimeZone('UTC')); // create a datetime object for waktuselesai with UTC timezone
                                $waktuselesai->setTimeZone($timezone); // set the timezone to WIB
                                $waktuselesaiFormatted = $formatter->asDatetime($waktuselesai, 'H:mm'); // format the waktuselesai time value only
                                if ($waktumulai->format('Y-m-d') === $waktuselesai->format('Y-m-d')) {
                                    // if waktumulai and waktuselesai are on the same day, format the time range differently
                                    $waktumulaiFormatted = $formatter->asDatetime($waktumulai, 'd MMMM Y, H:mm'); // format the waktumulai datetime value with the year and time
                                    // return $waktumulaiFormatted . ' - ' . $waktuselesaiFormatted . ' WIB'; // concatenate the formatted dates
                                    return Html::a(
                                        '<div class="icon-wrapper"><i class="fas text-success fa-calendar-check"></i></div>' .
                                            '<div class="text-wrapper">' . $waktumulaiFormatted . ' - ' . $waktuselesaiFormatted . ' WIB</div>',
                                        ['agenda/' . $model->fk_agenda],
                                        [
                                            'title' => 'Lihat rincian Agenda ini',
                                            'data-bs-toggle' => 'modal',
                                            'data-bs-target' => '#exampleModal',
                                            'class' => 'modal-link btn-lebar d-flex align-items-center', // Ensure it's flexbox for alignment
                                        ]
                                    );
                                } else {
                                    // if waktumulai and waktuselesai are on different days, format the date range normally
                                    $waktuselesaiFormatted = $formatter->asDatetime($waktuselesai, 'd MMMM Y, H:mm'); // format the waktuselesai datetime value
                                    // return $waktumulaiFormatted . ' WIB <br/>s.d ' . $waktuselesaiFormatted . ' WIB'; // concatenate the formatted dates
                                    return Html::a(
                                        '<div class="icon-wrapper"><i class="fas text-success fa-calendar-check"></i></div>' .
                                            '<div class="text-wrapper">' .
                                            $waktumulaiFormatted . ' WIB<br>s.d ' . $waktuselesaiFormatted . ' WIB' .
                                            '</div>',
                                        ['agenda/' . $model->fk_agenda],
                                        [
                                            'title' => 'Lihat rincian Agenda ini',
                                            'data-bs-toggle' => 'modal',
                                            'data-bs-target' => '#exampleModal',
                                            'class' => 'modal-link btn-lebar d-flex align-items-start',
                                        ]
                                    );
                                }
                            },
                        ],
                        'vAlign' => 'middle',
                        'hAlign' => 'left'
                    ],
                    [
                        'attribute' => 'fk_agenda',
                        'value' => function ($model) {
                            return $model->agendae->progress == 0 ? '<span title="Rencana" class="badge bg-primary rounded-pill"><i class="fas fa-plus-square"></i> Rencana</span>' : ($model->agendae->progress == 1 ? '<span title="Selesai" class="badge bg-success rounded-pill"><i class="fas fa-check"></i> Selesai</span>' : ($model->agendae->progress == 2 ? '<span title="Tunda" class="badge bg-secondary rounded-pill"><i class="fas fa-strikethrough"></i> Tunda</span>' : ($model->agendae->progress == 3 ? '<span title="Batal" class="badge bg-danger rounded-pill"><i class="fas fa-trash-alt"></i> Batal</span>' : '')));
                        },
                        'format' => 'html',
                        'label' => 'Progress Agenda',
                        'vAlign' => 'middle',
                        'hAlign' => 'center'
                    ],
                    [
                        'attribute' => 'jenis_zoom',
                        'value' => function ($data) {
                            return $data->zoomstypee->nama_zoomstype . ' | ' . $data->zoomstypee->kuota;
                        },
                        'header' => 'Jenis',
                        'vAlign' => 'middle'
                    ],
                    [
                        'attribute' => 'fk_surat',
                        'value' => function ($data) {
                            return $data->surate;
                        },
                        'header' => 'Nomor Surat',
                        'vAlign' => 'middle'
                    ],
                    [
                        'attribute' => 'proposer',
                        'value' => 'proposere.nama',
                        'vAlign' => 'middle'
                    ],
                    [
                        'class' => ActionColumn::class,
                        'header' => 'Aksi',
                        'template' => '{update}{view}{delete}',
                        'visibleButtons' => [
                            'delete' => function ($model, $key, $index) {
                                return (!Yii::$app->user->isGuest
                                    && Yii::$app->user->identity->username === $model['proposer'] //datanya sendiri
                                    && $model['agendae']['progress'] !== 1  && $model['agendae']['progress'] !== 3
                                ) ? true : false;
                            },
                            'update' => function ($model, $key, $index) {
                                return (!Yii::$app->user->isGuest
                                    && Yii::$app->user->identity->username === $model['proposer'] //datanya sendiri
                                    && $model['agendae']['progress'] !== 1  && $model['agendae']['progress'] !== 3
                                ) ? true : false;
                            },
                        ],
                        'buttons'  => [
                            'delete' => function ($url, $model, $key) {
                                return Html::a('<i class="fas text-danger fa-trash-alt"></i> ', $url, [
                                    'title' => 'Hapus data usulan zoom ini',
                                    'data-method' => 'post',
                                    'data-pjax' => 0,
                                    'data-confirm' => 'Anda yakin ingin menghapus data usulan zoom ini? <br/>Untuk Agenda <strong>' . $model['agendae']['kegiatan'] . '</strong>'
                                ]);
                            },
                            'update' => function ($url, $model, $key) {
                                return Html::a('<i class="fa">&#xf044;</i> ', ['update?id=' . $model->id_zooms . '&fk_agenda=' . $model->fk_agenda], ['title' => 'Update rincian data usulan zoom ini']);
                            },
                            'view' => function ($key, $client) {
                                return Html::a('<i class="fas fa-eye"></i> ', $key, [
                                    'title' => 'Lihat rincian data usulan zoom ini',
                                    'data-bs-toggle' => 'modal',
                                    'data-bs-target' => '#exampleModal',
                                    'class' => 'modal-link',
                                ]);
                            },
                        ],
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
                    GridView::CSV => ['label' => 'CSV', 'filename' => 'Permohonan Zoom dari Portal Pintar - ' . date('d-M-Y')],
                    GridView::HTML => ['label' => 'HTML', 'filename' => 'Permohonan Zoom dari Portal Pintar - ' . date('d-M-Y')],
                    GridView::EXCEL => ['label' => 'EXCEL', 'filename' => 'Permohonan Zoom dari Portal Pintar - ' . date('d-M-Y')],
                    GridView::TEXT => ['label' => 'TEXT', 'filename' => 'Permohonan Zoom dari Portal Pintar - ' . date('d-M-Y')],
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