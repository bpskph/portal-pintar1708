<?php
use yii\helpers\Html;
use yii\bootstrap5\ActiveForm;
/** @var yii\web\View $this */
/** @var app\models\Linkapp $model */
/** @var yii\widgets\ActiveForm $form */
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
        <?= $form->field($model, 'screenshot')->fileInput()->hint('Screenshot beranda aplikasi dari layar komputer', ['class' => '', 'style' => 'color: #999']) ?>
        <?php if (!$model->isNewRecord && file_exists(Yii::getAlias('@webroot/images/linkapp/' . $model->id_linkapp . '.png'))) : ?>
            <div class="mb-3 transparan" style="border-width:0px">
                <div class="row g-0">
                    <div class="col-md-2">
                        <h5 class="card-title">Foto Screenshot Saat Ini</h5>
                        <!-- <p class="card-text">This is a wider card with supporting text below as a natural lead-in to additional content. This content is a little bit longer.</p> -->
                    </div>
                    <div class="col-md-10 alert">
                        <img src="<?php echo Yii::$app->urlManager->createUrl('/images/linkapp/' . $model->id_linkapp . '.png') ?>" class="img-fluid rounded-start" alt="Foto Screenshot Saat Ini">
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