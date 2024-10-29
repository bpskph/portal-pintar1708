<?php
use yii\helpers\Html;
/** @var yii\web\View $this */
/** @var app\models\Laporan $model */
$this->title = 'Tambah Laporan Agenda # ' . $dataagenda->id_agenda;
?>
<div class="laporan-create">
    <h1><?= Html::encode($this->title) ?></h1>
    <?= $this->render('_form', [
        'model' => $model,
        'dataagenda' => $dataagenda,
        'header'=> $header,
        'waktutampil'=>$waktutampil
    ]) ?>
</div>
