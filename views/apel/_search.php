<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
/** @var yii\web\View $this */
/** @var app\models\ApelSearch $model */
/** @var yii\widgets\ActiveForm $form */
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
    <?php // echo $form->field($model, 'perwira') ?>
    <?php // echo $form->field($model, 'mc') ?>
    <?php // echo $form->field($model, 'uud') ?>
    <?php // echo $form->field($model, 'korpri') ?>
    <?php // echo $form->field($model, 'doa') ?>
    <?php // echo $form->field($model, 'ajudan') ?>
    <?php // echo $form->field($model, 'operator') ?>
    <?php // echo $form->field($model, 'bendera') ?>
    <?php // echo $form->field($model, 'reporter') ?>
    <?php // echo $form->field($model, 'timestamp') ?>
    <?php // echo $form->field($model, 'timestamp_apel_lastupdate') ?>
    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-warning']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn btn-outline-light']) ?>
    </div>
    <?php ActiveForm::end(); ?>
</div>
