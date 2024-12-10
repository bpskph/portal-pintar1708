<?php

use yii\helpers\Html;
use yii\bootstrap5\ActiveForm;
?>
<div class="container" data-aos="fade-up">
    <div class="card alert <?= ((!Yii::$app->user->isGuest && Yii::$app->user->identity->theme == 0) ? 'bg-light' : 'bg-dark') ?>">
        <h1>Upload Surat Internal</h1>
        <div class="row">
            <div class="col-sm-12">
                <div class="table-responsive">
                    <table class="table table-sm align-self-end <?= ((!Yii::$app->user->isGuest && Yii::$app->user->identity->theme == 0) ? '' : 'table-dark') ?>">
                        <tbody>
                            <tr>
                                <td class="col-sm-2">ID Surat</td>
                                <td>: <?= $model->id_suratrepo ?> </td>
                            </tr>
                            <tr>
                                <td class="col-sm-2">Nomor Surat</td>
                                <td>: <?= $model->nomor_suratrepo ?> </td>
                            </tr>
                            <tr>
                                <td class="col-sm-2">Penerima Surat</td>
                                <td>: <?= $model->penerima_suratrepo ?> </td>
                            </tr>
                            <tr>
                                <td>Perihal Surat</td>
                                <td>: <?= $model->perihal_suratrepo ?> </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
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
            <?= $form->field($model, 'filepdf')->fileInput()->label('Upload File PDF') ?>
            <div class="form-group text-end mb-3">
                <?= Html::submitButton('<i class="fas fa-save"></i> Upload', ['class' => 'btn btn btn-outline-warning']) ?>
            </div>
            <?php if (!$model->isNewRecord && file_exists(Yii::getAlias('@webroot/surat/internal/pdf/' . $model->id_suratrepo . '.pdf'))) : ?>
                <div class="mb-3 transparan" style="border-width:0px">
                    <div class="row g-0">
                        <div class="col-md-2">
                            <h5 class="card-title">File Saat Ini</h5>
                        </div>
                        <div class="col-md-10">
                            <div id="pdf-container">
                                <center>
                                    <h1><?= $this->title ?></h1>
                                </center>
                                <iframe id="pdf-iframe" src="<?= Yii::getAlias('@web') ?>/surat/internal/pdf/<?php echo $model->id_suratrepo ?>.pdf" width="100%" height="350px"></iframe>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
<script>
    function resizePdfIframe() {
        var windowHeight = $(window).height();
        var pdfIframeOffset = $('#pdf-container').offset().top;
        var pdfIframeHeight = windowHeight - pdfIframeOffset - 20; // subtract 20 for margin
        $('#pdf-iframe').height(pdfIframeHeight);
    }
    $(window).resize(function() {
        resizePdfIframe();
    });
    $(document).ready(function() {
        resizePdfIframe();
    });
</script>