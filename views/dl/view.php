<?php

use yii\helpers\Html;
use kartik\detail\DetailView;
use yii\bootstrap5\ActiveForm;

$this->title = 'Detail Data Perjalanan Dinas # ' . $model->id_dl;
\yii\web\YiiAsset::register($this);
?>
<div class="container" data-aos="fade-up">
    <h1><?= Html::encode($this->title) ?></h1>
    <div class="d-flex justify-content-between">
        <div class="p-2">
            <?php if (!Yii::$app->user->isGuest && $model->reporter === Yii::$app->user->identity->username && $model->deleted == 0) : ?>
                <?php $form = ActiveForm::begin(['action' => ['delete', 'id_dl' => $model->id_dl], 'method' => 'post']); ?>
                <?= Html::a('<i class="fas fa-edit"></i> Update', ['update', 'id' => $model->id_dl], ['class' => 'btn btn-sm btn-warning']) ?>
                <?= Html::submitButton('Delete', ['class' => 'btn btn-outline-danger btn-sm', 'onclick' => "return confirm('Anda yakin akan menghapus data DL ini?');"]) ?>
                <?php ActiveForm::end(); ?>
            <?php endif; ?>
        </div>
        <div class="p-2">
            <?= Html::a('<i class="fas fa-car"></i> List DL', ['index'], ['class' => 'btn btn-outline-warning btn-sm']) ?>
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
                'attribute' => 'id_dl',
                'label' => 'ID DL',
            ],
            'pegawai:ntext',
            [
                'attribute' => 'tanggal_mulai',
                'value' => \Yii::$app->formatter->asDatetime(strtotime($model->tanggal_mulai), "d MMMM y"),
            ],
            [
                'attribute' => 'tanggal_selesai',
                'value' => \Yii::$app->formatter->asDatetime(strtotime($model->tanggal_selesai), "d MMMM y"),
            ],
            [
                'attribute' => 'fk_tujuan',
                'value' => $model->tujuane->nama_tujuan,
            ],
            'tugas:ntext',
            [
                'attribute' => 'tim',
                'value' => $model->pelaksanae,
            ],
            [
                'attribute' => 'reporter',
                'value' => $model->reportere->nama,
            ],
            [
                'attribute' => 'deleted',
                'value' => $model->deleted == 0 ? '<span title="Data Aktif" class="badge bg-primary rounded-pill"><i class="fas fa-check"></i> Data Aktif</span>' : ($model->deleted == 1 ? '<span title="Data Dihapus" class="badge bg-danger rounded-pill"><i class="fas fa-trash"></i> Data Dihapus oleh Pengguna</span>' : ''),
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