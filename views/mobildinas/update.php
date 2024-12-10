<?php

use yii\helpers\Html;

$this->title = 'Ubah Usulan Peminjaman Mobil Dinas: ' . $model->id_mobildinas;
?>
<div class="mobildinas-update">
    <h1><?= Html::encode($this->title) ?></h1>
    <?= $this->render('_form', [
        'model' => $model,
        'searchModel' => $searchModel,
        'dataProvider' => $dataProvider,
    ]) ?>

</div>