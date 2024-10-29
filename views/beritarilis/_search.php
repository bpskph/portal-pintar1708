<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
/** @var yii\web\View $this */
/** @var app\models\BeritarilisSearch $model */
/** @var yii\widgets\ActiveForm $form */
?>
<div class="beritarilis-search">
    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>
    <?= $form->field($model, 'id_beritarilis') ?>
    <?= $form->field($model, 'tanggal_rilis') ?>
    <?= $form->field($model, 'waktu_rilis') ?>
    <?= $form->field($model, 'waktu_rilis_selesai') ?>
    <?= $form->field($model, 'materi_rilis') ?>
    <?php // echo $form->field($model, 'narasumber') ?>
    <?php // echo $form->field($model, 'lokasi') ?>
    <?php // echo $form->field($model, 'reporter') ?>
    <?php // echo $form->field($model, 'timestamp') ?>
    <?php // echo $form->field($model, 'timestamp_lastupdate') ?>
    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-warning']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn btn-outline-light']) ?>
    </div>
    <?php ActiveForm::end(); ?>
</div>
