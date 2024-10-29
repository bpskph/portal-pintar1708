<?php

use yii\helpers\Html;
use kartik\detail\DetailView;
use yii\web\View;

/** @var yii\web\View $this */
/** @var app\models\Linkmat $model */
$this->title = 'Detail Link Materi # ' . $model->id_linkmat;
\yii\web\YiiAsset::register($this);
$baseUrl = Yii::$app->request->baseUrl;
// $baseUrl = '/bengkulu'. Yii::$app->request->baseUrl; untuk di webapps
$script = <<< JS
    var baseUrl = '$baseUrl';
JS;
$this->registerJs($script, \yii\web\View::POS_HEAD);
$this->registerJsFile(Yii::$app->request->baseUrl . '/library/js/fi-linkmat-view.js', ['position' => View::POS_END, 'depends' => [\yii\web\JqueryAsset::class]]);
?>
<div class="container-fluid" data-aos="fade-up">
    <h1><?= Html::encode($this->title) ?></h1>
    <div id="ambilID" style="display:none"><?php echo $model->id_linkmat ?></div>
    <?= DetailView::widget([
        'model' => $model,
        'options' => ['class' => 'table ' . ((!Yii::$app->user->isGuest && Yii::$app->user->identity->theme == 0) ? '' : 'table-dark')],
        'condensed' => true,
        'striped' => (!Yii::$app->user->isGuest && Yii::$app->user->identity->theme == 0) ? true : false,
        'bordered' => false,
        'hover' => true,
        'hAlign' => 'left',
        'attributes' => [
            [
                'attribute' => 'id_linkmat',
                'label' => 'ID Materi',
            ],
            'judul',
            // 'link:ntext',
            [
                'attribute' => 'link',
                'format' => 'html', // set format to html
                'value' => (!Yii::$app->user->isGuest && Yii::$app->user->identity->theme == 1)
                    ? '<span class="bg-white" style="padding: 0 5px; border-radius: 5px;">' . Html::a($model->link, $model->link, ['class' => 'link-click']) . '</span' : Html::a($model->link, $model->link, ['class' => 'link-click']),
                // 'widgetOptions'=>['class'=>'class-link']
            ],
            'keyword:ntext',
            'keterangan:ntext',
            [
                'attribute' => 'views',
                'label' => 'Dilihat',
                'value' => $model->views . ' kali',
            ],
            [
                'attribute' => 'active',
                'value' => $model->active == 0 ? '<span title="Menunggu Moderasi" class="badge bg-secondary rounded-pill"><i class="fas fa-book-reader"></i> Menunggu Moderasi</span>' : ($model->active == 1 ? '<span title="Anggota" class="badge bg-primary rounded-pill"><i class="fas fa-user"></i> Aktif</span>' : ($model->active == 2 ? '<span title="Dihapus" class="badge bg-danger rounded-pill"><i class="fas fa-trash"></i> Dihapus</span>' : '')),
                'format' => 'html',
                'label' => 'Status Link',
            ],
            [
                'attribute' => 'owner',
                'value' => $model->ownere->nama,
            ],
            [
                'attribute' => 'timestamp',
                'value' => \Yii::$app->formatter->asDatetime(strtotime($model->timestamp), "d MMMM y 'pada' H:mm a"),
            ],
            [
                'attribute' => 'timestamp_lastupdate',
                'value' => \Yii::$app->formatter->asDatetime(strtotime($model->timestamp_lastupdate), "d MMMM y 'pada' H:mm a"),
            ],
        ],
    ]) ?>
</div>