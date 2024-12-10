<?php
use yii\helpers\Html;
$this->title = 'Tambah Surat Eksternal';
?>
<div class="suratrepoeks-create">
    <h1><?= Html::encode($this->title) ?></h1>
    <?= $this->render('_form', [
        'model' => $model,
        'dataagenda' => $dataagenda,
        'header' => $header,
        'waktutampil' => $waktutampil,
    ]) ?>
</div>