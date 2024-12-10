<?php

use app\models\beritarilis;
use app\models\Rooms;
use yii\helpers\Html;
use yii\bootstrap5\ActiveForm;
use kartik\select2\Select2;
use kartik\switchinput\SwitchInput;
use yii\helpers\ArrayHelper;
use kartik\datetime\DateTimePicker;

if ($model->isNewRecord) {
    $model->waktumulai = date("Y-m-d 10:00:00");
    $model->waktuselesai = date("Y-m-d 12:00:00");
}

?>
<div class="container-fluid" data-aos="fade-up">
    <div class="card alert <?= ((!Yii::$app->user->isGuest && Yii::$app->user->identity->theme == 0) ? 'bg-light' : 'bg-dark') ?>">
        <?php $form = ActiveForm::begin([
            'layout' => 'horizontal',
            'fieldConfig' => [
                'horizontalCssClasses' => [
                    'label' => 'col-sm-2',
                    'wrapper' => 'col-sm-10',
                    'hint' => 'col-sm-offset-2 col-sm-10',
                ],
            ],
        ]); ?>
        <?= $form->errorSummary($model) ?>
        <?= $form->field($model, 'waktumulai')->widget(DateTimePicker::classname(), [
            'options' => ['placeholder' => 'Pilih Tanggal dan Jam ...'],
            'pluginOptions' => [
                'autoclose' => true,
                'format' => 'yyyy-mm-dd hh:ii:ss',
            ]
        ]); ?>
        <?= $form->field($model, 'waktuselesai')->widget(DateTimePicker::classname(), [
            'options' => ['placeholder' => 'Pilih Tanggal dan Jam ...'],
            'pluginOptions' => [
                'autoclose' => true,
                'format' => 'yyyy-mm-dd hh:ii:ss',
            ]
        ]); ?>

        <?= $form->field($model, 'materi_rilis')->textarea(['rows' => 6]) ?>
        <?php $data = ArrayHelper::map(
            \app\models\Pengguna::find()->select('*')->asArray()->all(),
            'username',
            function ($model) {
                return $model['nama'];
            }
        ) ?>
        <?php if (!$model->isNewRecord) : ?>
            <?php $cek = Beritarilis::find()
                ->select('narasumber')
                ->where(['id_beritarilis' => $model->id_beritarilis])
                ->one();
            $data = str_replace('@bps.go.id', '', $cek->narasumber);
            $array = explode(", ", $data);
            ?>
            <?php $model->narasumber = $array; ?>
        <?php endif; ?>
        <?=
        $form->field($model, 'narasumber')->widget(Select2::class, [
            'data' => \yii\helpers\ArrayHelper::map(
                \app\models\Pengguna::find()->all(),
                'username',
                'nama'
            ),
            'theme' => Select2::THEME_KRAJEE,
            'options' => [
                'multiple' => true,
                'placeholder' => 'Pilih Narasumber ...',
            ],
        ]); ?>
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
        <div id="yes_t" <?= $model->pilihtempat == true ? ' style="display:none"' : '' ?>>
            <?= $form->field($model, 'lokasi')->widget(Select2::classname(), [
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
            <?= $form->field($model, 'lokasitext')->textInput(['maxlength' => true, 'value' => $model->isNewRecord ? '' : $model->tempate])
                ->hint('Isikan Lokasi Rilis (Di Luar Kantor)', ['class' => '', 'style' => 'color: #999']) ?>
        </div>
        <div class="form-group text-end mb-3">
            <?= Html::submitButton('<i class="fas fa-save"></i> Simpan', ['class' => 'btn btn btn-outline-warning']) ?>
        </div>
        <?php ActiveForm::end(); ?>
    </div>
</div>