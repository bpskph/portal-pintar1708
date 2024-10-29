<?php
use yii\helpers\Html;
/** @var yii\web\View $this */
/** @var app\models\Linkapp $model */
$this->title = 'Update Link Aplikasi # ' . $model->id_linkapp;
?>
<div class="linkapp-update">
    <h1><?= Html::encode($this->title) ?></h1>
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>
