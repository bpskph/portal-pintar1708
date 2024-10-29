<?php
use app\models\Agenda;
use app\models\Project;
use app\models\Projectmember;
use app\models\Rooms;
use yii\helpers\Html;
use yii\bootstrap5\ActiveForm;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use kartik\switchinput\SwitchInput;
use kartik\datetime\DateTimePicker;
use kartik\grid\ActionColumn;
use kartik\grid\GridView;
/** @var yii\web\View $this */
/** @var app\models\Agenda $model */
$this->title = 'Tunda Agenda';
?>
<style>
    label {
        color: #999 !important
    }
    body:not(.gelap) #agenda-waktumulai,
    body:not(.gelap) #agenda-waktuselesai {
        background-color: #fff !important;
    }
    .alert-danger {
        background-color: #cff4fc !important;
        color: #212529 !important;
        border-color: #cff4fc !important;
        border-radius: 1rem !important;
    }
    .gelap .kv-table-header {
        background: #454d55 !important
    }
</style>
<link rel='stylesheet' href='https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css'>
<link rel="stylesheet" href="./style.css">
<h1><?= Html::encode($this->title) ?></h1>
<div class="container-fluid" data-aos="fade-up">
    <div class="card alert <?= ((!Yii::$app->user->isGuest && Yii::$app->user->identity->theme == 0) ? 'bg-light' : 'bg-dark') ?>">
        <!-- <div class="group">
            <input type="text" id="calendar-tomorrow">
            <span class="bar"></span>
            <label class="input-label">From tomorrow</label>
        </div> -->
        <?php $form = ActiveForm::begin([
            'layout' => 'default',
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
            <div class="col-sm-6">
                <h5>
                    <span class="badge bg-primary">JUDUL RAPAT/AGENDA/PELATIHAN</span>
                </h5>
                <?= $form->field($model, 'kegiatan')->textInput(['readonly' => true])
                    ->label(false) ?>
                <h5>
                    <span class="badge bg-primary">JADWAL AWAL</span>
                </h5>
                <div class="row">
                    <div class="col-sm-6">
                        <label>Waktu Mulai</label>
                        <br />
                        <?php echo $model->waktumulai . ' WIB' ?>
                    </div>
                    <div class="col-sm-6">
                        <label>Waktu Selesai</label>
                        <br />
                        <?php echo $model->waktuselesai . ' WIB' ?>
                    </div>
                </div>
                <h5>
                    <span class="badge bg-primary">JADWAL BARU</span>
                </h5>
                <div class="row">
                    <div class="col-sm-6">
                        <?= $form->field($model, 'waktumulai_tunda')->textInput(['readonly' => false, 'placeholder' => 'Pilih Tanggal dan Jam']) ?>
                    </div>
                    <div class="col-sm-6">
                        <?= $form->field($model, 'waktuselesai_tunda')->textInput(['readonly' => false, 'placeholder' => 'Pilih Tanggal dan Jam']) ?>
                    </div>
                </div>
                <div class="form-group text-end mb-3">
                    <?= Html::submitButton('<i class="fas fa-save"></i> Simpan', ['class' => 'btn btn btn-outline-warning btn-block']) ?>
                </div>
            </div>
            <div class="col-sm-6">
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
$('#pilihpelaksana-switch').on('change', function(){
    if ($(this).prop('checked')) {
        $('#pelaksana-external').hide();
        $('#pelaksana-internal').show();
    } else {
        $('#pelaksana-internal').hide();
        $('#pelaksana-external').show();
    }
});
JS;
$this->registerJs($js);
?>
<script>
    document.getElementById("w1-warning-0").style.display = 'none';
</script>
<script src='https://cdnjs.cloudflare.com/ajax/libs/flatpickr/4.5.1/flatpickr.min.js'></script>
<script src='https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/ja.js'></script>
<script>
    flatpickr('#calendar-tomorrow', {
        "minDate": new Date().fp_incr(1),
        "enableTime": true
    });
    // Get the action ID from the view
    var actionId = '<?php echo Yii::$app->controller->action->id; ?>';
    if (actionId === 'update') {
        // Set the input value to the value of the waktuselesai attribute
        flatpickr('#agenda-waktumulai_tunda', {
            enableTime: true,
            dateFormat: "Y-m-d H:i",
            onReady: function(selectedDates, dateStr, instance) {
                var formattedDate = instance.formatDate(selectedDates[0], "Y-m-d H:i") + " WIB";
                instance.input.value = formattedDate;
            }
        });
        flatpickr('#agenda-waktuselesai_tunda', {
            enableTime: true,
            dateFormat: "Y-m-d H:i",
            onReady: function(selectedDates, dateStr, instance) {
                var formattedDate = instance.formatDate(selectedDates[0], "Y-m-d H:i") + " WIB";
                instance.input.value = formattedDate;
            }
        });
    } else {
        flatpickr("#agenda-waktumulai_tunda", {
            enableTime: true,
            dateFormat: "Y-m-d H:i",
            defaultDate: new Date(),
            onReady: function(selectedDates, dateStr, instance) {
                var formattedDate = instance.formatDate(selectedDates[0], "Y-m-d H:i") + " WIB";
                instance.input.value = formattedDate;
            }
        });
        flatpickr("#agenda-waktuselesai_tunda", {
            enableTime: true,
            dateFormat: "Y-m-d H:i",
            defaultDate: new Date(),
            onReady: function(selectedDates, dateStr, instance) {
                var formattedDate = instance.formatDate(selectedDates[0], "Y-m-d H:i") + " WIB";
                instance.input.value = formattedDate;
            }
        });
    }
</script>