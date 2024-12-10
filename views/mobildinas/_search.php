<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
?>

<div class="mobildinas-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id_mobildinas') ?>

    <?= $form->field($model, 'mulai') ?>

    <?= $form->field($model, 'selesai') ?>

    <?= $form->field($model, 'keperluan') ?>

    <?= $form->field($model, 'borrower') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
