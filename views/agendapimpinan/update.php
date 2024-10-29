<?php
use yii\helpers\Html;
/** @var yii\web\View $this */
/** @var app\models\Agendapimpinan $model */
$this->title = 'Update Agenda Pimpinan # ' . $model->id_agendapimpinan;
?>
<div class="agendapimpinan-update">
    <h1><?= Html::encode($this->title) ?></h1>
    <?= $this->render('_form', [
        'model' => $model,
        'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
    ]) ?>
</div>
