<?php
use yii\helpers\Html;
$this->title = 'Update Surat Eksternal # ' . $model->id_suratrepoeks;
?>
<div class="suratrepoeks-update">
    <h1><?= Html::encode($this->title) ?></h1>
    <?= $this->render('_form', [
        'model' => $model,
        'dataagenda' => $dataagenda,
        'header' => $header,
        'waktutampil' => $waktutampil,
    ]) ?>
</div>