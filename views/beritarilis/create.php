<?php
use yii\helpers\Html;
$this->title = 'Tambah Jadwal Rilis';
?>
<div class="beritarilis-create">
    <h1><?= Html::encode($this->title) ?></h1>
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>
