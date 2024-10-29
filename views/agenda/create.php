<?php
use yii\helpers\Html;
/** @var yii\web\View $this */
/** @var app\models\Agenda $model */
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