<?php
use yii\helpers\Html;
/** @var yii\web\View $this */
/** @var app\models\Suratrepo $model */
$this->title = 'Update Surat Internal # ' . $model->id_suratrepo;
?>
<div class="suratrepo-update">
    <h1><?= Html::encode($this->title) ?></h1>
    <?= $this->render('_form', [
        'model' => $model,
        'dataagenda' => $dataagenda,
        'header' => $header,
        'waktutampil' => $waktutampil,
    ]) ?>
</div>