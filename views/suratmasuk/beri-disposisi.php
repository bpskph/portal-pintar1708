<?php

use yii\helpers\Html;
use yii\bootstrap5\ActiveForm;
use kartik\date\DatePicker;
use kartik\select2\Select2;
?>
<div class="container-fluid" data-aos="fade-up">
    <div class="card alert <?= ((!Yii::$app->user->isGuest && Yii::$app->user->identity->theme == 0) ? 'bg-light' : 'bg-dark') ?>">
        <?php $form = ActiveForm::begin([
            'layout' => 'horizontal',
            'fieldConfig' => [
                'horizontalCssClasses' => [
                    'label' => 'col-sm-3',
                    'wrapper' => 'col-sm-9',
                    'hint' => 'col-sm-offset-3 col-sm-9',
                ],
            ],
        ]); ?>
        <div class="row">
            <div class="col-lg-8">
                <?= $header; ?>
                <?= $form->errorSummary($model) ?>

                <?= $form->field($model, 'tanggal_disposisi')->textInput(['maxlength' => true, 'value' => date('Y-m-d'), 'readonly' => true])
                    ->hint('Hari ini: ' . Yii::$app->formatter->asDatetime(strtotime("today"), "d MMMM y"), ['class' => '', 'style' => 'color: #999']) ?>

                <?=
                $form->field($model, 'tujuan_disposisi_team')->widget(Select2::class, [
                    'data' => \yii\helpers\ArrayHelper::map(
                        \app\models\Teamleader::find()->joinWith(['teame', 'penggunae'])->where('leader_status = 1')->all(),
                        'fk_team',
                        function ($model) {
                            return $model['teame']['nama_team'] . ' [Ketua: ' . $model['penggunae']['nama'] .  ']';
                        }
                    ),
                    'theme' => Select2::THEME_KRAJEE,
                    'options' => [
                        'multiple' => false,
                        'placeholder' => 'Pilih Tim Kerja ...',
                        'value' => $model->isNewRecord ? '' : $model->tujuan_disposisi_team,
                    ],
                ])->label('Disposisi Utama'); ?>

                <?=
                $form->field($model, 'tujuan_disposisi_team_lain')->widget(Select2::class, [
                    'data' => \yii\helpers\ArrayHelper::map(
                        \app\models\Teamleader::find()->joinWith(['teame', 'penggunae'])->where('leader_status = 1')->all(),
                        'fk_team',
                        function ($model) {
                            return $model['teame']['nama_team'] . ' [Ketua: ' . $model['penggunae']['nama'] .  ']';
                        }
                    ),
                    'theme' => Select2::THEME_KRAJEE,
                    'options' => [
                        'multiple' => true,
                        'placeholder' => 'Pilih Tim Kerja ...',
                        'value' => $model->isNewRecord ? '' : $model->tujuan_disposisi_team,
                    ],
                ])->label('Disposisi Lainnya'); ?>
            </div>
            <div class="col-lg-4">
                <iframe id="pdf-iframe" src="<?= Yii::getAlias('@web') ?>/surat/masuk/<?php echo $suratmasuk->id_suratmasuk ?>.pdf" width="100%" height="100%"></iframe>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-8">
                <?= $form->field($model, 'instruksi')->textarea(['rows' => 6, 'value' => $model->isNewRecord ? '' : $model->tujuan_disposisi_team]) ?>

                <div class="form-group text-end mb-3">
                    <?= Html::submitButton('<i class="fas fa-save"></i> Simpan', ['class' => 'btn btn btn-outline-warning']) ?>
                </div>
            </div>
            <div class="col-lg-4 mt-2">
                <h5>Instruksi dari Pimpinan: </h5>
                <?php if ($level == 1): ?>
                    <span class="fst-italic">Belum ada instruksi sebelumnya ...</span>
                <?php else: ?>
                    <div class="alert alert-dark">
                        <?= $disposisisatu->instruksi ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        <?php ActiveForm::end(); ?>
    </div>
</div>