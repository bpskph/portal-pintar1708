<?php
use yii\helpers\Html;
$this->title = 'Tambah Link Aplikasi';
?>
<div class="linkapp-create">
    <h1><?= Html::encode($this->title) ?></h1>
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>
