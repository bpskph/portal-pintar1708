<?php
use yii\helpers\Html;
$this->title = 'Update Laporan Agenda # ' . $dataagenda->id_agenda;
?>
<div class="laporan-update">
    <h1><?= Html::encode($this->title) ?></h1>
    <?= $this->render('_form', [
        'model' => $model,
        'dataagenda' => $dataagenda,
        'header'=> $header,
        'waktutampil'=>$waktutampil
    ]) ?>
</div>
