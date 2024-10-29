<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\Zooms $model */

$this->title = 'Update Permohonan Zoom: ' . $model->id_zooms;
?>
<div class="zooms-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'fk_agenda' => $fk_agenda,
    ]) ?>

</div>