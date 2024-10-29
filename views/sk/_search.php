<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\SkSearch $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="sk-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'id_sk') ?>

    <?= $form->field($model, 'nomor_sk') ?>

    <?= $form->field($model, 'tanggal_sk') ?>

    <?= $form->field($model, 'tentang_sk') ?>

    <?= $form->field($model, 'nama_dalam_sk') ?>

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
