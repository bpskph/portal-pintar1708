<?php

use yii\helpers\Html;

$this->title = 'Pengajuan Permohonan Zoom';
?>
<div class="zooms-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'fk_agenda' => $fk_agenda
    ]) ?>

</div>