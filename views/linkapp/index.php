<?php

use kartik\form\ActiveForm;
use yii\helpers\Html;
use yii\web\View;
// set the bootstrap grid class for each column
$colClass = 'col-md-3'; // use the appropriate class based on your requirements
$this->title = 'Portal Aplikasi';
$baseUrl = Yii::$app->request->baseUrl;
// $baseUrl = '/bengkulu'. Yii::$app->request->baseUrl; untuk di webapps
$script = <<< JS
    var baseUrl = '$baseUrl';
JS;
$this->registerJs($script, \yii\web\View::POS_HEAD);
$this->registerJsFile(Yii::$app->request->baseUrl . '/library/js/fi-copy-link-linkapp.js', ['position' => View::POS_END, 'depends' => [\yii\web\JqueryAsset::class]]);
$this->registerJsFile(Yii::$app->request->baseUrl . '/library/js/fi-linkapp.js', ['position' => View::POS_END, 'depends' => [\yii\web\JqueryAsset::class]]);
$this->registerCssFile(Yii::$app->request->baseUrl . '/library/css/fi-linkapp.css', ['position' => View::POS_END, 'depends' => [\yii\web\JqueryAsset::class]]);
?>
<div class="container-fluid" data-aos="fade-up">
    <h1 class="text-center"><?= Html::encode($this->title) ?></h1>
    <div class="text-center">
        <?php if (Yii::$app->controller->action->id == 'index') : ?>
            <?= Html::a('<i class="fas fa-list"></i> Ganti View', ['indexgrid'], ['class' => 'btn btn ' . ((!Yii::$app->user->isGuest && Yii::$app->user->identity->theme == 0) ? 'btn-outline-dark' : 'btn-outline-light') . ' btn-sm']) ?>
            <?php if (!Yii::$app->user->isGuest && Yii::$app->user->identity->level == 0) : ?>
               | <?= Html::a('<i class="fas fa-folder-plus"></i> Tambah Data Baru', ['create'], ['class' => 'btn btn btn-outline-warning btn-sm']) ?>
            <?php endif; ?>
        <?php elseif (Yii::$app->controller->action->id == 'indexgrid') : ?>
            <?= Html::a('<i class="fas fa-images"></i> Ganti View', ['index'], ['class' => 'btn btn ' . ((!Yii::$app->user->isGuest && Yii::$app->user->identity->theme == 0) ? 'btn-outline-dark' : 'btn-outline-light') . ' btn-sm']) ?>
            <?php if (!Yii::$app->user->isGuest && Yii::$app->user->identity->level == 0) : ?>
               | <?= Html::a('<i class="fas fa-folder-plus"></i> Tambah Data Baru', ['create'], ['class' => 'btn btn btn-outline-warning btn-sm']) ?>
            <?php endif; ?>
        <?php endif; ?>
    </div>
    <hr class="bps" />
    <!-- LABEL FILTER -->

    <div class="card <?= ((!Yii::$app->user->isGuest && Yii::$app->user->identity->theme == 0) ? '' : 'bg-dark') ?>">
        <?php $form = ActiveForm::begin([
            'method' => 'get',
            'action' => ['index'],
            'type' => ActiveForm::TYPE_FLOATING,
            'formConfig' => ['labelSpan' => 4],
        ]); ?>
        <div class="card-body">
            <div class="row justify-content-center">
                <?= $form->field($searchModel, 'keyword')->checkboxList($keywords, [])->label(false) ?>
            </div>
            <div class="card-footer text-center">
                <?= Html::submitButton('Filter', ['class' => 'btn btn-warning']) ?>
                <?= Html::a('Reset', ['index'], ['class' => 'btn btn btn-outline-warning']) ?>
            </div>
            <?php ActiveForm::end(); ?>
        </div>
        <div class="row align-items-top">
            <?php foreach ($dataProvider->getModels() as $model) : ?>
                <div class="<?= $colClass ?> mb-2">
                    <div class="card animated zoomIn" style="height:100%">
                        <div class="card-body p-1">
                            <a href="<?= $model->link ?>" target="_blank" class="link-click-image" data-link-id="<?= $model->id_linkapp ?>" id="<?= $model->id_linkapp ?>">
                                <?= Html::img(Yii::$app->request->baseUrl . '/images/linkapp/' . $model->id_linkapp . '.png', ['alt' => 'Image', 'class' => 'card-img-top']) ?>
                            </a>
                        </div>
                        <div class="card-body">
                            <h5 class="card-title link-click" data-link-id="<?= $model->id_linkapp ?>">
                                <?= Html::a($model->judul, $model->link, ['target' => '_blank']) ?>
                            </h5>
                            <div class="d-flex justify-content-between" style="margin-bottom: -0.8rem;">
                                <div class="p-2">
                                    <p class="card-text"><?= $model->keyword ?></p>
                                </div>
                                <div class="p-2">
                                </div>
                                <div class="p-2">
                                    <p class="badge bg-primary"><i class="fas fa-eye"></i> <?= $model->views ?> x</p>
                                    <?php
                                    $link = Yii::$app->request->hostInfo . Yii::$app->request->baseUrl . '/linkapp/' . $model->id_linkapp;
                                    $judul = $model->judul; // Make sure to properly encode the model title
                                    $content = 'Lihat tautan aplikasi dari Sistem Portal Pintar (' . Yii::$app->request->hostInfo . Yii::$app->request->baseUrl . '), yaitu ' . $judul . ' ke ' . $model->link;
                                    // Generate the HTML for the link/button to copy the content
                                    $buttonHtml = Html::a(
                                        '<i class="fas fa-share-alt"></i> ',
                                        '#', // We'll use JavaScript to handle the click event
                                        [
                                            'title' => 'Bagikan rincian link ini',
                                            'class' => 'copy-link-button',
                                            'data-content' => $content, // Store the content as data for the button
                                        ]
                                    );
                                    echo $buttonHtml;
                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>