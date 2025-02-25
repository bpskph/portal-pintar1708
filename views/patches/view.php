<?php

use yii\helpers\Html;
use kartik\detail\DetailView;
$this->title = 'Detail Patch/Update/Perbaikan Portal Pintar # ' . $model->id_patches;
?>
<div class="container-fluid" data-aos="fade-up">
    <h1><?= Html::encode($this->title) ?></h1>
    <?php
    $formatter = Yii::$app->formatter;
    $formatter->locale = 'id-ID'; // set the locale to Indonesian
    $timezone = new \DateTimeZone('Asia/Jakarta'); // create a timezone object for WIB
    $waktumulai = new \DateTime($model->timestamp, new \DateTimeZone('Asia/Jakarta')); // create a datetime object for waktumulai with UTC timezone
    $waktumulai->setTimeZone($timezone); // set the timezone to WIB
    $waktumulaiFormatted = $formatter->asDatetime($waktumulai, 'd MMMM Y, H:mm'); // format the waktumulai datetime value
    $waktuTampil =  $waktumulaiFormatted . ' WIB';
    ?>
    <?= DetailView::widget([
        'model' => $model,
        'options' => ['class' => 'table ' . ((!Yii::$app->user->isGuest && Yii::$app->user->identity->theme == 0) ? '' : 'table-dark')],
        'condensed' => true,
        'striped' => (!Yii::$app->user->isGuest && Yii::$app->user->identity->theme == 0) ? true : false,
        'bordered' => false,
        'hover' => true,
        'hAlign' => 'left',
        'attributes' => [
            [
                'attribute' => 'id_patches',
                'label' => 'ID Patch/Update',
            ],
            [
                'attribute' => 'title',
                'value' => $model->title ? $model->title : '-'
            ],
            [
                'attribute' => 'description',
                'format' => 'html', // set format to html
            ],
            [
                'attribute' => 'timestamp',
                'value' => $waktuTampil,
            ],
            [
                'attribute' => 'is_notification',
                'value' => $model->is_notification == 0 ? '<span title="Tidak Diumumkan" class="badge bg-secondary rounded-pill"><i class="fas fa-bell-slash"></i> Tidak Diumumkan dalam Notifikasi Sistem</span>' : ($model->is_notification == 1 ? '<span title="Diumumkan" class="badge bg-primary rounded-pill"><i class="fas fa-bell"></i> Diumumkan dalam Notifikasi Sistem</span>' : ''),
                'format' => 'html',
            ],            
        ],
    ]) ?>
</div>