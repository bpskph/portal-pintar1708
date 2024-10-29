<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\Mobildinas $model */

$this->title = 'Tambah Usulan Peminjaman Mobil Dinas: ' . $model->id_mobildinas;
?>
<div class="mobildinas-update">
    <h1><?= Html::encode($this->title) ?></h1>
    <?= $this->render('_form', [
        'model' => $model,
        'searchModel' => $searchModel,
        'dataProvider' => $dataProvider,
    ]) ?>

</div>