<?php

use app\models\Projectmember;
use yii\helpers\Html;
use kartik\grid\GridView;
use kartik\grid\SerialColumn;
use kartik\grid\ActionColumn;

$this->title = 'Rekap Tim Kerja dalam Aplikasi Ini';
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
            <?= Html::a('<i class="fas fa-file-archive"></i> Arsip Tim Kerja', ['projectmember/index?year='], ['class' => 'btn btn btn-outline-warning btn-sm']) ?>
            <?php if (!Yii::$app->user->isGuest && Yii::$app->user->identity->level == 0) : ?>
                |
                <?= Html::a('<i class="fas fa-user-plus"></i> Tambah Anggota Baru', ['create'], ['class' => 'btn btn btn-outline-warning btn-sm']) ?>
                |
                <?= Html::a('<i class="fas fa-users"></i> Manajemen Pegawai', ['pengguna/index'], ['class' => 'btn btn btn-outline-warning btn-sm']) ?>
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
                        'value' => 'teame.id_team',
                        'group' => true,
                        'enableSorting' => false,
                        'mergeHeader' => true,
                        'label' => '',
                        'vAlign' => 'middle',
                        'hAlign' => 'center'
                    ],
                    [
                        'attribute' => 'teame',
                        'value' => 'teame.nama_team',
                        'group' => true,
                        'enableSorting' => false,
                        // 'mergeHeader' => true,
                        'label' => 'Tim Kerja',
                        'vAlign' => 'middle',
                        'filterInputOptions' => [
                            'class'       => 'form-control',
                            'placeholder' => 'Cari Tim Kerja ...'
                        ]
                    ],
                    [
                        'value' => 'projecte.tahun',
                        'group' => true,
                        'enableSorting' => false,
                        'mergeHeader' => true,
                        'label' => 'Tahun',
                        'vAlign' => 'middle',
                        'hAlign' => 'center'
                    ],
                    [
                        'attribute' => 'fk_project',
                        'value' => 'projecte.nama_project',
                        'group' => true,
                        'enableSorting' => false,
                        'label' => 'Project',
                        'vAlign' => 'middle',
                        'filterInputOptions' => [
                            'class'       => 'form-control',
                            'placeholder' => 'Cari Project Tim Kerja ...'
                        ]
                    ],
                    [
                        'attribute' => 'pegawai',
                        'value' => function ($data) {
                            return $data->penggunae->nama;
                        },
                        'enableSorting' => false,
                        'filterInputOptions' => [
                            'class'       => 'form-control',
                            'placeholder' => 'Cari Nama/Username Pegawai ...'
                        ]
                    ],
                    [
                        'attribute' => 'member_status',
                        'value' => function ($data) {
                            if ($data->member_status == 3)
                                return '<center><span title="Operator" class="badge bg-success rounded-pill"><i class="fab fa-ubuntu"></i> Operator Agenda</span></center>';
                            elseif ($data->member_status == 2)
                                return '<center><span title="Penanggung Jawab" class="badge bg-primary rounded-pill"><i class="fas fa-book-reader"></i> Penanggung Jawab</span></center>';
                            elseif ($data->member_status == 1)
                                return '<center><span title="Anggota" class="badge bg-secondary rounded-pill"><i class="fas fa-user"></i> Anggota</span></center>';
                            elseif ($data->member_status == 0)
                                return '<center><span title="Tidak Aktif" class="badge bg-danger rounded-pill"><i class="fas fa-trash"></i> Tidak Aktif</span></center>';
                            else
                                return '';
                        },
                        'header' => 'Keterangan',
                        'enableSorting' => false,
                        'filter' => false,
                        'format' => 'html',
                        'filter' => \yii\helpers\Html::activeDropDownList($searchModel, 'member_status', [
                            '' => 'Cari Status Anggota ...',
                            1 => 'Anggota',
                            2 => 'Penanggung Jawab',
                            3 => 'Operator Agenda'
                        ], ['class' => 'form-control']),
                    ],
                    [
                        'class' => ActionColumn::class,
                        'header' => 'Aksi',
                        'template' => (!Yii::$app->user->isGuest && Yii::$app->user->identity->theme) == 0
                            ? '{update}{toggleketua}{toggleoperator}{delete}{aktifkanlagi}'
                            : '{update}{toggleketua}{toggleoperator}{delete}{aktifkanlagi}',
                        'visible' => !Yii::$app->user->isGuest ? true : false,
                        'visibleButtons' => [
                            'delete' => function ($model, $key, $index) {
                                $cek = Projectmember::find()->where(['fk_project' => $model->fk_project, 'member_status' => 2, 'pegawai' => $model->pegawai])->count();
                                if (Yii::$app->user->identity->level === 0) {
                                    if ($model->member_status != 0) {
                                        return $cek < 1 ? true : false;
                                    } else
                                        return false;
                                } else
                                    return false;
                            },
                            'aktifkanlagi' => function ($model, $key, $index) {
                                if (Yii::$app->user->identity->level === 0) {
                                    return ($model->member_status == 0) ? true : false;
                                } else
                                    return false;
                            },
                            'update' => function ($model, $key, $index) {
                                if (Yii::$app->user->identity->level === 0) {
                                    return ($model->member_status != 0) ? true : false;
                                } else
                                    return false;
                            },
                            'toggleketua' => function ($model, $key, $index) {
                                if (Yii::$app->user->identity->level === 0) {
                                    if ($model->member_status == 0)
                                        return false; // untuk member aktif saja
                                    elseif ($model->member_status == 2)
                                        return true; //untuk ketua
                                    else { //kalau aktif, bukan ketuaf
                                        $cek = Projectmember::find()->where(['fk_project' => $model->fk_project, 'member_status' => 2])->count();
                                        return ($cek > 0) ? false : true;
                                    }
                                } else
                                    return false;
                            },
                            'toggleoperator' => function ($model, $key, $index) {
                                $pengguna = Yii::$app->user->identity->username;
                                $ketua = Projectmember::find()
                                    ->select('*')
                                    ->where(['fk_project' => $model->fk_project])
                                    ->andWhere(['pegawai' => $pengguna])
                                    ->andWhere(['member_status' => 2])
                                    ->count();
                                // die($ketua);
                                return ((Yii::$app->user->identity->level === 0 || $ketua > 0) && ($model->member_status == 1 || $model->member_status == 3)) ? true : false; // ketua tidak bisa jadi operator
                            },
                        ],
                        'buttons'  => [
                            'delete' => function ($url, $model, $key) {
                                return Html::a('<i class="fas text-danger fa-trash-alt"></i> ', $url, [
                                    'title' => 'Nonaktifkan pengguna ini',
                                    'data-method' => 'post',
                                    'data-pjax' => 0,
                                    'data-confirm' => 'Anda yakin ingin menonaktifkan pengguna ini? <br/><strong>' . $model['penggunae']['nama'] . '</strong> dari <strong>' . $model['projecte']['panggilan_project'] . '</strong>'
                                ]);
                            },
                            'aktifkanlagi' => function ($url, $model, $key) {
                                return Html::a('<i class="fas text-success fa-recycle"></i>', $url, [
                                    'title' => 'Aktifkan pengguna ini',
                                    'data-method' => 'post',
                                    'data-pjax' => 0,
                                    'data-confirm' => 'Anda yakin ingin mengaktifkan kembali pengguna ini? <br/><strong>' . $model['penggunae']['nama'] . '</strong> dari <strong>' . $model['projecte']['panggilan_project'] . '</strong>'
                                ]);
                            },
                            'toggleketua' => function ($url, $model, $key) {
                                return Html::a('<i class="fa text-success">&#xf21b;</i> ', 'toggleketua?id=' . $key, [
                                    'title' => 'Jadikan/Batalkan Ketua',
                                    'data-method' => 'post',
                                    'data-pjax' => 0,
                                    'data-confirm' => 'Anda yakin ingin menjadikan atau membatalkan pegawai ini sebagai ketua di <strong>'
                                        . $model->projecte->panggilan_project . '</strong>?'
                                ]);
                            },
                            'toggleoperator' => function ($url, $model, $key) {
                                return Html::a('<i class="fab fa-ubuntu text-secondary  "></i> ', 'toggleoperator?id=' . $key, [
                                    'title' => 'Jadikan/Batalkan Operator',
                                    'data-method' => 'post',
                                    'data-pjax' => 0,
                                    'data-confirm' => 'Anda yakin ingin menjadikan atau membatalkan pegawai ini sebagai operator di <strong>'
                                        . $model->projecte->panggilan_project . '</strong>?'
                                ]);
                            },
                            'update' => function ($key, $client) {
                                return Html::a('<i class="fa">&#xf044;</i> ', $key, ['title' => 'Update rincian anggota pada project ini']);
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
                'export' => false,
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
    const spans = document.querySelectorAll('span.bg-white'); // select all spans with the class 'bg-white'
    spans.forEach(span => { // loop through each selected span element
        if (span.innerHTML === '') { // check if the innerHTML property is empty
            span.style.display = 'none'; // hide the span element
        }
    });
</script>