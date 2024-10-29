<?php
use yii\helpers\Html;
/** @var yii\web\View $this */
/** @var app\models\Projectmember $model */
$this->title = 'Tambah Anggota Project Tim Kerja';
?>
<div class="container-fluid" data-aos="fade-up">
    <h1><?= Html::encode($this->title) ?></h1>
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>