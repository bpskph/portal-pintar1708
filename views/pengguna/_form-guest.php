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
<section class="section register min-vh-100 d-flex flex-column align-items-center justify-content-center py-4">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8 col-md-6 d-flex flex-column align-items-center justify-content-center bg-info-light alert">
                <div class="d-flex justify-content-center py-4">
                    <a href="<?php echo Yii::$app->request->baseUrl; ?>" class="logo d-flex align-items-center w-auto">
                        <img src="<?php echo Yii::$app->request->baseUrl; ?>/images/favicon.png" alt="">
                        <span class="d-none d-lg-block">Daftar Portal Pintar</span>
                    </a>
                </div><!-- End Logo -->
                <!-- <div class=row" style="width:50%;"> -->
                <div class="container">
                    <?php
                    $formnip = ActiveForm::begin([
                        'id' => 'EmailForm',
                        'type' => ActiveForm::TYPE_INLINE,
                        'fieldConfig' => [
                            'options' => ['class' => 'form-group mt-2 mb-2 mr-2'],
                            'template' => "{label}\n{input}\n{hint}\n{error}"
                        ]
                    ]);
                    echo $formnip->field($modelusername, 'email', [
                        'options' => ['class' => 'mr-2 email-field', 'style' => 'width:50%'],
                        'inputOptions' => ['placeholder' => 'email@bps.go.id', 'style' => 'width:100%']
                    ])->textInput();
                    echo '<div class="d-grid">';
                    echo Html::submitButton('Ambil Data Community', ['class' => 'btn btn-warning']);
                    echo '</div>';
                    ActiveForm::end();
                    ?>
                </div>
                <!-- </div> -->
                <br />
                <?php if ($ada == 'YA') : ?>
                    <div class="alert alert-secondary alert-dismissible">
                        <?php if (Yii::$app->user->isGuest) : ?>
                            <p>
                                Data email tersebut | <b><?= $namasat->username . '@bps.go.id' ?> </b> | sudah masuk ke dalam sistem. Hubungi Admin jika Anda tidak dapat login.
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
                        <div class="card card-info">
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
                                <?= $form->field($model, 'nip')->textInput(['readonly' => true, 'value' => $model->isNewRecord ? $profil[$key]['attributes']['attribute-nip'][0] : $model->nip]) ?>
                                <?php if (Yii::$app->user->isGuest) : ?>
                                    <?= $form->field($model, 'nama')->textInput(['maxlength' => true, 'value' => $model->isNewRecord ? $profil[$key]['attributes']['attribute-nama'][0]  : $model->nama, 'readonly' => true]) ?>
                                <?php else : ?>
                                    <?= $form->field($model, 'nama')->textInput(['maxlength' => true, 'value' => $model->isNewRecord ? $profil[$key]['attributes']['attribute-nama'][0]  : $model->nama]) ?>
                                <?php endif; ?>
                            </div>
                            <div class="card-footer text-right">
                                <?= Html::submitButton('<i class="fas fa-save"></i> Simpan', ['class' => 'btn btn btn-outline-warning btn-block checkBtn']) ?>
                            </div>
                        </div>
                        <?php ActiveForm::end(); ?>
                    </div>
                <?php endif; ?>
            </div>
            <div class="credits text-center">
                <p class="small mb-0">Sudah Punya Akun? <?= Html::a('Login', ['site/login'], ['class' => 'bg-warning', 'style' => 'padding: 0 5px; border-radius: 5px']) ?></p>
            </div>
        </div>
    </div>
</div>