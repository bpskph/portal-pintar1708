<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
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
    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-warning']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn btn-outline-light']) ?>
    </div>
    <?php ActiveForm::end(); ?>
</div>
