<?php
use yii\helpers\Html;
/** @var yii\web\View $this */
/** @var app\models\Apel $model */
$this->title = 'Update Jadwal Apel/Upacara # ' . $model->id_apel;
?>
<div class="apel-update">
    <h1><?= Html::encode($this->title) ?></h1>
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>
