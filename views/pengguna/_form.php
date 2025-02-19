<?php

use yii\helpers\Html;
use kartik\form\ActiveForm;
use yii\helpers\Url;
use yii\web\View;

$this->registerJsFile(Yii::$app->request->baseUrl . '/library/js/fi-projectmember-form.js', ['position' => View::POS_END, 'depends' => [\yii\web\JqueryAsset::class]]);
?>
<div class="container" data-aos="fade-up">
    <div class="row justify-content-center">
        <center>
            <h1><?= $this->title ?></h1>
        </center>
        <hr style="color:transparent" />
        <?php if (Yii::$app->session->hasFlash('success')) : ?>
            <div class="alert alert-info alert-dismissable">
                <h4><i class="icon fa fa-check"></i>Disimpan!</h4>
                <?= Yii::$app->session->getFlash('success') ?>
            </div>
        <?php endif; ?>
        <div class="col-lg-8 col-md-6 d-flex flex-column align-items-center justify-content-center bg-info-light alert">
            <?php if (Yii::$app->controller->action->id == 'create') : ?>
                <div class="container">
                    <?php
                    $formnip = ActiveForm::begin([
                        'id' => 'EmailForm',
                        'type' => ActiveForm::TYPE_INLINE,
                        'fieldConfig' => [
                            'options' => ['class' => 'form-group mt-2 mb-2 mr-2 row'],
                            'template' => "{label}\n{input}\n{hint}\n{error}"
                        ]
                    ]);
                    echo $formnip->field($modelusername, 'email', [
                        'options' => ['class' => 'mr-2 email-field col-lg-6', 'style' => 'width:66.66%'],
                        'inputOptions' => ['placeholder' => 'email@bps.go.id', 'style' => 'width:100%']
                    ])->textInput();
                    echo '<div class="d-grid col-lg-4">';
                    echo Html::submitButton('Ambil Data Community', ['class' => 'btn btn-outline-warning']);
                    echo '</div>';
                    ActiveForm::end();
                    ?>
                <?php endif; ?>
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
                                <b><a href="<?= Url::to(['view', 'username' => $namasat->username]) ?>" class="modal-link btn btn-warning text-decoration-none btn-sm" data-bs-toggle="modal" data-bs-target="#exampleModal"><?= $namasat->nama ?></a> </b> | sudah masuk ke dalam sistem.
                                Jika status pengguna tidak aktif, silahkan aktifkan kembali dari menu manajemen pengguna.
                            </p>
                        <?php endif; ?>
                    </div>
                <?php elseif ($ada == '') : ?>
                    <div class="alert <?= ((!Yii::$app->user->isGuest && Yii::$app->user->identity->theme == 0) ? 'bg-light' : 'bg-dark') ?> alert-dismissable">
                        <p>
                            Silahkan masukkan alamat email BPS pengguna yang akan ditambahkan.
                        </p>
                    </div>
                <?php elseif ($bengkulu == 'TIDAK') : ?>
                    <div class="alert alert-secondary alert-dismissable">
                        <p>
                            Mohon maaf, pegawai yang dapat ditambahkan ke sistem Portal Pintar hanya pegawai yang pada Community BPS tercatat di <?=Yii::$app->params['namaSatker']?>.
                        </p>
                    </div>
                <?php elseif ($ada == 'COMMUNITY') : ?>
                    <div class="alert alert-secondary alert-dismissable">
                        <p>
                            Data pegawai tidak ditemukan di Community. Mohon periksa kembali alamat email pegawai.
                        </p>
                    </div>
                <?php elseif ($ada == 'TIDAK') : ?>
                    <div class="wrapper">
                        <?php $form = ActiveForm::begin([
                            'enableClientValidation' => true,
                            'options' => [
                                'name' => 'Form'
                            ]
                        ]); ?>
                        <?= $form->errorSummary($model) ?>
                        <div class="card alert <?= ((!Yii::$app->user->isGuest && Yii::$app->user->identity->theme == 0) ? 'bg-light' : 'bg-dark') ?>">
                            <div class="card-header">
                                <h3 class="card-title">Identitas Pengguna</h3>
                            </div>
                            <div class="card-body">
                                <?= $form->field($model, 'username')->textInput(['maxlength' => true, 'style' => 'text-transform: lowercase', 'readonly' => true, 'value' => $model->isNewRecord ? $profil[$key]['username'] : $model->username]) ?>
                                <?php if ($model->isNewRecord) { ?>
                                    <?= $form->field($model, 'password')->passwordInput(['maxlength' => true]) ?>
                                    <?= $form->field($model, 'password_repeat')->passwordInput(['maxlength' => true, 'value' => $model->password]) ?>
                                <?php } ?>
                                <?= $form->field($model, 'nipbaru')->textInput(['readonly' => true, 'value' => $model->isNewRecord ? $profil[$key]['attributes']['attribute-nip'][0] : $model->nipbaru]) ?>
                                <?= $form->field($model, 'nip')->textInput(['readonly' => true, 'value' => $model->isNewRecord ? $profil[$key]['attributes']['attribute-nip-lama'][0] : $model->nip]) ?>
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
                    </div>
                </div>
                <?php ActiveForm::end(); ?>
            <?php endif; ?>
        </div>
    </div>
</div>