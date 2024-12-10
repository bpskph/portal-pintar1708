<?php
use yii\helpers\Html;
$this->title = 'Update Link Materi # ' . $model->id_linkmat;
?>
<div class="linkmat-update">
    <h1><?= Html::encode($this->title) ?></h1>
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>
