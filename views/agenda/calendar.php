<?php

use yii\web\View;
use yii\widgets\ListView;
use yii\widgets\Pjax;

$eventsKalender = json_encode($eventsKalender, JSON_UNESCAPED_UNICODE);
$script = <<< JS
    var eventsKalender = $eventsKalender;
JS;
$this->registerJs($script, \yii\web\View::POS_HEAD);
$this->registerJsFile('https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js', ['position' => \yii\web\View::POS_END, 'depends' => [\yii\web\JqueryAsset::class]]);
$this->registerJsFile(Yii::$app->request->baseUrl . '/library/js/fi-calendar.js', ['position' => \yii\web\View::POS_END, 'depends' => [\yii\web\JqueryAsset::class]]);
$this->title = 'AGENDA PLANS';
?>
<style>
    .fc-h-event {
        background-color: #cda45e;
        border: 1px solid #b89354;
        display: block;
    }

    .fc .fc-event-title-container {
        white-space: normal !important;
    }

    .fc .fc-event {
        padding: 0 1px !important;
        white-space: normal !important;
    }

    .fc .fc-event-time {
        display: none;
    }

    #event-details {
        opacity: 0;
        transition: opacity 0.5s ease-in-out;
    }

    #event-details.show {
        opacity: 1;
    }

    .table.text-light> :not(caption)>*>* {
        color: #fff;
    }
</style>

<div class="container-fluid row" data-aos="fade-up">
    <div class="section-header">
        <h2>KALENDER AGENDA</h2>
    </div>

    <div class="col-lg-8">
        <div class="card <?= ((!Yii::$app->user->isGuest && Yii::$app->user->identity->theme == 0) ? '' : 'bg-dark') ?>">
            <div class="card-body">
                <div id="calendar"></div>
            </div>
        </div>
        <hr />
    </div>

    <div class="col-lg-4">
        <div class="card <?= ((!Yii::$app->user->isGuest && Yii::$app->user->identity->theme == 0) ? '' : 'bg-dark text-light') ?>" id="event-details">
            <div class="card-body">
                <table class="table <?= ((!Yii::$app->user->isGuest && Yii::$app->user->identity->theme == 0) ? '' : 'text-light') ?>">
                    <tbody>
                        <tr>
                            <td><i class="fas fa-wave-square"></i> Project</td>
                            <td>:</td>
                            <td><span id="project"></span></td>
                        </tr>
                        <tr>
                            <td><i class="fas fa-asterisk"></i> Kegiatan</td>
                            <td>:</td>
                            <td><span id="kegiatan"></span></td>
                        </tr>
                        <tr>
                            <td><i class="fas fa-user"></i> Dipimpin</td>
                            <td>:</td>
                            <td><span id="leader"></span></td>
                        </tr>
                        <tr>
                            <td><i class="fas fa-user"></i> Diusulkan</td>
                            <td>:</td>
                            <td><span id="reporter"></span></td>
                        </tr>
                        <tr>
                            <td><i class="far fa-clock"></i> Waktu</td>
                            <td>:</td>
                            <td><span id="waktu"></span></td>
                        </tr>
                        <tr>
                            <td><i class="fas fa-link"></i> Detail</td>
                            <td>:</td>
                            <td><span id="detail"></span></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>