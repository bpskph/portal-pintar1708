<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\Popups $model */

$this->title = 'Create Popups';
$this->params['breadcrumbs'][] = ['label' => 'Popups', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="popups-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
