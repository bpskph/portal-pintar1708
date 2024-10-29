<?php
use yii\helpers\Html;
use yii\widgets\ListView;
/* @var $this yii\web\View */
/* @var $searchModel app\models\QuestionsCari */
/* @var $dataProvider yii\data\ActiveDataProvider */
$this->title = 'Daftar Surat Internal Agenda # ' . $dataagenda->id_agenda;
?>
<style>
    .callout {
        background-color: #fff;
        border: 1px solid #e4e7ea;
        border-left: 4px solid #c8ced3;
        border-radius: .25rem;
        margin: 1rem 0;
        padding: .75rem 1.25rem;
        position: relative;
    }
    .callout h4 {
        font-size: 1.3125rem;
        margin-top: 0;
        margin-bottom: .8rem
    }
    .callout p:last-child {
        margin-bottom: 0;
    }
    .callout-default {
        border-left-color: #007bff;
        background-color: #fff;
    }
    .callout-default h4 {
        color: #777;
    }
</style>
<div class="container-fluid" data-aos="fade-up">
    <h1 class="text-center"><?= Html::encode($this->title) ?></h1>
    <hr class="bps" />
    <div id="w0" class="callout callout-default">
        <h5>Surat-surat</h5>
        <p><?php echo $dataagenda->kegiatan ?> pada <?php echo $waktutampil ?></p>
    </div>
    <div class="card transparan alert">
        <div class="card-body table-responsive p-0">
            <?php if (!Yii::$app->user->isGuest && Yii::$app->user->identity->username == $dataagenda->reporter) : ?>
                <p class="text-right">
                    <?= Html::a('<i class="fas fa-folder-plus"></i> Tambah Data Baru', ['create'], ['class' => 'btn btn btn-outline-warning btn-sm']) ?>
                </p>
            <?php endif; ?>
            <?=
            ListView::widget([
                'dataProvider' => $dataProvider,
                'layout' => "{pager}\n{summary}\n{items}",
                'itemView' => '_view',
            ]);
            ?>
        </div>
    </div>
</div>