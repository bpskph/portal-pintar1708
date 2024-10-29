<?php

use yii\helpers\Html;
use yii\web\View;
use kartik\grid\SerialColumn;
use kartik\grid\ActionColumn;
use kartik\grid\GridView;
use app\controllers\LinkColumnLinkapp;

$this->title = 'Portal Aplikasi';
$this->params['breadcrumbs'][] = $this->title;
$baseUrl = Yii::$app->request->baseUrl;
// $baseUrl = '/bengkulu'. Yii::$app->request->baseUrl; untuk di webapps
$script = <<< JS
    var baseUrl = '$baseUrl';
JS;
$this->registerJs($script, \yii\web\View::POS_HEAD);
$this->registerJsFile(Yii::$app->request->baseUrl . '/library/js/fi-copy-link-linkapp.js', ['position' => View::POS_END, 'depends' => [\yii\web\JqueryAsset::class]]);
$this->registerJsFile(Yii::$app->request->baseUrl . '/library/js/fi-linkappgrid.js', ['position' => View::POS_END, 'depends' => [\yii\web\JqueryAsset::class]]);
$this->registerCssFile(Yii::$app->request->baseUrl . '/library/css/fi-linkapp.css', ['position' => View::POS_END, 'depends' => [\yii\web\JqueryAsset::class]]);

?>
<style>
    .kv-table-header {
        background: transparent !important;
    }
</style>
<div class="container-fluid" data-aos="fade-up">
    <h1 class="text-center"><?= Html::encode($this->title) ?></h1>
    <div class="text-center">
        <?php if (Yii::$app->controller->action->id == 'index') : ?>
            <?= Html::a('<i class="fas fa-list"></i> Ganti View', ['indexgrid'], ['class' => 'btn btn ' . ((!Yii::$app->user->isGuest && Yii::$app->user->identity->theme == 0) ? 'btn-outline-dark' : 'btn-outline-light') . ' btn-sm']) ?>
            <?php if (!Yii::$app->user->isGuest && Yii::$app->user->identity->level == 0) : ?>
                | <?= Html::a('<i class="fas fa-folder-plus"></i> Tambah Data Baru', ['create'], ['class' => 'btn btn btn-outline-warning btn-sm']) ?>
            <?php endif; ?>
        <?php elseif (Yii::$app->controller->action->id == 'indexgrid') : ?>
            <?= Html::a('<i class="fas fa-images"></i> Ganti View', ['index'], ['class' => 'btn btn ' . ((!Yii::$app->user->isGuest && Yii::$app->user->identity->theme == 0) ? 'btn-outline-dark' : 'btn-outline-light') . ' btn-sm']) ?>
            <?php if (!Yii::$app->user->isGuest && Yii::$app->user->identity->level == 0) : ?>
                | <?= Html::a('<i class="fas fa-folder-plus"></i> Tambah Data Baru', ['create'], ['class' => 'btn btn btn-outline-warning btn-sm']) ?>
            <?php endif; ?>
        <?php endif; ?>
    </div>
    <hr class="bps" />

    <?php echo $this->render('_search', ['model' => $searchModel]);
    ?>
    <div class="card <?= ((!Yii::$app->user->isGuest && Yii::$app->user->identity->theme == 0) ? '' : 'bg-dark') ?>">
        <div class="card-body table-responsive p-0">
            <?php
            $layout = '
                        <div class="card-header ' . (!Yii::$app->user->isGuest ? Yii::$app->user->identity->themechoice : '')  . '">
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
                    // 'judul',
                    [
                        'attribute' => 'judul',
                        'filterInputOptions' => [
                            'class'       => 'form-control',
                            'placeholder' => 'Cari Judul ...'
                        ]
                    ],
                    [
                        'class' => LinkColumnLinkapp::class,
                        'attribute' => 'link',
                        'linkOptions' => [
                            // add any additional options for the link element here
                        ],
                        'filterInputOptions' => [
                            'class'       => 'form-control',
                            'placeholder' => 'Cari Link ...'
                        ]
                    ],
                    [
                        'attribute' => 'keyword',
                        'filterInputOptions' => [
                            'class'       => 'form-control',
                            'placeholder' => 'Cari Keyword ...'
                        ]
                    ],
                    // 'views',
                    [
                        'attribute' => 'owner',
                        'value' => 'ownere.nama',
                        'mergeHeader' => 'true',
                        'visible' => (!Yii::$app->user->isGuest && Yii::$app->user->identity->level == 0) ? true : false,
                    ],
                    [
                        'attribute' => 'views',
                        'mergeHeader' => true,
                        'label' => 'Dilihat',
                        'value' => function ($model) {
                            return $model->views . ' kali';
                        }
                    ],
                    [
                        'class' => ActionColumn::class,
                        'header' => 'Aksi',
                        'template' => (Yii::$app->user->isGuest || Yii::$app->user->identity->theme == 0)
                            ? '{update}{delete}{share}{aktifkanlagi}'
                            : '{update}{delete}{share}{aktifkanlagi}',
                        'visible' => !Yii::$app->user->isGuest && Yii::$app->user->identity->level === 0 ? true : false,
                        'visibleButtons' => [
                            'delete' => function ($model, $key, $index) {
                                return (Yii::$app->user->identity->username === $model['owner'] //datanya sendiri
                                    && $model['active'] == 1
                                ) ? true : false;
                            },
                            'update' => function ($model, $key, $index) {
                                return (Yii::$app->user->identity->username === $model['owner'] //datanya sendiri
                                    && $model['active'] == 1
                                ) ? true : false;
                            },
                            'aktifkanlagi' => function ($model, $key, $index) {
                                return (Yii::$app->user->identity->username === $model['owner'] //datanya sendiri
                                    && $model['active'] == 0
                                ) ? true : false;
                            },
                        ],
                        'buttons'  => [
                            'delete' => function ($url, $model, $key) {
                                return Html::a('<i class="fas text-danger fa-trash-alt"></i> ', $url, [
                                    'title' => 'Nonaktifkan link ini',
                                    'data-method' => 'post',
                                    'data-pjax' => 0,
                                    'data-confirm' => 'Anda yakin ingin menonaktifkan link ini? <br/><strong>' . $model['judul'] . '</strong>'
                                ]);
                            },
                            'aktifkanlagi' => function ($url, $model, $key) {
                                return Html::a('<i class="fas text-success fa-recycle"></i>', $url, [
                                    'title' => 'Aktifkan link ini',
                                    'data-method' => 'post',
                                    'data-pjax' => 0,
                                    'data-confirm' => 'Anda yakin ingin mengaktifkan kembali link ini? <br/><strong>' . $model['judul'] . '</strong>'
                                ]);
                            },
                            'update' => function ($key, $client) {
                                return Html::a('<i class="fa">&#xf044;</i> ', $key, ['title' => 'Update rincian link ini']);
                            },
                            'share' => function ($url, $model, $key) {
                                $link = Yii::$app->request->hostInfo . Yii::$app->request->baseUrl . '/linkapp/' . $model->id_linkapp;
                                $judul = $model->judul; // Make sure to properly encode the model title
                                $content = 'Lihat tautan aplikasi dari Sistem Portal Pintar (' . Yii::$app->request->hostInfo . Yii::$app->request->baseUrl . '), yaitu ' . $judul . ' ke ' . $model->link;
                                // Generate the HTML for the link/button to copy the content
                                $buttonHtml = Html::a(
                                    '<i class="fas fa-share-alt"></i> ',
                                    '#', // We'll use JavaScript to handle the click event
                                    [
                                        'title' => 'Bagikan rincian link ini',
                                        'class' => 'copy-link-button',
                                        'data-content' => $content, // Store the content as data for the button
                                    ]
                                );
                                return $buttonHtml;
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
                    GridView::CSV => ['label' => 'CSV', 'filename' => 'Link Aplikasi Portal Pintar - ' . date('d-M-Y')],
                    GridView::HTML => ['label' => 'HTML', 'filename' => 'Link Aplikasi Portal Pintar - ' . date('d-M-Y')],
                    GridView::EXCEL => ['label' => 'EXCEL', 'filename' => 'Link Aplikasi Portal Pintar - ' . date('d-M-Y')],
                    GridView::TEXT => ['label' => 'TEXT', 'filename' => 'Link Aplikasi Portal Pintar - ' . date('d-M-Y')],
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