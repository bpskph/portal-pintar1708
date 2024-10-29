<?php
use yii\helpers\Html;
use yii\bootstrap5\ActiveForm;
use dosamigos\ckeditor\CKEditor;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use yii\redactor\widgets\Redactor;
/** @var yii\web\View $this */
/** @var app\models\Suratrepo $model */
/** @var yii\widgets\ActiveForm $form */
?>
<div class="container-fluid" data-aos="fade-up">
    <div class="card alert <?= ((!Yii::$app->user->isGuest && Yii::$app->user->identity->theme == 0) ? 'bg-light' : 'bg-dark') ?>">
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
            <?php
            $model->isi_suratrepoeks = $model->isNewRecord ? $autofillString : $model->isi_suratrepoeks;
            echo $form->field($model, 'komentar')->widget(Redactor::className(), [
                'clientOptions' => [
                    'lang' => 'en',
                    'plugins' => ['clips', 'counter', 'fontcolor', 'table', 'fullscreen', 'textdirection', 'textexpander'],
                    'options' => [
                        'autocomplete' => 'on'
                    ],
                    'buttons' => [
                        'html', 'formatting', 'bold', 'italic', 'deleted',
                        'unorderedlist', 'orderedlist', 'outdent', 'indent', 'table', 'link',
                        'alignment', 'horizontalrule', 'clips', 'fontcolor', 'backcolor', 'fullscreen', 'textdirection'
                    ]
                ]
            ]);
            ?>
            <div class="form-group text-end mb-3">
                <?= Html::submitButton('<i class="fas fa-save"></i> Kirim Koreksi', ['class' => 'btn btn btn-outline-warning']) ?>
            </div>
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