<?php

use app\models\Agenda;
use app\models\Agendapimpinan;
use app\models\Laporan;
use app\models\Project;
use app\models\Projectmember;
use app\models\Rooms;
use yii\helpers\Html;
use kartik\grid\SerialColumn;
use kartik\grid\ActionColumn;
use kartik\grid\GridView;
use kartik\daterange\DateRangePicker;
use yii\helpers\ArrayHelper;
use yii\web\View;

$this->registerJsFile(Yii::$app->request->baseUrl . '/library/js/fi-copy-link-agenda.js', ['position' => View::POS_END, 'depends' => [\yii\web\JqueryAsset::class]]);
$this->registerJsFile(Yii::$app->request->baseUrl . '/library/js/fi-agenda-index.js', ['position' => View::POS_END, 'depends' => [\yii\web\JqueryAsset::class]]);
$this->registerCssFile(Yii::$app->request->baseUrl . '/library/css/fi-agenda-index.css', ['position' => View::POS_HEAD, 'depends' => [\yii\web\JqueryAsset::class]]);
$this->title = 'Agenda Kantor BPS Provinsi Bengkulu';

?>
<?php
// $agendas = Agenda::find()->select('*')
//     ->joinWith(['laporane'])
//     ->andWhere(['pemimpin' => Yii::$app->user->identity->username])
//     ->andWhere(['approval' => 0])
//     ->andWhere(['deleted' => 0])
//     ->andWhere(['progress' => 1])
//     // ->andWhere('id_agenda > 340')
//     ->orderBy(['waktumulai' => SORT_ASC])
//     ->all();
if (!Yii::$app->user->isGuest)
    $agendas = Agenda::find()
        ->select('*')
        ->leftJoin('laporan', 'agenda.id_agenda = laporan.id_laporan') // LEFT JOIN with laporan table
        ->where(['pemimpin' => Yii::$app->user->identity->username])
        ->andWhere(['>', 'id_agenda', 340])
        ->andWhere([
            'or',
            ['laporan.id_laporan' => null],
            [
                'and',
                ['approval' => 0],
                ['deleted' => 0],
                ['progress' => 1],
            ]
        ]) // Conditions for no matching laporans
        ->orderBy(['waktumulai' => SORT_ASC])
        ->all();

?>
<?php if (!Yii::$app->user->isGuest && count($agendas) > 0) : ?>
    <div class="toast-container position-fixed p-3 bottom-0 end-0" data-aos="fade-left" id="toastSurat">
        <div class="toast show shadow-sm p-1 mb-2 rounded" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="toast-header bg-warning text-dark">
                <img src="<?php echo Yii::$app->request->baseUrl ?>/images/favicon.png" class="rounded me-2" width="20" height="20" alt="Persetujuan Surat">
                <strong class="me-auto">Kegiatan yang Belum Tersedia Laporan Atau Laporannya Belum Disetujui</strong>
                <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
            <div class="toast-body bg-light text-dark">
                Sejak 4 Juni 2024, berikut daftar <strong>kegiatan yang Anda pimpin</strong> yang belum tersedia laporan atau laporannya belum Anda setujui:
                <table class="table table-sm">
                    <tbody>
                        <?php foreach ($agendas as $agenda) : ?>
                            <?php
                            $formatter = Yii::$app->formatter;
                            $formatter->locale = 'id-ID'; // set the locale to Indonesian
                            $timezone = new \DateTimeZone('Asia/Jakarta'); // create a timezone object for WIB
                            $waktumulai = new \DateTime($agenda->waktumulai, new \DateTimeZone('UTC')); // create a datetime object for waktumulai with UTC timezone
                            $waktumulai->setTimeZone($timezone); // set the timezone to WIB
                            $waktumulaiFormatted = $formatter->asDatetime($waktumulai, 'd MMMM Y, H:mm'); // format the waktumulai datetime value
                            $waktuselesai = new \DateTime($agenda->waktuselesai, new \DateTimeZone('UTC')); // create a datetime object for waktuselesai with UTC timezone
                            $waktuselesai->setTimeZone($timezone); // set the timezone to WIB
                            $waktuselesaiFormatted = $formatter->asDatetime($waktuselesai, 'H:mm'); // format the waktuselesai time value only
                            if ($waktumulai->format('Y-m-d') === $waktuselesai->format('Y-m-d')) {
                                // if waktumulai and waktuselesai are on the same day, format the time range differently
                                $waktumulaiFormatted = $formatter->asDatetime($waktumulai, 'd MMMM Y, H:mm'); // format the waktumulai datetime value with the year and time
                                $waktuFormatted = $waktumulaiFormatted . ' - ' . $waktuselesaiFormatted . ' WIB'; // concatenate the formatted dates
                            } else {
                                // if waktumulai and waktuselesai are on different days, format the date range normally
                                $waktuselesaiFormatted = $formatter->asDatetime($waktuselesai, 'd MMMM Y, H:mm'); // format the waktuselesai datetime value
                                $waktuFormatted = $waktumulaiFormatted . ' WIB <br/>s.d ' . $waktuselesaiFormatted . ' WIB'; // concatenate the formatted dates
                            }
                            ?>
                            <tr>
                                <th scope="row"><i class="fas fa-calendar-alt text-info me-2"></i><?= $agenda->kegiatan ?></th>
                                <td><?= $waktuFormatted ?></td>
                                <?php
                                // if (isset($agenda['laporane']) && $agenda['laporane'] !== null) {
                                if (isset($agenda['laporane'])) {
                                    if ($agenda['laporane']['approval'] == 1) {
                                        $tampil = 'Disetujui';
                                        $link = 'laporan';
                                    } else {
                                        $tampil = 'Belum Disetujui';
                                        $link = 'laporan';
                                    }
                                } else {
                                    $tampil = 'Belum Ada Laporan';
                                    $link = 'agenda';
                                }
                                ?>
                                <?= '<td><strong>' . $tampil . '</strong></td>' ?>

                                <td><a href="<?= Yii::$app->request->baseUrl . '/' . $link . '/' . $agenda->id_agenda ?>" target="_blank"><i class="fas fa-eye"></i> Lihat</a></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
<?php endif; ?>
<?php
$agendapimpinan = Agendapimpinan::find()->select('*')
    ->where(['>=', 'DATE(waktumulai)', date('Y-m-d')])
    ->andWhere(['deleted' => '0'])
    ->orderBy(['waktumulai' => SORT_ASC])
    ->one(); ?>
<?php if (!empty($agendapimpinan)) : ?>
    <?php
    $formatter = Yii::$app->formatter;
    $formatter->locale = 'id-ID'; // set the locale to Indonesian
    $timezone = new \DateTimeZone('Asia/Jakarta'); // create a timezone object for WIB
    $waktumulai = new \DateTime($agendapimpinan->waktumulai, new \DateTimeZone('UTC')); // create a datetime object for waktumulai_tunda with UTC timezone
    $waktumulai->setTimeZone($timezone); // set the timezone to WIB
    $waktumulaiFormatted = $formatter->asDatetime($waktumulai, 'd MMMM Y, H:mm'); // format the waktumulai_tunda datetime value
    $waktuselesai = new \DateTime($agendapimpinan->waktuselesai, new \DateTimeZone('UTC')); // create a datetime object for waktuselesai_tunda with UTC timezone
    $waktuselesai->setTimeZone($timezone); // set the timezone to WIB
    $waktuselesaiFormatted = $formatter->asDatetime($waktuselesai, 'd MMMM Y, H:mm'); // format the waktuselesai_tunda time value only
    if ($waktumulai->format('Y-m-d') === $waktuselesai->format('Y-m-d')) {
        // if waktumulai_tunda and waktuselesai_tunda are on the same day, format the time range differently
        $waktumulaiFormatted = $formatter->asDatetime($waktumulai, 'd MMMM Y, H:mm'); // format the waktumulai_tunda datetime value with the year and time
        $waktutampil =  $waktumulaiFormatted . ' - ' . $waktuselesaiFormatted . ' WIB'; // concatenate the formatted dates
    } else {
        // if waktumulai_tunda and waktuselesai_tunda are on different days, format the date range normally
        $waktumulaiFormatted = $formatter->asDatetime($waktumulai, 'd MMMM Y, H:mm'); // format the waktuselesai_tunda datetime value
        $waktutampil =  $waktumulaiFormatted . ' WIB s.d ' . $waktuselesaiFormatted . ' WIB'; // concatenate the formatted dates
    }
    ?>
    <div class="toast-container position-fixed p-3 bottom-0 end-0" data-aos="fade-left">
        <div class="toast show shadow-sm p-1 mb-2 rounded" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="toast-header bg-info">
                <img src="<?php echo Yii::$app->request->baseUrl ?>/images/favicon.png" class="rounded me-2" width="20" height="20" alt="Agenda Pimpinan">
                <strong class="me-auto">Agenda Pimpinan Terkini</strong>
                <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
            <div class="toast-body bg-light text-dark">
                <table class="table table-sm">
                    <tbody>
                        <tr>
                            <th scope="row"><i class="fas fa-calendar-alt text-info"></i></th>
                            <td><?= $waktutampil ?></td>
                        </tr>
                        <tr>
                            <th scope="row"><i class="fab fa-black-tie text-info"></i></th>
                            <td><?= $agendapimpinan->kegiatan ?></td>
                        </tr>
                        <tr>
                            <th scope="row"><i class="fas fa-street-view text-info"></i></th>
                            <td><?= $agendapimpinan->tempat ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
<?php endif; ?>
<?php
$ada = $dataProvider->getModels();
?>
<?php
$roomOptions = ArrayHelper::map(Rooms::find()->select(['id_rooms', 'nama_ruangan'])->all(), 'id_rooms', 'nama_ruangan');
$roomOptions['other'] = 'Lainnya';
$projectOptions = ArrayHelper::map(Project::find()->select(['id_project', 'panggilan_project'])->all(), 'id_project', 'panggilan_project');
$projectOptions['other'] = 'Lainnya';
?>
<?php
$kolomTampil = [
    [
        'class' => SerialColumn::class,
    ],
    [
        'attribute' => 'kegiatan',
        'filterInputOptions' => [
            'class'       => 'form-control',
            'placeholder' => 'Filter ...'
        ],
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
        'attribute' => 'waktu',
        'value' => function ($model) {
            if ($model->waktumulai_tunda != NULL && $model->waktuselesai_tunda) {
                $formatter = Yii::$app->formatter;
                $formatter->locale = 'id-ID'; // set the locale to Indonesian
                $timezone = new \DateTimeZone('Asia/Jakarta'); // create a timezone object for WIB
                $waktumulai_tunda = new \DateTime($model->waktumulai_tunda, new \DateTimeZone('UTC')); // create a datetime object for waktumulai_tunda with UTC timezone
                $waktumulai_tunda->setTimeZone($timezone); // set the timezone to WIB
                $waktumulai_tundaFormatted = $formatter->asDatetime($waktumulai_tunda, 'd MMMM Y, H:mm'); // format the waktumulai_tunda datetime value
                $waktuselesai_tunda = new \DateTime($model->waktuselesai_tunda, new \DateTimeZone('UTC')); // create a datetime object for waktuselesai_tunda with UTC timezone
                $waktuselesai_tunda->setTimeZone($timezone); // set the timezone to WIB
                $waktuselesai_tundaFormatted = $formatter->asDatetime($waktuselesai_tunda, 'H:mm'); // format the waktuselesai_tunda time value only
                if ($waktumulai_tunda->format('Y-m-d') === $waktuselesai_tunda->format('Y-m-d')) {
                    // if waktumulai_tunda and waktuselesai_tunda are on the same day, format the time range differently
                    $waktumulai_tundaFormatted = $formatter->asDatetime($waktumulai_tunda, 'd MMMM Y, H:mm'); // format the waktumulai_tunda datetime value with the year and time
                    return $waktumulai_tundaFormatted . ' - ' . $waktuselesai_tundaFormatted . ' WIB'; // concatenate the formatted dates
                } else {
                    // if waktumulai_tunda and waktuselesai_tunda are on different days, format the date range normally
                    $waktuselesai_tundaFormatted = $formatter->asDatetime($waktuselesai_tunda, 'd MMMM Y, H:mm'); // format the waktuselesai_tunda datetime value
                    return $waktumulai_tundaFormatted . ' WIB <br/>s.d ' . $waktuselesai_tundaFormatted . ' WIB'; // concatenate the formatted dates
                }
            } else {
                return '<center>-</center>';
            }
        },
        'label' => 'Waktu Penundaan',
        'format' => 'html',
        'vAlign' => 'middle'
    ],
    [
        'attribute' => 'metode',
        'value' => function ($data) {
            if ($data->metode == 0)
                return '<center><span title="Online" class="badge bg-primary rounded-pill"><i class="fas fa-signal"></i> Online</span></center>';
            elseif ($data->metode == 1)
                return '<center><span title="Offline" class="badge bg-success rounded-pill"><i class="fas fa-warehouse"></i> Offline</span></center>';
            elseif ($data->metode == 2)
                return '<center><span title="Hybrid" class="badge bg-secondary rounded-pill"><i class="fab fa-mix"></i> Hybrid</span></center>';
            else
                return '';
        },
        'header' => 'Jenis',
        'enableSorting' => false,
        'format' => 'html',
        'filter' => \yii\helpers\Html::activeDropDownList($searchModel, 'metode', [
            '' => 'Filter ...',
            0 => 'Online',
            1 => 'Offline',
            2 => 'Hybrid'
        ], ['class' => 'form-control']),
        'vAlign' => 'middle',
        'hAlign' => 'center'
    ],
    [
        'attribute' => 'progress',
        'value' => function ($data) {
            if ($data->progress == 0)
                return '<center><span title="Rencana" class="badge bg-primary rounded-pill"><i class="fas fa-plus-square"></i> Rencana</span></center>';
            elseif ($data->progress == 1)
                return '<center><span title="Selesai" class="badge bg-success rounded-pill"><i class="fas fa-check"></i> Selesai</span></center>';
            elseif ($data->progress == 2)
                return '<center><span title="Tunda" class="badge bg-secondary rounded-pill"><i class="fas fa-strikethrough"></i> Tunda</span></center>';
            elseif ($data->progress == 3)
                return '<center><span title="Batal" class="badge bg-danger rounded-pill"><i class="fas fa-trash-alt"></i> Batal</span></center>';
            else
                return '';
        },
        'header' => 'Progress',
        'enableSorting' => false,
        'filter' => false,
        'format' => 'html',
        'filter' => \yii\helpers\Html::activeDropDownList($searchModel, 'metode', [
            '' => 'Filter ...',
            0 => 'Rencana',
            1 => 'Selesai',
            2 => 'Tunda',
            3 => 'Batal'
        ], ['class' => 'form-control']),
        'vAlign' => 'middle',
        'hAlign' => 'center'
    ],
    [
        'attribute' => 'tempat',
        'value' => 'tempate',
        'filter' => Html::activeDropDownList($searchModel, 'tempat', $roomOptions, ['class' => 'form-control', 'prompt' => 'Filter ...']),
        'vAlign' => 'middle',
        'label' => 'Ruangan'
    ],
    [
        'attribute' => 'pelaksana',
        'value' => 'pelaksanae',
        'filter' => Html::activeDropDownList($searchModel, 'pelaksana', $projectOptions, ['class' => 'form-control', 'prompt' => 'Filter ...']),
        'vAlign' => 'middle',
        'label' => 'Pelaksana'
    ],
    [
        'attribute' => 'reporter',
        'value' => 'reportere.nama',
        'label' => 'Diusulkan Oleh',
        'mergeHeader' => true
    ],
    [
        'class' => ActionColumn::class,
        'header' => 'Atur Jadwal',
        'template' => (Yii::$app->user->isGuest || Yii::$app->user->identity->theme == 0)
            ? '{selesai}{tunda}{batal}{rencana}'
            : '{selesai}{tunda}{batal}{rencana}',
        'visible' => Yii::$app->user->isGuest ? false : true,
        'visibleButtons' => [
            'batal' => function ($model, $key, $index) {
                return (!Yii::$app->user->isGuest
                    && Yii::$app->user->identity->username === $model['reporter'] //datanya sendiri
                    && $model['progress'] !== 1 && $model['progress'] !== 3
                ) ? true : false;
            },
            'tunda' => function ($model, $key, $index) {
                return (!Yii::$app->user->isGuest
                    && Yii::$app->user->identity->username === $model['reporter'] //datanya sendiri
                    && $model['progress'] !== 1 && $model['progress'] !== 3 && $model['progress'] !== 2
                ) ? true : false;
            },
            'selesai' => function ($model, $key, $index) {
                return (!Yii::$app->user->isGuest
                    && Yii::$app->user->identity->username === $model['reporter'] //datanya sendiri
                    && $model['progress'] !== 1 && $model['progress'] !== 3
                ) ? true : false;
            },
            'rencana' => function ($model, $key, $index) {
                return (!Yii::$app->user->isGuest
                    && Yii::$app->user->identity->username === $model['reporter'] //datanya sendiri
                    && $model['progress'] == 3
                ) ? true : false;
            },
        ],
        'buttons'  => [
            'batal' => function ($url, $model, $key) {
                return Html::a('<i class="fas text-danger fa-plus-square"></i> ', $url, [
                    'title' => 'Batalkan agenda ini',
                    'data-method' => 'post',
                    'data-pjax' => 0,
                    'data-confirm' => 'Anda yakin ingin membatalkan agenda ini? <br/><strong>' . $model['kegiatan'] . '</strong>'
                ]);
            },
            'selesai' => function ($url, $model, $key) {
                return Html::a('<i class="fas text-success fa-check-square"></i> ', $url, [
                    'title' => 'Selesaikan agenda ini',
                    'data-method' => 'post',
                    'data-pjax' => 0,
                    'data-confirm' => 'Anda yakin ingin menandai agenda ini selesai? <br/><strong>' . $model['kegiatan'] . '</strong>'
                ]);
            },
            'rencana' => function ($url, $model, $key) {
                return Html::a('<i class="fas text-primary fa-recycle"></i> ', $url, [
                    'title' => 'Rencanakan kembali agenda ini',
                    'data-method' => 'post',
                    'data-pjax' => 0,
                    'data-confirm' => 'Anda yakin ingin merencanakan kembali agenda ini selesai? <br/><strong>' . $model['kegiatan'] . '</strong>'
                ]);
            },
            'tunda' => function ($key, $client) {
                return Html::a('<i class="fab fa-tumblr-square"></i> ', $key, ['title' => 'Tunda agenda ini']);
            },
        ],
    ],
    [
        'class' => ActionColumn::class,
        'header' => 'Aksi',
        'template' => (Yii::$app->user->isGuest || Yii::$app->user->identity->theme == 0)
            ? '{view}{update}{share}{emailblast}{wa_blast}{editpeserta}'
            : '{view}{update}{share}{emailblast}{wa_blast}{editpeserta}',
        'visibleButtons' => [
            'delete' => function ($model, $key, $index) {
                return (!Yii::$app->user->isGuest
                    && Yii::$app->user->identity->username === $model['reporter'] //datanya sendiri
                    && $model['progress'] !== 1  && $model['progress'] !== 3
                ) ? true : false;
            },
            'update' => function ($model, $key, $index) {
                return (!Yii::$app->user->isGuest
                    && Yii::$app->user->identity->username === $model['reporter'] //datanya sendiri
                    && $model['progress'] !== 1  && $model['progress'] !== 3
                ) ? true : false;
            },
            'emailblast' => function ($model, $key, $index) {
                return (!Yii::$app->user->isGuest
                    && Yii::$app->user->identity->username === $model['reporter'] //datanya sendiri
                    && $model['progress'] !== 1  && $model['progress'] !== 3
                ) ? true : false;
            },
            'wa_blast' => function ($model, $key, $index) {
                return (!Yii::$app->user->isGuest
                    && Yii::$app->user->identity->username === $model['reporter'] //datanya sendiri
                    && $model['progress'] !== 1  && $model['progress'] !== 3
                ) ? true : false;
            },
            'share' => function ($model, $key, $index) {
                return ($model['progress'] !== 3
                ) ? true : false;
            },
            'editpeserta' => function ($model, $key, $index) {
                return (!Yii::$app->user->isGuest
                    && Yii::$app->user->identity->username === $model['reporter'] //datanya sendiri
                    && $model['progress'] == 1
                ) ? true : false;
            },
        ],
        'buttons'  => [
            'delete' => function ($url, $model, $key) {
                return Html::a('<i class="fas text-danger fa-trash-alt"></i> ', $url, [
                    'title' => 'Hapus agenda ini',
                    'data-method' => 'post',
                    'data-pjax' => 0,
                    'data-confirm' => 'Anda yakin ingin menghapus agenda ini? <br/><strong>' . $model['kegiatan'] . '</strong>'
                ]);
            },
            'update' => function ($key, $client) {
                return Html::a('<i class="fa">&#xf044;</i> ', $key, ['title' => 'Update rincian agenda ini']);
            },
            'emailblast' => function ($key, $client) {
                return Html::a('<i class="fas fa-envelope-open-text"></i> ', $key, ['title' => 'Kirim undangan via email']);
            },
            'wa_blast' => function ($url, $model, $key) {
                return Html::a('<i class="fab fa-whatsapp"></i> ', $url, [
                    'title' => 'Kirim undangan via WhatsApp',
                    'data-pjax' => 0,
                    'data-confirm' => 'Anda yakin ingin mengirimkan WhatsApp Blast untuk agenda ini? <br/><strong>' . $model['kegiatan'] . '</strong>'
                ]);
                return Html::a('<i class="fab fa-whatsapp"></i> ', $key, ['title' => 'Kirim notifikasi via WhatsApp']);
            },
            'editpeserta' => function ($key, $client) {
                return Html::a('<i class="fas fa-users-cog"></i> ', $key, ['title' => 'Edit Data Peserta']);
            },
            'view' => function ($key, $client) {
                return Html::a('<i class="fas fa-eye"></i> ', $key, [
                    'title' => 'Lihat rincian agenda ini',
                    'data-bs-toggle' => 'modal',
                    'data-bs-target' => '#exampleModal',
                    'class' => 'modal-link',
                ]);
            },
            'share' => function ($url, $model, $key) {
                $link = Yii::$app->request->hostInfo . Yii::$app->request->baseUrl . '/agenda/' . $model->id_agenda;
                $judul = $model->kegiatan; // Make sure to properly encode the model title
                if ($model->waktumulai_tunda != NULL && $model->waktuselesai_tunda) {
                    $formatter = Yii::$app->formatter;
                    $formatter->locale = 'id-ID'; // set the locale to Indonesian
                    $timezone = new \DateTimeZone('Asia/Jakarta'); // create a timezone object for WIB
                    $waktumulai_tunda = new \DateTime($model->waktumulai_tunda, new \DateTimeZone('UTC')); // create a datetime object for waktumulai_tunda with UTC timezone
                    $waktumulai_tunda->setTimeZone($timezone); // set the timezone to WIB
                    $waktumulai_tundaFormatted = $formatter->asDatetime($waktumulai_tunda, 'd MMMM Y, H:mm'); // format the waktumulai_tunda datetime value
                    $waktuselesai_tunda = new \DateTime($model->waktuselesai_tunda, new \DateTimeZone('UTC')); // create a datetime object for waktuselesai_tunda with UTC timezone
                    $waktuselesai_tunda->setTimeZone($timezone); // set the timezone to WIB
                    $waktuselesai_tundaFormatted = $formatter->asDatetime($waktuselesai_tunda, 'H:mm'); // format the waktuselesai_tunda time value only
                    if ($waktumulai_tunda->format('Y-m-d') === $waktuselesai_tunda->format('Y-m-d')) {
                        // if waktumulai_tunda and waktuselesai_tunda are on the same day, format the time range differently
                        $waktumulai_tundaFormatted = $formatter->asDatetime($waktumulai_tunda, 'd MMMM Y, H:mm'); // format the waktumulai_tunda datetime value with the year and time
                        $watkutampilfinal = $waktumulai_tundaFormatted . ' - ' . $waktuselesai_tundaFormatted . ' WIB'; // concatenate the formatted dates
                    } else {
                        // if waktumulai_tunda and waktuselesai_tunda are on different days, format the date range normally
                        $waktuselesai_tundaFormatted = $formatter->asDatetime($waktuselesai_tunda, 'd MMMM Y, H:mm'); // format the waktuselesai_tunda datetime value
                        $watkutampilfinal = $waktumulai_tundaFormatted . ' WIB <br/>s.d ' . $waktuselesai_tundaFormatted . ' WIB'; // concatenate the formatted dates
                    }
                } else {
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
                        $watkutampilfinal = $waktumulaiFormatted . ' - ' . $waktuselesaiFormatted . ' WIB'; // concatenate the formatted dates
                    } else {
                        // if waktumulai and waktuselesai are on different days, format the date range normally
                        $waktuselesaiFormatted = $formatter->asDatetime($waktuselesai, 'd MMMM Y, H:mm'); // format the waktuselesai datetime value
                        $watkutampilfinal = $waktumulaiFormatted . ' WIB <br/>s.d ' . $waktuselesaiFormatted . ' WIB'; // concatenate the formatted dates
                    }
                }
                $content = 'Lihat detail Agenda "' . $judul . '" pada ' . $watkutampilfinal . ' di Sistem Portal Pintar ke ' . $link;
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
        'class' => ActionColumn::class,
        'header' => 'Laporan',
        'template' => (Yii::$app->user->isGuest || Yii::$app->user->identity->theme == 0)
            ? '{laporan}'
            : '{laporan}',
        'visibleButtons' => [
            'laporan' => function ($model, $key, $index) {
                return ($model['progress'] == 1
                ) ? true : false;
            },
        ],
        'buttons'  => [
            'laporan' => function ($url, $model, $key) {
                $people = Projectmember::find()
                    ->where(['fk_project' => $model['pelaksana']])
                    ->andWhere(['NOT', ['member_status' => 0]])
                    ->asArray()
                    ->all();
                $member = [];
                foreach ($people as $value) {
                    array_push($member, $value['pegawai']);
                }
                // die (var_dump($people));
                if ((!Yii::$app->user->isGuest && empty($model['laporane']['id_laporan'])) && (in_array(Yii::$app->user->identity->username, $member) || $model->reporter == Yii::$app->user->identity->username)) {
                    return Html::a('<i class="fas fa-folder-plus"></i> ', ['laporan/create?agenda=' . $model->id_agenda], ['title' => 'Tambahkan laporan agenda ini']);
                } else if (!empty($model['laporane']['id_laporan']) && $model['laporane']['approval'] == 0) {
                    return Html::a('<i class="fab text-danger fa-readme"></i> ', ['laporan/' . $model->id_agenda], [
                        'title' => 'Laporan agenda belum disetujui',
                        'data-bs-toggle' => 'modal',
                        'data-bs-target' => '#exampleModal',
                        'class' => 'modal-link',
                    ]);
                } else if (!empty($model['laporane']['id_laporan']) && $model['laporane']['approval'] == 1) {
                    return Html::a('<i class="fab text-success fa-readme"></i> ', ['laporan/' . $model->id_agenda], [
                        'title' => 'Lihat laporan agenda ini',
                        'data-bs-toggle' => 'modal',
                        'data-bs-target' => '#exampleModal',
                        'class' => 'modal-link',
                    ]);
                } else {
                    return '';
                }
            },
        ],
    ],
    [
        'class' => ActionColumn::class,
        'header' => 'Surat',
        'template' => (Yii::$app->user->isGuest || Yii::$app->user->identity->theme == 0)
            ? '{listsurat}{createsurat}'
            : '{listsurat}{createsurat}',
        'visibleButtons' => [
            'listsurat' => function ($model, $key, $index) {
                return $model->getSuratrepoe()->count() > 0;
            },
            'createsurat' => function ($model, $key, $index) {
                return (!Yii::$app->user->isGuest && $model['reporter'] == Yii::$app->user->identity->username
                ) ? true : false;
            },
        ],
        'buttons'  => [
            'createsurat' => function ($url, $model, $key) {
                if (!Yii::$app->user->isGuest) {
                    return Html::a('<i class="fas fa-folder-plus"></i> ', ['suratrepo/create/' . $model->id_agenda], ['title' => 'Tambahkan surat terkait agenda ini']);
                } else {
                    return '';
                }
            },
            'listsurat' => function ($url, $model, $key) {
                return Html::a('<i class="fas fa-book-reader text-success"></i> ', ['suratrepo/list?agenda=' . $model->id_agenda], ['title' => 'Lihat surat-surat terkait agenda ini', 'class' => 'modalButton', 'data-pjax' => '0']);
            },
        ],
    ],
];
?>

<div class="container-fluid" data-aos="fade-up">
    <h1 class="text-center"><?= Html::encode($this->title) ?></h1>
    <hr class="bps" />
    <p>
    <div class="d-flex justify-content-between" style="margin-bottom: -0.8rem;">
        <?php if (count($popups) > 0) : ?>
            <div class="p-2">
                <button type="button" class="btn btn-outline-danger btn-sm" data-bs-toggle="modal" data-bs-target="#kliksaya">
                    <i class="fas fa-mouse"></i> Baca Pengumuman
                </button>
                <div class="modal fade modal-dark modal-lg" id="kliksaya" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content" style="padding-left: 4rem; padding-right: 4rem;">
                            <div class="modal-header <?= ((!Yii::$app->user->isGuest && Yii::$app->user->identity->theme == 0) ? '' : 'bg-dark') ?>">
                                <h5 class="modal-title">PORTAL PINTAR</h5>
                                <h5><?= count($popups) ?> Pengumuman</h5>
                            </div>
                            <div class="modal-body <?= ((!Yii::$app->user->isGuest && Yii::$app->user->identity->theme == 0) ? '' : 'bg-dark') ?>">
                                <div id="popupCarousel" class="carousel slide" data-bs-ride="carousel">
                                    <div class="carousel-inner">
                                        <?php foreach ($popups as $index => $popup) : ?>
                                            <div class="carousel-item <?= $index === 0 ? 'active' : '' ?>">
                                                <h5><?= $popup->judul_popups ?></h5>
                                                <p><?= $popup->rincian_popups ?></p>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                    <button class="carousel-control-prev" type="button" data-bs-target="#popupCarousel" data-bs-slide="prev">
                                        <span class="carousel-control-prev-icon" aria-hidden="true" style="margin-left: -200px"></span>
                                        <span class="visually-hidden">Previous</span>
                                    </button>
                                    <button class="carousel-control-next" type="button" data-bs-target="#popupCarousel" data-bs-slide="next">
                                        <span class="carousel-control-next-icon" aria-hidden="true" style="margin-right: -200px"></span>
                                        <span class="visually-hidden">Next</span>
                                    </button>
                                </div>
                            </div>
                            <div class="modal-footer <?= ((!Yii::$app->user->isGuest && Yii::$app->user->identity->theme == 0) ? '' : 'bg-dark') ?>">
                                <button type="button" class="btn btn-info" data-bs-dismiss="modal">OK</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
        <div class="p-2">
        </div>
        <div class="p-2">
            <?= Html::a('<i class="fas fa-file-archive"></i> Arsip Agenda', ['agenda/index?owner=&year=&nopage=0'], ['class' => 'btn btn-outline-warning btn-sm']) ?>
            |
            <?= Html::a('<i class="fas fa-book-reader"></i> Agenda Pimpinan', ['agendapimpinan/index'], ['class' => 'btn btn-outline-warning btn-sm']) ?>
            |
            <?php if (!Yii::$app->user->isGuest) : ?>
                <?= Html::a('<i class="fas fa-scroll"></i> Surat Internal', ['suratrepo/index?owner=&year=' . date("Y")], ['class' => 'btn btn ' . ((!Yii::$app->user->isGuest && Yii::$app->user->identity->theme == 0) ? 'btn-outline-dark' : 'btn-outline-light') . ' btn-sm']) ?>
                |
                <?= Html::a('<i class="fas fa-scroll"></i> Surat Eksternal', ['suratrepoeks/index?owner=&year=' . date("Y")], ['class' => 'btn btn-outline-success btn-sm']) ?>
                |
            <?php endif; ?>
            <?= Html::a('<i class="fas fa-handshake"></i> Zoom', ['zooms/index'], ['class' => 'btn btn btn-outline-warning btn-sm']) ?>
            |
            <?= Html::a('<i class="fas fa-car"></i> Mobil Dinas', ['mobildinas/index'], ['class' => 'btn btn btn-outline-warning btn-sm']) ?>
            |
            <?= Html::a('<i class="fas fa-file-signature"></i> Portal SK', ['sk/index'], ['class' => 'btn btn btn-outline-warning btn-sm']) ?>
            |
            <?= Html::a('<i class="fas fa-suitcase-rolling"></i> Dinas Luar', ['dl/index'], ['class' => 'btn btn btn-outline-warning btn-sm']) ?>
            |
            <?= Html::a('<i class="fas fa-business-time"></i> Jadwal Rilis', ['beritarilis/index'], ['class' => 'btn btn btn-outline-warning btn-sm']) ?>
            |
            <?= Html::a('<i class="fas fa-flag"></i> Apel', ['apel/index'], ['class' => 'btn btn btn-outline-warning btn-sm']) ?>
            |
            <?= Html::a('<i class="fas fa-plus-square"></i> Usulkan Agenda', ['create'], ['class' => 'btn btn btn-outline-warning btn-sm']) ?>
        </div>
    </div>
    </p>

    <?php if ($ada == NULL) : ?>
        <div class="card text-center <?= ((!Yii::$app->user->isGuest && Yii::$app->user->identity->theme == 0) ? '' : 'bg-dark') ?>">
            <div class="card-body">
                <h2><em>Belum Ada Agenda di Tahun <?php echo date("Y") ?> <br /> atau di Pencarian yang Anda Maksud</em></h2>
                <hr />
                <?= Html::a('<i class="fas fa-file-archive"></i> Klik untuk Lihat Arsip Agenda', ['agenda/index?owner=&year=&nopage=0'], ['class' => 'btn btn ' . ((!Yii::$app->user->isGuest && Yii::$app->user->identity->theme == 0) ? 'btn-outline-dark' : 'btn-outline-light') . ' btn-lg']) ?>
            </div>
        </div>
    <?php else : ?>
        <?php echo $this->render('_search', ['model' => $searchModel]);
        ?>
        <div class="card <?= ((!Yii::$app->user->isGuest && Yii::$app->user->identity->theme == 0) ? '' : 'bg-dark') ?>">
            <div class="card-body table-responsive p-0">
                <?php
                $layout = '
                        <div class="card-header">
                            <div class="d-flex justify-content-between" style="margin-bottom: -0.8rem;">
                                <div class="p-2">
                                {nopage}
                                {export}
                                {custom}
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
                        {pager}
                    ';
                ?>
                <?= GridView::widget([
                    'dataProvider' => $dataProvider,
                    'tableOptions' => ['class' => 'table table-condensed ' . ((!Yii::$app->user->isGuest && Yii::$app->user->identity->theme == 0) ? '' : 'table-dark')],
                    'columns' => $kolomTampil,
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
                        GridView::CSV => ['label' => 'CSV', 'filename' => 'Agenda dari Portal Pintar - ' . date('d-M-Y')],
                        GridView::HTML => ['label' => 'HTML', 'filename' => 'Agenda dari Portal Pintar - ' . date('d-M-Y')],
                        GridView::EXCEL => ['label' => 'EXCEL', 'filename' => 'Agenda dari Portal Pintar - ' . date('d-M-Y')],
                        GridView::TEXT => ['label' => 'TEXT', 'filename' => 'Agenda dari Portal Pintar - ' . date('d-M-Y')],
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
                    'replaceTags' => [
                        '{custom}' => function () {
                            return (!Yii::$app->user->isGuest) ? '
                            <div class="btn-group">
                                ' .
                                Html::a('<span class="btn ' . (!Yii::$app->user->isGuest && Yii::$app->user->identity->theme == 0 ? 'btn-dark' : 'btn-light') . ' me-1"> Usulan Saya (' . date("Y") . ')</span>', 'index?owner=' . Yii::$app->user->identity->username . '&year=' . date("Y") . '&nopage=0', ['title' => 'Tampikan Usulan Anda', 'data-pjax' => 0])
                                .
                                Html::a('<span class="btn ' . (!Yii::$app->user->isGuest && Yii::$app->user->identity->theme == 0 ? 'btn-outline-dark' : 'btn-outline-light') . ' me-1"> Usulan Saya (Since 2023)</span>', 'index?owner=' . Yii::$app->user->identity->username . '&year=&nopage=0', ['title' => 'Tampikan Usulan Anda', 'data-pjax' => 0])
                                .
                                Html::a('<span class="btn btn-warning me-1"> Semua (' . date("Y") . ')</span>', 'index?owner=&year=' . date("Y") . '&nopage=0', ['title' => 'Tampikan Usulan Semua', 'data-pjax' => 0])
                                .
                                Html::a('<span class="btn btn btn-outline-warning"> Semua (Since 2023)</span>', 'index?owner=&year=&nopage=0', ['title' => 'Tampikan Usulan Semua', 'data-pjax' => 0])
                                .
                                '
                            </div>
                        ' : '';
                        },
                        '{nopage}' => function () use ($owner, $year, $nopage) {
                            return '
                            <div class="btn-group">
                                ' .
                                Html::a('<span class="btn btn btn-outline-secondary"> ' . ($nopage == 0 ? '<i class="fas fa-expand"></i> Semua' : '<i class="fas fa-compress"></i> Halaman') . '</span>', 'index?owner=' . $owner . '&year=' . $year . '&nopage=' . ($nopage == 0 ? 1 : 0), ['title' => 'Tampilan Data Semua', 'data-pjax' => 0])
                                .
                                '
                            </div>
                        ';
                        }
                    ]
                ]); ?>
            </div>
        </div>
    <?php endif; ?>
</div>