<?php

use kartik\grid\GridView;
use app\models\AccessLogs;
use app\models\AccessLogsSearch;
use kartik\form\ActiveForm;
use kartik\grid\SerialColumn;
use yii\data\ActiveDataProvider;
use yii\helpers\Html;

$searchModel = new AccessLogsSearch();
// $dataProvider = new ActiveDataProvider([
//     'query' => AccessLogs::find(),
//     'pagination' => [
//         'pageSize' => 20,
//     ],
// ]);
$dataProvider = $searchModel->search(Yii::$app->request->queryParams);

$this->title = 'Riwayat Akses Aplikasi per 19 Juni 2024';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="container-fluid" data-aos="fade-up">
    <h1 class="text-center"><?= Html::encode($this->title) ?></h1>
    <hr class="bps" />
    <div class="card <?= ((!Yii::$app->user->isGuest && Yii::$app->user->identity->theme == 0) ? '' : 'bg-dark') ?>">
        <div class="card-body" style="margin: 0 auto!important">
            <?php
            $form = ActiveForm::begin([
                'action' => ['dashboard'],
                'method' => 'get',
                'type' => ActiveForm::TYPE_INLINE,
                'fieldConfig' => ['options' => ['class' => 'form-group mr-2']]
            ]);
            ?>
            <?= $form->field($searchModel, 'controller', ['autoPlaceholder' => false,])->textInput(['placeholder' => 'Controller ...']) ?>
            <?= $form->field($searchModel, 'action', ['autoPlaceholder' => false,])->textInput(['placeholder' => 'Action ...']) ?>
            <?= $form->field($searchModel, 'user_id', ['autoPlaceholder' => false,])->textInput(['placeholder' => 'User ...']) ?>

            <div class="form-group">
                <?= Html::submitButton('Search', ['class' => 'btn btn-warning mr-2']) ?>
                <?= Html::a('Reset', ['index'], ['class' => 'btn btn btn-outline-warning', 'style' => 'text-decoration:none']) ?>
            </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
    <div class="card <?= ((!Yii::$app->user->isGuest && Yii::$app->user->identity->theme == 0) ? '' : 'bg-dark') ?>">
        <div class="card-body table-responsive p-0">
            <?php
            $layout = '
                        <div class="card-header ' . Yii::$app->user->identity->themechoice . '">
                            <div class="d-flex justify-content-between" style="margin-bottom: -0.8rem;">
                                <div class="p-2">
                                {toolbar}
                                </div>
                                <div class="p-2" style="margin-top:0.5rem;">
                                {summary}
                                </div>
                                <div class="p-2">
                                {pager}
                                </div>
                            </div>                            
                        </div>  
                        {items}
                    ';
            ?>
            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'tableOptions' => ['class' => 'table table-condensed ' . ((!Yii::$app->user->isGuest && Yii::$app->user->identity->theme == 0) ? '' : 'table-dark')],
                'columns' => [
                    [
                        'class' => SerialColumn::class,
                    ],
                    'controller',
                    'action',
                    // 'user_id',
                    // 'created_at',
                    [
                        'attribute' => 'user_id',
                        'value' => function ($model) {
                            if ($model->user_id)
                                return $model->user_id;
                            else
                                return '<em>guest</em>';
                        },
                        'format' => 'html',
                    ],
                    'user_ip',
                    'user_agent',
                    [
                        'attribute' => 'timestamp',
                        'value' => function ($model) {
                            $formatter = Yii::$app->formatter;
                            $formatter->locale = 'id-ID'; // set the locale to Indonesian
                            $timezone = new \DateTimeZone('Asia/Jakarta'); // create a timezone object for WIB
                            $waktumulai = new \DateTime($model->timestamp, new \DateTimeZone('UTC')); // create a datetime object for waktumulai with UTC timezone
                            $waktumulai->setTimeZone($timezone); // set the timezone to WIB
                            $waktumulaiFormatted = $formatter->asDatetime($waktumulai, 'd MMMM Y, H:mm'); // format the waktumulai datetime value
                            return $waktumulaiFormatted . ' WIB';
                        },
                        'label' => 'Timestamp Akses',
                        'format' => 'html',
                        'vAlign' => 'middle'
                    ],
                ],
                'layout' => $layout,
                'bordered' => false,
                'striped' => false,
                'condensed' => false,
                'hover' => true,
                'headerRowOptions' => ['class' => 'kartik-sheet-style'],
                'filterRowOptions' => ['class' => 'kartik-sheet-style'],
                'export' => false,
                'pjax' => false,
                'pjaxSettings' => [
                    'neverTimeout' => true,
                    // 'enablePushState' => false,
                    'options' => ['id' => 'some_pjax_id'],
                ],
                'pager' => [
                    'firstPageLabel' => '<i class="fas fa-angle-double-left"></i>',
                    'lastPageLabel' => '<i class="fas fa-angle-double-right"></i>',
                    'prevPageLabel' => '<i class="fas fa-angle-left"></i>',   // Set the label for the "previous" page button
                    'nextPageLabel' => '<i class="fas fa-angle-right"></i>',
                    'maxButtonCount' => 10,
                ],
                'toggleDataOptions' => ['minCount' => 10],
                'floatOverflowContainer' => true,
                'floatHeader' => true,
                'floatHeaderOptions' => [
                    'scrollingTop' => '0',
                    'position' => 'absolute',
                    'top' => 50
                ],
            ]); ?>
        </div>
    </div>
</div>