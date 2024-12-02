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
$waktutampil = new \DateTime($model->tanggal_suratrepo, new \DateTimeZone('UTC')); // create a datetime object for waktumulai with UTC timezone
$waktutampil->setTimeZone($timezone); // set the timezone to WIB
$waktutampil = $formatter->asDatetime($waktutampil, 'd MMMM Y'); // format the waktumulai datetime value
?>
<div class="card dashboard <?= ((!Yii::$app->user->isGuest && Yii::$app->user->identity->theme == 0) ? 'bg-light' : 'bg-dark') ?>">
    <div class="card-body">
        <h5 class="card-title <?= ((!Yii::$app->user->isGuest && Yii::$app->user->identity->theme == 0) ? '' : 'text-light') ?>" >
            <?= $model->agendae->kegiatan ?> 
            <sup><?= Html::a('<i class="fas fa-glasses"></i> Detail', ['suratrepo/' . $model->id_suratrepo], ['class' => 'cetak modalButton badge bg-secondary', 'data-pjax' => '0']) ?></sup>
        </h5>
        <div class="activity">
            <table class="table table-bordered table-striped <?= ((!Yii::$app->user->isGuest && Yii::$app->user->identity->theme == 0) ? 'table-light' : 'table-dark') ?>">
                <tbody>
                    <tr>
                        <th><i class="bi bi-circle-fill text-success"></i> Kepada</th>
                        <td><?= $model->penerima_suratrepo ?></td>
                    </tr>
                    <tr>
                        <th><i class="bi bi-circle-fill text-danger"></i> Tanggal</th>
                        <td><?= $waktutampil ?></td>
                    </tr>
                    <tr>
                        <th><i class="bi bi-circle-fill text-primary"></i> Perihal</th>
                        <td><?= $model->perihal_suratrepo ?></td>
                    </tr>
                    <tr>
                        <th><i class="bi bi-circle-fill text-info"></i> Subjek</th>
                        <td><?= $model->suratsubkodee->fk_suratkode . '-' . $model->suratsubkodee->rincian_suratsubkode ?></td>
                    </tr>
                    <tr>
                        <th><i class="bi bi-circle-fill text-warning"></i> Nomor</th>
                        <td><?= $model->nomor_suratrepo ?></td>
                    </tr>
                </tbody>
            </table>
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