<?php

use app\models\Agendapimpinan;
use yii\helpers\Html;
use yii\bootstrap5\ActiveForm;
use kartik\select2\Select2;
use kartik\grid\GridView;
use kartik\datetime\DateTimePicker;

// Assuming $model is an instance of your model class
if ($model->isNewRecord) {
    $model->waktumulai = date("Y-m-d 10:00:00");
    $model->waktuselesai = date("Y-m-d 12:00:00");
}
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
                <?= $form->field($model, 'kegiatan')->textInput([])
                    ->label('Kegiatan Pimpinan') ?>
                <div class="row">
                    <div class="col-sm-6">
                        <?= $form->field($model, 'waktumulai')->widget(DateTimePicker::classname(), [
                            'options' => ['placeholder' => 'Pilih Tanggal dan Jam ...'],
                            'pluginOptions' => [
                                'autoclose' => true,
                                'format' => 'yyyy-mm-dd hh:ii:ss',
                            ]
                        ]); ?>
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
                <?= $form->field($model, 'tempat')->textInput(['maxlength' => true]) ?>
                <?php if (!$model->isNewRecord) : ?>
                    <?php $cek = Agendapimpinan::find()
                        ->select('pendamping')
                        ->where(['id_agendapimpinan' => $model->id_agendapimpinan])
                        ->one();
                    $data = str_replace('@bps.go.id', '', $cek->pendamping);
                    $array = explode(", ", $data);
                    ?>
                    <?php $model->pendamping = $array; ?>
                <?php endif; ?>
                <?=
                $form->field($model, 'pendamping')->widget(Select2::class, [
                    'data' => \yii\helpers\ArrayHelper::map(
                        \app\models\Pengguna::find()->all(),
                        'username',
                        'nama'
                    ),
                    'theme' => Select2::THEME_KRAJEE,
                    'options' => [
                        'multiple' => true,
                        'placeholder' => 'Pilih Pegawai ...',
                    ],
                ]); ?>
                <?= $form->field($model, 'pendamping_lain')->textarea(['rows' => 3])
                    ->hint('Input nama badan/orang di luar ' . Yii::$app->params['namaSatker'] . '. Contoh: <b>Kepala BPS Kota Bengkulu, Dian Putra Nugraha</b>', ['class' => '', 'style' => 'color: #999']) ?>
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
                            <h5 class="card-title text-center <?php echo (Yii::$app->user->identity->theme == 0 ? '' : 'text-light') ?>">Rencana Agenda Pimpinan 2 Minggu ke Depan<br /><span><?php echo date("d-F-Y") . ' s.d. ' . date('d-F-Y', strtotime('+2 weeks')) ?></span></h5>
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