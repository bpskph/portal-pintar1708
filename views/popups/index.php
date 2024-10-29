<?php

use yii\helpers\Html;
use kartik\grid\SerialColumn;
use kartik\grid\ActionColumn;
use kartik\grid\GridView;
use kartik\daterange\DateRangePicker;

$this->title = 'Riwayat Pengumuman (Popups) Melalui Sistem Portal Pintar';

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
            <?php if (!Yii::$app->user->isGuest && Yii::$app->user->identity->level == 0) : ?>
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
                        'attribute' => 'rincian_popups',
                        'vAlign' => 'middle',
                        'format' => 'html',
                    ],                    
                    [
                        'attribute' => 'timestamp_lastupdate',
                        'value' => function ($model) {
                            $formatter = Yii::$app->formatter;
                            $formatter->locale = 'id-ID'; // set the locale to Indonesian
                            $timezone = new \DateTimeZone('Asia/Jakarta'); // create a timezone object for WIB
                            $waktumulai = new \DateTime($model->timestamp_lastupdate, new \DateTimeZone('UTC')); // create a datetime object for waktumulai with UTC timezone
                            $waktumulai->setTimeZone($timezone); // set the timezone to WIB
                            $waktumulaiFormatted = $formatter->asDatetime($waktumulai, 'd MMMM Y, H:mm'); // format the waktumulai datetime value                           
                            return $waktumulaiFormatted . ' WIB';
                        },
                        'filter' => DateRangePicker::widget([
                            'model' => $searchModel,
                            'attribute' => 'timestamp_lastupdate',
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
                        'label' => 'Timestamp (Updated)',
                        'format' => 'html',
                        'vAlign' => 'middle'
                    ],  
                    [
                        'attribute' => 'deleted',
                        'value' => function ($data) {
                            if ($data->deleted == 0)
                                return '<center><span title="Ya" class="badge bg-success rounded-pill"><i class="fas fa-check"></i> Ya</span></center>';
                            elseif ($data->deleted == 1)
                                return '<center><span title="Tidak" class="badge bg-secondary rounded-pill"><i class="fas fa-cross"></i> Tidak</span></center>';                            
                            else
                                return '';
                        },
                        'header' => 'Tampil pada Popup',
                        'format' => 'html',  
                        'vAlign' => 'middle'                     
                    ],                  
                    [
                        'class' => ActionColumn::class,
                        'header' => 'Aksi',
                        'visible'=> Yii::$app->user->identity->level == 0 ? true: false,
                        'template' => (Yii::$app->user->isGuest || Yii::$app->user->identity->theme == 0)
                            ? '{update}{delete}'
                            : '{update}{delete}',                        
                        'buttons'  => [
                            'delete' => function ($url, $model, $key) {
                                return Html::a('<i class="fas text-danger fa-trash-alt"></i> ', $url, [
                                    'title' => 'Hapus data popup ini',
                                    'data-method' => 'post',
                                    'data-pjax' => 0,
                                    'data-confirm' => 'Anda yakin ingin menghapus popup ini?'
                                ]);
                            },
                            'update' => function ($key, $client) {
                                return Html::a('<i class="fa">&#xf044;</i> ', $key, ['title' => 'Update rincian popup ini']);
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
                    GridView::CSV => ['label' => 'CSV', 'filename' => 'Popups Portal Pintar - ' . date('d-M-Y')],
                    GridView::HTML => ['label' => 'HTML', 'filename' => 'Popups Portal Pintar - ' . date('d-M-Y')],
                    GridView::EXCEL => ['label' => 'EXCEL', 'filename' => 'Popups Portal Pintar - ' . date('d-M-Y')],
                    GridView::TEXT => ['label' => 'TEXT', 'filename' => 'Popups Portal Pintar - ' . date('d-M-Y')],
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