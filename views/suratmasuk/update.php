<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\Suratmasuk $model */

$this->title = 'Ubah Data Surat Masuk: ' . $model->id_suratmasuk;
?>
<div class="suratmasuk-update">
    <h1><?= Html::encode($this->title) ?></h1>
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>
