<?php

use kartik\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
use kartik\grid\SerialColumn;
use kartik\grid\ActionColumn;
use yii\web\View;

$this->title = 'Notifikasi Anda';
$this->params['breadcrumbs'][] = $this->title;
$this->registerJsFile(Yii::$app->request->baseUrl . '/library/js/fi-notification.js', ['position' => View::POS_END, 'depends' => [\yii\web\JqueryAsset::class]]);
?>
<div class="container-fluid" data-aos="fade-up">
    <h1 class="text-center"><?= Html::encode($this->title) ?></h1>
    <hr class="bps" />
    <div class="card <?= ((!Yii::$app->user->isGuest && Yii::$app->user->identity->theme == 0) ? '' : 'bg-dark') ?>">
        <div class="card-body table-responsive p-0">
            <?php
            $layout = '
                        <div class="card-header ' . (!Yii::$app->user->isGuest ? Yii::$app->user->identity->themechoice : '') . '">
                            <div class="d-flex justify-content-between" style="margin-bottom: -0.3rem;">
                                <div class="p-2">
                                {toolbar}
                                {tandai}
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
                    [
                        'attribute' => 'link',
                        'value' => function ($model) {
                            return ucfirst($model->link);
                        },
                        'vAlign' => 'middle',
                    ],
                    [
                        'attribute' => 'message',
                        'format' => 'html',
                        'vAlign' => 'middle',
                    ],
                    [
                        'attribute' => 'created_at',
                        'value' => function ($model) {
                            $formatter = Yii::$app->formatter;
                            $formatter->locale = 'id-ID'; // set the locale to Indonesian
                            $timezone = new \DateTimeZone('Asia/Jakarta'); // create a timezone object for WIB
                            $waktumulai = new \DateTime($model->created_at, new \DateTimeZone('UTC')); // create a datetime object for waktumulai with UTC timezone
                            $waktumulai->setTimeZone($timezone); // set the timezone to WIB
                            $waktumulaiFormatted = $formatter->asDatetime($waktumulai, 'd MMMM Y, H:mm'); // format the waktumulai datetime value
                            return $waktumulaiFormatted . ' WIB';
                        },
                        'label' => 'Kiriman Notifikasi',
                        'format' => 'html',
                        'vAlign' => 'middle'
                    ],
                    // [
                    //     'header' => '',
                    //     'value' => function ($model) {
                    //         return Url::to([$model->link . '/view', 'id' => $model->link_id]);
                    //     }
                    // ],
                    [
                        'class' => ActionColumn::class,
                        'header' => '',
                        'contentOptions' => ['style' => 'text-align: center'],
                        'template' => (Yii::$app->user->isGuest || Yii::$app->user->identity->theme == 0)
                            ? '{view}'
                            : '{view}',
                        'buttons'  => [
                            // 'view' => function ($url, $model, $key) {
                            //     return Html::a('<i class="fas fa-eye"></i> Lihat', [$model->link . '/view', 'id' => $model->link_id], [
                            //         'title' => 'Lihat rincian notifikasi ini',
                            //         'class' => 'btn btn-primary btn-sm',
                            //     ]);
                            // },
                            'view' => function ($url, $model, $key) {
                                $viewUrl = Url::to([$model->link . '/view', 'id' => $model->link_id]);//localhost
                                $markAsReadUrl = Url::to(['notification/mark-as-read', 'id' => $model->id]);//localhost

                                // $viewUrl = Url::to(['../bengkulu/portalpintar/' . $model->link . '/view', 'id' => $model->link_id]);//webapps
                                // $markAsReadUrl = Url::to(['../bengkulu/portalpintar/notification/mark-as-read', 'id' => $model->id]);//webapps

                                return Html::a('<i class="fas fa-eye"></i> Lihat', '#', [
                                    'title' => 'Lihat rincian notifikasi ini',
                                    'class' => 'btn btn-primary btn-sm',
                                    'data-view-url' => $viewUrl,
                                    'data-mark-as-read-url' => $markAsReadUrl,
                                    'onclick' => 'handleMarkAsReadAndView(event, this)',
                                ]);
                            },
                        ],
                    ],
                    [
                        'class' => 'yii\grid\ActionColumn',
                        'template' => '{mark-as-read}',
                        'contentOptions' => ['style' => 'width: auto; vertical-align: middle; text-align: center; white-space: nowrap;'],
                        'buttons' => [
                            'mark-as-read' => function ($url, $model) {
                                return Html::a('<i class="fas fa-check"></i> Tandai Dibaca', $url, [
                                    'title' => Yii::t('app', 'Tandai Dibaca'),
                                    'class' => 'btn btn-success btn-sm',
                                    'style' => 'min-width: 110px; white-space: nowrap;', // Adjust min-width as needed
                                ]);
                            },
                        ],
                        'urlCreator' => function ($action, $model, $key, $index) {
                            if ($action === 'mark-as-read') {
                                return Url::to(['notification/mark-as-read', 'id' => $model->id]);
                            }
                            return '';
                        },
                        'visibleButtons' => [
                            'mark-as-read' => function ($model) {
                                return ($model->is_read ? false : true);
                            }
                        ]
                    ],
                ],
                'rowOptions' => function ($model) {
                    return [
                        'class' => $model->is_read ? 'table-secondary' : '',
                        // 'style' => $model->is_read ? 'color: #6C757D !important;' : ''
                    ];
                },
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
                'replaceTags' => [
                    '{tandai}' => function () {
                        return (!Yii::$app->user->isGuest) ? '
                            <div class="btn-group">
                                ' .
                            Html::a('<span class="btn btn-success ms-1 me-1"> <i class="fas fa-check"></i> Tandai Semua Dibaca</span>', ['notification/markallread'], ['title' => 'Klik untuk Tandai Semua Dibaca', 'data-pjax' => 0])
                            .
                            '
                            </div>
                        ' : '';
                    },
                ]
            ]); ?>
        </div>
    </div>
</div>