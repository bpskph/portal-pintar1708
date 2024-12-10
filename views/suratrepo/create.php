<?php
use yii\helpers\Html;
$this->title = 'Tambah Surat Internal';
?>
<div class="suratrepo-create">
    <h1><?= Html::encode($this->title) ?></h1>
    <?= $this->render('_form', [
        'model' => $model,
        'dataagenda' => $dataagenda,
        'header' => $header,
        'waktutampil' => $waktutampil,
    ]) ?>
</div>