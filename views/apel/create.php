<?php
use yii\helpers\Html;
/** @var yii\web\View $this */
/** @var app\models\Apel $model */
$this->title = 'Tambah Jadwal Apel/Upacara';
?>
<div class="apel-create">
    <h1><?= Html::encode($this->title) ?></h1>
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>
