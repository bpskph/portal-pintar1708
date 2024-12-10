<?php

use yii\helpers\Html;
use yii\web\View;
use kartik\grid\SerialColumn;
use kartik\grid\ActionColumn;
use kartik\grid\GridView;
use kartik\daterange\DateRangePicker;

$this->title = 'Jadwal Request/Peminjaman Mobil Dinas BPS Provinsi Bengkulu';

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
                        'attribute' => 'waktu',
                        'value' => function ($model) {
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
                                return $waktumulaiFormatted . ' - ' . $waktuselesaiFormatted . ' WIB'; // concatenate the formatted dates
                            } else {
                                // if waktumulai and waktuselesai are on different days, format the date range normally
                                $waktuselesaiFormatted = $formatter->asDatetime($waktuselesai, 'd MMMM Y, H:mm'); // format the waktuselesai datetime value
                                return $waktumulaiFormatted . ' WIB <br/>s.d ' . $waktuselesaiFormatted . ' WIB'; // concatenate the formatted dates
                            }
                        },
                        'filter' => DateRangePicker::widget([
                            'model' => $searchModel,
                            'attribute' => 'waktu',
                            'convertFormat' => true,
                            'pluginOptions' => [
                                'locale' => [
                                    'format' => 'd M Y',
                                ],
                                'opens' => 'left',
                            ],
                            'options' => [
                                'class' => 'form-control',
                                'placeholder' => 'Filter ...'
                            ],
                        ]),
                        'label' => 'Waktu',
                        'format' => 'html',
                        'vAlign' => 'middle'
                    ],
                    [
                        'attribute' => 'keperluan',
                        'value' => 'keperluane.nama_mobildinaskeperluan',
                        'vAlign' => 'middle'
                    ],
                    [
                        'attribute' => 'keperluan_lainnya',
                        'value' => function ($data) {
                            if ($data->keperluan_lainnya == null)
                                return '-';
                            else
                                return $data->keperluan_lainnya;
                        },
                        'vAlign' => 'middle'
                    ],
                    [
                        'attribute' => 'borrower',
                        'value' => 'borrowere.nama',
                        'vAlign' => 'middle'
                    ],
                    [
                        'attribute' => 'approval',
                        'value' => function ($model) {
                            return $model->approval == 1 ?
                                '<span title="Disetujui" class="badge bg-primary rounded-pill"><i class="fas fa-check"></i> Usulan Disetujui</span>' : ($model->approval == 3 ?
                                    '<span title="Persetujuan Usulan Dibatalkan" class="badge bg-danger rounded-pill"><i class="fas fa-trash"></i> Persetujuan Usulan Dibatalkan oleh Umum</span>' : ($model->approval == 0 ?
                                        '<span title="Menunggu Konfirmasi" class="badge bg-secondary rounded-pill"><i class="fas fa-question"></i> Menunggu Konfirmasi</span>' : '<span title="Usulan Ditolak" class="badge bg-danger rounded-pill"><i class="fas fa-times"></i> Usulan Ditolak</span>'));
                        },
                        'header' => 'Persetujuan',
                        'format' => 'html',
                        'vAlign' => 'middle',
                    ],
                    [
                        'class' => ActionColumn::class,
                        'header' => 'Aksi',
                        'template' => '{update}{view}{setujui}{tolak}{batal}',
                        'visibleButtons' => [
                            'delete' => function ($model, $key, $index) {
                                return (!Yii::$app->user->isGuest
                                    && Yii::$app->user->identity->username === $model['borrower'] //datanya sendiri
                                    && $model['approval'] == 0
                                ) ? true : false;
                            },
                            'update' => function ($model, $key, $index) {
                                return (!Yii::$app->user->isGuest
                                    && Yii::$app->user->identity->username === $model['borrower'] //datanya sendiri
                                    && $model['approval'] == 0
                                ) ? true : false;
                            },
                            'setujui' => function ($model, $key, $index) {
                                return ($model->approval == 0 && !Yii::$app->user->isGuest && (1 === Yii::$app->user->identity->approver_mobildinas || 0 === Yii::$app->user->identity->level) //datanya sendiri                               
                                ) ? true : false;
                            },
                            'tolak' => function ($model, $key, $index) {
                                return ($model->approval == 0 && !Yii::$app->user->isGuest && (1 === Yii::$app->user->identity->approver_mobildinas || 0 === Yii::$app->user->identity->level) //datanya sendiri                               
                                ) ? true : false;
                            },
                            'batal' => function ($model, $key, $index) {
                                return ($model->approval == 1 && !Yii::$app->user->isGuest && (1 === Yii::$app->user->identity->approver_mobildinas || 0 === Yii::$app->user->identity->level) //datanya sendiri                               
                                ) ? true : false;
                            },
                        ],
                        'buttons'  => [
                            'delete' => function ($url, $model, $key) {
                                return Html::a('<i class="fas text-danger fa-trash-alt"></i> ', $url, [
                                    'title' => 'Hapus data usulan ini',
                                    'data-method' => 'post',
                                    'data-pjax' => 0,
                                    'data-confirm' => 'Anda yakin ingin menghapus usulan ini?'
                                ]);
                            },
                            'setujui' => function ($url, $model, $key) {
                                return Html::a('<i class="fas text-primary fa-check"></i> ', $url, [
                                    'title' => 'Setujui Peminjaman Ini',
                                    'data-method' => 'post',
                                    'data-pjax' => 0,
                                    'data-confirm' => 'Anda yakin ingin menyetujui peminjaman mobil dinas ini? Dari <br/><strong>' . $model['borrower'] . '</strong>',
                                ]);
                            },
                            'tolak' => function ($key, $client) {
                                return Html::a('<i class="fas text-warning fa-times"></i> ', $key, [
                                    'title' => 'Tolak usulan peminjaman ini',
                                    'data-bs-toggle' => 'modal',
                                    'data-bs-target' => '#exampleModal',
                                    'class' => 'modal-link',
                                ]);
                            },
                            'batal' => function ($key, $client) {
                                return Html::a('<i class="fas text-danger fa-times"></i> ', $key, [
                                    'title' => 'Batalkan persetujuan peminjaman ini',
                                    'data-bs-toggle' => 'modal',
                                    'data-bs-target' => '#exampleModal',
                                    'class' => 'modal-link',
                                ]);
                            },
                            'update' => function ($key, $client) {
                                return Html::a('<i class="fa">&#xf044;</i> ', $key, ['title' => 'Update rincian link ini']);
                            },
                            'view' => function ($key, $client) {
                                return Html::a('<i class="fas fa-eye"></i> ', $key, [
                                    'title' => 'Lihat rincian usulan peminjaman ini',
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
                    'pjax' => true,
                ],
                'exportConfig' => [
                    GridView::CSV => ['label' => 'CSV', 'filename' => 'Link usulan zoom Portal Pintar - ' . date('d-M-Y')],
                    GridView::HTML => ['label' => 'HTML', 'filename' => 'Link usulan zoom Portal Pintar - ' . date('d-M-Y')],
                    GridView::EXCEL => ['label' => 'EXCEL', 'filename' => 'Link usulan zoom Portal Pintar - ' . date('d-M-Y')],
                    GridView::TEXT => ['label' => 'TEXT', 'filename' => 'Link usulan zoom Portal Pintar - ' . date('d-M-Y')],
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
            ]); ?>
        </div>
    </div>
</div>