<?php

use yii\helpers\Html;
use kartik\form\ActiveForm;
use yii\helpers\ArrayHelper;
use kartik\select2\Select2;
use kartik\daterange\DateRangePicker;
?>
<div class="card <?= ((!Yii::$app->user->isGuest && Yii::$app->user->identity->theme == 0) ? '' : 'bg-dark') ?>">
    <div class="card-body" style="margin: 0 auto!important">
        <?php
        $form = ActiveForm::begin([
            'action' => ['index?year=&from=&for='],
            'method' => 'get',
            'type' => ActiveForm::TYPE_INLINE,
            'fieldConfig' => ['options' => ['class' => 'form-group mr-2']]
        ]);
        ?>
        <?= $form->field($model, 'pengirim_suratmasuk', ['autoPlaceholder' => false,])->textInput(['placeholder' => 'Satker Pengirim ...']) ?>

        <?= $form->field($model, 'perihal_suratmasuk', ['autoPlaceholder' => false,])->textInput(['placeholder' => 'Perihal Surat ...']) ?>

        <?= DateRangePicker::widget([
            'model' => $model,
            'attribute' => 'tanggal_diterima',
            'convertFormat' => true,
            'pluginOptions' => [
                'locale' => [
                    'format' => 'd M Y',
                ],
                'opens' => 'left',
            ],
            'options' => [
                'class' => 'form-control mr-2',
                'placeholder' => 'Tanggal Diterima ...'
            ],
        ]);
        ?>

        <?= $form->field($model, 'nomor_suratmasuk', ['autoPlaceholder' => false,])->textInput(['placeholder' => 'Nomor Surat ...']) ?>

        <?= DateRangePicker::widget([
            'model' => $model,
            'attribute' => 'tanggal_suratmasuk',
            'convertFormat' => true,
            'pluginOptions' => [
                'locale' => [
                    'format' => 'd M Y',
                ],
                'opens' => 'left',
            ],
            'options' => [
                'class' => 'form-control mr-2',
                'placeholder' => 'Tanggal pada Surat ...'
            ],
        ]);
        ?>

        <?= $form->field($model, 'reporter', [
            'autoPlaceholder' => false,
        ])->textInput([
            'placeholder' => 'Penginput Data ...',
        ]) ?>

        <?= $form->field($model, 'pemberidisposisi', [
            'autoPlaceholder' => false,
        ])->textInput([
            'placeholder' => 'Pemberi Disposisi ...',
        ]) ?>

        <?= $form->field($model, 'penerimadisposisi', [
            'autoPlaceholder' => false,
        ])->textInput([
            'placeholder' => 'Penerima Disposisi ...',
        ]) ?>

        <br />
        <div class="form-group">
            <?= Html::submitButton('Search', ['class' => 'btn btn-warning mr-2']) ?>
            <?= Html::a('Reset', ['index?for=&from=&year=' . date("Y")], ['class' => 'btn btn btn-outline-warning', 'style' => 'text-decoration:none']) ?>
        </div>
        <?php ActiveForm::end(); ?>
    </div>
</div>