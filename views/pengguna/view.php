<?php

use yii\helpers\Html;
use kartik\detail\DetailView;

/** @var yii\web\View $this */
/** @var app\models\Pengguna $model */
$this->title = "Detail Pegawai #" . $model->nama;
\yii\web\YiiAsset::register($this);
?>
<div class="container" data-aos="fade-up">
    <?php if (Yii::$app->user->identity->level == 0) : ?>
        <p>
            <?= Html::a('Update', ['update', 'id' => $model->username], ['class' => 'btn btn-warning']) ?>
            <?php if (Yii::$app->user->identity->username != $model->username) : ?>
                <?= Html::a('Delete', ['delete', 'id' => $model->username], [
                    'class' => 'btn btn-danger',
                    'data' => [
                        'confirm' => 'Anda yakin akan menghapus pengguna ini?',
                        'method' => 'post',
                    ],
                ]) ?>
            <?php endif; ?>
        </p>
    <?php endif; ?>
    <?= DetailView::widget([
        'model' => $model,
        'options' => ['class' => 'table ' . ((!Yii::$app->user->isGuest && Yii::$app->user->identity->theme == 0) ? '' : 'table-dark')],
        'condensed' => true,
        'striped' => (!Yii::$app->user->isGuest && Yii::$app->user->identity->theme == 0) ? true : false,
        'bordered' => false,
        'hover' => true,
        'hAlign' => 'left',
        'attributes' => [
            'nama',
            'username',
            [
                'attribute' => 'username',
                'value' => $model->username . '@bps.go.id',
                'label' => 'Email'
            ],
            [
                'attribute' => 'password',
                'value' => '******',
            ],
            [
                'attribute' => 'formattedNip',
                'label' => 'NIP',
            ],
            [
                'attribute' => 'nip',
                'label' => 'NIP BPS',
            ],
            [
                'attribute' => 'FormattedPhoneNumber',
            ],
            [
                'attribute' => 'tgl_daftar',
                'value' => \Yii::$app->formatter->asDatetime(strtotime($model->tgl_daftar), "d MMMM y 'pada' H:mm a"),
            ],
            [
                'attribute' => 'tgl_update',
                'value' => \Yii::$app->formatter->asDatetime(strtotime($model->tgl_update), "d MMMM y 'pada' H:mm a"),
            ],
            [
                'attribute' => 'level',
                'label' => 'Status Akses',
                'value' => ($model->level == 1 ? '<span title="Aktif" class="badge bg-primary rounded-pill"><i class="fas fa-check"></i> Aktif</span>'
                    : ($model->level == 0 ? '<span title="Admin" class="badge bg-success rounded-pill"><i class="fas fa-user-secret"></i> Admin</span>'
                        : '<span title="Non Aktif" class="badge bg-danger rounded-pill"><i class="fas fa-trash-alt"></i> Tidak Aktif</span>')),
                'format' => 'html',
            ],
            [
                'attribute' => 'theme',
                'value' => $model->theme == 0 ? '<span title="Light" class="badge bg-secondary rounded-pill"><i class="fas fa-sun"></i> Light Theme</span>' : '<span title="Hybrid" class="badge bg-secondary rounded-pill"><i class="fas fa-moon"></i> Dark Theme</span>',
                'format' => 'html',
                'label' => 'Tema yang Dipakai',
            ],
        ],
    ]) ?>
</div>