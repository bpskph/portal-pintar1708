<?php

use app\models\Project;
use app\models\Suratsubkode;
use yii\helpers\Html;
use yii\bootstrap5\ActiveForm;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use yii\bootstrap5\BootstrapAsset;
use kartik\select2\Select2Asset;
use kartik\date\DatePicker;
use yii\web\View;

Select2Asset::register($this);
BootstrapAsset::register($this);

if ($model->isNewRecord) {
    $model->tanggal_suratrepoeks = date("Y-m-d");
}

// Registering your custom JS and CSS files
$this->registerJsFile(Yii::$app->request->baseUrl . '/library/js/fi-suratrepoeks-form.js', ['position' => View::POS_END, 'depends' => [\yii\web\JqueryAsset::class]]);

?>
<style>
   /* Radio Button Styling */
   .custom-radio-wrapper {
        display: flex;
        align-items: center;
        margin-bottom: 10px;
    }

    .custom-radio-option {
        appearance: none;
        -webkit-appearance: none;
        -moz-appearance: none;
        display: inline-block;
        width: 20px;
        height: 20px;
        border: 2px solid #ccc;
        border-radius: 50%;
        margin-right: 10px;
        position: relative;
        cursor: pointer;
        vertical-align: middle;
    }

    .custom-radio-option:checked {
        border-color: #007bff;
        background-color: #007bff;
    }

    .custom-radio-option:checked::after {
        content: "";
        width: 10px;
        height: 10px;
        background: white;
        border-radius: 50%;
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
    }

    /* Label Styling */
    .custom-radio-label {
        font-size: 16px;
        font-weight: 500;
        color: #d1d1d1; /* Light color */
        transition: font-weight 0.2s ease-in-out, color 0.2s ease-in-out;
    }

    /* Bold label when selected */
    .custom-radio-option:checked + .custom-radio-label {
        font-weight: bold;
        color: #007bff;
    }

    /* #suratrepoeks-sent_by {
        border-style: solid;
        border-color: #ffc107;
        border-width: thin;
        background-color: #ffe8a4;
        border-radius: 5px;
    }

    #suratrepoeks-sent_by>.form-check-label {
        font-size: 16px;
        font-weight: 500;
        color: rgb(255, 255, 255);
    } */
</style>
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
                <?= $form->field($model, 'id_suratrepoeks')->hiddenInput()->label(false) ?>
                <?= $form->field($model, 'invisibility')->checkbox()->label('&nbsp;Tandai ini jika Anda ingin merahasiakan konten surat Anda&nbsp;', ['style' => 'background-color: #ffc107; border-radius: 5px']); ?>
                <?= $form->field($model, 'penerima_suratrepoeks')->textInput(['maxlength' => true])
                    ->hint('Jika daftar "Kepada" lebih dari satu, pisahkan dengan koma. Contoh: <b>Direktur Rakyat Bengkulu, Direktur Bengkulu Ekspress</b>', ['class' => '', 'style' => 'color: #999']) ?>
                <?= $form->field($model, 'tanggal_suratrepoeks')->widget(DatePicker::classname(), [
                    'options' => ['placeholder' => 'Pilih Tanggal ...'],
                    'pluginOptions' => [
                        'autoclose' => true,
                        'format' => 'yyyy-mm-dd',
                        'endDate' => date('Y-m-d') // Set end date to today
                    ]
                ])->hint('Untuk menjaga ketertiban nomor, surat yang dapat diinput adalah sebatas tanggal hari ini.', ['class' => '', 'style' => 'color: #999']) ?>
                <?= $form->field($model, 'perihal_suratrepoeks')->textarea(['rows' => 3])
                    ->hint('Jika ingin memisahkan perihal menjadi beberapa baris, pisahkan dengan "&ltbr/&gt". Contoh: <b>Usulan Penetapan Penggunaan (PSP) &ltbr/&gt BMN Wilayah ' . Yii::$app->params['namaSatker'] . '</b>', ['class' => '', 'style' => 'color: #999']) ?>
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
                            var tanggal = $("#' . Html::getInputId($model, 'tanggal_suratrepoeks') . '").val();
                            var sifat = $("#suratrepoeks-sifat").val();
                            console.log(sifat);
                            var actionId = "' . (Yii::$app->controller->action->id == 'update' ? $model->id_suratrepoeks : '') . '"
                            $.post("' . str_replace("http://", "https://", Yii::$app->request->hostInfo) . '/' .  Yii::$app->params['versiAplikasi'] . '/' . Yii::$app->controller->id . '/getnomorsurat?id=" + $(this).val() + "&tanggal=" + tanggal + "&sifat=" + sifat + "&action=" + actionId, function(data) {
                                $("input#suratrepoeks-nomor_suratrepoeks").val(data);
                            });
                        ',
                    ],
                    'pluginOptions' => [
                        'allowClear' => true
                    ],
                ]);
                ?>
                <?php if (Yii::$app->user->identity->issekretaris || Yii::$app->user->identity->issdmleader) : ?>
                    <?= $form->field($model, 'sifat')->widget(Select2::classname(), [
                        'data' => [0 => "Biasa", 1 => "Penting", 2 => "Rahasia"],
                        'options' => [
                            'placeholder' => 'Sifat Surat',
                            'onchange' => '
                                var tanggal = $("#' . Html::getInputId($model, 'tanggal_suratrepoeks') . '").val();
                                var id = $("#suratrepoeks-fk_suratsubkode").val();
                                console.log(id);
                                var actionId = "' . (Yii::$app->controller->action->id == 'update' ? $model->id_suratrepoeks : '') . '"
                                $.post("' . str_replace("http://", "https://", Yii::$app->request->hostInfo) . '/' . Yii::$app->params['versiAplikasi'] . '/' . Yii::$app->controller->id . '/getnomorsurat?id=" + id + "&tanggal=" + tanggal + "&sifat=" + $(this).val() + "&action=" + actionId, function(data) {
                                    $("input#suratrepoeks-nomor_suratrepoeks").val(data);
                                });
                            ',
                        ],
                        'pluginOptions' => [
                            'allowClear' => true
                        ],
                    ]);
                    ?>
                <?php else : ?>
                    <?= $form->field($model, 'sifat')->widget(Select2::classname(), [
                        'data' => [0 => "Biasa"],
                        'options' => [
                            'placeholder' => 'Sifat Surat',
                            'onchange' => '
                                var tanggal = $("#' . Html::getInputId($model, 'tanggal_suratrepoeks') . '").val();
                                var id = $("#suratrepoeks-fk_suratsubkode").val();
                                console.log(id);
                                var actionId = "' . (Yii::$app->controller->action->id == 'update' ? $model->id_suratrepoeks : '') . '"
                                $.post("' . str_replace("http://", "https://", Yii::$app->request->hostInfo) . '/' . Yii::$app->params['versiAplikasi'] . '/' . Yii::$app->controller->id . '/getnomorsurat?id=" + id + "&tanggal=" + tanggal + "&sifat=" + $(this).val() + "&action=" + actionId, function(data) {
                                    $("input#suratrepoeks-nomor_suratrepoeks").val(data);
                                });
                            ',
                        ],
                        'pluginOptions' => [
                            'allowClear' => true
                        ],
                    ]);
                    ?>
                <?php endif; ?>
                <?php if (Yii::$app->user->identity->issekretaris) : ?>
                    <?= $form->field($model, 'jenis')->dropDownList([0 => 'Surat Biasa', 1 => 'Surat Perintah Lembur', 2 => 'Surat Keterangan', 3 => 'Berita Acara'], [
                        'prompt' => 'Pilih Jenis ...',
                    ]); ?>
                <?php else : ?>
                    <?= $form->field($model, 'jenis')->dropDownList([0 => 'Surat Biasa', 2 => 'Surat Keterangan', 3 => 'Berita Acara'], [
                        'prompt' => 'Pilih Jenis ...',
                    ]); ?>
                <?php endif; ?>
                <div class="row mb-2">
                    <div class="col-lg-2">

                    </div>
                    <div class="col-lg-8">
                        <div id="biasa_file" style="display:<?php echo (($model->isNewRecord) || (!$model->isNewRecord && $model->jenis == 0) ? 'block' : 'none') ?>">
                            <a href="<?php echo Yii::$app->request->baseUrl; ?>/images/template-surat/biasa.docx" class="btn btn-sm btn-outline-warning"><i class="fas fa-file-word"></i> Unduh Template Surat Dinas Biasa</a>
                        </div>
                        <div id="lembur_file" style="display:<?php echo (!$model->isNewRecord && $model->jenis == 1 ? 'block' : 'none') ?>">
                            <a href="<?php echo Yii::$app->request->baseUrl; ?>/images/template-surat/lembur.docx" class="btn btn-sm btn-outline-warning"><i class="fas fa-file-word"></i> Unduh Template Surat Lembur</a>
                        </div>
                        <div id="keterangan_file" style="display:<?php echo (!$model->isNewRecord && $model->jenis == 2 ? 'block' : 'none') ?>">
                            <a href="<?php echo Yii::$app->request->baseUrl; ?>/images/template-surat/keterangan.docx" class="btn btn-sm btn-outline-warning"><i class="fas fa-file-word"></i> Unduh Template Surat Keterangan</a>
                        </div>
                        <div id="bast_file" style="display:<?php echo (!$model->isNewRecord && $model->jenis == 3 ? 'block' : 'none') ?>">
                            <a href="<?php echo Yii::$app->request->baseUrl; ?>/images/template-surat/bast.docx" class="btn btn-sm btn-outline-warning"><i class="fas fa-file-word"></i> Unduh Template Surat Berita Acara</a>
                        </div>
                    </div>
                </div>

                <?= $form->field($model, 'nomor_suratrepoeks')->textInput(['readonly' => true])  ?>
                <?= $form->field($model, 'ttd_by')->widget(Select2::classname(), [
                    'data' => ArrayHelper::map(
                        \app\models\Suratrepoeksttd::find()
                            ->select('*')
                            ->where(['deleted' => 0])
                            ->asArray()->all(),
                        'id_suratrepoeksttd',
                        function ($model) {
                            return $model['nama'] . ' [' . $model['jabatan'] .  ']';
                        }
                    ),
                    'options' => ['placeholder' => 'Pilih TTD Surat'],
                    'pluginOptions' => [
                        'allowClear' => true
                    ],
                ]);
                ?>
                <?= $form->field($model, 'approver')->widget(Select2::classname(), [
                    'data' => ArrayHelper::map(
                        \app\models\Pengguna::find()
                            ->joinWith(['projectmembere', 'teamleadere', 'projecte'])
                            ->select('*')
                            ->where(['project.tahun' => date("Y")])
                            ->andWhere(['member_status' => 2])
                            ->orWhere(['leader_status' => 1])
                            ->orWhere(['username' => 'sahranudin'])
                            ->orWhere(['username' => 'winrizal'])
                            ->orderBy(['nipbaru' => SORT_ASC])
                            ->asArray()->all(),
                        'username',
                        function ($model) {
                            return $model['nama'] . ' [' . $model['username'] .  '@bps.go.id]';
                        }
                    ),
                    'options' => ['placeholder' => 'Pilih Penyetuju Surat'],
                    'pluginOptions' => [
                        'allowClear' => true
                    ],
                ])->hint('Penyetuju Surat adalah Ketua Proyek Menurut SK <b>Tahun Berjalan (' . date("Y") . ')</b>', ['class' => '', 'style' => 'color: #999']) ?>

                <?php if ($model->isNewRecord)
                    $model->sent_by = 0
                ?>
                <?= $form->field($model, 'sent_by')->radioList([
                        0 => "PDF Surat akan dikirim oleh Sekretaris " . Yii::$app->params['namaSatker'],
                        1 => "PDF Surat akan dikirim oleh Anda (Tim Teknis)",
                        2 => "PDF Surat tidak dikirim melalui surel/email"
                    ], [
                        'item' => function ($index, $label, $name, $checked, $value) {
                            return '<div class="custom-radio-wrapper">
                                <input type="radio" id="sent_by_' . $value . '" name="' . $name . '" value="' . $value . '" 
                                    class="custom-radio-option" ' . ($checked ? 'checked' : '') . '>
                                <label for="sent_by_' . $value . '" class="custom-radio-label">' . $label . '</label>
                            </div>';
                        }
                    ]); ?>


                <?= $form->field($model, 'tembusan')->textarea(['rows' => 3])
                    ->hint('Jika daftar tembusan lebih dari satu, pisahkan dengan koma. Contoh: <b>Kepala ' . Yii::$app->params['namaSatker'] . ', Kepala Bagian Umum ' . Yii::$app->params['namaSatker'] . '</b>', ['class' => '', 'style' => 'color: #999']) ?>

                <?= $form->field($model, 'shared_to')->widget(Select2::classname(), [
                    'name' => 'shared_to',
                    'data' => ArrayHelper::map(
                        Project::find()
                            ->select('*, team.panggilan_team as namateam')
                            ->joinWith(['teame', 'projectmembere', 'teamleadere'])
                            ->where(['tahun' => date("Y")])
                            ->andWhere([
                                'or',
                                ['projectmember.member_status' => 3, 'projectmember.pegawai' => Yii::$app->user->identity->username],
                                ['projectmember.member_status' => 2, 'projectmember.pegawai' => Yii::$app->user->identity->username],
                                ['projectmember.member_status' => 1, 'projectmember.pegawai' => Yii::$app->user->identity->username],
                                ['teamleader.leader_status' => 1, 'teamleader.nama_teamleader' => Yii::$app->user->identity->username],
                            ])
                            ->asArray()
                            ->all(),
                        'id_project',
                        function ($model) {
                            return $model['id_project'] . '. ' . $model['nama_project'] . ' [' . $model['panggilan_project'] .  ' | ' . $model['namateam'] . '] ';
                        }
                    ),
                    'options' => [
                        'placeholder' => 'Pilih Tim yang Dapat Melihat Konten Surat Anda (Kosongkan Jika Tidak Perlu)',
                        'onchange' => '
                            if ($(this).val() !== "") {
                                $("#' . Html::getInputId($model, 'is_shared') . '").prop("checked", true);
                            }
                        ',
                    ],
                    'pluginOptions' => [
                        'allowClear' => true,
                    ],
                ])->hint('Jika Anda ingin membagikan konten surat ini kepada seluruh Anggota Project Anda (termasuk Ketua Tim pada project tersebut), pilih tim yang Anda inginkan. <b>Hati-hati dalam menggunakan fitur ini untuk surat rahasia dan/atau surat yang dirahasiakan oleh pemiliknya.</b>', ['class' => 'hint-dark mt-2 ps-2', 'style' => 'background-color: #ffc107; border-radius: 5px; font-style: italic;'])
                ?>
                <div class="form-group text-end mb-3">
                    <i>Mohon upload surat yang telah di-ttd dan di-scan pada Beranda Surat Eksternal.</i>
                    <?= Html::submitButton('<i class="fas fa-save"></i> Simpan', ['class' => 'btn btn btn-outline-warning']) ?>
                </div>
                <?php ActiveForm::end(); ?>
            </div>
            <div class="col-lg-4 order-1 order-lg-2 hero-img" data-aos="zoom-out" data-aos-delay="300">
                <div id="biasa" style="display:<?php echo (($model->isNewRecord) || (!$model->isNewRecord && $model->jenis == 0) ? 'block' : 'none') ?>">
                    <h5 class="text-center">Contoh Surat Biasa</h5>
                    <img src="<?php echo Yii::$app->request->baseUrl; ?>/images/template-surat/biasa.png" class="img-fluid animated" alt="">
                </div>
                <div id="lembur" style="display:<?php echo (!$model->isNewRecord && $model->jenis == 1 ? 'block' : 'none') ?>">
                    <h5 class="text-center">Contoh Surat Perintah Lembur</h5>
                    <img src="<?php echo Yii::$app->request->baseUrl; ?>/images/template-surat/lembur.png" class="img-fluid animated" alt="">
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