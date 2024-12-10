<?php
use yii\helpers\Html;
$this->title = 'Tambah Link Materi';
?>
<div class="linkmat-create">
    <h1><?= Html::encode($this->title) ?></h1>
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>
