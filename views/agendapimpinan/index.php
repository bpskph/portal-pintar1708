<?php

use app\models\Pengguna;
use yii\helpers\Html;
use yii\web\View;
use kartik\grid\SerialColumn;
use kartik\grid\ActionColumn;
use kartik\grid\GridView;
use kartik\daterange\DateRangePicker;

$this->title = 'Agenda Pimpinan';

$this->registerCssFile(Yii::$app->request->baseUrl . '/library/css/fi-agenda-index.css', ['position' => View::POS_HEAD, 'depends' => [\yii\web\JqueryAsset::class]]);
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
            echo Html::a('<i class="fas fa-home"></i> Beranda Agenda', $homeUrl, ['class' => 'btn btn btn-outline-warning btn-sm']);
            ?>
            <?php if (!Yii::$app->user->isGuest && (Yii::$app->user->identity->level == 0 || Yii::$app->user->identity->issekretaris)) : ?>
                |
                <?= Html::a('<i class="fas fa-folder-plus"></i> Tambah Data Baru', ['create'], ['class' => 'btn btn btn-outline-warning btn-sm']) ?>
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
                        'attribute' => 'kegiatan',
                        'vAlign' => 'middle'
                    ],
                    [
                        'attribute' => 'waktu',
                        'value' => function ($model) {
                            $formatter = Yii::$app->formatter;
                            $formatter->locale = 'id-ID'; // set the locale to Indonesian
                            $timezone = new \DateTimeZone('Asia/Jakarta'); // create a timezone object for WIB
                            $waktumulai = new \DateTime($model->waktumulai, new \DateTimeZone('UTC')); // create a datetime object for waktumulai with UTC timezone
                            $waktumulai->setTimeZone($timezone); // set the timezone to WIB
                            $waktumulaiFormatted = $formatter->asDatetime($waktumulai, 'd MMMM Y, H:mm'); // format the waktumulai datetime value
                            $waktuselesai = new \DateTime($model->waktuselesai, new \DateTimeZone('UTC')); // create a datetime object for waktuselesai with UTC timezone
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
                        'attribute' => 'tempat',
                        'vAlign' => 'middle'
                    ],
                    // 'pendamping',
                    [
                        'attribute' => 'pendamping',
                        'value' => function ($model) {
                            if ($model->pendamping != null) {
                                // Step 1: Get the list of email addresses from the peserta attribute in the agenda table
                                $emailList = explode(', ', $model->pendamping);
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
                        'class' => ActionColumn::class,
                        'header' => 'Aksi',
                        'template' => (Yii::$app->user->isGuest || Yii::$app->user->identity->theme == 0)
                            ? '{update}{view}{delete}'
                            : '{update}{view}{delete}',
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
                                    'title' => 'Hapus agenda pimpinan ini',
                                    'data-method' => 'post',
                                    'data-pjax' => 0,
                                    'data-confirm' => 'Anda yakin ingin menghapus agenda pimpinan ini? <br/><strong>' . $model['kegiatan'] . '</strong>'
                                ]);
                            },
                            'update' => function ($key, $client) {
                                return Html::a('<i class="fa">&#xf044;</i> ', $key, ['title' => 'Update rincian agenda pimpinan ini']);
                            },
                            'view' => function ($key, $client) {
                                return Html::a('<i class="fas fa-eye"></i> ', $key, [
                                    'title' => 'Lihat rincian agenda pimpinan ini',
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
                    GridView::CSV => ['label' => 'CSV', 'filename' => 'Agenda Pimpinan dari Portal Pintar - ' . date('d-M-Y')],
                    GridView::HTML => ['label' => 'HTML', 'filename' => 'Agenda Pimpinan dari Portal Pintar - ' . date('d-M-Y')],
                    GridView::EXCEL => ['label' => 'EXCEL', 'filename' => 'Agenda Pimpinan dari Portal Pintar - ' . date('d-M-Y')],
                    GridView::TEXT => ['label' => 'TEXT', 'filename' => 'Agenda Pimpinan dari Portal Pintar - ' . date('d-M-Y')],
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