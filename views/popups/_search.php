<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
?>

<div class="popups-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id_popups') ?>

    <?= $form->field($model, 'judul_popups') ?>

    <?= $form->field($model, 'rincian_popups') ?>

    <?= $form->field($model, 'deleted') ?>

    <?= $form->field($model, 'timestamp') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
