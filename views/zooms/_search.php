<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\ZoomsSearch $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="zooms-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id_zooms') ?>

    <?= $form->field($model, 'fk_agenda') ?>

    <?= $form->field($model, 'jenis_zoom') ?>

    <?= $form->field($model, 'jenis_surat') ?>

    <?= $form->field($model, 'fk_surat') ?>

    <?php // echo $form->field($model, 'proposer') ?>

    <?php // echo $form->field($model, 'deleted') ?>

    <?php // echo $form->field($model, 'timestamp') ?>

    <?php // echo $form->field($model, 'timestamp_lastupdate') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
