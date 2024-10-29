<?php

use app\models\Suratrepo;
use app\models\Suratsubkode;
use yii\helpers\Html;
use yii\bootstrap5\ActiveForm;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use kartik\date\DatePicker;
use yii\bootstrap5\BootstrapAsset;
use kartik\select2\Select2Asset;
use yii\web\View;

Select2Asset::register($this);
BootstrapAsset::register($this);

// $actionId = Yii::$app->controller->action->id;
// $script = <<< JS
//     var actionId = '$actionId';
// JS;
// $this->registerJs($script, \yii\web\View::POS_HEAD);

if ($model->isNewRecord) {
    $model->tanggal_suratrepo = date("Y-m-d");
}

// // Registering flatpickr CSS and JS files
// $this->registerCssFile('https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css', ['position' => View::POS_END]);
// $this->registerJsFile('https://cdnjs.cloudflare.com/ajax/libs/flatpickr/4.6.9/flatpickr.min.js', ['position' => View::POS_END]);
// $this->registerJsFile('https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/ja.js', ['position' => View::POS_END]);

// // Registering your custom JS and CSS files
$this->registerJsFile(Yii::$app->request->baseUrl . '/library/js/fi-suratrepo-form.js', ['position' => View::POS_END, 'depends' => [\yii\web\JqueryAsset::class]]);
?>

<div class="container-fluid" data-aos="fade-up">
    <div class="card alert <?= ((!Yii::$app->user->isGuest && Yii::$app->user->identity->theme == 0) ? 'bg-light' : 'bg-dark') ?>">
        <div class="row">
            <div class="col-lg-8">
                <?php if ($dataagenda !== 'noagenda') : ?>
                    <div class="row">
                        <?= $header; ?>
                    </div>
                    <hr class="bps" />
                <?php endif; ?>
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
                <?= $form->field($model, 'id_suratrepo')->hiddenInput()->label(false) ?>
                <?= $form->field($model, 'penerima_suratrepo')->textarea(['rows' => 3])
                    ->hint('Jika daftar penerima surat ("Kepada"-nya) lebih dari satu, pisahkan dengan koma. Contoh: <b>Kepala Bagian Umum BPS Provinsi Bengkulu, Ketua Project Sekretariat RB</b>', ['class' => '', 'style' => 'color: #999']) ?>
                <?php // $form->field($model, 'tanggal_suratrepo')->textInput(['readonly' => false, 'placeholder' => 'Pilih Tanggal', 'value' => $model->tanggal_suratrepo])
                //->hint('Untuk menjaga ketertiban nomor, surat yang dapat diinput adalah sebatas tanggal hari ini.', ['class' => '', 'style' => 'color: #999']) 
                ?>
                <?= $form->field($model, 'tanggal_suratrepo')->widget(DatePicker::classname(), [
                    'options' => ['placeholder' => 'Pilih Tanggal ...'],
                    'pluginOptions' => [
                        'autoclose' => true,
                        'format' => 'yyyy-mm-dd',
                        'endDate' => date('Y-m-d') // Set end date to today
                    ]
                ])->hint('Untuk menjaga ketertiban nomor, surat yang dapat diinput adalah sebatas tanggal hari ini.', ['class' => '', 'style' => 'color: #999']) ?>

                <?= $form->field($model, 'perihal_suratrepo')->textarea(['rows' => 3])
                    ->hint('Jika ingin memisahkan perihal menjadi beberapa baris, pisahkan dengan "&ltbr/&gt". Contoh: <b>Usulan Penetapan Penggunaan (PSP) &ltbr/&gt BMN Wilayah BPS Provinsi Bengkulu</b>', ['class' => '', 'style' => 'color: #999']) ?>
                <?= $form->field($model, 'lampiran')->textInput(['maxlength' => true])
                    ->hint('Contoh Pengisian: <b>1 (Satu) Berkas</b><br/>Kosongkan bila tidak ada lampiran. ', ['class' => '', 'style' => 'color: #999']) ?>
                <?= $form->field($model, 'fk_suratsubkode')->widget(Select2::classname(), [
                    'name' => 'fk_suratsubkode',
                    'data' => ArrayHelper::map(
                        Suratsubkode::find()->select('*')->asArray()->all(),
                        'id_suratsubkode',
                        function ($model) {
                            return $model['fk_suratkode'] . '-' . $model['kode_suratsubkode'] . '-' . $model['rincian_suratsubkode'];
                        }
                    ),
                    'options' => [
                        'placeholder' => 'Pilih Cakupan Surat',
                        'onchange' => '
                    var tanggal = $("#' . Html::getInputId($model, 'tanggal_suratrepo') . '").val();
                    var actionId = "' . (Yii::$app->controller->action->id == 'update' ? $model->id_suratrepo : '') . '"
                    $.post("' . Yii::$app->request->hostInfo . '/' . Yii::$app->params['versiAplikasi'] . '/' . Yii::$app->controller->id . '/getnomorsurat?id=" + $(this).val() + "&tanggal=" + tanggal + "&action=" + actionId, function(data) {
                        $("input#suratrepo-nomor_suratrepo").val(data);
                    });
                ',
                    ],
                    'pluginOptions' => [
                        'allowClear' => true
                    ],
                ]);
                ?>
                <?= $form->field($model, 'jenis')->dropDownList([0 => 'Surat Biasa', 1 => 'Nota Dinas', 2 => 'Surat Keterangan', 3 => 'Berita Acara', 4 => 'Lainnya'], [
                    'prompt' => 'Pilih Jenis ...',
                    // 'onchange' => '
                    //     var id = $("#suratrepo-fk_suratsubkode").val();
                    //     var surat = $("#suratrepo-id_suratrepo").val();
                    //     console.log(surat);
                    //     var actionId = "' . Yii::$app->controller->action->id . '";
                    //     $.post("' . Yii::$app->request->hostInfo . '/' . Yii::$app->params['versiAplikasi'] . '/' . Yii::$app->controller->id . '/gettemplate?id=" + $(this).val() + "&action=" + actionId + "&surat=" + surat, function(data) {
                    //         $("#isi_suratrepo").redactor("code.set", data); // Update the Redactor value
                    //     });
                    // ',
                ]); ?>

                <div class="row mb-2">
                    <div class="col-lg-2">

                    </div>
                    <div class="col-lg-8">
                        <div id="biasa_file" style="display:<?php echo (($model->isNewRecord) || (!$model->isNewRecord && $model->jenis == 0) ? 'block' : 'none') ?>">
                            <a href="<?php echo Yii::$app->request->baseUrl; ?>/images/template-surat/biasa_internal.docx" class="btn btn-sm btn-outline-warning"><i class="fas fa-file-word"></i> Unduh Template Surat Dinas Biasa</a>
                        </div>
                        <div id="notadinas_file" style="display:<?php echo (!$model->isNewRecord && $model->jenis == 1 ? 'block' : 'none') ?>">
                            <a href="<?php echo Yii::$app->request->baseUrl; ?>/images/template-surat/notadinas.docx" class="btn btn-sm btn-outline-warning"><i class="fas fa-file-word"></i> Unduh Template Surat Perintah Lembur</a>
                        </div>
                        <div id="keterangan_file" style="display:<?php echo (!$model->isNewRecord && $model->jenis == 2 ? 'block' : 'none') ?>">
                            <a href="<?php echo Yii::$app->request->baseUrl; ?>/images/template-surat/keterangan.docx" class="btn btn-sm btn-outline-warning"><i class="fas fa-file-word"></i> Unduh Template Surat Keterangan</a>
                        </div>
                        <div id="bast_file" style="display:<?php echo (!$model->isNewRecord && $model->jenis == 3 ? 'block' : 'none') ?>">
                            <a href="<?php echo Yii::$app->request->baseUrl; ?>/images/template-surat/bast.docx" class="btn btn-sm btn-outline-warning"><i class="fas fa-file-word"></i> Unduh Template Surat Berita Acara</a>
                        </div>
                    </div>
                </div>

                <?= $form->field($model, 'nomor_suratrepo')->textInput(['readonly' => true])  ?>
                <!-- AUTOFILL SURAT -->
                <?php
                $autofillString =
                    "
                    <p style='text-indent:.5in;'>Dalam rangka peningkatan pemahaman Reformasi Birokrasi dalam tim sekretariat RB BPS Provinsi Bengkulu, bersama ini  mengundang Bapak/Ibu untuk hadir pada:</p>
                    <p>Hari/Tanggal&nbsp; &nbsp; &nbsp; &nbsp;: Jumat/2 Februari 2023</p>
                    <p>Waktu&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;: 14.00 WIB s.d. selesai</p>
                    <p>Agenda&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;: Review Pilar dan Rencana Kegiatan Bulanan</p>
                    <p>Tempat &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; : Ruang Agro BPS Provinsi Bengkulu</p>
                    <p style='text-indent:.5in;'>Demikian disampaikan, atas perhatian diucapkan terima kasih.</p>
                    <br/>                    
                    ";
                ?>

                <?php
                if (!$model->isNewRecord) {
                    $cari = Suratrepo::findOne(['id_suratrepo' => $model->id_suratrepo]);
                    $model->pihak_pertama = $cari->pihak_pertama;
                    $model->pihak_kedua = $cari->pihak_kedua;
                    $model->ttd_by = $cari->ttd_by;
                    $model->ttd_by_jabatan = $cari->ttd_by_jabatan;
                }
                ?>
                <div id="pihak" style="display:<?php echo (!$model->isNewRecord && $model->jenis == 3 ? 'block' : 'none') ?>">
                    <?= $form->field($model, 'pihak_pertama')->widget(Select2::classname(), [
                        'data' => ArrayHelper::map(
                            \app\models\Pengguna::find()->select('*')->asArray()->all(),
                            'username',
                            function ($model) {
                                return $model['nama'] . ' [' . $model['nipbaru'] .  ']';
                            }
                        ),
                        'options' => [
                            'placeholder' => 'Pilih Pihak Pertama',
                            'value' => $model->isNewRecord ? '' : $model->pihak_pertama,
                        ],
                        'pluginOptions' => [
                            'allowClear' => true
                        ],
                    ]);
                    ?>
                    <?= $form->field($model, 'pihak_kedua')->widget(Select2::classname(), [
                        'data' => ArrayHelper::map(
                            \app\models\Pengguna::find()->select('*')->asArray()->all(),
                            'username',
                            function ($model) {
                                return $model['nama'] . ' [' . $model['nipbaru'] .  ']';
                            }
                        ),
                        'options' => [
                            'placeholder' => 'Pilih Pihak Kedua',
                            'value' => $model->isNewRecord ? '' : $model->pihak_kedua,
                        ],
                        'pluginOptions' => [
                            'allowClear' => true
                        ],
                    ]);
                    ?>
                </div>
                <div id="ttdby" style="display:<?php echo ($model->isNewRecord || (!$model->isNewRecord && $model->jenis != 3) ? 'block' : 'none') ?>">
                    <?= $form->field($model, 'ttd_by')->textInput([
                        'value' => $model->isNewRecord ? '' : $model->ttd_by,
                    ]); ?>
                    <?= $form->field($model, 'ttd_by_jabatan')->textInput(); ?>
                </div>
                <?= $form->field($model, 'tembusan')->textarea(['rows' => 3])
                    ->hint('Jika daftar tembusan lebih dari satu, pisahkan dengan koma. Contoh: <b>Kepala BPS Provinsi Bengkulu, Kepala Bagian Umum BPS Provinsi Bengkulu</b>', ['class' => '', 'style' => 'color: #999']) ?>
                <div class="form-group text-end mb-3">
                    <i>Mohon upload surat yang telah di-ttd dan di-scan pada Beranda Surat Internal.</i>
                    <?= Html::submitButton('<i class="fas fa-save"></i> Simpan', ['class' => 'btn btn btn-outline-warning']) ?>
                </div>
                <?php ActiveForm::end(); ?>
            </div>
            <div class="col-lg-4 order-1 order-lg-2 hero-img" data-aos="zoom-out" data-aos-delay="300">
                <div id="biasa" style="display:<?php echo (($model->isNewRecord) || (!$model->isNewRecord && $model->jenis == 0) ? 'block' : 'none') ?>">
                    <h5 class="text-center">Contoh Surat Biasa</h5>
                    <img src="<?php echo Yii::$app->request->baseUrl; ?>/images/template-surat/biasa_internal.png" class="img-fluid animated" alt="">
                </div>
                <div id="notadinas" style="display:<?php echo (!$model->isNewRecord && $model->jenis == 1 ? 'block' : 'none') ?>">
                    <h5 class="text-center">Contoh Surat Nota Dinas</h5>
                    <img src="<?php echo Yii::$app->request->baseUrl; ?>/images/template-surat/notadinas.png" class="img-fluid animated" alt="">
                </div>
                <div id="keterangan" style="display:<?php echo (!$model->isNewRecord && $model->jenis == 2 ? 'block' : 'none') ?>">
                    <h5 class="text-center">Contoh Surat Keterangan</h5>
                    <img src="<?php echo Yii::$app->request->baseUrl; ?>/images/template-surat/keterangan.png" class="img-fluid animated" alt="">
                </div>
                <div id="bast" style="display:<?php echo (!$model->isNewRecord && $model->jenis == 3 ? 'block' : 'none') ?>">
                    <h5 class="text-center">Contoh Surat Berita Acara</h5>
                    <img src="<?php echo Yii::$app->request->baseUrl; ?>/images/template-surat/bast.png" class="img-fluid animated" alt="">
                </div>
            </div>
        </div>
    </div>
</div>