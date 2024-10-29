<?php
use yii\helpers\Html;
use yii\bootstrap5\ActiveForm;
?>
<div class="container-fluid" data-aos="fade-up">
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
        <?= $form->field($model, 'judul_popups')->textInput() ?>
        <?= $form->field($model, 'rincian_popups')->textarea(['rows' => 6]) ?>
        <div class="form-group text-end mb-3">
            <?= Html::submitButton('<i class="fas fa-save"></i> Simpan', ['class' => 'btn btn btn-outline-warning']) ?>
        </div>
        <?php ActiveForm::end(); ?>
    </div>
</div>