<?php

use yii\helpers\Html;
use yii\bootstrap5\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\Patches $model */
/** @var yii\widgets\ActiveForm $form */
?>
<div class="container" data-aos="fade-up">
    <div class="card alert <?= ((!Yii::$app->user->isGuest && Yii::$app->user->identity->theme == 0) ? 'bg-light' : 'bg-dark') ?>">
        <?php $form = ActiveForm::begin([
            'layout' => 'horizontal',
            'fieldConfig' => [
                'horizontalCssClasses' => [
                    'label' => 'col-sm-2',
                    'wrapper' => 'col-sm-10',
                    'hint' => 'col-sm-offset-2 col-sm-10',
                ],
            ],
        ]); ?>
        <?= $form->errorSummary($model) ?>

        <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>
        <?= $form->field($model, 'description')->textarea(['rows' => 6]) ?>
        <?= $form->field($model, 'is_notification')->checkbox()->label('&nbsp;Tandai ini jika Anda ingin memberitahukan pengguna atas update ini.&nbsp;', ['style' => 'background-color: #ffc107; border-radius: 5px']); ?>

        <div class="form-group text-end mb-3">
            <?= Html::submitButton('<i class="fas fa-save"></i> Simpan', ['class' => 'btn btn btn-outline-warning']) ?>
        </div>

        <?php ActiveForm::end(); ?>
    </div>
</div>