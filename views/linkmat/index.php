<?php
use yii\helpers\Html;
use yii\web\View;
use kartik\grid\SerialColumn;
use kartik\grid\ActionColumn;
use kartik\grid\GridView;
use yii\bootstrap5\Modal;
use app\controllers\LinkColumnLinkmat;

$this->title = 'Portal Sharing';
$baseUrl = Yii::$app->request->baseUrl;
// $baseUrl = '/bengkulu'. Yii::$app->request->baseUrl; untuk di webapps
$script = <<< JS
    var baseUrl = '$baseUrl';
JS;
$this->registerJs($script, \yii\web\View::POS_HEAD);
$this->registerJsFile(Yii::$app->request->baseUrl . '/library/js/fi-copy-link-linkapp.js', ['position' => View::POS_END, 'depends' => [\yii\web\JqueryAsset::class]]);
$this->registerJsFile(Yii::$app->request->baseUrl . '/library/js/fi-linkmat.js', ['position' => View::POS_END, 'depends' => [\yii\web\JqueryAsset::class]]);
$this->registerCssFile(Yii::$app->request->baseUrl . '/library/css/fi-linkapp.css', ['position' => View::POS_END, 'depends' => [\yii\web\JqueryAsset::class]]);

?>
<div class="container-fluid" data-aos="fade-up">
    <h1 class="text-center"><?= Html::encode($this->title) ?></h1>
    <hr class="bps" />
    <?php if (!Yii::$app->user->isGuest) : ?>
        <p class="text-center">
            <?= Html::a('<i class="fas fa-folder-plus"></i> Tambah Data Baru', ['create'], ['class' => 'btn btn btn-outline-warning btn-sm']) ?>
        </p>
    <?php endif; ?>
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
                        'attribute' => 'judul',
                    ],
                    [
                        'attribute' => 'keyword',
                    ],
                    [
                        'attribute' => 'owner',
                        'value' => 'ownere.nama',
                    ],
                    [
                        'attribute' => 'active',
                        'value' => function ($data) {
                            if ($data->active == 0)
                                return '<center><span title="Menunggu Moderasi" class="badge bg-secondary rounded-pill"><i class="fas fa-book-reader"></i> Menunggu Moderasi</span></center>';
                            elseif ($data->active == 1)
                                return '<center><span title="Anggota" class="badge bg-primary rounded-pill"><i class="fas fa-user"></i> Aktif</span></center>';
                            elseif ($data->active == 2)
                                return '<center><span title="Dihapus" class="badge bg-danger rounded-pill"><i class="fas fa-trash"></i> Dihapus</span></center>';
                            else
                                return '';
                        },
                        'header' => 'Keterangan',
                        'enableSorting' => false,
                        'filter' => false,
                        'format' => 'html',
                        'filter' => \yii\helpers\Html::activeDropDownList($searchModel, 'active', [
                            '' => 'Cari Status Link ...',
                            0 => 'Menunggu Moderasi',
                            1 => 'Aktif',
                            2 => 'Dihapus'
                        ], ['class' => 'form-control']),
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
                            ? '{update}{view}{share}{delete}{moderasi}'
                            : '{update}{view}{share}{delete}{moderasi}',
                        'visibleButtons' => [
                            'delete' => function ($model, $key, $index) {
                                return ((!Yii::$app->user->isGuest && Yii::$app->user->identity->username === $model['owner'] //datanya sendiri
                                )
                                    && $model['active'] == 1
                                ) ? true : false;
                            },
                            'update' => function ($model, $key, $index) {
                                return ((!Yii::$app->user->isGuest && Yii::$app->user->identity->username === $model['owner'] //datanya sendiri
                                )
                                    && ($model['active'] == 0 || $model['active'] == 1)
                                ) ? true : false;
                            },
                            'moderasi' => function ($model, $key, $index) {
                                return (!Yii::$app->user->isGuest && Yii::$app->user->identity->level == 0 //admin saja
                                    // && $model['active'] == 0
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
                            'moderasi' => function ($url, $model, $key) {
                                if ($model->active == 0) {
                                    return Html::a('<i class="fas text-success fa-check-double"></i>', $url, [
                                        'title' => 'Moderasi link ini',
                                        'data-method' => 'post',
                                        'data-pjax' => 0,
                                        'data-confirm' => 'Anda yakin ingin memoderasi link ini? <br/><strong>' . $model['judul'] . '</strong>'
                                    ]);
                                } elseif ($model->active == 1) {
                                    return Html::a('<i class="fas text-danger fa-times"></i>', $url, [
                                        'title' => 'Sembunyikan link ini',
                                        'data-method' => 'post',
                                        'data-pjax' => 0,
                                        'data-confirm' => 'Anda yakin ingin membatalkan moderasi link ini? <br/><strong>' . $model['judul'] . '</strong>'
                                    ]);
                                }
                            },
                            'update' => function ($key, $client) {
                                return Html::a('<i class="fa">&#xf044;</i> ', $key, ['title' => 'Update rincian link ini']);
                            },
                            'view' => function ($key, $client) {
                                return Html::a('<i class="fas fa-eye"></i> ', $key, [
                                    'title' => 'Lihat rincian materi ini',
                                    'data-bs-toggle' => 'modal',
                                    'data-bs-target' => '#exampleModal',
                                    'class' => 'modal-link',
                                ]);
                            },
                            // 'share' => function ($key, $client) {
                            //     return Html::a('<i class="fas fa-share-alt"></i> ', $key, ['title' => 'Bagikan rincian link ini']);
                            // },
                            'share' => function ($url, $model, $key) {
                                $link = Yii::$app->request->hostInfo . Yii::$app->request->baseUrl . '/linkmat/' . $model->id_linkmat;
                                $judul = $model->judul; // Make sure to properly encode the model title
                                $content = 'Lihat tautan materi ' . $judul . ' di Sistem Portal Pintar ke ' . $link;
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
                    [
                        'class' => LinkColumnLinkmat::class,
                        'attribute' => 'link',
                        'header' => '(Klik untuk Akses Link)',
                        'hAlign' => 'left',
                        'linkOptions' => [
                            // add any additional options for the link element here
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
            ]); ?>
        </div>
    </div>
</div>