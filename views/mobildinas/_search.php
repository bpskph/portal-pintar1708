<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\MobildinasSearch $model */
/** @var yii\widgets\ActiveForm $form */
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

    <?php // echo $form->field($model, 'approval') ?>

    <?php // echo $form->field($model, 'timestamp') ?>

    <?php // echo $form->field($model, 'timestamp_lastupdate') ?>

    <?php // echo $form->field($model, 'deleted') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
