<?php
use yii\helpers\Html;
$this->title = 'Create Patches';
$this->params['breadcrumbs'][] = ['label' => 'Patches', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="patches-create">
    <h1><?= Html::encode($this->title) ?></h1>
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>
