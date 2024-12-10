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
        <?= $form->field($model, 'judul')->textInput(['maxlength' => true]) ?>
        <?= $form->field($model, 'link')->textInput(['rows' => 6]) ?>
        <?= $form->field($model, 'keyword', [])->textarea(['rows' => 3])
            ->hint('Input satu atau lebih keyword dan pisahkan dengan koma', ['class' => '', 'style' => 'color: #999']) ?>
        <?= $form->field($model, 'keterangan')->textarea(['rows' => 3])
            ->hint('Deskripsi lebih lanjut mengenai materi ini (opsional).', ['class' => '', 'style' => 'color: #999']) ?>
        <div class="form-group text-end mb-3">
            <?= Html::submitButton('<i class="fas fa-save"></i> Simpan', ['class' => 'btn btn btn-outline-warning']) ?>
        </div>
        <?php ActiveForm::end(); ?>
    </div>
</div>