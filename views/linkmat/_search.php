<?php
use yii\helpers\Html;
// use yii\widgets\ActiveForm;
use kartik\form\ActiveForm;
/** @var yii\web\View $this */
/** @var app\models\AgendaSearch $model */
/** @var yii\widgets\ActiveForm $form */
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
        <?= $form->field($model, 'judul', ['autoPlaceholder' => false,])->textInput(['placeholder' => 'Judul ...']) ?>
        <?= $form->field($model, 'link', ['autoPlaceholder' => false,])->textInput(['placeholder' => 'Link ...']) ?>
        <?= $form->field($model, 'keyword', ['autoPlaceholder' => false,])->textInput(['placeholder' => 'Keyword ...']) ?>
        <?= $form->field($model, 'owner', ['autoPlaceholder' => false,])->textInput(['placeholder' => 'Owner ...']) ?>
        <?=
        $form->field($model, 'active')->dropDownList([
            0 => 'Menunggu Moderasi',
            1 => 'Aktif',
            2 => 'Dihapus'
        ], ['prompt' => 'Keterangan ...'])
        ?>
        <div class="form-group">
            <?= Html::submitButton('Search', ['class' => 'btn btn-warning mr-2']) ?>
            <?= Html::a('Reset', ['index'], ['class' => 'btn btn btn-outline-warning', 'style' => 'text-decoration:none']) ?>
        </div>
        <?php ActiveForm::end(); ?>
    </div>
</div>