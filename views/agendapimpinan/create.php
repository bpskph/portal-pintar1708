<?php
use yii\helpers\Html;
/** @var yii\web\View $this */
/** @var app\models\Agendapimpinan $model */
$this->title = 'Tambah Agenda Pimpinan';
?>
<div class="agendapimpinan-create">
    <h1><?= Html::encode($this->title) ?></h1>
    <?= $this->render('_form', [
        'model' => $model,
        'searchModel' => $searchModel,
        'dataProvider' => $dataProvider,
    ]) ?>
</div>