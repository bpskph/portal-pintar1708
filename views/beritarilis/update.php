<?php
use yii\helpers\Html;
$this->title = 'Update Jadwal Rilis # ' . $model->id_beritarilis;
?>
<div class="beritarilis-update">
    <h1><?= Html::encode($this->title) ?></h1>
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>