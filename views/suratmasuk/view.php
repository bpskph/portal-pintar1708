<?php

use app\models\Suratmasukdisposisi;
use yii\helpers\Html;
use kartik\detail\DetailView;

$this->title = "Detail Surat Masuk #" . $model->id_suratmasuk;
\yii\web\YiiAsset::register($this);
?>
<div class="container" data-aos="fade-up">
    <h1><?= Html::encode($this->title) ?></h1>

    <div class="d-flex justify-content-between">
        <div class="p-2">
            <?php $disposisi = Suratmasukdisposisi::findOne(['fk_suratmasuk' => $model['id_suratmasuk']]);?>
            <?php if (Yii::$app->user->identity->approver_mobildinas === 1 || Yii::$app->user->identity->issekretaris || Yii::$app->user->identity->level == 0 && empty($disposisi) ) : ?>
                <p>
                    <?= Html::a('<i class="fas fa-edit"></i> Update', ['update', 'id' => $model->id_suratmasuk], ['class' => 'btn btn-sm btn-warning']) ?>
                    <?= Html::a('Delete', ['delete', 'id' => $model->id_suratmasuk], [
                        'class' => 'btn btn-sm btn-danger',
                        'data' => [
                            'confirm' => 'Anda yakin akan menghapus data surat ini?',
                            'method' => 'post',
                        ],
                    ]) ?>
                </p>
            <?php endif; ?>
        </div>
        <div class="p-2">
            <?= Html::a('<i class="fas fa-car"></i> List Surat Masuk', ['index', 'year' => ''], ['class' => 'btn btn-outline-warning btn-sm']) ?>
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
            'id_suratmasuk',
            'pengirim_suratmasuk',
            'perihal_suratmasuk:ntext',
            [
                'attribute' => 'tanggal_diterima',
                'value' => \Yii::$app->formatter->asDatetime(strtotime($model->tanggal_diterima), "d MMMM y"),
            ],
            'nomor_suratmasuk',
            [
                'attribute' => 'tanggal_suratmasuk',
                'value' => \Yii::$app->formatter->asDatetime(strtotime($model->tanggal_suratmasuk), "d MMMM y"),
            ],
            [
                'attribute' => 'sifat',
                'value' => $model->sifat == 0 ? '<span title="Biasa" class="badge bg-primary rounded-pill"><i class="fas fa-scroll"></i> Biasa</span>' : ($model->sifat == 1 ? '<span title="Terbatas" class="badge bg-success rounded-pill"><i class="fas fa-warehouse"></i> Terbatas</span>' : '<span title="Rahasia" class="badge bg-danger rounded-pill"><i class="fas fa-key"></i> Rahasia</span>'),
                'format' => 'html',
                'label' => 'Sifat Surat',
            ],
            [
                'attribute' => 'reporter',
                'value' => $model->reportere->nama,
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
    <iframe id="pdf-iframe" src="<?= Yii::getAlias('@web') ?>/surat/masuk/<?php echo $model->id_suratmasuk ?>.pdf" width="100%" height="700px"></iframe>
</div>