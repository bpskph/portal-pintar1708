<?php

use app\models\Agenda;
use app\models\Project;
use app\models\Rooms;
use yii\helpers\Html;
use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\BootstrapAsset;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use kartik\switchinput\SwitchInput;
use kartik\grid\GridView;
use kartik\select2\Select2Asset;
use kartik\datetime\DateTimePicker;
use yii\web\View;

Select2Asset::register($this);
BootstrapAsset::register($this);

if ($model->isNewRecord) {
    $model->waktumulai = date("Y-m-d 10:00:00");
    $model->waktuselesai = date("Y-m-d 12:00:00");
}

// Registering your custom JS and CSS files
$this->registerJsFile(Yii::$app->request->baseUrl . '/library/js/fi-agenda-form.js', ['position' => View::POS_END, 'depends' => [\yii\web\JqueryAsset::class]]);
?>
<style>
    .col-sm-3 {
        display: inline-flex;
    }
</style>
<!-- <link rel='stylesheet' href='https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css'> -->
<div class="container-fluid" data-aos="fade-up">
    <div class="card alert <?= ((!Yii::$app->user->isGuest && Yii::$app->user->identity->theme == 0) ? 'bg-light' : 'bg-dark') ?>">
        <?php $form = ActiveForm::begin([
            'layout' => 'default',
            'id' => 'agenda-form-id',
            'fieldConfig' => [
                'template' => "{label}\n{beginWrapper}\n{input}\n{hint}\n{error}\n{endWrapper}",
                'horizontalCssClasses' => [
                    'label' => 'col-sm-3',
                    'offset' => 'offset-sm-4',
                    'wrapper' => 'col-sm-9',
                    'error' => '',
                    'hint' => '',
                ],
            ],
            'enableClientValidation' => true
        ]); ?>
        <div class="row">
            <div class="col-sm-8">
                <div class="row">
                    <div class="col-sm-6">
                        <?= $form->field($model, 'kegiatan')->textInput([])
                            ->label('Judul Rapat/Agenda/Pelatihan') ?>
                    </div>
                    <div class="col-sm-6">
                        <?= $form->field($model, 'fk_kategori')->widget(Select2::classname(), [
                            'data' => ArrayHelper::map(
                                \app\models\Kategori::find()->select('id_kategori, nama_kategori')->asArray()->all(),
                                'id_kategori',
                                function ($model) {
                                    return $model['id_kategori'] . '. ' . $model['nama_kategori'];
                                }
                            ),
                            'options' => ['placeholder' => 'Pilih Kategori Agenda'],
                            'pluginOptions' => [
                                'allowClear' => true
                            ],
                        ]);
                        ?>
                    </div>
                </div>

                <div class="row">
                    <div class="col-sm-6">
                        <?= $form->field($model, 'waktumulai')->widget(DateTimePicker::classname(), [
                            'options' => ['placeholder' => 'Pilih Tanggal dan Jam ...'],
                            'pluginOptions' => [
                                'autoclose' => true,
                                'format' => 'yyyy-mm-dd hh:ii:ss'
                            ]
                        ]); ?>
                        <?php // $form->field($model, 'waktumulai')->textInput(['readonly' => false, 'placeholder' => 'Pilih Tanggal dan Jam', 'value' => $model->waktumulai]) 
                        ?>
                    </div>
                    <div class="col-sm-6">
                        <?= $form->field($model, 'waktuselesai')->widget(DateTimePicker::classname(), [
                            'options' => ['placeholder' => 'Pilih Tanggal dan Jam ...'],
                            'pluginOptions' => [
                                'autoclose' => true,
                                'format' => 'yyyy-mm-dd hh:ii:ss'
                            ]
                        ]); ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-6">
                        <?= $form->field($model, 'metode')->widget(Select2::classname(), [
                            'data' => [0 => "Online", 1 => "Offline", 2 => "Hybrid"],
                            'options' => ['placeholder' => 'Jenis Agenda'],
                            'pluginOptions' => [
                                'allowClear' => true
                            ],
                        ]);
                        ?>
                    </div>
                    <div class="col-sm-6">
                        <?= $form->field($model, 'progress')->widget(Select2::classname(), [
                            'data' => [0 => "Direncanakan", 1 => "Selesai", 2 => "Ditunda", 3 => "Dibatalkan"],
                            'options' => ['placeholder' => 'Progress Kegiatan'],
                            'pluginOptions' => [
                                'allowClear' => true
                            ],
                        ]);
                        ?>
                    </div>
                </div>
                <?php
                if (!$model->isNewRecord) {
                    $db = Rooms::findOne(['id_rooms' => $model->tempat]);
                    if ($db !== null) {
                        $model->pilihtempat =  false;
                    } else {
                        $model->pilihtempat  = true;
                    }
                }
                ?>
                <?php
                if (!$model->isNewRecord) {
                    $db = Project::findOne(['id_project' => $model->pelaksana]);
                    if ($db !== null) {
                        $model->pilihpelaksana =  false;
                    } else {
                        $model->pilihpelaksana  = true;
                    }
                }
                ?>
                <div class="row">
                    <div class="col-sm-6">
                        <div class="row">
                            <div class="col-sm-4">
                                <?= $form->field($model, 'pilihtempat')->widget(SwitchInput::classname(), [
                                    'pluginOptions' => [
                                        'onText' => 'LUAR',
                                        'offText' => 'BPS',
                                        'onColor' => 'warning',
                                        'offColor' => 'warning',
                                        'handleWidth' => 40,
                                    ],
                                    'value' => $model->isNewRecord ? false : true,
                                    'pluginEvents' => [
                                        'switchChange.bootstrapSwitch' => 'function(event, state) {
                                        if(state) {
                                            $("#no_t").show();
                                            $("#yes_t").hide();
                                        } else {
                                            $("#no_t").hide();
                                            $("#yes_t").show();
                                        }
                                    }'
                                    ]
                                ]); ?>
                            </div>
                            <div class="col-sm-8">
                                <div id="yes_t" <?= $model->pilihtempat == true ? ' style="display:none"' : '' ?>>
                                    <?= $form->field($model, 'tempat')->widget(Select2::classname(), [
                                        'name' => 'tempat',
                                        'data' => ArrayHelper::map(
                                            Rooms::find()->select('*')->asArray()->all(),
                                            'id_rooms',
                                            function ($model) {
                                                return $model['nama_ruangan'];
                                            }
                                        ),
                                        'options' => ['placeholder' => 'Pilih Tempat Agenda'],
                                        'pluginOptions' => [
                                            'allowClear' => true
                                        ],
                                    ]);
                                    ?>
                                </div>
                                <div id="no_t" <?= $model->pilihtempat == false ? ' style="display:none"' : '' ?>>
                                    <?= $form->field($model, 'tempattext')->textInput(['maxlength' => true, 'value' => $model->isNewRecord ? '' : $model->tempate])
                                        ->hint('Isikan Lokasi Agenda (Di Luar Kantor)', ['class' => '', 'style' => 'color: #999']) ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="row">
                            <div class="col-sm-4">
                                <?= $form->field($model, 'pilihpelaksana')->widget(SwitchInput::classname(), [
                                    'pluginOptions' => [
                                        'onText' => 'LUAR',
                                        'offText' => 'BPS',
                                        'onColor' => 'warning',
                                        'offColor' => 'warning',
                                        'handleWidth' => 40,
                                    ],
                                    'value' => $model->isNewRecord ? false : true,
                                    'pluginEvents' => [
                                        'switchChange.bootstrapSwitch' => 'function(event, state) {
                                        if(state) {
                                            $("#no").show();
                                            $("#yes").hide();
                                            $("#maybe").hide();
                                        } else {
                                            $("#no").hide();
                                            $("#yes").show();
                                            $("#maybe").show();
                                        }
                                    }'
                                    ]
                                ]); ?>
                            </div>
                            <div class="col-sm-8">
                                <div id="yes" <?= $model->pilihpelaksana == true ? ' style="display:none"' : '' ?>>
                                    <?= $form->field($model, 'pelaksana')->widget(Select2::classname(), [
                                        'name' => 'pelaksana',
                                        'data' => ArrayHelper::map(
                                            Project::find()
                                                ->select('*, team.panggilan_team as namateam')
                                                ->joinWith(['teame', 'projectmembere', 'teamleadere'])
                                                ->where(['project.tahun' => date("Y")])
                                                ->andWhere(['project.aktif' => 1])
                                                ->andWhere(['projectmember.member_status' => 3, 'projectmember.pegawai' => Yii::$app->user->identity->username])
                                                ->orWhere(['projectmember.member_status' => 2, 'projectmember.pegawai' => Yii::$app->user->identity->username])
                                                ->orWhere(['teamleader.leader_status' => 1, 'teamleader.nama_teamleader' => Yii::$app->user->identity->username])
                                                ->asArray()
                                                ->all(),
                                            'id_project',
                                            function ($model) {
                                                return $model['id_project'] . '. ' . $model['nama_project'] . ' [' . $model['panggilan_project'] .  ' | ' . $model['namateam'] . '] ';
                                            }
                                        ),
                                        'options' => ['placeholder' => 'Pilih Pelaksana Agenda'],
                                        'pluginOptions' => [
                                            'allowClear' => true
                                        ],
                                    ])->hint('Isikan Pelaksana Agenda <strong>(Tim Tahun ' . date("Y") . ')</strong>', ['class' => '', 'style' => 'color: #999']);
                                    ?>
                                </div>
                                <div id="no" <?= $model->pilihpelaksana == false ? ' style="display:none"' : '' ?>>
                                    <?= $form->field($model, 'pelaksanatext')->textInput(['maxlength' => true, 'value' => $model->isNewRecord ? '' : $model->pelaksanalengkape])
                                        ->hint('Isikan Pelaksana Agenda (Eksternal)', ['class' => '', 'style' => 'color: #999']) ?>
                                </div>
                            </div>
                            <div id="maybe" <?= $model->pilihpelaksana == true ? ' style="display:none"' : '' ?>>
                                <?= $form->field($model, 'surat_lanjutan')->checkbox()->label('&nbsp;Tandai ini jika Anda ingin lanjut membuat Surat Undangan,&nbsp;<br/><strong>&nbsp;Khusus Agenda Internal</strong>&nbsp;', ['style' => 'background-color: #ffc107; border-radius: 5px']); ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="mb-2 card <?= ((!Yii::$app->user->isGuest && Yii::$app->user->identity->theme == 0) ? '' : 'bg-dark text-light') ?>" id="petunjuk-zoom" style="display:none">
                    <div class="card-body">
                        <p class="card-text text-info"><em>Per Tanggal 1 Maret 2024, pengajuan permohonan layanan zoom melalui tautan https://s.bps.go.id/ZM1700 sudah tidak berlaku, <br />
                                dan dialihkan ke Portal Pintar. Formulir akan ditampilkan setelah Anda men-submit formulir agenda ini.</em></p>
                    </div>
                </div>
                <?php // $form->field($model, 'peserta')->textarea(['rows' => 3]) 
                ?>
                <?php if (!$model->isNewRecord) : ?>
                    <?php $cek = Agenda::find()
                        ->select('peserta')
                        ->where(['id_agenda' => $model->id_agenda])
                        ->one();
                    $data = str_replace('@bps.go.id', '', $cek->peserta);
                    $array = explode(", ", $data);
                    ?>
                    <?php $model->peserta = $array; ?>
                <?php endif; ?>
                <?= $form->field($model, 'presensi')->textInput([])
                    ->label('Link Presensi')->hint('<strong>Isikan jika sudah ada.</strong>', ['class' => '', 'style' => 'color: #999']); ?>
                <?= $form->field($model, 'pemimpin')->widget(Select2::classname(), [
                    'data' => ArrayHelper::map(
                        \app\models\Pengguna::find()->select('*')->where('level<>2')->asArray()->all(),
                        'username',
                        function ($model) {
                            return $model['nama'] . ' [' . $model['username'] .  '@bps.go.id]';
                        }
                    ),
                    'options' => ['placeholder' => 'Pilih Pemimpin Rapat'],
                    'pluginOptions' => [
                        'allowClear' => true
                    ],
                ]);
                ?>
                <?php if ($model->isNewRecord) : ?>
                    <?php
                    $teams = Project::find()
                        ->select('*')
                        ->joinWith(['projectmembere'])
                        ->where('member_status <> 0')
                        ->andWhere(['project.aktif' => 1])
                        ->andWhere('tahun=' . date("Y"))
                        ->asArray()
                        ->all();

                    $listteams = \yii\helpers\ArrayHelper::map($teams, 'id_project', 'panggilan_project');

                    // Add "all" option
                    $listteams = ['0' => '<span class="badge bg-warning text-dark">Semua Pegawai</span>'] + $listteams;
                    ?>
                    <?= $form->field($searchModel, 'teams')->checkboxList($listteams, [
                        'id' => 'team-checkboxes',
                        'item' => function ($index, $label, $name, $checked, $value) {
                            $checkbox = Html::checkbox($name, $checked, [
                                'value' => $value,
                                'label' => false, // We will create the label manually
                            ]);
                            $teamName = Html::label($label, 'team_' . $value);
                            // return $teamName . $checkbox;
                            return "<div class='col-sm-3'>$checkbox&nbsp;$teamName</div>";
                        },
                    ])->label('Pilih Daftar Tim <strong>(Tahun ' . date("Y") . ')</strong> yang Diundang <span class="badge bg-warning text-dark">(klik nama tim)</span>') ?>
                    <?php $chosenmembers = [];
                    ?>
                <?php endif; ?>
                <?=
                $form->field($model, 'peserta')->widget(Select2::class, [
                    'data' => \yii\helpers\ArrayHelper::map(
                        \app\models\Pengguna::find()->where('level<>2')->all(),
                        'username',
                        'nama'
                    ),
                    'theme' => Select2::THEME_KRAJEE,
                    'options' => [
                        'multiple' => true,
                        'placeholder' => 'Pilih Pegawai ...',
                        'value' => $model->isNewRecord ? $chosenmembers : $model->peserta,
                    ],
                ])->label('Atau Pilih dari Daftar Pegawai'); ?>
                <?= $form->field($model, 'peserta_lain')->textarea(['rows' => 3])
                    ->hint('Input nama badan/orang di luar BPS Provinsi Bengkulu serta alamat email valid (<b>hanya</b> jika ingin mengirimkan undangan digital via email blast). Data dapat terisi lebih dari satu, pisahkan dengan koma. Contoh: <b>Bappeda Provinsi Bengkulu, Nofriana, S.Pd., dianputra@bps.go.id, khansa.safira19@gmail.com</b>', ['class' => '', 'style' => 'color: #999']) ?>
                <?= $form->field($model, 'id_lanjutan')->widget(Select2::classname(), [
                    'data' => ArrayHelper::map(
                        Agenda::find()
                            ->select('*')
                            ->where(['progress' => 1])
                            ->andWhere(['>=', 'waktuselesai', date('Y-m-d H:i:s', strtotime('-2 months'))])
                            ->asArray()->all(),
                        'id_agenda',
                        function ($model) {
                            $formatter = Yii::$app->formatter;
                            $timezone = new \DateTimeZone('Asia/Jakarta'); // create a timezone object for WIB
                            $waktumulai = new \DateTime($model['waktumulai'], new \DateTimeZone('UTC')); // create a datetime object for waktumulai with UTC timezone
                            $waktumulai->setTimeZone($timezone); // set the timezone to WIB
                            $waktumulaiFormatted = $formatter->asDatetime($waktumulai, 'd MMMM Y'); // format the waktumulai datetime value
                            $waktuselesai = new \DateTime($model['waktuselesai'], new \DateTimeZone('UTC')); // create a datetime object for waktuselesai with UTC timezone
                            $waktuselesai->setTimeZone($timezone); // set the timezone to WIB
                            $waktuselesaiFormatted = $formatter->asDatetime($waktuselesai, 'H:mm'); // format the waktuselesai time value only
                            if ($waktumulai->format('Y-m-d') === $waktuselesai->format('Y-m-d')) {
                                // if waktumulai and waktuselesai are on the same day, format the time range differently
                                $waktumulaiFormatted = $formatter->asDatetime($waktumulai, 'd MMMM Y'); // format the waktumulai datetime value with the year and time
                                return $model['kegiatan']   . ' [' . $waktumulaiFormatted . ']'; // concatenate the formatted dates
                            } else {
                                // if waktumulai and waktuselesai are on different days, format the date range normally
                                $waktuselesaiFormatted = $formatter->asDatetime($waktuselesai, 'd MMMM Y'); // format the waktuselesai datetime value
                                return $model['kegiatan']   . ' [' . $waktumulaiFormatted . ' s.d ' . $waktuselesaiFormatted . ']'; // concatenate the formatted dates
                            }
                        }
                    ),
                    'options' => ['placeholder' => 'Pilih Agenda Yang Berkaitan (Opsional)'],
                    'pluginOptions' => [
                        'allowClear' => true
                    ],
                ]);
                ?>

                <div class="form-group text-end mb-3">
                    <?= Html::submitButton('<i class="fas fa-save"></i> Simpan', ['class' => 'btn btn btn-outline-warning btn-block']) ?>
                </div>
            </div>
            <div class="col-sm-4">
                <?= $form->errorSummary($model) ?>
                <?php if (Yii::$app->session->hasFlash('warning')) : ?>
                    <div class="alert alert-danger alert-dismissable">

                        <h4><i class="fas fa-exclamation-triangle"></i></h4>
                        <?= Yii::$app->session->getFlash('warning') ?>
                    </div>
                    <br />
                <?php endif; ?>
                <?php if ($dataProvider->totalCount > 0) : ?>
                    <div class="card <?= ((!Yii::$app->user->isGuest && Yii::$app->user->identity->theme == 0) ? '' : 'bg-dark') ?>">
                        <div class="card-body table-responsive p-0">
                            <h5 class="card-title text-center <?php echo (Yii::$app->user->identity->theme == 0 ? '' : 'text-light') ?>">Kegiatan yang <i>Direncanakan</i> 2 Minggu ke Depan<br /><span><?php echo date("d-F-Y") . ' s.d. ' . date('d-F-Y', strtotime('+2 weeks')) ?></span></h5>
                            <?php
                            $layout = '
                        <div class=" ' . (!Yii::$app->user->isGuest ? Yii::$app->user->identity->themechoice : '') . '">
                            <div class="d-flex justify-content-between" style="margin-bottom: -0.8rem;">
                                <div class="p-2">                                
                                </div>                                
                                <div class="p-2">                                
                                </div>
                                <div class="p-2" style="margin-top:0.5rem;">
                                <span class="text-secondary">{summary}</span>
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
                                        'label' => 'Waktu',
                                        'format' => 'html',
                                        'vAlign' => 'middle'
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
                                        'attribute' => 'tempat',
                                        'value' => 'tempate',
                                        'vAlign' => 'middle'
                                    ],
                                    [
                                        // 'attribute' => 'zoomse',
                                        'value' => 'zoomse',
                                        'header' => 'Zoom Meeting',
                                        'vAlign' => 'middle',
                                        'format' => 'html'
                                    ],
                                ],
                                'layout' => $layout,
                                'bordered' => false,
                                'striped' => false,
                                'condensed' => false,
                                'hover' => true,
                                'headerRowOptions' => ['class' => 'kartik-sheet-style ' . (Yii::$app->user->identity->theme == 1 ? '' : 'bg-info-light')],
                                'filterRowOptions' => ['class' => 'kartik-sheet-style'],
                                'export' => false,
                                'pjax' => false,
                                'pjaxSettings' => [
                                    'neverTimeout' => true,
                                    'options' => ['id' => 'some_pjax_id'],
                                ],
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
                <?php endif; ?>
            </div>
        </div>
        <?php ActiveForm::end(); ?>
    </div>
</div>
<?php
$js = <<< JS

JS;
$this->registerJs($js);
?>
<script>
    // document.getElementById("w1-warning-0").style.display = 'none';
</script>