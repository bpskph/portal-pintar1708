<?php

use yii\helpers\Html;
use kartik\detail\DetailView;
use yii\bootstrap5\ActiveForm;

$this->title = 'Detail Usulan Peminjaman Mobil Dinas # ' . $model->id_mobildinas;
\yii\web\YiiAsset::register($this);
?>
<div class="container" data-aos="fade-up">
    <h1><?= Html::encode($this->title) ?></h1>
    <div class="d-flex justify-content-between">
        <div class="p-2">
            <?php if (!Yii::$app->user->isGuest && $model->borrower === Yii::$app->user->identity->username && $model->deleted == 0) : ?>
                <?php $form = ActiveForm::begin(['action' => ['delete', 'id_mobildinas' => $model->id_mobildinas], 'method' => 'post']); ?>
                <?= Html::a('<i class="fas fa-edit"></i> Update', ['update', 'id' => $model->id_mobildinas], ['class' => 'btn btn-sm btn-warning']) ?>
                <?= Html::submitButton('Delete', ['class' => 'btn btn-outline-danger btn-sm', 'onclick' => "return confirm('Anda yakin akan menghapus usulan peminjaman ini?');"]) ?>
                <?php ActiveForm::end(); ?>
            <?php endif; ?>
        </div>
        <div class="p-2">
            <?= Html::a('<i class="fas fa-car"></i> List Peminjaman Mobil Dinas', ['index'], ['class' => 'btn btn-outline-warning btn-sm']) ?>
        </div>
    </div>

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
                'attribute' => 'id_mobildinas',
                'label' => 'ID Usulan Peminjaman',
            ],
            [
                'attribute' => 'mulai',
                'value' => \Yii::$app->formatter->asDatetime(strtotime($model->mulai), "d MMMM y 'pada' H:mm a"),
            ],
            [
                'attribute' => 'selesai',
                'value' => \Yii::$app->formatter->asDatetime(strtotime($model->selesai), "d MMMM y 'pada' H:mm a"),
            ],
            [
                'attribute' => 'keperluan',
                'value' => $model->keperluane->nama_mobildinaskeperluan,
            ],
            [
                'attribute' => 'keperluan_lainnya',
                'value' => ($model->keperluan_lainnya ? $model->keperluan_lainnya : '-'),
            ],
            [
                'attribute' => 'borrower',
                'value' => $model->borrowere->nama,
            ],
            [
                'attribute' => 'approval',
                'value' => $model->approval == 1 ?
                    '<span title="Disetujui" class="badge bg-primary rounded-pill"><i class="fas fa-check"></i> Usulan Disetujui</span>' : ($model->approval == 3 ?
                        '<span title="Persetujuan Usulan Dibatalkan" class="badge bg-danger rounded-pill"><i class="fas fa-trash"></i> Persetujuan Usulan Dibatalkan oleh Umum</span>' : ($model->approval == 0 ?
                            '<span title="Menunggu Konfirmasi" class="badge bg-secondary rounded-pill"><i class="fas fa-question"></i> Menunggu Konfirmasi</span>' : '<span title="Usulan Ditolak" class="badge bg-danger rounded-pill"><i class="fas fa-times"></i> Usulan Ditolak</span>')),
                'format' => 'html',
                'label' => 'Status Persetujuan',
            ],
            [
                'attribute' => 'alasan_tolak_batal',
                'value' =>  ($model->alasan_tolak_batal ? $model->alasan_tolak_batal : '-'),
            ],
            [
                'attribute' => 'deleted',
                'value' => $model->deleted == 0 ? '<span title="Usulan Aktif" class="badge bg-primary rounded-pill"><i class="fas fa-check"></i> Usulan Aktif</span>' : ($model->deleted == 1 ? '<span title="Usulan Dihapus" class="badge bg-danger rounded-pill"><i class="fas fa-trash"></i> Usulan Dihapus oleh Pengguna</span>' : ''),
                'format' => 'html',
                'label' => 'Status Usulan',
            ],
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