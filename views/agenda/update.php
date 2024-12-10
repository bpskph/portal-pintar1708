<?php
use yii\helpers\Html;
$this->title = 'Update Agenda # ' . $model->id_agenda;
?>
<div class="agenda-update">
    <h1><?= Html::encode($this->title) ?></h1>
    <?= $this->render('_form', [
        'model' => $model,
        'searchModel' => $searchModel,
        'dataProvider' => $dataProvider,
    ]) ?>
</div>