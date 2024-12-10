<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
?>
<div class="laporan-search">
    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>
    <?= $form->field($model, 'id_laporan') ?>
    <?= $form->field($model, 'laporan') ?>
    <?= $form->field($model, 'dokumentasi') ?>
    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-warning']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn btn-outline-light']) ?>
    </div>
    <?php ActiveForm::end(); ?>
</div>
