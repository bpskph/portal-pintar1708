<?php

use yii\helpers\Html;
use yii\bootstrap5\ActiveForm;
use kartik\date\DatePicker;
use kartik\select2\Select2;
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
            'options' => ['enctype' => 'multipart/form-data']
        ]); ?>
        <?= $form->errorSummary($model) ?>
        <?= $form->field($model, 'pengirim_suratmasuk')->textInput(['maxlength' => true])
            ->hint('Instansi/Satker pengirim surat.', ['class' => '', 'style' => 'color: #999']) ?>

        <?= $form->field($model, 'perihal_suratmasuk')->textarea(['rows' => 6]) ?>

        <?= $form->field($model, 'tanggal_diterima')->widget(DatePicker::classname(), [
            'options' => ['placeholder' => 'Tanggal Diterima ...'],
            'pluginOptions' => [
                'autoclose' => true,
                'format' => 'yyyy-mm-dd',
                'endDate' => date('Y-m-d') // Set end date to today
            ]
        ])->hint('Tanggal surat yang dapat diinput adalah sebatas tanggal hari ini.', ['class' => '', 'style' => 'color: #999']) ?>

        <?= $form->field($model, 'nomor_suratmasuk')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'tanggal_suratmasuk')->widget(DatePicker::classname(), [
            'options' => ['placeholder' => 'Tanggal pada Surat ...'],
            'pluginOptions' => [
                'autoclose' => true,
                'format' => 'yyyy-mm-dd',
                'endDate' => date('Y-m-d') // Set end date to today
            ]
        ])->hint('Tanggal pada surat tidak bisa lebih awal daripada tanggal surat diterima.', ['class' => '', 'style' => 'color: #999']) ?>

        <?= $form->field($model, 'sifat')->widget(Select2::classname(), [
            'data' => [0 => "Biasa", 1 => "Terbatas", 2 => "Rahasia"],
            'options' => [
                'placeholder' => 'Sifat Surat',
            ],
            'pluginOptions' => [
                'allowClear' => true
            ],
        ]);
        ?>

        <input type="hidden" name="<?= Yii::$app->request->csrfParam; ?>" value="<?= Yii::$app->request->csrfToken; ?>" />
        <?= $form->field($model, 'filepdf')->fileInput()->label('Upload File PDF') ?>

        <?php if (!$model->isNewRecord && file_exists(Yii::getAlias('@webroot/surat/masuk/' . $model->id_suratmasuk . '.pdf'))) : ?>
            <div class="mb-3 transparan" style="border-width:0px">
                <div class="row g-0">
                    <div class="col-md-2">
                        <h5 class="card-title">File Saat Ini</h5>
                    </div>
                    <div class="col-md-10">
                        <div id="pdf-container">
                            <iframe id="pdf-iframe" src="<?= Yii::getAlias('@web') ?>/surat/masuk/<?php echo $model->id_suratmasuk ?>.pdf" width="100%" height="350px"></iframe>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>


        <div class="form-group text-end mb-3">
            <?= Html::submitButton('<i class="fas fa-save"></i> Simpan', ['class' => 'btn btn btn-outline-warning']) ?>
        </div>
        <?php ActiveForm::end(); ?>
    </div>
</div>