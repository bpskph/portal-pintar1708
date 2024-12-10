<?php

use app\models\Pengguna;
use yii\helpers\Html;
use kartik\grid\SerialColumn;
use kartik\grid\ActionColumn;
use kartik\grid\GridView;

$this->title = 'Portal DL';
$baseUrl = Yii::$app->request->baseUrl;
$script = <<< JS
    var baseUrl = '$baseUrl';
JS;
$this->registerJs($script, \yii\web\View::POS_HEAD);

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
                        'attribute' => 'pegawai',
                        'value' => function ($model) {
                            if ($model->pegawai != null) {
                                // Step 1: Get the list of email addresses from the peserta attribute in the agenda table
                                $emailList = explode(', ', $model->pegawai);
                                // Step 2: Extract the username (without "@bps.go.id") from each email address
                                $usernames = [];
                                foreach ($emailList as $email) {
                                    $username = substr($email, 0, strpos($email, '@'));
                                    $usernames[] = $username;
                                }
                                // Step 3: Query the pengguna table for the list of names that correspond to the extracted usernames
                                $names = Pengguna::find()
                                    ->select('nama')
                                    ->where(['in', 'username', $usernames])
                                    ->column();
                                // Step 4: Convert the list of names to a string in the format that can be used for autofill
                                // $autofillString = implode('<br> ', $names);
                                $listItems = '';
                                if (count($names) <= 1) {
                                    $autofillString = $names[0];
                                } else {
                                    foreach ($names as $key => $name) {
                                        $listItems .= '<li>' .  ' ' . $name . '</li>';
                                    }
                                    $autofillString = '<ol>' . $listItems . '</ol>';
                                }
                                // print_r($autofillString);
                                // Step 5: Set the content of the editor using the html option
                            } else {
                                $autofillString = '-';
                            }
                            return $autofillString;
                        },
                        'format' => 'html',
                        'vAlign' => 'middle'
                    ],
                    [
                        'attribute' => 'tanggal_mulai',
                        'value' => function ($model) {
                            $formatter = Yii::$app->formatter;
                            $formatter->locale = 'id-ID'; // set the locale to Indonesian
                            $timezone = new \DateTimeZone('Asia/Jakarta'); // create a timezone object for WIB
                            $waktumulai = new \DateTime($model->tanggal_mulai, new \DateTimeZone('UTC')); // create a datetime object for waktumulai with UTC timezone
                            $waktumulai->setTimeZone($timezone); // set the timezone to WIB
                            $waktumulaiFormatted = $formatter->asDatetime($waktumulai, 'd MMMM Y'); // format the waktumulai datetime value
                            $waktuselesai = new \DateTime($model->tanggal_selesai, new \DateTimeZone('UTC')); // create a datetime object for waktuselesai with UTC timezone
                            $waktuselesai->setTimeZone($timezone); // set the timezone to WIB
                            $waktuselesaiFormatted = $formatter->asDatetime($waktuselesai, 'd MMMM Y'); // format the waktuselesai time value only
                            if ($waktumulai->format('Y-m') === $waktuselesai->format('Y-m')) {
                                // if waktumulai and waktuselesai are on the same month, format the time range differently
                                $waktumulaiFormatted = $formatter->asDatetime($waktumulai, 'd'); // format the waktumulai datetime value with the year and time
                                return $waktumulaiFormatted . ' - ' . $waktuselesaiFormatted; // concatenate the formatted dates
                            } else {
                                // if waktumulai and waktuselesai are on different days, format the date range normally
                                $waktuselesaiFormatted = $formatter->asDatetime($waktuselesai, 'd MMMM Y'); // format the waktuselesai datetime value
                                return $waktumulaiFormatted . ' s.d ' . $waktuselesaiFormatted; // concatenate the formatted dates
                            }
                        },
                        'label' => 'Tanggal Dinas Luar',
                        'format' => 'html',
                    ],
                    [
                        'attribute' => 'fk_tujuan',
                        'value' => function ($model) {
                            return '[' . $model->fk_tujuan . '] ' . $model->tujuane->nama_tujuan;
                        },
                        'label' => 'Tujuan Dinas Luar',
                    ],
                    'tugas:ntext',
                    [
                        'attribute' => 'tim',
                        'value' => function ($model) {
                            return $model->pelaksanae;
                        }
                    ],
                    [
                        'attribute' => 'reporter',
                        'value' => function ($model) {
                            return $model->reportere->nama;
                        }
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
                                return Html::a('<i class="fas fa-eye"></i> ', ['dl/view?id_dl=' . $model->id_dl], [
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
                'rowOptions' => function ($model, $key, $index, $grid) {
                    return ['class' => 'kv-align-middle'];
                },
                'export' => [
                    'fontAwesome' => true,
                    'label' => '<i class="fa">&#xf56d;</i>',
                    'pjax' => false,
                ],
                'exportConfig' => [
                    GridView::CSV => ['label' => 'CSV', 'filename' => 'List DL Portal Pintar - ' . date('d-M-Y')],
                    GridView::HTML => ['label' => 'HTML', 'filename' => 'List DL Portal Pintar - ' . date('d-M-Y')],
                    GridView::EXCEL => ['label' => 'EXCEL', 'filename' => 'List DL Portal Pintar - ' . date('d-M-Y')],
                    GridView::TEXT => ['label' => 'TEXT', 'filename' => 'List DL Portal Pintar - ' . date('d-M-Y')],
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