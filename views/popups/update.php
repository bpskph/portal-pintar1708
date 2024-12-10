<?php

use yii\helpers\Html;

$this->title = 'Update Popups: ' . $model->id_popups;
$this->params['breadcrumbs'][] = ['label' => 'Popups', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id_popups, 'url' => ['view', 'id_popups' => $model->id_popups]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="popups-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
