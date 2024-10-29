<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\Mobildinas $model */

$this->title = 'Tambah Usulan Peminjaman Mobil Dinas';
?>
<div class="mobildinas-create">
    <h1><?= Html::encode($this->title) ?></h1>
    <?= $this->render('_form', [
        'model' => $model,
        'searchModel' => $searchModel,
        'dataProvider' => $dataProvider,
    ]) ?>

</div>