<?php

use yii\helpers\Html;
use yii\web\View;

$this->title = 'Detail Laporan Agenda # ' . $model->id_laporan;
$this->registerCssFile(Yii::$app->request->baseUrl . '/library/css/fi-page-invoice.css', ['position' => View::POS_END, 'depends' => [\yii\web\JqueryAsset::class]]);
\yii\web\YiiAsset::register($this);
?>
<style>
    .p-2 {
        margin-right: -0.5rem !important;
        margin-left: -0.5rem !important;
    }

    .cetak:hover {
        color: #fff !important;
    }

    #source-html {
        font-family: "Poppins", sans-serif !important;
    }
</style>

<div class="container" data-aos="fade-up">
    <h1><?= Html::encode($this->title) ?></h1>
    <div class=" d-flex justify-content-center mb-2">
        <?php
        $homeUrl = ['agenda/index?owner=&year=' . date("Y") . '&nopage=0'];
        echo Html::a('<i class="fas fa-home"></i> Agenda Utama', $homeUrl, ['class' => 'btn btn btn-outline-warning btn-sm']);
        ?>
    </div>
    <div class="d-flex justify-content-between">
        <div class="p-2">
            <h5>
                <?php if ($model->approval == 0) { ?>
                    <span class="badge bg-danger"><i class="fas fa-exclamation"></i> Belum disetujui</span>
                <?php } else { ?>
                    <span class="badge bg-success"><i class="fas fa-clipboard-check"></i> Telah disetujui</span>
                <?php } ?>
                <?php // Html::a('<i class="fas fa-file-pdf"></i> PDF', ['cetaklaporan', 'id' => $model->id_laporan], ['class' => 'badge bg-primary cetak', 'target' => '_blank']) 
                ?>
            </h5>
        </div>
        <div class="p-2">
            <?php if ($model->dokumentasi != null): ?>
                <?= Html::a('<i class="fas fa-link"></i> Link Dokumentasi', $model->dokumentasi, ['class' => 'btn btn-sm btn-warning', 'target' => '_blank']) ?>
            <?php endif; ?>
            <?php if (!Yii::$app->user->isGuest && $model->agendae->reporter === Yii::$app->user->identity->username && $model->agendae->progress == 1 && $model->approval == 0) : ?>
                <?= Html::a('<i class="fas fa-edit"></i> Update', ['update', 'id' => $model->id_laporan], ['class' => 'btn btn-sm btn-warning']) ?>
            <?php endif; ?>
            <?php if (!Yii::$app->user->isGuest && $model->agendae->pemimpin === Yii::$app->user->identity->username && $model->approval == 0) : ?>
                <?= Html::a('<i class="far fa-thumbs-up"></i> Setujui', ['setujui', 'id' => $model->id_laporan], [
                    'class' => 'btn btn-sm btn-success',
                    'data' => [
                        'confirm' => 'Anda yakin akan menyetujui laporan ini?',
                        'method' => 'post',
                    ],
                ]) ?>
            <?php endif; ?>
        </div>
    </div>

    <div class="card <?= ((!Yii::$app->user->isGuest && Yii::$app->user->identity->theme == 0) ? '' : 'bg-dark text-light') ?>">
        <div class="card-body">
            <h3>
                <span class="badge bg-primary">Detail Kegiatan</span>
            </h3>
            <?= $header; ?>
            <?php if (file_exists(Yii::getAlias('@webroot/laporans/' . $model->id_laporan . '.pdf'))) : ?>
                <div class="text-center">
                    <div id="pdf-container container" data-aos="fade-up">
                        <h5 class="text-center mt-2 mb-2"><em>Jika tampilan file belum berubah (untuk upload ulang), <br /> lakukan clear cache pada browser Anda, atau lihat melalui Moda Privasi (Incognito). Terima kasih.</em></h5>
                        <iframe id="pdf-iframe" src="<?= Yii::getAlias('@web') ?>/laporans/<?php echo $model->id_laporan ?>.pdf" width="100%" height="500px"></iframe>
                    </div>
                </div>
            <?php else: ?>
                <div id="pdf-container container" data-aos="fade-up">
                    <h5 class="text-center mt-2 mb-2"><em>Berkas PDF belum tersedia (belum diunggah oleh penyusun agenda).<br />Jika berkas sudah diupload namun belum tampil, mohon lakukan clear cache pada browser Anda, atau lihat melalui Moda Privasi (Incognito). Terima kasih.</em></h5>
                </div>
            <?php endif; ?>
            <br />
        </div>
    </div>
</div>