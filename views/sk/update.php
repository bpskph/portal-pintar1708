<?php

use yii\helpers\Html;

$this->title = 'Update Data SK # ' . $model->id_sk;
?>
<div class="sk-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
