<?php
use yii\helpers\Html;
use kartik\detail\DetailView;
use app\models\Pengguna;
$this->title = 'Detail Agenda # ' . $model->id_agendapimpinan;
\yii\web\YiiAsset::register($this);
?>
<style>
    .p-2 {
        margin-right: -0.5rem !important;
        margin-left: -0.5rem !important;
    }
    ol {
        padding-left: 1rem;
        margin-bottom: 0rem;
    }
</style>
<div class="container-fluid" data-aos="fade-up">
    <h1><?= Html::encode($this->title) ?></h1>
    <div class="d-flex justify-content-between">
        <div class="p-2">
            <?php
            $homeUrl = ['index'];
            echo Html::a('<i class="fas fa-home"></i> Agenda Utama Pimpinan', $homeUrl, ['class' => 'btn btn btn-outline-warning btn-sm']);
            ?>
            <?php if (!Yii::$app->user->isGuest && $model->reporter === Yii::$app->user->identity->username) : ?>
                |
                <?= Html::a('<i class="fas fa-edit"></i> Update', ['update', 'id' => $model->id_agendapimpinan], ['class' => 'btn btn-sm btn-warning']) ?>
                <?= Html::a('<i class="fas fa-trash"></i> Hapus', ['delete', 'id' => $model->id_agendapimpinan], [
                    'class' => 'btn btn-sm btn-danger',
                    'data' => [
                        'confirm' => 'Anda yakin akan menghapus agenda pimpinan ini dari sistem?',
                        'method' => 'post',
                    ],
                ]) ?>
            <?php endif; ?>
        </div>
        <div class="p-2">
        </div>
    </div>
    <?php
    $formatter = Yii::$app->formatter;
    $formatter->locale = 'id-ID'; // set the locale to Indonesian
    $timezone = new \DateTimeZone('Asia/Jakarta'); // create a timezone object for WIB
    $waktumulai = new \DateTime($model->waktumulai, new \DateTimeZone('Asia/Jakarta')); // create a datetime object for waktumulai with UTC timezone
    $waktumulai->setTimeZone($timezone); // set the timezone to WIB
    $waktumulaiFormatted = $formatter->asDatetime($waktumulai, 'd MMMM Y, H:mm'); // format the waktumulai datetime value
    $waktuselesai = new \DateTime($model->waktuselesai, new \DateTimeZone('Asia/Jakarta')); // create a datetime object for waktuselesai with UTC timezone
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
    // Step 1: Get the list of email addresses from the peserta attribute in the agenda table
    $emailList = explode(', ', $model->pendamping);
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
    $autofillString = '<ol>' . $listItems . '</ol>';
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
                'attribute' => 'id_agendapimpinan',
                'label' => 'ID Agenda',
            ],
            'kegiatan:ntext',
            [
                'attribute' => 'waktumulai',
                'value' => $waktu,
                'label' => 'Waktu'
            ],
            'tempat',
            [
                'attribute' => 'pendamping',
                'value' => ($model->pendamping == null ? '-' : $autofillString),
                'format' => 'html'
            ],
            [
                'attribute' => 'pendamping_lain',
                'value' => ($model->pendamping_lain == null ? '-' : $model->pendamping_lain)
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
                'attribute' => 'timestamp_agendapimpinan_lastupdate',
                'value' => \Yii::$app->formatter->asDatetime(strtotime($model->timestamp_agendapimpinan_lastupdate), "d MMMM y 'pada' H:mm a"),
                'label' => 'Terakhir Diupdate'
            ],
        ],
    ]) ?>
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