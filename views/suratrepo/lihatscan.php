<?php

use yii\helpers\Html;
use yii\web\View;

$this->title = 'Scan Surat Internal';
$this->params['breadcrumbs'][] = $this->title;
$this->registerJsFile(Yii::$app->request->baseUrl . '/library/js/fi-suratrepo-lihatscan.js', ['position' => View::POS_END, 'depends' => [\yii\web\JqueryAsset::class]]);
?>
<div id="pdf-container container" data-aos="fade-up">

    <h1 class="text-center"><?= $this->title ?></h1>

    <h5 class="text-center mt-2 mb-2"><em>Jika tampilan file belum berubah (untuk upload ulang), <br /> lakukan clear cache pada browser Anda, atau lihat melalui Moda Privasi (Incognito). Terima kasih.</em></h5>

    <div class=" d-flex justify-content-center mb-2">
        <?= Html::a('<i class="fas fa-scroll"></i> Surat Internal', ['index?owner=&year=' . date("Y")], ['class' => 'btn btn btn-outline-warning btn-sm']) ?>
    </div>

    <?php if (
        !$model->isNewRecord
        && file_exists(Yii::getAlias('@webroot/surat/internal/word/' . $model->id_suratrepo . '.doc'))
    ) : ?>
        <div class="text-center mb-3">
            <a href="<?= Yii::$app->request->baseUrl . '/surat/internal/word/' . $model->id_suratrepo . '.doc' ?>" class="btn btn-outline-warning"><i class="fas fa-file-word"></i> Klik untuk Mengunduh Word File</a>
        </div>
    <?php elseif (
        !$model->isNewRecord
        && file_exists(Yii::getAlias('@webroot/surat/internal/word/' . $model->id_suratrepo . '.docx'))
    ) : ?>
        <div class="text-center mb-3">
            <a href="<?= Yii::$app->request->baseUrl . '/surat/internal/word/' . $model->id_suratrepo . '.docx' ?>" class="btn btn-outline-warning"><i class="fas fa-file-word"></i> Klik untuk Mengunduh Word File</a>
        </div>
    <?php endif; ?>

    <iframe id="pdf-iframe" src="<?= Yii::getAlias('@web') ?>/surat/internal/pdf/<?php echo $model->id_suratrepo ?>.pdf" width="100%" height="500px"></iframe>
</div>