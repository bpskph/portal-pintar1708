<?php
use yii\helpers\Html;
$this->title = 'Tambah Agenda';
?>
<div class="agenda-create">
    <h1><?= Html::encode($this->title) ?></h1>
    <?= $this->render('_form', [
        'model' => $model,
        'searchModel' => $searchModel,
        'dataProvider' => $dataProvider,
    ]) ?>
</div>