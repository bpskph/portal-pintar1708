<?php
use yii\helpers\Html;
// use yii\widgets\ActiveForm;
use kartik\form\ActiveForm;
use yii\helpers\ArrayHelper;
/** @var yii\web\View $this */
/** @var app\models\AgendaSearch $model */
/** @var yii\widgets\ActiveForm $form */
?>
<div class="card <?= ((!Yii::$app->user->isGuest && Yii::$app->user->identity->theme == 0) ? '' : 'bg-dark') ?>">
    <div class="card-body" style="margin: 0 auto!important">
        <?php
        $form = ActiveForm::begin([
            'action' => ['indexgrid'],
            'method' => 'get',
            'type' => ActiveForm::TYPE_INLINE,
            'fieldConfig' => ['options' => ['class' => 'form-group']]
        ]);
        ?>
        <?= $form->field($model, 'judul', ['autoPlaceholder' => false,])->textInput(['placeholder' => 'Judul ...']) ?>
        <?= $form->field($model, 'link', ['autoPlaceholder' => false,])->textInput(['placeholder' => 'Link ...', 'class'=>'']) ?>
        <?= $form->field($model, 'keyword', ['autoPlaceholder' => false,])->textInput(['placeholder' => 'Keyword ...', 'class'=>'']) ?>
        <div class="form-group">
            <?= Html::submitButton('Search', ['class' => 'btn btn-warning']) ?>
            <?= Html::a('Reset', ['index'], ['class' => 'btn btn btn-outline-warning', 'style' => 'text-decoration:none']) ?>
        </div>
        <?php ActiveForm::end(); ?>
    </div>
</div>