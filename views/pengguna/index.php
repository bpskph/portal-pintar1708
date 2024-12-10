<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use kartik\grid\SerialColumn;
use kartik\grid\ActionColumn;

$this->title = 'Rekap Pegawai dalam Aplikasi Ini';
?>
<style>
    .kv-table-header {
        background: transparent !important;
    }
</style>
<div class="container-fluid" data-aos="fade-up">
    <h1><?= $this->title ?></h1>
    <hr class="bps" />
    <p>
    <div class="d-flex justify-content-between" style="margin-bottom: -0.8rem;">
        <div class="p-2">
        </div>
        <div class="p-2">
        </div>
        <div class="p-2">
            <?php if (Yii::$app->user->identity->level == 0) : ?>
                <?= Html::a('<i class="fas fa-user-plus"></i> Tambah Data Baru', ['create'], ['class' => 'btn btn btn-outline-warning btn-sm']) ?>
                |
                <?= Html::a('<i class="fas fa-network-wired"></i> Manajemen Tim Kerja', ['projectmember/index?year=' . date("Y")], ['class' => 'btn btn btn-outline-warning btn-sm']) ?>
            <?php endif; ?>
        </div>
    </div>
    </p>

    <?php echo $this->render('_search', ['model' => $searchModel]);
    ?>
    <div class="card <?= ((!Yii::$app->user->isGuest && Yii::$app->user->identity->theme == 0) ? '' : 'bg-dark') ?>">
        <div class="card-body table-responsive p-0">
            <?php
            $layout = '
                        <div class="card-header ' . Yii::$app->user->identity->themechoice . '">
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
                    'nama',
                    [
                        'attribute' => 'username',
                        'value' => function ($model) {
                            return $model->username . '@bps.go.id';
                        }
                    ],
                    [
                        'attribute' => 'formattedNip',
                        'label' => 'NIP',
                    ],
                    [
                        'attribute' => 'nip',
                        'label' => 'NIP BPS',
                    ],
                    [
                        'class' => ActionColumn::class,
                        'header' => 'Aksi',
                        'template' => Yii::$app->user->identity->theme == 0
                            ? '{update}{delete}{aktifkanlagi}{approverevokelevel}'
                            : '{update}{delete}{aktifkanlagi}{approverevokelevel}',
                        'visibleButtons' => [
                            'delete' => function ($model, $key, $index) {
                                if (Yii::$app->user->identity->level === 0) {
                                    return (Yii::$app->user->identity->username === $model['username'] //datanya sendiri
                                        || $model->level == 2
                                    ) ? false : true;
                                } else
                                    return false;
                            },
                            'aktifkanlagi' => function ($model, $key, $index) {
                                return ($model->level == 2) ? true : false;
                            },
                            'approverevokelevel' => function ($model, $key, $index) {
                                if (Yii::$app->user->identity->level === 0) {
                                    return (Yii::$app->user->identity->username === $model['username'] //datanya sendiri
                                        || $model->level == 2 //pengguna tidak aktif tidak bisa jadi admin
                                    ) ? false : true;
                                } else
                                    return false;
                            },
                        ],
                        'buttons'  => [
                            'delete' => function ($url, $model, $key) {
                                return Html::a('<i class="fas text-danger fa-trash-alt"></i> ', $url, [
                                    'title' => 'Nonaktifkan pengguna ini',
                                    'data-method' => 'post',
                                    'data-pjax' => 0,
                                    'data-confirm' => 'Anda yakin ingin menonaktifkan pengguna ini? <br/><strong>' . $model['nama'] . '</strong>'
                                ]);
                            },
                            'aktifkanlagi' => function ($url, $model, $key) {
                                return Html::a('<i class="fas text-danger fa-recycle"></i>', $url, [
                                    'title' => 'Aktifkan pengguna ini',
                                    'data-method' => 'post',
                                    'data-pjax' => 0,
                                    'data-confirm' => 'Anda yakin ingin mengaktifkan kembali pengguna ini? <br/><strong>' . $model['nama'] . '</strong>'
                                ]);
                            },
                            'approverevokelevel' => function ($url, $model, $key) {
                                return Html::a('<i class="fa text-success">&#xf21b;</i> ', 'approverevokelevel?id=' . $key, [
                                    'title' => 'Jadikan admin',
                                    'data-method' => 'post',
                                    'data-pjax' => 0,
                                    'data-confirm' => 'Anda yakin ingin me-revoke/approve level pegawai ini? <br/><strong>'
                                        . $model->nama . '</strong> sebagai <strong>Admin</strong>'
                                ]);
                            },
                            'update' => function ($key, $client) {
                                return Html::a('<i class="fa">&#xf044;</i> ', $key, ['title' => 'Update rincian pengguna ini']);
                            },
                        ],
                    ],
                    [
                        'attribute' => 'level',
                        'label' => 'Status Akses',
                        'format' => 'raw',
                        'value' => function ($data) {
                            if ($data['level'] === 1)
                                return '<center><span title="Aktif" class="badge bg-primary rounded-pill"><i class="fas fa-check"></i></span></center>';
                            elseif ($data['level'] === 0)
                                return '<center><span title="Admin" class="badge bg-success rounded-pill"><i class="fas fa-user-secret"></i></span></center>';
                            elseif ($data['level'] === 2)
                                return '<center><span title="Non Aktif" class="badge bg-danger rounded-pill"><i class="fas fa-trash-alt"></i></span></center>';
                            else
                                return '<center><i class="fas fa-times"></i></center>';
                        },
                        'filter' => false,
                        'mergeHeader' => true,
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
                ],
                'exportConfig' => [
                    GridView::CSV => ['label' => 'CSV', 'filename' => 'Pengguna Portal Pintar - ' . date('d-M-Y')],
                    GridView::HTML => ['label' => 'HTML', 'filename' => 'Pengguna Portal Pintar - ' . date('d-M-Y')],
                    GridView::EXCEL => ['label' => 'EXCEL', 'filename' => 'Pengguna Portal Pintar - ' . date('d-M-Y')],
                    GridView::TEXT => ['label' => 'TEXT', 'filename' => 'Pengguna Portal Pintar - ' . date('d-M-Y')],
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
<script>
    const spans = document.querySelectorAll('span.bg-white'); // select all spans with the class 'bg-white'
    spans.forEach(span => { // loop through each selected span element
        if (span.innerHTML === '') { // check if the innerHTML property is empty
            span.style.display = 'none'; // hide the span element
        }
    });
</script>