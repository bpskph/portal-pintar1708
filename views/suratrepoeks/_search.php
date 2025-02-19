<?php
use yii\helpers\Html;
use kartik\form\ActiveForm;
use yii\helpers\ArrayHelper;
use kartik\select2\Select2;
use kartik\daterange\DateRangePicker;
?>
<div class="card <?= ((!Yii::$app->user->isGuest && Yii::$app->user->identity->theme == 0) ? '' : 'bg-dark') ?>">
    <div class="card-body" style="margin: 0 auto!important">
        <?php
        $form = ActiveForm::begin([
            'action' => ['index?owner=&year='],
            'method' => 'get',
            'type' => ActiveForm::TYPE_INLINE,
            'fieldConfig' => ['options' => ['class' => 'form-group mr-2']]
        ]);
        ?>
        <?=
        $form->field($model, 'fk_agenda')->widget(Select2::classname(), [
            'data' => ArrayHelper::map(
                \app\models\Agenda::find()->select('*')->where(['<>', 'progress', '3'])->asArray()->all(),
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
            'options' => ['placeholder' => 'Agenda ...'],
            'pluginOptions' => [
                'allowClear' => true,
                'width' => '300px',
            ],
        ]);
        ?>
        <?= $form->field($model, 'penerima_suratrepoeks', ['autoPlaceholder' => false,])->textInput(['placeholder' => 'Kepada ...']) ?>
        <?= DateRangePicker::widget([
            'model' => $model,
            'attribute' => 'tanggal_suratrepoeks',
            'convertFormat' => true,
            'pluginOptions' => [
                'locale' => [
                    'format' => 'd M Y',
                ],
                'opens' => 'left',
            ],
            'options' => [
                'class' => 'form-control mr-2',
                'placeholder' => 'Tanggal ...'
            ],
        ]);
        ?>
        <?= $form->field($model, 'perihal_suratrepoeks', [
            'autoPlaceholder' => false,
        ])->textInput([
            'placeholder' => 'Perihal ...',
            'style' => 'max-width: 150px;', // Set the desired max-width value
        ]) ?>
        <?=
        $form->field($model, 'sifat')->dropDownList([
            0 => "Biasa", 1 => "Penting"
        ], ['prompt' => 'Sifat ...'])
        ?>
        <?= $form->field($model, 'nomor_suratrepoeks', [
            'autoPlaceholder' => false,
        ])->textInput([
            'placeholder' => 'Nomor ...',
            'style' => 'max-width: 150px;', // Set the desired max-width value
        ]) ?>
        <?=
        $form->field($model, 'jenis')->dropDownList([
            0 => 'Surat Biasa', 1 => 'Surat Perintah Lembur', 2 => 'Surat Keterangan'
        ], ['prompt' => 'Jenis ...'])
        ?>
        <?= $form->field($model, 'owner', [
            'autoPlaceholder' => false,
        ])->textInput([
            'placeholder' => 'Owner ...',
            'style' => 'max-width: 150px;', // Set the desired max-width value
        ]) ?>
        <br />
        <div class="form-group">
            <?= Html::submitButton('Search', ['class' => 'btn btn-warning mr-2']) ?>
            <?= Html::a('Reset', ['index?owner=&year='.date("Y")], ['class' => 'btn btn btn-outline-warning', 'style' => 'text-decoration:none']) ?>
        </div>
        <?php ActiveForm::end(); ?>
    </div>
</div>