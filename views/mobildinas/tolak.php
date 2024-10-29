<?php

use yii\helpers\Html;
use yii\bootstrap5\ActiveForm;

?>
<div class="container-fluid" data-aos="fade-up">
    <div class="card alert <?= ((!Yii::$app->user->isGuest && Yii::$app->user->identity->theme == 0) ? 'bg-light' : 'bg-dark') ?>">
        <?php $form = ActiveForm::begin([
            'layout' => 'default',
            'fieldConfig' => [
                'template' => "{label}\n{beginWrapper}\n{input}\n{hint}\n{error}\n{endWrapper}",
                'horizontalCssClasses' => [
                    'label' => 'col-sm-3',
                    'offset' => 'offset-sm-4',
                    'wrapper' => 'col-sm-9',
                    'error' => '',
                    'hint' => '',
                ],
            ],
            'enableClientValidation' => true
        ]); ?>
        <div class="row">
            <?= $form->field($model, 'alasan_tolak_batal')->textarea(['rows' => 3])->hint('Bubuhkan alasan penolakan peminjaman atau pembatalan persetujuannya', ['class' => '', 'style' => 'color: #999']) ?>

            <div class="form-group text-end mb-3">
                <?= Html::submitButton('<i class="fas fa-save"></i> Tolak/Batal', ['class' => 'btn btn btn-outline-danger btn-block']) ?>
            </div>
        </div>
        <?php ActiveForm::end(); ?>
    </div>
</div>