<?php
use yii\bootstrap5\Modal;
use yii\helpers\Html;
?>
<style>
    .p-2 {
        margin-right: -0.5rem !important;
        margin-left: -0.5rem !important;
    }
    .cetak:hover {
        color: #fff !important;
    }
</style>
<?php 
$waktutampil = '';
$formatter = Yii::$app->formatter;
$formatter->locale = 'id-ID'; // set the locale to Indonesian
$timezone = new \DateTimeZone('Asia/Jakarta'); // create a timezone object for WIB
$waktutampil = new \DateTime($model->tanggal_suratrepoeks, new \DateTimeZone('UTC')); // create a datetime object for waktumulai with UTC timezone
$waktutampil->setTimeZone($timezone); // set the timezone to WIB
$waktutampil = $formatter->asDatetime($waktutampil, 'd MMMM Y'); // format the waktumulai datetime value
?>
<div class="card dashboard">
    <div class="card-body">
        <h5 class="card-title"><?= $model->agendae->kegiatan ?> <sup> <?= Html::a('<i class="fas fa-glasses"></i> Detail', ['suratrepoeks/' . $model->id_suratrepoeks], ['class' => 'cetak modalButton badge bg-secondary', 'data-pjax' => '0']) ?></sup></h5>
        <div class="activity">
            <div class="activity-item d-flex">
                <div class="activite-label">Kepada</div>
                <i class="bi bi-circle-fill activity-badge text-success align-self-start"></i>
                <div class="activity-content">
                    <?= $model->penerima_suratrepoeks ?>
                </div>
            </div><!-- End activity item-->
            <div class="activity-item d-flex">
                <div class="activite-label">Tanggal</div>
                <i class="bi bi-circle-fill activity-badge text-danger align-self-start"></i>
                <div class="activity-content">
                    <?= $waktutampil ?>
                </div>
            </div><!-- End activity item-->
            <div class="activity-item d-flex">
                <div class="activite-label">Perihal</div>
                <i class="bi bi-circle-fill activity-badge text-primary align-self-start"></i>
                <div class="activity-content">
                    <?= $model->perihal_suratrepoeks ?>
                </div>
            </div><!-- End activity item-->
            <div class="activity-item d-flex">
                <div class="activite-label">Subjek</div>
                <i class="bi bi-circle-fill activity-badge text-info align-self-start"></i>
                <div class="activity-content">
                    <?= $model->suratsubkodee->fk_suratkode . '-' . $model->suratsubkodee->rincian_suratsubkode ?>
                </div>
            </div><!-- End activity item-->
            <div class="activity-item d-flex">
                <div class="activite-label">Nomor</div>
                <i class="bi bi-circle-fill activity-badge text-warning align-self-start"></i>
                <div class="activity-content">
                    <?= $model->nomor_suratrepoeks ?>
                </div>
            </div><!-- End activity item-->
        </div>
    </div>
</div>
<?php
Modal::begin([
    'title' => '',
    'id' => 'modal',
    'size' => 'modal-lg'
]);
echo '<div id="modalContent"></div>';
Modal::end();
?>
<script>
    $(function() {
        // changed id to class
        $('.modalButton').click(function() {
            $.get($(this).attr('href'), function(data) {
                $('#modal').modal('show').find('#modalContent').html(data)
            });
            return false;
        });
    });
</script>