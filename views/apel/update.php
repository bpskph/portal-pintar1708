<?php
use yii\helpers\Html;
$this->title = 'Update Jadwal Apel/Upacara # ' . $model->id_apel;
?>
<div class="apel-update">
    <h1><?= Html::encode($this->title) ?></h1>
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>
