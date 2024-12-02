<?php
use yii\helpers\Html;
use kartik\form\ActiveForm;
use kartik\builder\Form;
use yii\helpers\ArrayHelper;
use yii\bootstrap5\Modal;
use yii\helpers\Url;
?>
<style>
    .control-label {
        float: left;
    }
    /* Style the label as a clickable element */
    .form-checkbox-label {
        display: inline-block;
        position: relative;
        padding-left: 25px;
        margin-right: 10px;
        cursor: pointer;
    }
    .form-check {
        padding-left: 0rem !important;
    }
</style>
<script>
    $(document).ready(function() {
        $("#EmailForm").addClass("justify-content-center");
    });
</script>
<section class="section min-vh-100 align-items-center py-4">
    <div class="container">
        <div class="row justify-content-center">
            <center>
                <h1><?= $this->title ?></h1>
            </center>
            <div class="col-lg-8 col-md-6 d-flex flex-column align-items-center justify-content-center bg-info-light alert">
                <?php if ($ada == 'YA') : ?>
                    <div class="alert alert-secondary alert-dismissible">
                        <?php if (Yii::$app->user->isGuest) : ?>
                            <p>
                                Data email tersebut | <b><?= $namasat->username . '@bps.go.id' ?> | </b> sudah masuk ke dalam sistem. Hubungi Admin jika Anda tidak dapat login.
                            </p>
                        <?php else : ?>
                            <p>
                                Pegawai dengan email tersebut |
                                <b><a href="<?= Url::to(['view', 'username' => $namasat->username]) ?>" class="modal-link btn btn-warning text-decoration-none btn-sm"><?= $namasat->nama ?></a> | </b> sudah masuk ke dalam sistem.
                                Jika status pengguna tidak aktif, silahkan aktifkan kembali dari menu manajemen pengguna.
                            </p>
                        <?php endif; ?>
                    </div>
                <?php elseif ($ada == '') : ?>
                    <div class="alert bg-light alert-dismissable">
                        <p>
                            Silahkan masukkan alamat email BPS pengguna yang akan ditambahkan.
                        </p>
                    </div>
                <?php elseif ($bengkulu == 'TIDAK') : ?>
                    <div class="alert alert-secondary alert-dismissable">
                        <p>
                            Mohon maaf, pegawai yang dapat ditambahkan ke sistem Portal Pintar hanya pegawai yang pada Community BPS tercatat di Kabupaten Bengkulu Selatan.
                        </p>
                    </div>
                <?php elseif ($ada == 'COMMUNITY') : ?>
                    <div class="alert alert-secondary alert-dismissable">
                        <p>
                            Data pegawai tidak ditemukan di Community. Mohon periksa kembali alamat email pegawai.
                        </p>
                    </div>
                <?php elseif ($ada == 'TIDAK') : ?>
                    <div class="wrapper" style="width: 100%">
                        <?php $form = ActiveForm::begin([
                            'enableClientValidation' => true,
                            'options' => [
                                'name' => 'Form'
                            ]
                        ]); ?>
                        <?= $form->errorSummary($model) ?>
                        <div class=" alecardrt <?= ((!Yii::$app->user->isGuest && Yii::$app->user->identity->theme == 0) ? 'bg-light' : 'bg-dark') ?>">
                            <div class="card-header">
                                <h3 class="card-title">Identitas Pengguna</h3>
                            </div>
                            <div class="card-body">
                                <?php //echo $this->context->action->id
                                ?>
                                <?= $form->field($model, 'username')->textInput(['maxlength' => true, 'style' => 'text-transform: lowercase', 'readonly' => true, 'value' => $model->isNewRecord ? $profil[$key]['username'] : $model->username]) ?>
                                <?php if ($model->isNewRecord) { ?>
                                    <?= $form->field($model, 'password')->passwordInput(['maxlength' => true]) ?>
                                    <?= $form->field($model, 'password_repeat')->passwordInput(['maxlength' => true, 'value' => $model->password]) ?>
                                <?php } ?>
                                <?= $form->field($model, 'nipbaru')->textInput(['readonly' => true, 'value' => $model->isNewRecord ? $profil[$key]['attributes']['attribute-nip'][0] : $model->nipbaru]) ?>
                                <?= $form->field($model, 'nip')->textInput(['readonly' => true, 'value' => $model->isNewRecord ? $profil[$key]['attributes']['attribute-nip'][0] : $model->nip]) ?>
                                <?php if (Yii::$app->user->isGuest) : ?>
                                    <?= $form->field($model, 'nama')->textInput(['maxlength' => true, 'value' => $model->isNewRecord ? $profil[$key]['attributes']['attribute-nama'][0]  : $model->nama, 'readonly' => true]) ?>
                                <?php else : ?>
                                    <?= $form->field($model, 'nama')->textInput(['maxlength' => true, 'value' => $model->isNewRecord ? $profil[$key]['attributes']['attribute-nama'][0]  : $model->nama]) ?>
                                <?php endif; ?>
                                <?= $form->field($model, 'nomor_hp')->textInput([])->hint('Awali nomor HP dengan angka 62 tanpa tanda kutip.') ?>
                            </div>
                            <div class="card-footer text-right">
                                <?= Html::submitButton('<i class="fas fa-save"></i> Simpan', ['class' => 'btn btn btn-outline-warning btn-block checkBtn']) ?>
                            </div>
                        </div>
                        <?php ActiveForm::end(); ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>