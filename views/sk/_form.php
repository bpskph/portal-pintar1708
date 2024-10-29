<?php

use app\models\Sk;
use yii\helpers\Html;
use yii\bootstrap5\ActiveForm;
use kartik\select2\Select2;
use kartik\date\DatePicker;

if ($model->isNewRecord) {
    $model->tanggal_sk = date("Y-m-d");
}

// Registering flatpickr CSS and JS files
// $this->registerCssFile('https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css', ['position' => View::POS_END]);
// $this->registerJsFile('https://cdnjs.cloudflare.com/ajax/libs/flatpickr/4.6.9/flatpickr.min.js', ['position' => View::POS_END]);
// $this->registerJsFile('https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/ja.js', ['position' => View::POS_END]);

// Registering your custom JS and CSS files
// $this->registerJsFile(Yii::$app->request->baseUrl . '/library/js/fi-sk-form.js', ['position' => View::POS_END, 'depends' => [\yii\web\JqueryAsset::class]]);
?>
<div class="container" data-aos="fade-up">
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
        <?= $form->errorSummary($model) ?>

        <?= $form->field($model, 'nomor_sk')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'tanggal_sk')->widget(DatePicker::classname(), [
            'options' => ['placeholder' => 'Pilih Tanggal ...'],
            'pluginOptions' => [
                'autoclose' => true,
                'format' => 'yyyy-mm-dd'
            ]
        ]); ?>
        
        <?= $form->field($model, 'tentang_sk')->textarea(['rows' => 6]) ?>

        <?php if (!$model->isNewRecord) : ?>
            <?php $cek = Sk::find()
                ->select('nama_dalam_sk')
                ->where(['id_sk' => $model->id_sk])
                ->one();
            $data = str_replace('@bps.go.id', '', $cek->nama_dalam_sk);
            $array = explode(", ", $data);
            ?>
            <?php $model->nama_dalam_sk = $array; ?>
        <?php endif; ?>
        <?=
        $form->field($model, 'nama_dalam_sk')->widget(Select2::class, [
            'data' => \yii\helpers\ArrayHelper::map(
                \app\models\Pengguna::find()->all(),
                'username',
                'nama'
            ),
            'theme' => Select2::THEME_KRAJEE,
            'options' => [
                'multiple' => true,
                'placeholder' => 'Pilih Daftar Pegawai Penerima SK ...',
            ],
        ]); ?>

        <?= $form->field($model, 'filepdf')->fileInput()->label('Upload File PDF SK') ?>

        <div class="form-group text-end mb-3">
            <?= Html::submitButton('<i class="fas fa-save"></i> Simpan', ['class' => 'btn btn btn-outline-warning']) ?>
        </div>
        <?php if (!$model->isNewRecord && file_exists(Yii::getAlias('@webroot/sk/' . $model->id_sk . '.pdf'))) : ?>
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
                            <iframe id="pdf-iframe" src="<?= Yii::getAlias('@web') ?>/sk/<?php echo $model->id_sk ?>.pdf" width="100%" height="350px"></iframe>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
        <?php ActiveForm::end(); ?>
    </div>
</div>