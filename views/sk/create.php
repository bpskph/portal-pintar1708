<?php

use yii\helpers\Html;

$this->title = 'Tambah Data SK Baru';
?>
<div class="sk-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
