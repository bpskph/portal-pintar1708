<?php

use app\models\Agendapimpinan;
use app\models\Mobildinaskeperluan;
use yii\helpers\Html;
use yii\bootstrap5\ActiveForm;
use kartik\select2\Select2;
use kartik\grid\GridView;
use yii\helpers\ArrayHelper;
use kartik\datetime\DateTimePicker;
use yii\web\View;

// $actionId = Yii::$app->controller->action->id;
// $script = <<< JS
//     var actionId = '$actionId';
// JS;
// $this->registerJs($script, \yii\web\View::POS_HEAD);

// Assuming $model is an instance of your model class
if ($model->isNewRecord) {
    $model->mulai = date("Y-m-d 10:00:00");
    $model->selesai = date("Y-m-d 12:00:00");
}

// Registering flatpickr CSS and JS files
// $this->registerCssFile('https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css', ['position' => View::POS_END]);
// $this->registerJsFile('https://cdnjs.cloudflare.com/ajax/libs/flatpickr/4.6.9/flatpickr.min.js', ['position' => View::POS_END]);
// $this->registerJsFile('https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/ja.js', ['position' => View::POS_END]);

// Registering your custom JS and CSS files
// $this->registerJsFile(Yii::$app->request->baseUrl . '/library/js/fi-mobildinas-form.js', ['position' => View::POS_END, 'depends' => [\yii\web\JqueryAsset::class]]);

?>
<div class="container-fluid" data-aos="fade-up">
    <div class="card alert <?= ((!Yii::$app->user->isGuest && Yii::$app->user->identity->theme == 0) ? 'bg-light' : 'bg-dark') ?>">
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
                <?= $form->field($model, 'keperluan')->widget(Select2::classname(), [
                    'name' => 'tempat',
                    'data' => ArrayHelper::map(
                        Mobildinaskeperluan::find()->select('*')->asArray()->all(),
                        'id_mobildinaskeperluan',
                        function ($model) {
                            return $model['nama_mobildinaskeperluan'];
                        }
                    ),
                    'options' => ['placeholder' => 'Pilih Keperluan Peminjaman'],
                    'pluginOptions' => [
                        'allowClear' => true
                    ],
                ]);
                ?>
                <?= $form->field($model, 'keperluan_lainnya')->textInput(['maxlength' => true])->hint('Kosongkan jika tidak ada', ['class' => '', 'style' => 'color: #999']) ?>
                <div class="row">
                    <div class="col-sm-6">
                        <?= $form->field($model, 'mulai')->widget(DateTimePicker::classname(), [
                            'options' => ['placeholder' => 'Pilih Tanggal dan Jam ...'],
                            'pluginOptions' => [
                                'autoclose' => true,
                                'format' => 'yyyy-mm-dd hh:ii:ss',
                            ]
                        ]); ?>

                    </div>
                    <div class="col-sm-6">
                        <?= $form->field($model, 'selesai')->widget(DateTimePicker::classname(), [
                            'options' => ['placeholder' => 'Pilih Tanggal dan Jam ...'],
                            'pluginOptions' => [
                                'autoclose' => true,
                                'format' => 'yyyy-mm-dd hh:ii:ss'
                            ]
                        ]); ?>
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
                            <h5 class="card-title text-center <?php echo (Yii::$app->user->identity->theme == 0 ? '' : 'text-light') ?>">Rencana Peminjaman Mobil Dinas 2 Minggu ke Depan<br /><span><?php echo date("d-F-Y") . ' s.d. ' . date('d-F-Y', strtotime('+2 weeks')) ?></span></h5>
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
                                            $waktumulai = new \DateTime($model->mulai, new \DateTimeZone('UTC')); // create a datetime object for waktumulai with UTC timezone
                                            $waktumulai->setTimeZone($timezone); // set the timezone to WIB
                                            $waktumulaiFormatted = $formatter->asDatetime($waktumulai, 'd MMMM Y, H:mm'); // format the waktumulai datetime value
                                            $waktuselesai = new \DateTime($model->selesai, new \DateTimeZone('UTC')); // create a datetime object for waktuselesai with UTC timezone
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
                                    ],
                                    [
                                        'attribute' => 'borrower',
                                        'value' => 'borrowere.nama'
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