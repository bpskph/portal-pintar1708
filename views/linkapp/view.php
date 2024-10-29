<?php
use yii\helpers\Html;
use yii\widgets\DetailView;
/** @var yii\web\View $this */
/** @var app\models\Linkapp $model */
$this->title = $model->id_linkapp;
$this->params['breadcrumbs'][] = ['label' => 'Linkapps', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="linkapp-view">
    <h1><?= Html::encode($this->title) ?></h1>
    <p>
        <?= Html::a('Update', ['update', 'id_linkapp' => $model->id_linkapp], ['class' => 'btn btn-warning']) ?>
        <?= Html::a('Delete', ['delete', 'id_linkapp' => $model->id_linkapp], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id_linkapp',
            'judul',
            'link:ntext',
            'keyword:ntext',
            'views',
        ],
    ]) ?>
</div>
