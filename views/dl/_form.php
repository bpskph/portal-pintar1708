<?php

use app\models\Dl;
use yii\helpers\Html;
use yii\bootstrap5\ActiveForm;
use kartik\select2\Select2;
use kartik\date\DatePicker;

if ($model->isNewRecord) {
    $model->tanggal_mulai = date("Y-m-d");
    $model->tanggal_selesai = date("Y-m-d");
}

?>
<div class="container" data-aos="fade-up">
    <div class="card alert <?= ((!Yii::$app->user->isGuest && Yii::$app->user->identity->theme == 0) ? 'bg-light' : 'bg-dark') ?>">
        <?php $form = ActiveForm::begin([
            'layout' => 'horizontal',
            'fieldConfig' => [
                'horizontalCssClasses' => [
                    'label' => 'col-sm-3',
                    'wrapper' => 'col-sm-9',
                    'hint' => 'col-sm-offset-3 col-sm-9',
                ],
            ],
        ]); ?>
        <?= $form->errorSummary($model) ?>

        <?php if (!$model->isNewRecord) : ?>
            <?php $cek = Dl::find()
                ->select('pegawai')
                ->where(['id_dl' => $model->id_dl])
                ->one();
            $data = str_replace('@bps.go.id', '', $cek->pegawai);
            $array = explode(", ", $data);
            ?>
            <?php $model->pegawai = $array; ?>
        <?php endif; ?>
        <?=
        $form->field($model, 'pegawai')->widget(Select2::class, [
            'data' => \yii\helpers\ArrayHelper::map(
                \app\models\Pengguna::find()->all(),
                'username',
                'nama'
            ),
            'theme' => Select2::THEME_KRAJEE,
            'options' => [
                'multiple' => true,
                'placeholder' => 'Pilih Daftar Pegawai Pelaksana Tugas ...',
            ],
        ]); ?>


        <?= $form->field($model, 'tanggal_mulai')->widget(DatePicker::classname(), [
            'options' => ['placeholder' => 'Pilih Tanggal ...'],
            'pluginOptions' => [
                'autoclose' => true,
                'format' => 'yyyy-mm-dd'
            ]
        ]); ?>

        <?= $form->field($model, 'tanggal_selesai')->widget(DatePicker::classname(), [
            'options' => ['placeholder' => 'Pilih Tanggal ...'],
            'pluginOptions' => [
                'autoclose' => true,
                'format' => 'yyyy-mm-dd'
            ]
        ]); ?>

        <?=
        $form->field($model, 'fk_tujuan')->widget(Select2::class, [
            'data' => \yii\helpers\ArrayHelper::map(
                \app\models\Dltujuan::find()->joinWith('tujuanprove')->all(),
                'id_dltujuan',
                function ($model) {
                    return '[' . $model['id_dltujuan'] . '] ' . $model['nama_tujuan'] . ' di Provinsi ' . $model['tujuanprove']['nama_tujuanprov'];
                }
            ),
            'theme' => Select2::THEME_KRAJEE,
            'options' => [
                'multiple' => false,
                'placeholder' => 'Pilih Kabupaten/Kota Tujuan ...',
            ],
        ]); ?>

        <?= $form->field($model, 'tugas')->textarea(['rows' => 6]) ?>

        <?=
        $form->field($model, 'tim')->widget(Select2::class, [
            'data' => \yii\helpers\ArrayHelper::map(
                \app\models\Project::find()
                    ->select('*, team.panggilan_team as namateam')
                    ->joinWith(['teame', 'projectmembere', 'teamleadere'])
                    ->where(['project.tahun' => date("Y")])
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
            'theme' => Select2::THEME_KRAJEE,
            'options' => [
                'multiple' => false,
                'placeholder' => 'Pilih Tim (Kosongkan Jika Tidak Ada Mewakili Tim Tertentu) ...',
            ],
            'pluginOptions' => [
                'allowClear' => true
            ],
        ]); ?>

        <div class="form-group text-end mb-3">
            <?= Html::submitButton('<i class="fas fa-save"></i> Simpan', ['class' => 'btn btn btn-outline-warning']) ?>
        </div>

        <?php ActiveForm::end(); ?>
    </div>
</div>