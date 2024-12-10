<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
?>
<div class="apel-search">
    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>
    <?= $form->field($model, 'id_apel') ?>
    <?= $form->field($model, 'jenis_apel') ?>
    <?= $form->field($model, 'tanggal_apel') ?>
    <?= $form->field($model, 'pembina_inspektur') ?>
    <?= $form->field($model, 'pemimpin_komandan') ?>
    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-warning']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn btn-outline-light']) ?>
    </div>
    <?php ActiveForm::end(); ?>
</div>
