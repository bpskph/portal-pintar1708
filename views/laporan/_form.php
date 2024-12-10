<?php
use yii\helpers\Html;
use yii\bootstrap5\ActiveForm;
?>
<div class="container-fluid" data-aos="fade-up">
    <div class="card alert <?= ((!Yii::$app->user->isGuest && Yii::$app->user->identity->theme == 0) ? 'bg-light' : 'bg-dark') ?>">
        <div class="row">
            <?= $header; ?>
        </div>
        <!-- <hr class="bps" /> -->
        <div class="row">
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
            <input type="hidden" name="<?= Yii::$app->request->csrfParam; ?>" value="<?= Yii::$app->request->csrfToken; ?>" />
            <?= $form->field($model, 'dokumentasi')->textInput(['rows' => 6])->hint('<b>Masukkan link kumpulan foto/dokumentasi</b>', ['class' => '', 'style' => 'color: #999']) ?>
            <?= $form->field($model, 'filepdf')->fileInput()->label('Upload File PDF')->hint('<b>File maksimum 2 MB</b>', ['class' => '', 'style' => 'color: #999']) ?>
            <a href="https://docs.google.com/document/d/1P3AoqU5-wd2xI9ATMzEB0soPdYyFClx5/edit" target="_blank" class="button btn btn-primary"><i class="fas fa-file-word"></i> Lihat Format Laporan</a>
            <div class="form-group text-end mb-3">
                <?= Html::submitButton('<i class="fas fa-save"></i> Upload', ['class' => 'btn btn btn-outline-warning']) ?>
            </div>
            <?php if (!$model->isNewRecord && file_exists(Yii::getAlias('@webroot/laporan/' . $model->id_laporan . '.pdf'))) : ?>
                <div class="mb-3 transparan" style="border-width:0px">
                    <div class="row g-0">
                        <div class="col-md-2">
                            <h5 class="card-title">File Saat Ini</h5>
                            <!-- <p class="card-text">This is a wider card with supporting text below as a natural lead-in to additional content. This content is a little bit longer.</p> -->
                        </div>
                        <div class="col-md-10">
                            <div id="pdf-container">
                                <center>
                                    <h1><?= $this->title ?></h1>
                                </center>
                                <iframe id="pdf-iframe" src="<?= Yii::getAlias('@web') ?>/laporans/<?php echo $model->id_laporan ?>.pdf" width="100%" height="350px"></iframe>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
            <?php ActiveForm::end(); ?>
        </div>

    </div>
</div>