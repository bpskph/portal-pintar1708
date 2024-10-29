<?php
use yii\helpers\Html;
// use yii\widgets\ActiveForm;
use kartik\form\ActiveForm;
use yii\helpers\ArrayHelper;
use kartik\daterange\DateRangePicker;
use app\models\Project;
use app\models\Rooms;
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
        <?= $form->field($model, 'kegiatan', ['autoPlaceholder' => false,])->textInput(['placeholder' => 'Kegiatan ...']) ?>
        <?= DateRangePicker::widget([
            'model' => $model,
            'attribute' => 'waktu',
            'convertFormat' => true,
            'pluginOptions' => [
                'locale' => [
                    'format' => 'd M Y',
                ],
                'opens' => 'left',
            ],
            'options' => [
                'class' => 'form-control mr-2',
                'placeholder' => 'Waktu ...'
            ],
        ]);
        ?>
        <?= $form->field($model, 'tempat', ['autoPlaceholder' => false,])->textInput(['placeholder' => 'Tempat ...']) ?>
        <div class="form-group">
            <?= Html::submitButton('Search', ['class' => 'btn btn-warning mr-2']) ?>
            <?= Html::a('Reset', ['index'], ['class' => 'btn btn btn-outline-warning', 'style' => 'text-decoration:none']) ?>
        </div>
        <?php ActiveForm::end(); ?>
    </div>
</div>