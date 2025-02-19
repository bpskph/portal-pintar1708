<?php

use yii\helpers\Html;
use yii\bootstrap5\ActiveForm;

?>
<?php
$script = <<< JS
$(document).ready(function() {
    $('#lapor-disposisi-id').on('beforeSubmit', function() {
        // Show loading overlay
        $('#loading-overlay').show();
        // Disable all buttons to prevent multiple clicks
        $('button, input[type="submit"]').prop('disabled', true);
    });
});
JS;
$this->registerJs($script);
?>

<!-- Loading Overlay -->
<div id="loading-overlay" style="display: none; position: fixed; width: 100%; height: 100%; top: 0; left: 0; background: rgba(0, 0, 0, 0.5); z-index: 9999; text-align: center;">
    <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); color: white; font-size: 48px;">
        <div class="spinner-border text-light" role="status">
            <span class="sr-only">Loading...</span>
        </div>
        <p>Memproses laporan disposisi<br/>Mohon tunggu...</p>
    </div>
</div>
<div class="container" data-aos="fade-up">
    <div class="card alert <?= ((!Yii::$app->user->isGuest && Yii::$app->user->identity->theme == 0) ? 'bg-light' : 'bg-dark') ?>">
        <?php $form = ActiveForm::begin([
            'id' => 'lapor-disposisi-id',
            'layout' => 'default',
            'fieldConfig' => [
                'template' => "{label}\n{beginWrapper}\n{input}\n{hint}\n{error}\n{endWrapper}",
                'horizontalCssClasses' => [
                    'label' => 'col-sm-3',
                    'offset' => 'offset-sm-4',
                    'wrapper' => 'col-sm-9',
                    'error' => '',
                    'hint' => '',
                ],
            ],
            'enableClientValidation' => true
        ]); ?>
        <div class="row">
            <?= $form->field($model, 'laporan_penyelesaian')->textarea(['rows' => 3, 'class' => 'w-100' . (Yii::$app->user->identity->theme === 1 ? ' bg-dark text-light' : '')])->hint('Rincikan aktivitas penyelesaian tugas dalam disposisi ini.', ['class' => '', 'style' => 'color: #999']) ?>

            <div class="form-group text-end mb-3">
                <?= Html::submitButton('<i class="fas fa-save"></i> Laporkan', ['class' => 'btn btn btn-outline-success btn-block']) ?>
            </div>
        </div>
        <?php ActiveForm::end(); ?>
    </div>
</div>