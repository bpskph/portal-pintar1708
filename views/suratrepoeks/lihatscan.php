<?php

/** @var yii\web\View $this */

use yii\web\View;
use yii\helpers\Html;

$this->title = 'Scan Surat Eksternal';
$this->params['breadcrumbs'][] = $this->title;
// Registering your custom JS and CSS files
$this->registerJsFile(Yii::$app->request->baseUrl . '/library/js/fi-suratrepo-lihatscan.js', ['position' => View::POS_END, 'depends' => [\yii\web\JqueryAsset::class]]);
?>
<div id="pdf-container container" data-aos="fade-up">

    <h1 class="text-center"><?= $this->title ?></h1>

    <h5 class="text-center mt-2 mb-2"><em>Jika tampilan file belum berubah (untuk upload ulang), <br /> lakukan clear cache pada browser Anda, atau lihat melalui Moda Privasi (Incognito). Terima kasih.</em></h5>

    <div class=" d-flex justify-content-center mb-2">
    <?= Html::a('<i class="fas fa-scroll"></i> Surat Eksternal', ['index?owner=&year='.date("Y")], ['class' => 'btn btn-outline-success btn-sm']) ?>
    </div>
    <?php if (
        !$model->isNewRecord
        && file_exists(Yii::getAlias('@webroot/surat/eksternal/word/' . $model->id_suratrepoeks . '.doc'))
    ) : ?>
        <div class="text-center mb-3">
            <a href="<?= Yii::$app->request->baseUrl . '/surat/eksternal/word/' . $model->id_suratrepoeks . '.doc' ?>" class="btn btn-outline-warning"><i class="fas fa-file-word"></i> Klik untuk Mengunduh Word File</a>
        </div>
    <?php elseif (
        !$model->isNewRecord
        && file_exists(Yii::getAlias('@webroot/surat/eksternal/word/' . $model->id_suratrepoeks . '.docx'))
    ) : ?>
        <div class="text-center mb-3">
            <a href="<?= Yii::$app->request->baseUrl . '/surat/eksternal/word/' . $model->id_suratrepoeks . '.docx' ?>" class="btn btn-outline-warning"><i class="fas fa-file-word"></i> Klik untuk Mengunduh Word File</a>
        </div>
    <?php endif; ?>

    <iframe id="pdf-iframe" src="<?= Yii::getAlias('@web') ?>/surat/eksternal/pdf/<?php echo $model->id_suratrepoeks ?>.pdf" width="100%" height="700px"></iframe>
</div>