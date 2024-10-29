<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\DlSearch $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="dl-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id_dl') ?>

    <?= $form->field($model, 'pegawai') ?>

    <?= $form->field($model, 'tanggal_mulai') ?>

    <?= $form->field($model, 'tanggal_selesai') ?>

    <?= $form->field($model, 'fk_tujuan') ?>

    <?php // echo $form->field($model, 'tugas') ?>

    <?php // echo $form->field($model, 'tim') ?>

    <?php // echo $form->field($model, 'reporter') ?>

    <?php // echo $form->field($model, 'deleted') ?>

    <?php // echo $form->field($model, 'timestamp') ?>

    <?php // echo $form->field($model, 'timestamp_lastupdate') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
