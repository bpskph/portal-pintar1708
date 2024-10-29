<?php

use app\models\Apel;
use yii\helpers\Html;
use yii\bootstrap5\ActiveForm;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use kartik\date\DatePicker;

/** @var yii\web\View $this */
/** @var app\models\Linkapp $model */
/** @var yii\widgets\ActiveForm $form */
// $actionId = Yii::$app->controller->action->id;
// $script = <<< JS
//     var actionId = '$actionId';
// JS;
// $this->registerJs($script, \yii\web\View::POS_HEAD);

if ($model->isNewRecord) {
    $model->tanggal_apel = date("Y-m-d");
}

?>
<style>
    body:not(.gelap) #apel-tanggal_apel {
        background-color: #fff !important;
    }
</style>

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
        <?= $form->field($model, 'jenis_apel')->widget(Select2::classname(), [
            'data' => [0 => "Apel", 1 => "Upacara"],
            'options' => ['placeholder' => 'Jenis Apel'],
            'pluginOptions' => [
                'allowClear' => true
            ],
        ]);
        ?>
        <?php // $form->field($model, 'tanggal_apel')->textInput(['readonly' => false, 'placeholder' => 'Pilih Tanggal', 'value' => $model->tanggal_apel]) 
        ?>
        <?= $form->field($model, 'tanggal_apel')->widget(DatePicker::classname(), [
            'options' => ['placeholder' => 'Pilih Tanggal ...'],
            'pluginOptions' => [
                'autoclose' => true,
                'format' => 'yyyy-mm-dd'
            ]
        ]); ?>
        <?php $data = ArrayHelper::map(
            \app\models\Pengguna::find()->select('*')->asArray()->all(),
            'username',
            function ($model) {
                return $model['nama'];
            }
        ) ?>
        <?= $form->field($model, 'pembina_inspektur')->widget(Select2::classname(), [
            'data' => $data,
            'options' => ['placeholder' => 'Pilih Pembina/Inspektur'],
            'pluginOptions' => [
                'allowClear' => true
            ],
        ]);
        ?>
        <?= $form->field($model, 'pemimpin_komandan')->widget(Select2::classname(), [
            'data' => $data,
            'options' => ['placeholder' => 'Pilih Pemimpin/Komandan'],
            'pluginOptions' => [
                'allowClear' => true
            ],
        ]);
        ?>
        <?= $form->field($model, 'perwira')->widget(Select2::classname(), [
            'data' => $data,
            'options' => ['placeholder' => 'Pilih Perwira'],
            'pluginOptions' => [
                'allowClear' => true
            ],
        ]);
        ?>
        <?= $form->field($model, 'mc')->widget(Select2::classname(), [
            'data' => $data,
            'options' => ['placeholder' => 'Pilih MC'],
            'pluginOptions' => [
                'allowClear' => true
            ],
        ]);
        ?>
        <?= $form->field($model, 'uud')->widget(Select2::classname(), [
            'data' => $data,
            'options' => ['placeholder' => 'Pilih Pembaca UUD'],
            'pluginOptions' => [
                'allowClear' => true
            ],
        ]);
        ?>
        <?= $form->field($model, 'korpri')->widget(Select2::classname(), [
            'data' => $data,
            'options' => ['placeholder' => 'Pilih Pembaca Panca Prasetya KORPRI'],
            'pluginOptions' => [
                'allowClear' => true
            ],
        ]);
        ?>
        <?= $form->field($model, 'doa')->widget(Select2::classname(), [
            'data' => $data,
            'options' => ['placeholder' => 'Pilih Pembaca Doa'],
            'pluginOptions' => [
                'allowClear' => true
            ],
        ]);
        ?>
        <?= $form->field($model, 'ajudan')->widget(Select2::classname(), [
            'data' => $data,
            'options' => ['placeholder' => 'Pilih Ajudan'],
            'pluginOptions' => [
                'allowClear' => true
            ],
        ]);
        ?>
        <?= $form->field($model, 'operator')->widget(Select2::classname(), [
            'data' => $data,
            'options' => ['placeholder' => 'Pilih Operator Lagu'],
            'pluginOptions' => [
                'allowClear' => true
            ],
        ]);
        ?>
        <?php if (!$model->isNewRecord) : ?>
            <?php $cek = Apel::find()
                ->select('bendera')
                ->where(['id_apel' => $model->id_apel])
                ->one();
            $data = str_replace('@bps.go.id', '', $cek->bendera);
            $array = explode(", ", $data);
            ?>
            <?php $model->bendera = $array; ?>
        <?php endif; ?>
        <?=
        $form->field($model, 'bendera')->widget(Select2::class, [
            'data' => \yii\helpers\ArrayHelper::map(
                \app\models\Pengguna::find()->all(),
                'username',
                'nama'
            ),
            'theme' => Select2::THEME_KRAJEE,
            'options' => [
                'multiple' => true,
                'placeholder' => 'Pilih Pengibar Bendera ...',
            ],
        ]); ?>
        <?= $form->field($model, 'tambahsatu_text')->textInput(['maxlength' => true])->hint('Isikan Jabatan Petugas Tambahan Pertama, misal: Pembaca Teks Proklamasi') ?>
        <?= $form->field($model, 'tambahsatu_petugas')->widget(Select2::classname(), [
            'data' => $data,
            'options' => ['placeholder' => 'Pilih Petugas Tambahan 1'],
            'pluginOptions' => [
                'allowClear' => true
            ],
        ]);
        ?>
        <?= $form->field($model, 'tambahdua_text')->textInput(['maxlength' => true])->hint('Isikan Jabatan Petugas Tambahan Kedua, misal: Pembaca Sumpah Pemuda') ?>
        <?= $form->field($model, 'tambahdua_petugas')->widget(Select2::classname(), [
            'data' => $data,
            'options' => ['placeholder' => 'Pilih Petugas Tambahan 2'],
            'pluginOptions' => [
                'allowClear' => true
            ],
        ]);
        ?>
        <div class="form-group text-end mb-3">
            <?= Html::submitButton('<i class="fas fa-save"></i> Simpan', ['class' => 'btn btn btn-outline-warning']) ?>
        </div>
        <?php ActiveForm::end(); ?>
    </div>
</div>