<?php
use app\models\Agenda;
use yii\helpers\Html;
use yii\bootstrap5\ActiveForm;
use kartik\select2\Select2;
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
                <?=
                $form->field($model, 'peserta')->widget(Select2::class, [
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
                <?= $form->field($model, 'peserta_lain')->textarea(['rows' => 3])
                    ->hint('Input nama badan/orang di luar BPS Kabupaten Bengkulu Selatan serta alamat email valid (<b>hanya</b> jika ingin mengirimkan undangan digital via email blast). Data dapat terisi lebih dari satu, pisahkan dengan koma. Contoh: <b>Bappeda Kabupaten Bengkulu Selatan, Nofriana, S.Pd., dianputra@bps.go.id, khansa.safira19@gmail.com</b>', ['class' => '', 'style' => 'color: #999']) ?>
                <div class="form-group text-end mb-3">
                    <?= Html::submitButton('<i class="fas fa-save"></i> Simpan', ['class' => 'btn btn btn-outline-warning btn-block']) ?>
                </div>
            </div>
            <div class="col-sm-6">
                <?= $form->errorSummary($model) ?>
            </div>
        </div>
        <?php ActiveForm::end(); ?>
    </div>
</div>
<script>
    document.getElementById("w1-warning-0").style.display = 'none';
</script>