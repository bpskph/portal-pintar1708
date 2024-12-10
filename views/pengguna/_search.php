<?php
use yii\helpers\Html;
use kartik\form\ActiveForm;
?>
<div class="card <?= ((!Yii::$app->user->isGuest && Yii::$app->user->identity->theme == 0) ? '' : 'bg-dark') ?>">
    <div class="card-body" style="margin: 0 auto!important">
        <?php
        $form = ActiveForm::begin([
            'action' => ['index'],
            'method' => 'get',
            'type' => ActiveForm::TYPE_INLINE,
            'fieldConfig' => ['options' => ['class' => 'form-group mr-2']]
        ]);
        ?>
        <?= $form->field($model, 'nama', ['autoPlaceholder' => false,])->textInput(['placeholder' => 'Nama ...']) ?>
        <?= $form->field($model, 'username', ['autoPlaceholder' => false,])->textInput(['placeholder' => 'Username ...']) ?>
        <?= $form->field($model, 'nip', ['autoPlaceholder' => false,])->textInput(['placeholder' => 'NIP ...']) ?>
        <?=
        $form->field($model, 'level')->dropDownList([
            0 => 'Admin',
            1 => 'Aktif',
            2 => 'Dihapus'
        ], ['prompt' => 'Status Akses ...'])
        ?>
        <div class="form-group">
            <?= Html::submitButton('Search', ['class' => 'btn btn-warning mr-2']) ?>
            <?= Html::a('Reset', ['index'], ['class' => 'btn btn btn-outline-warning', 'style' => 'text-decoration:none']) ?>
        </div>
        <?php ActiveForm::end(); ?>
    </div>
</div>
