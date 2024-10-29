<?php
use yii\helpers\Html;
/** @var yii\web\View $this */
/** @var app\models\Patches $model */
$this->title = 'Update Patches: ' . $model->id_patches;
$this->params['breadcrumbs'][] = ['label' => 'Patches', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id_patches, 'url' => ['view', 'id_patches' => $model->id_patches]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="patches-update">
    <h1><?= Html::encode($this->title) ?></h1>
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>
