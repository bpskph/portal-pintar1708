<?php

use yii\helpers\Html;

$this->title = 'Update Data Perjalanan Dinas # ' . $model->id_dl;
?>
<div class="dl-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
