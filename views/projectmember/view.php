<?php
use yii\helpers\Html;
use kartik\detail\DetailView;
$this->title = 'Detail Anggota Project Tim Kerja # ' . $model->id_projectmember;
\yii\web\YiiAsset::register($this);
?>
<div class="container-fluid" data-aos="fade-up">
    <h1><?= $this->title ?></h1>
    <?php if (Yii::$app->user->identity->level == 0) : ?>
        <p>
            <?= Html::a('Update', ['update', 'id' => $model->id_projectmember], ['class' => 'btn btn-warning']) ?>
        </p>
    <?php endif; ?>
    <?= DetailView::widget([
        'model' => $model,
        'condensed' => false,
        'striped' => false,
        'bordered' => false,
        'hover' => true,
        'hAlign' => 'left',
        'buttons1' => '',
        'attributes' => [
            'id_projectmember',
            'fk_project',
            'pegawai',
            'member_status',
        ],
    ]) ?>
</div>