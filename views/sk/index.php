<?php
use yii\helpers\Html;
use kartik\grid\SerialColumn;
use kartik\grid\ActionColumn;
use kartik\grid\GridView;

$this->title = 'Portal SK';

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
            <?php if (!Yii::$app->user->isGuest && (Yii::$app->user->identity->sk_maker === 1 || Yii::$app->user->identity->level === 0)) : ?>
                |
                <?= Html::a('<i class="fas fa-folder-plus"></i> Tambah Data Baru', ['create'], ['class' => 'btn btn btn-outline-warning btn-sm']) ?>
            <?php endif; ?>
        </div>
    </div>
    </p>
    <?php //echo $this->render('_search', ['model' => $searchModel]);
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
                        'attribute' => 'nomor_sk',
                        'label' => 'Tanggal pada SK',
                    ],
                    [
                        'attribute' => 'tanggal_sk',
                        'value' => function ($model) {
                            $formatter = Yii::$app->formatter;
                            $formatter->locale = 'id-ID'; // set the locale to Indonesian
                            $timezone = new \DateTimeZone('Asia/Jakarta'); // create a timezone object for WIB
                            $waktumulai = new \DateTime($model->tanggal_sk, new \DateTimeZone('UTC')); // create a datetime object for waktumulai with UTC timezone
                            $waktumulai->setTimeZone($timezone); // set the timezone to WIB
                            $waktumulaiFormatted = $formatter->asDatetime($waktumulai, 'd MMMM Y'); // format the waktumulai datetime value
                            return $waktumulaiFormatted;
                        },
                        'label' => 'Tanggal pada SK',
                        'format' => 'html',
                    ],
                    [
                        'attribute' => 'tentang_sk',
                        'label' => 'Judul/Perihal pada SK',
                    ],
                    [
                        'attribute' => 'nama_dalam_sk',
                    ],
                    [
                        'attribute' => 'reporter',
                    ],
                    [
                        'class' => ActionColumn::class,
                        'header' => 'Aksi',
                        'template' => '{update}{view}',
                        'visibleButtons' => [
                            'delete' => function ($model, $key, $index) {
                                return ((!Yii::$app->user->isGuest && Yii::$app->user->identity->username === $model['reporter'] //datanya sendiri
                                )
                                    && $model['deleted'] == 0
                                ) ? true : false;
                            },
                            'update' => function ($model, $key, $index) {
                                return ((!Yii::$app->user->isGuest && Yii::$app->user->identity->username === $model['reporter'] //datanya sendiri
                                )
                                    && $model['deleted'] == 0
                                ) ? true : false;
                            },
                        ],
                        'buttons'  => [
                            'delete' => function ($url, $model, $key) {
                                return Html::a('<i class="fas text-danger fa-trash-alt"></i> ', $url, [
                                    'title' => 'Hapus SK ini',
                                    'data-method' => 'post',
                                    'data-pjax' => 0,
                                    'data-confirm' => 'Anda yakin ingin menonaktifkan link ini? <br/><strong>' . $model['nomor_sk'] . '</strong>'
                                ]);
                            },
                            'update' => function ($key, $client) {
                                return Html::a('<i class="fa">&#xf044;</i> ', $key, ['title' => 'Update rincian SK ini']);
                            },
                            'view' => function ($url, $model, $key) {
                                return Html::a('<i class="fas fa-eye"></i> ', ['sk/view?id_sk=' . $model->id_sk], [
                                    'title' => 'Lihat rincian SK ini',
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
                    GridView::CSV => ['label' => 'CSV', 'filename' => 'List SK Portal Pintar - ' . date('d-M-Y')],
                    GridView::HTML => ['label' => 'HTML', 'filename' => 'List SK Portal Pintar - ' . date('d-M-Y')],
                    GridView::EXCEL => ['label' => 'EXCEL', 'filename' => 'List SK Portal Pintar - ' . date('d-M-Y')],
                    GridView::TEXT => ['label' => 'TEXT', 'filename' => 'List SK Portal Pintar - ' . date('d-M-Y')],
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