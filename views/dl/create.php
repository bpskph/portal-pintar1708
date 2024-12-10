<?php

use yii\helpers\Html;

$this->title = 'Tambah Data Perjalanan Dinas';
?>
<div class="dl-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
