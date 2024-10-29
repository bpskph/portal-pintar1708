<?php

use yii\helpers\Html;
use kartik\detail\DetailView;
use yii\bootstrap5\ActiveForm;

$this->title = 'Detail Usulan Zoom # ' . $model->id_zooms;
\yii\web\YiiAsset::register($this);
?>
<div class="container" data-aos="fade-up">
    <h1><?= Html::encode($this->title) ?></h1>
    <div class="d-flex justify-content-between">
        <div class="p-2">
            <?php if (
                !Yii::$app->user->isGuest
                && $model->proposer === Yii::$app->user->identity->username
                && $model->deleted == 0
                && $model->agendae->progress !== 1
                && $model->agendae->progress !== 3
            ) : ?>
                <?php $form = ActiveForm::begin(['action' => ['delete', 'id' => $model->id_zooms], 'method' => 'post']); ?>
                <?= Html::a('<i class="fas fa-edit"></i> Update', ['update', 'id' => $model->id_zooms, 'fk_agenda' => $model->fk_agenda], ['class' => 'btn btn-sm btn-warning']) ?>
                <?= Html::submitButton('Delete', ['class' => 'btn btn-outline-danger btn-sm', 'onclick' => "return confirm('Anda yakin akan menghapus usulan zoom ini?');"]) ?>
                <?php ActiveForm::end(); ?>
            <?php endif; ?>
        </div>
        <div class="p-2">
        </div>
    </div>
    <?php
    $formatter = Yii::$app->formatter;
    $formatter->locale = 'id-ID'; // set the locale to Indonesian
    $timezone = new \DateTimeZone('Asia/Jakarta'); // create a timezone object for WIB
    $waktumulai = new \DateTime($model->agendae->waktumulai, new \DateTimeZone('UTC')); // create a datetime object for waktumulai with UTC timezone
    $waktumulai->setTimeZone($timezone); // set the timezone to WIB
    $waktumulaiFormatted = $formatter->asDatetime($waktumulai, 'd MMMM Y, H:mm'); // format the waktumulai datetime value
    $waktuselesai = new \DateTime($model->agendae->waktuselesai, new \DateTimeZone('UTC')); // create a datetime object for waktuselesai with UTC timezone
    $waktuselesai->setTimeZone($timezone); // set the timezone to WIB
    $waktuselesaiFormatted = $formatter->asDatetime($waktuselesai, 'H:mm'); // format the waktuselesai time value only
    if ($waktumulai->format('Y-m-d') === $waktuselesai->format('Y-m-d')) {
        // if waktumulai and waktuselesai are on the same day, format the time range differently
        $waktumulaiFormatted = $formatter->asDatetime($waktumulai, 'd MMMM Y, H:mm'); // format the waktumulai datetime value with the year and time
        $waktu = $waktumulaiFormatted . ' - ' . $waktuselesaiFormatted . ' WIB'; // concatenate the formatted dates
    } else {
        // if waktumulai and waktuselesai are on different days, format the date range normally
        $waktuselesaiFormatted = $formatter->asDatetime($waktuselesai, 'd MMMM Y, H:mm'); // format the waktuselesai datetime value
        $waktu =  $waktumulaiFormatted . ' WIB s.d ' . $waktuselesaiFormatted . ' WIB'; // concatenate the formatted dates
    }
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
                'attribute' => 'id_zooms',
                'label' => 'ID Usulan Zoom',
            ],
            [
                'attribute' => 'fk_agenda',
                'value' => $model->agendae->kegiatan,
            ],
            [
                'attribute' => 'fk_agenda',
                'value' => $waktu,
                'label' => 'Waktu (Sesuai Agenda)'
            ],
            [
                'attribute' => 'fk_agenda',
                'value' => $model->agendae->progress == 0 ? '<span title="Rencana" class="badge bg-primary rounded-pill"><i class="fas fa-plus-square"></i> Rencana</span>' : ($model->agendae->progress == 1 ? '<span title="Selesai" class="badge bg-success rounded-pill"><i class="fas fa-check"></i> Selesai</span>' : ($model->agendae->progress == 2 ? '<span title="Tunda" class="badge bg-secondary rounded-pill"><i class="fas fa-strikethrough"></i> Tunda</span>' : ($model->agendae->progress == 3 ? '<span title="Batal" class="badge bg-danger rounded-pill"><i class="fas fa-trash-alt"></i> Batal</span>' : ''))),
                'format' => 'html',
                'label' => 'Progress Agenda',
            ],
            [
                'attribute' => 'jenis_zoom',
                'value' => $model->zoomstypee->nama_zoomstype .  ' | ' . $model->zoomstypee->kuota,
            ],
            // 'jenis_surat',
            [
                'attribute' => 'fk_surat',
                'value' => $model->surate,
            ],
            // 'fk_surat',
            [
                'attribute' => 'proposer',
                'value' => $model->proposere->nama,
            ],
            [
                'attribute' => 'deleted',
                'value' => $model->deleted == 0 ? '<span title="Usulan Aktif" class="badge bg-primary rounded-pill"><i class="fas fa-check"></i> Usulan Aktif</span>' : ($model->deleted == 1 ? '<span title="Usulan Dihapus" class="badge bg-danger rounded-pill"><i class="fas fa-trash"></i> Usulan Dihapus oleh Pengguna</span>' : ''),
                'format' => 'html',
                'label' => 'Status Usulan',
            ],
            // [
            //     'attribute' => 'proposer',
            //     'value' => $model->proposere->nama,
            // ],
            [
                'attribute' => 'timestamp',
                'value' => \Yii::$app->formatter->asDatetime(strtotime($model->timestamp), "d MMMM y 'pada' H:mm a"),
            ],
            [
                'attribute' => 'timestamp_lastupdate',
                'value' => \Yii::$app->formatter->asDatetime(strtotime($model->timestamp_lastupdate), "d MMMM y 'pada' H:mm a"),
            ],
        ],
    ]) ?>
</div>