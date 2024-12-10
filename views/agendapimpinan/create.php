<?php
use yii\helpers\Html;
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