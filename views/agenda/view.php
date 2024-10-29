<?php

use yii\helpers\Html;
use kartik\detail\DetailView;
use app\controllers\LinkColumnAgendaView;
use app\models\Pengguna;

/** @var yii\web\View $this */
/** @var app\models\Agenda $model */
$this->title = 'Detail Agenda # ' . $model->id_agenda;
\yii\web\YiiAsset::register($this);
?>
<style>
    .p-2 {
        margin-right: -0.5rem !important;
        margin-left: -0.5rem !important;
    }
</style>
<div class="container-fluid" data-aos="fade-up">
    <h1><?= Html::encode($this->title) ?></h1>
    <div class="d-flex justify-content-between">
        <div class="p-2">
            <?php if (!Yii::$app->user->isGuest && $model->reporter === Yii::$app->user->identity->username && $model->progress != 1) : ?>
                <?= Html::a('<i class="fas fa-edit"></i> Update', ['update', 'id' => $model->id_agenda], ['class' => 'btn btn-sm btn-warning']) ?>
                <?= Html::a('<i class="fas fa-trash"></i> Hapus', ['delete', 'id' => $model->id_agenda], [
                    'class' => 'btn btn-sm btn-danger',
                    'data' => [
                        'confirm' => 'Anda yakin akan menghapus agenda ini dari sistem?',
                        'method' => 'post',
                        'bs-toggle'=>'modal',
                        'bs-dismiss'=>'modal'
                    ],
                ]) ?>
            <?php endif; ?>
        </div>
        <div class="p-2">
        <h5>
                <?php if ($model->progress == 1 && !isset($model->laporane->id_laporan)) { ?>
                    <span class="badge bg-danger"><i class="fas fa-exclamation"></i> Belum Ada Laporan</span>
                <?php } elseif ($model->progress == 1 && isset($model->laporane->id_laporan)) { ?>
                    <?= Html::a('<i class="fas fa-file"></i> Lihat Laporan', ['laporan/view', 'id' => $model->laporane->id_laporan], ['class' => 'btn btn-sm btn-success']) ?>                    
                <?php } ?>                
            </h5>
        </div>
    </div>
    <?php
    $formatter = Yii::$app->formatter;
    $formatter->locale = 'id-ID'; // set the locale to Indonesian
    $timezone = new \DateTimeZone('Asia/Jakarta'); // create a timezone object for WIB
    $waktumulai = new \DateTime($model->waktumulai, new \DateTimeZone('UTC')); // create a datetime object for waktumulai with UTC timezone
    $waktumulai->setTimeZone($timezone); // set the timezone to WIB
    $waktumulaiFormatted = $formatter->asDatetime($waktumulai, 'd MMMM Y, H:mm'); // format the waktumulai datetime value
    $waktuselesai = new \DateTime($model->waktuselesai, new \DateTimeZone('UTC')); // create a datetime object for waktuselesai with UTC timezone
    $waktuselesai->setTimeZone($timezone); // set the timezone to WIB
    $waktuselesaiFormatted = $formatter->asDatetime($waktuselesai, 'H:mm'); // format the waktuselesai time value only
    if ($waktumulai->format('Y-m-d') === $waktuselesai->format('Y-m-d')) {
        // if waktumulai and waktuselesai are on the same day, format the time range differently
        $waktumulaiFormatted = $formatter->asDatetime($waktumulai, 'd MMMM Y, H:mm'); // format the waktumulai datetime value with the year and time
        $waktu = $waktumulaiFormatted . ' - ' . $waktuselesaiFormatted . ' WIB'; // concatenate the formatted dates
    } else {
        // if waktumulai and waktuselesai are on different days, format the date range normally
        $waktuselesaiFormatted = $formatter->asDatetime($waktuselesai, 'd MMMM Y, H:mm'); // format the waktuselesai datetime value
        $waktu =  $waktumulaiFormatted . ' WIB s.d ' . $waktuselesaiFormatted . ' WIB'; // concatenate the formatted dates
    }
    ?>
    <?php
    if ($model->waktumulai_tunda != NULL && $model->waktuselesai_tunda) {
        $formatter = Yii::$app->formatter;
        $formatter->locale = 'id-ID'; // set the locale to Indonesian
        $timezone = new \DateTimeZone('Asia/Jakarta'); // create a timezone object for WIB
        $waktumulai_tunda = new \DateTime($model->waktumulai_tunda, new \DateTimeZone('UTC')); // create a datetime object for waktumulai_tunda with UTC timezone
        $waktumulai_tunda->setTimeZone($timezone); // set the timezone to WIB
        $waktumulai_tundaFormatted = $formatter->asDatetime($waktumulai_tunda, 'd MMMM Y, H:mm'); // format the waktumulai_tunda datetime value
        $waktuselesai_tunda = new \DateTime($model->waktuselesai_tunda, new \DateTimeZone('UTC')); // create a datetime object for waktuselesai_tunda with UTC timezone
        $waktuselesai_tunda->setTimeZone($timezone); // set the timezone to WIB
        $waktuselesai_tundaFormatted = $formatter->asDatetime($waktuselesai_tunda, 'H:mm'); // format the waktuselesai_tunda time value only
        if ($waktumulai_tunda->format('Y-m-d') === $waktuselesai_tunda->format('Y-m-d')) {
            // if waktumulai_tunda and waktuselesai_tunda are on the same day, format the time range differently
            $waktumulai_tundaFormatted = $formatter->asDatetime($waktumulai_tunda, 'd MMMM Y, H:mm'); // format the waktumulai_tunda datetime value with the year and time
            $waktu_tunda = $waktumulai_tundaFormatted . ' - ' . $waktuselesai_tundaFormatted . ' WIB'; // concatenate the formatted dates
        } else {
            // if waktumulai_tunda and waktuselesai_tunda are on different days, format the date range normally
            $waktuselesai_tundaFormatted = $formatter->asDatetime($waktuselesai_tunda, 'd MMMM Y, H:mm'); // format the waktuselesai_tunda datetime value
            $waktu_tunda =  $waktumulai_tundaFormatted . ' WIB s.d ' . $waktuselesai_tundaFormatted . ' WIB'; // concatenate the formatted dates
        }
    } else {
        $waktu_tunda = '-';
    }
    ?>
    <?php
    // Step 1: Get the list of email addresses from the peserta attribute in the agenda table
    $emailList = explode(', ', $model->peserta);
    // Step 2: Extract the username (without "@bps.go.id") from each email address
    $usernames = [];
    foreach ($emailList as $email) {
        $username = substr($email, 0, strpos($email, '@'));
        $usernames[] = $username;
    }
    // Step 3: Query the pengguna table for the list of names that correspond to the extracted usernames
    $names = Pengguna::find()
        ->select('nama')
        ->where(['in', 'username', $usernames])
        ->column();
    // Step 4: Convert the list of names to a string in the format that can be used for autofill
    // $autofillString = implode('<br> ', $names);
    $listItems = '';
    foreach ($names as $key => $name) {
        $listItems .= '<li>' .  ' ' . $name . '</li>';
    }
    $autofillString = '<b>Peserta Kegiatan :</b> <ol>' . $listItems . '</ol>';
    // print_r($autofillString);
    // Step 5: Set the content of the editor using the html option
    ?>
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
                'attribute' => 'id_agenda',
                'label' => 'ID Agenda',
            ],
            'kegiatan:ntext',
            [
                'attribute' => 'fk_kategori',
                'value' => $model->kategorie->nama_kategori,
                'label' => 'Kategori Kegiatan'
            ],
            [
                'attribute' => 'waktumulai',
                'value' => $waktu,
                'label' => 'Waktu'
            ],
            [
                'attribute' => 'waktumulai_tunda',
                'value' => $waktu_tunda,
                'label' => 'Waktu Penundaan'
            ],
            [
                'attribute' => 'metode',
                'value' => $model->metode == 0 ? '<span title="Online" class="badge bg-primary rounded-pill"><i class="fas fa-signal"></i> Online</span>' : ($model->metode == 1 ? '<span title="Offline" class="badge bg-success rounded-pill"><i class="fas fa-warehouse"></i> Offline</span>' : '<span title="Hybrid" class="badge bg-secondary rounded-pill"><i class="fab fa-mix"></i> Hybrid</span>'),
                'format' => 'html',
                'label' => 'Jenis Agenda',
            ],
            // 'progress',
            [
                'attribute' => 'progress',
                'value' => $model->progress == 0 ? '<span title="Rencana" class="badge bg-primary rounded-pill"><i class="fas fa-plus-square"></i> Rencana</span>' : ($model->progress == 1 ? '<span title="Selesai" class="badge bg-success rounded-pill"><i class="fas fa-check"></i> Selesai</span>' : ($model->progress == 2 ? '<span title="Tunda" class="badge bg-secondary rounded-pill"><i class="fas fa-strikethrough"></i> Tunda</span>' : ($model->progress == 3 ? '<span title="Batal" class="badge bg-danger rounded-pill"><i class="fas fa-trash-alt"></i> Batal</span>' : ''))),
                'format' => 'html',
                'label' => 'Progress',
            ],
            [
                'attribute' => 'tempat',
                'value' => $model->tempate,
            ],
            [
                'attribute' => 'pelaksana',
                'value' => $model->pelaksanalengkape,
            ],
            [
                'attribute' => 'pemimpin',
                'value' => $model->pemimpine->nama,
                'label' => 'Pemimpin Rapat/Kegiatan'
            ],
            [
                'attribute' => 'id_lanjutan',
                'value' => $model->lanjutan,
                'label' => 'Lanjutan Dari'
            ],
            [
                'attribute' => 'reporter',
                'value' => $model->reportere->nama,
                'label' => 'Pengusul'
            ],
            [
                'attribute' => 'timestamp',
                'value' => \Yii::$app->formatter->asDatetime(strtotime($model->timestamp), "d MMMM y 'pada' H:mm a"),
                'label' => 'Diusulkan'
            ],
            [
                'attribute' => 'timestamp_lastupdate',
                'value' => \Yii::$app->formatter->asDatetime(strtotime($model->timestamp_lastupdate), "d MMMM y 'pada' H:mm a"),
                'label' => 'Terakhir Diupdate'
            ],
        ],
    ]) ?>
    <br />
    <div class="card <?= ((!Yii::$app->user->isGuest && Yii::$app->user->identity->theme == 0) ? '' : 'bg-dark text-light') ?>">
        <div class="card-body">
            <?php echo $autofillString ?>
            <b>Peserta Tambahan :</b>
            <?php echo $model->peserta_lain != null ? $model->peserta_lain : '-' ?>
        </div>
    </div>
</div>
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
        </div>
    </div>
</div>
<script>
    $(document).on('click', '.modalButtonAgenda', function(e) {
        e.preventDefault();
        var url = $(this).data('url');
        var modal = $('#myModal');
        modal.find('.modal-content').load(url, function() {
            modal.modal('show');
        });
    });
</script>