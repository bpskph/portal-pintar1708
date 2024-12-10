<?php
use yii\helpers\Html;
use kartik\form\ActiveForm;
?>
<div class="card <?= ((!Yii::$app->user->isGuest && Yii::$app->user->identity->theme == 0) ? '' : 'bg-dark') ?>">
    <div class="card-body" style="margin: 0 auto!important">
        <?php
        $form = ActiveForm::begin([
            'action' => ['index?year='],
            'method' => 'get',
            'type' => ActiveForm::TYPE_INLINE,
            'fieldConfig' => ['options' => ['class' => 'form-group mr-2']]
        ]);
        ?>
        <?= $form->field($model, 'teame', ['autoPlaceholder' => false,])->textInput(['placeholder' => 'Tim Kerja ...']) ?>
        <?= $form->field($model, 'tahun', ['autoPlaceholder' => false,])->dropDownList(
            $model->getYears(),
            ['class' => 'form-control input', 'prompt' => 'Tahun...']
        ) ?>
        <?= $form->field($model, 'fk_project', ['autoPlaceholder' => false,])->textInput(['placeholder' => 'Project ...']) ?>
        <?= $form->field($model, 'pegawai', ['autoPlaceholder' => false,])->textInput(['placeholder' => 'Pegawai ...']) ?>
        <?=
        $form->field($model, 'member_status')->dropDownList([
            1 => 'Anggota',
            2 => 'Ketua',
            3 => 'Operator'
        ], ['prompt' => 'Status Anggota ...'])
        ?>
        <div class="form-group">
            <?= Html::submitButton('Search', ['class' => 'btn btn-warning mr-2']) ?>
            <?= Html::a('Reset', ['index?year='.date("Y")], ['class' => 'btn btn btn-outline-warning', 'style' => 'text-decoration:none']) ?>
        </div>
        <?php ActiveForm::end(); ?>
    </div>
</div>