<?php

/** @var yii\web\View $this */
/** @var yii\bootstrap5\ActiveForm $form */
/** @var app\models\ContactForm $model */

use app\models\Pengguna;
use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Html;
use yii\redactor\widgets\Redactor;
use yii\web\View;

$this->title = 'Undangan Digital Portal Pintar';
$this->params['breadcrumbs'][] = $this->title;
?>
<style>
    .callout {
        background-color: #fff;
        border: 1px solid #e4e7ea;
        border-left: 4px solid #c8ced3;
        border-radius: .25rem;
        margin: 1rem 0;
        padding: .75rem 1.25rem;
        position: relative;
    }

    .callout h4 {
        font-size: 1.3125rem;
        margin-top: 0;
        margin-bottom: .8rem
    }

    .callout p:last-child {
        margin-bottom: 0;
    }

    .callout-default {
        border-left-color: #007bff;
        background-color: #fff;
    }

    .callout-default h4 {
        color: #777;
    }

    .redactor-editor,
    .redactor-box {
        background: transparent !important;
    }
</style>
<div class="wrapper">
    <div class="row">
        <div class="col-12">
            <div class="card alert">
                <h1><?= Html::encode($this->title) ?></h1>
                <?php if (Yii::$app->session->hasFlash('contactFormSubmitted')) : ?>
                    <div class="callout callout-default <?= ((!Yii::$app->user->isGuest && Yii::$app->user->identity->theme == 0) ? '' : 'bg-dark') ?>">
                        <h5>Sukses Mengirim Email</h5>
                        <p>Peserta yang terdaftar dan peserta tambahan (alamat email valid) akan menerima undangan digital dari Portal Pintar.</p>
                        <p>Terima kasih.</p>
                        <?php
                        $homeUrl = ['agenda/index?owner=&year=' . date("Y") . '&nopage=0'];
                        echo Html::a('<i class="fas fa-home"></i> Kembali ke Agenda', $homeUrl, ['class' => 'btn btn btn-outline-warning btn-sm']);
                        ?>
                    </div>
                <?php else : ?>
                    <p>
                        Berikut adalah template undangan. Anda dapat mengubah sesuai kebutuhan.
                    </p>
                    <?php
                    $autofillString =
                        "
                        <p style=''>Kepada Yth.</p>
                        <p style='margin-left:20pt;'>(terlampir nama)</p>
                        <p style='margin-left:20pt;'>di tempat</p>
                        <p style='text-indent:.5in;text-align:justify'>Dalam rangka <b>" . $dataagenda->kegiatan . "</b>, 
                        bersama ini kami mengundang Bapak/Ibu untuk hadir pada:</p>
                        " . $header . "                        
                        <p style='text-indent:.5in;text-align:justify'>Demikian disampaikan, atas perhatian dan kehadiran Bapak/Ibu diucapkan terima kasih.</p>
                        ";
                    ?>
                    <?php
                    // Step 1: Get the list of email addresses from the peserta attribute in the agenda table
                    $emailList = explode(', ', $dataagenda->peserta);
                    // Step 2: Extract the username (without "@bps.go.id") from each email address
                    $usernames = [];
                    foreach ($emailList as $email) {
                        $username = substr($email, 0, strpos($email, '@'));
                        $usernames[] = $username;
                    }
                    // Step 3: Query the pengguna table for the list of names that correspond to the extracted usernames
                    $names = Pengguna::find()
                        ->select('nama')
                        ->where(['in', 'username', $usernames])
                        ->column();
                    // Step 4: Convert the list of names to a string in the format that can be used for autofill
                    // $autofillString = implode('<br> ', $names);
                    $listItems = '';
                    foreach ($names as $key => $name) {
                        $listItems .= '<li>' .  ' ' . $name . '</li>';
                    }
                    $autofillString2 = '<b>Peserta Kegiatan :</b> <ol>' . $listItems . '</ol>';
                    $autofillString2 =  $autofillString2 . (($dataagenda->peserta_lain != null) ? '<b>Peserta Tambahan : </b><br/>' . $dataagenda->peserta_lain : '');
                    // print_r($autofillString);
                    // Step 5: Set the content of the editor using the html option
                    ?>
                    <div class="row">
                        <div class="col-lg-5">
                            <?php $form = ActiveForm::begin(['id' => 'contact-form']); ?>
                            <?= $form->field($model, 'name')->textInput(['value' => 'Portal Pintar', 'readonly' => true])->label('Nama Pengirim Email') ?>
                            <?= $form->field($model, 'email')->textInput(['value' => 'portalpintar@bps.go.id', 'readonly' => true])->label('Email') ?>
                            <?= $form->field($model, 'subject')->textInput(['value' => 'Undangan Digital Portal Pintar'])->label('Subjek Email') ?>
                            <?php
                            echo $form->field($model, 'body')->widget(Redactor::className(), [
                                'clientOptions' => [
                                    'lang' => 'en',
                                    'plugins' => ['clips', 'counter', 'fontcolor', 'table', 'fullscreen', 'textdirection', 'textexpander'],
                                    'buttons' => [
                                        'html',
                                        'formatting',
                                        'bold',
                                        'italic',
                                        'deleted',
                                        'unorderedlist',
                                        'orderedlist',
                                        'outdent',
                                        'indent',
                                        'table',
                                        'link',
                                        'alignment',
                                        'horizontalrule',
                                        'clips',
                                        'fontcolor',
                                        'backcolor',
                                        'fullscreen',
                                        'textdirection'
                                    ],
                                    'initRb' => true, // Initialize Redactor Bootstrap for correct styling
                                ],
                            ]);
                            ?>

                            <div class="form-group">
                                <?= Html::submitButton('Kirim Undangan Digital', ['class' => 'btn btn btn-outline-warning', 'name' => 'contact-button']) ?>
                            </div>
                            <?php ActiveForm::end(); ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>