<?php

use yii\helpers\Html;
use kartik\detail\DetailView;
use yii\web\View;

$this->title = 'Detail Surat # ' . $model->id_suratrepoeks;
\yii\web\YiiAsset::register($this);
$this->registerJsFile(Yii::$app->request->baseUrl . '/library/js/fi-copy-clipboard.js', ['position' => View::POS_END, 'depends' => [\yii\web\JqueryAsset::class]]);
?>
<link href="<?php echo Yii::$app->request->baseUrl; ?>/library/fi-page-invoice.css" rel="stylesheet">
<div class="container-fluid" data-aos="fade-up">
    <h1><?= Html::encode($this->title) ?></h1>
    <div class="d-flex justify-content-between">
        <div class="p-2">
            <h5>
                <?php if ($model->approval == 0 && $model->approver !== Yii::$app->user->identity->username) { //yang punya surat 
                ?>
                    <span class="badge bg-danger"><i class="fas fa-exclamation"></i> Belum disetujui</span>
                <?php } elseif ($model->approval == 0 && $model->approver == Yii::$app->user->identity->username) { //yang harusnya menyetujui 
                ?>
                    <?= Html::a('<i class="fas fa-check-double"></i> Setujui Surat Ini', ['suratrepoeks/setujui/' . $model->id_suratrepoeks], ['class' => 'btn btn-outline-primary btn-sm']) ?>
                <?php } else { ?>
                    <span class="badge bg-success"><i class="fas fa-clipboard-check"></i> Telah disetujui</span>
                <?php } ?>
                <?php if (                    
                    (Yii::$app->user->identity->username === $model['owner']
                        || Yii::$app->user->identity->username === $model['approver']
                        || Yii::$app->user->identity->issekretaris === true)
                ) : ?>
                <?php endif; ?>
                <?= Html::a('<i class="fas fa-scroll"></i> Surat Eksternal', ['index?owner=&year=' . date("Y")], ['class' => 'btn btn-outline-success btn-sm']) ?>
            </h5>
        </div>
        <div class="p-2">
            <?php if (!Yii::$app->user->isGuest && $model->deleted == 0 && (($model->owner === Yii::$app->user->identity->username && $model->fk_agenda == NULL) || ($model->owner === Yii::$app->user->identity->username && $model->fk_agenda != NULL && $model->agendae->progress != 3))) : ?>
                <?= Html::a('<i class="fas fa-edit"></i> Update', ['update', 'id' => $model->id_suratrepoeks], ['class' => 'btn btn-sm btn-warning']) ?>
                <?= Html::a('<i class="far fa-trash-alt"></i> Hapus', ['delete', 'id' => $model->id_suratrepoeks], [
                    'class' => 'btn btn-sm btn-danger',
                    'data' => [
                        'confirm' => 'Anda yakin akan menghapus surat ini?',
                        'method' => 'post',
                    ],
                ]) ?>
            <?php endif; ?>
        </div>
    </div>
    <div class="card <?= ((!Yii::$app->user->isGuest && Yii::$app->user->identity->theme == 0) ? '' : 'bg-dark') ?>">
        <div class="card-body">
            <?php if (isset($model->fk_agenda)) : ?>
                <h3>
                    <span class="badge bg-primary">Detail Agenda</span>
                </h3>
                <?= $header; ?>
                <hr class="bps" />
            <?php endif; ?>
            <h3>
                <span class="badge bg-primary">Detail Surat</span>
            </h3>
            <?=
            DetailView::widget([
                'model' => $model,
                'options' => ['class' => 'table ' . ((!Yii::$app->user->isGuest && Yii::$app->user->identity->theme == 0) ? '' : 'table-dark')],
                'condensed' => true,
                'striped' => false,
                'bordered' => false,
                'hover' => true,
                'hAlign' => 'left',
                'attributes' => [
                    'id_suratrepoeks',
                    'penerima_suratrepoeks',
                    [
                        'attribute' => 'tanggal_suratrepoeks',
                        'value' => \Yii::$app->formatter->asDatetime(strtotime($model->tanggal_suratrepoeks), "d MMMM y"),
                    ],
                    'perihal_suratrepoeks:ntext',
                    [
                        'attribute' => 'lampiran',
                        'value' => empty($model->lampiran) ? '-' : $model->lampiran,
                    ],
                    [
                        'attribute' => 'fk_suratsubkode',
                        'value' => $model->suratsubkodee->fk_suratkode . '-' . $model->suratsubkodee->rincian_suratsubkode,
                    ],
                    [
                        'attribute' => 'sifat',
                        'value' => $model->sifat == 0 ? '<span title="Biasa" class="badge bg-primary rounded-pill"><i class="fas fa-scroll"></i> Biasa</span>' : ($model->sifat == 1 ? '<span title="Penting" class="badge bg-success rounded-pill"><i class="fas fa-warehouse"></i> Penting</span>' : '<span title="Rahasia" class="badge bg-danger rounded-pill"><i class="fas fa-key"></i> Rahasia</span>'),
                        'format' => 'html',
                        'label' => 'Sifat Surat',
                    ],
                    [
                        'attribute' => 'jenis',
                        'value' => $model->jenis == 0 ? '<span class="badge bg-primary rounded-pill"><i class="fas fa-scroll"></i> Surat Biasa</span>' : ($model->jenis == 1 ? '<span class="badge bg-success rounded-pill"><i class="fas fa-scroll"></i> Surat Perintah Lembur</span>' : ($model->jenis == 2 ? '<span  class="badge bg-secondary rounded-pill"><i class="fas fa-scroll"></i> Surat Keterangan</span>' : ($model->jenis == 3 ? '<span  class="badge bg-warning rounded-pill"><i class="fas fa-scroll"></i> Berita Acara</span>' : ''))),
                        'format' => 'html',
                        'label' => 'Jenis Surat',
                    ],
                    // 'nomor_suratrepoeks',
                    [
                        'attribute' => 'nomor_suratrepoeks',
                        'format' => 'raw',
                        'value' => $model->nomor_suratrepoeks . ' ' . Html::button('<i class="fas fa-copy"></i> Copy', [
                            'class' => 'btn btn-primary btn-sm ms-2',
                            'onclick' => 'copyToClipboard("' . $model->nomor_suratrepoeks . '")',
                        ]),
                    ],
                    [
                        'attribute' => 'ttd_by',
                        'value' => (empty($model->ttd_by)) ? '-' : $model->ttdbye->jabatan . '<br/>' . $model->ttdbye->nama,
                        'format' => 'html',
                    ],
                    [
                        'attribute' => 'owner',
                        'value' => $model->ownere->nama,
                    ],
                    [
                        'attribute' => 'shared_to',
                        'value' => $model->shared_to ? $model->projecte : '<span title="Biasa" class="badge bg-warning rounded-pill"><i class="fas fa-times"></i> Tidak Dibagikan ke Tim</span>',
                        'format' => 'html',
                    ],
                    [
                        'attribute' => 'timestamp',
                        'value' => \Yii::$app->formatter->asDatetime(strtotime($model->timestamp), "d MMMM y 'pada' H:mm a"),
                    ],
                    [
                        'attribute' => 'timestamp_suratrepoeks_lastupdate',
                        'value' => \Yii::$app->formatter->asDatetime(strtotime($model->timestamp_suratrepoeks_lastupdate), "d MMMM y 'pada' H:mm a"),
                    ],
                ],
            ])
            ?>
            <br />                     
        </div>
    </div>
</div>