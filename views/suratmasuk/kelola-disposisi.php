<?php

use app\models\Suratmasukdisposisi;
use yii\helpers\Html;
use yii\bootstrap5\ActiveForm;
use kartik\date\DatePicker;
use kartik\select2\Select2;
?>
<?php
$script = <<< JS
$(document).ready(function() {
    $('#kelola-disposisi-id').on('beforeSubmit', function() {
        // Show loading overlay
        $('#loading-overlay').show();
        // Disable all buttons to prevent multiple clicks
        $('button, input[type="submit"]').prop('disabled', true);
    });
});
JS;
$this->registerJs($script);
?>

<!-- Loading Overlay -->
<div id="loading-overlay" style="display: none; position: fixed; width: 100%; height: 100%; top: 0; left: 0; background: rgba(0, 0, 0, 0.5); z-index: 9999; text-align: center;">
    <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); color: white; font-size: 48px;">
        <div class="spinner-border text-light" role="status">
            <span class="sr-only">Loading...</span>
        </div>
        <p>Memproses disposisi<br/>Mohon tunggu...</p>
    </div>
</div>
<div class="container-fluid" data-aos="fade-up">
    <div class="card alert <?= ((!Yii::$app->user->isGuest && Yii::$app->user->identity->theme == 0) ? 'bg-light' : 'bg-dark') ?>">
        <?php if ($model): ?>
            <?php $form = ActiveForm::begin([
                'id' => 'kelola-disposisi-id',
                'layout' => 'horizontal',
                'fieldConfig' => [
                    'horizontalCssClasses' => [
                        'label' => 'col-sm-3',
                        'wrapper' => 'col-sm-9',
                        'hint' => 'col-sm-offset-3 col-sm-9',
                    ],
                ],
            ]); ?>
            <div class="row">
                <div class="col-lg-6">
                    <?= $header; ?>
                    <?= $form->errorSummary($model) ?>
                    <?= $form->field($model, 'tanggal_disposisi')->textInput(['maxlength' => true, 'value' => date('Y-m-d'), 'readonly' => true])
                        ->hint('Hari ini: ' . Yii::$app->formatter->asDatetime(strtotime("today"), "d MMMM y"), ['class' => '', 'style' => 'color: #999']) ?>
                    <?php if (Yii::$app->user->identity->issuratmasukpejabat && empty($disposisisatu_penerima)): ?>
                        <?=
                        $form->field($model, 'tujuan_disposisi_team')->widget(Select2::class, [
                            'data' => \yii\helpers\ArrayHelper::map(
                                \app\models\Teamleader::find()->joinWith(['teame', 'penggunae', 'projecte'])->where('leader_status = 1')->andWhere('tahun = ' . date("Y"))->all(),
                                'fk_team',
                                function ($model) {
                                    return $model['teame']['nama_team'] . ' [Ketua: ' . $model['penggunae']['nama'] .  ']';
                                }
                            ),
                            'theme' => Select2::THEME_KRAJEE,
                            'options' => [
                                'multiple' => false,
                                'placeholder' => 'Pilih Tim Kerja ...',
                                'value' => $model->isNewRecord ? '' : $model->tujuan_disposisi_team,
                            ],
                        ])->label('Disposisi Utama'); ?>

                        <?php
                        $tujuanDisposisiTeamLain = [];
                        if (!$model->isNewRecord) {
                            $tujuanDisposisiTeamLain = array_column($disposisilain, 'tujuan_disposisi_team'); // Replace 'tujuan_disposisi_team' with the actual column name
                        }

                        ?>
                        <?=
                        $form->field($model, 'tujuan_disposisi_team_lain')->widget(Select2::class, [
                            'data' => \yii\helpers\ArrayHelper::map(
                                \app\models\Teamleader::find()->joinWith(['teame', 'penggunae', 'projecte'])->where('leader_status = 1')->andWhere('tahun = ' . date("Y"))->all(),
                                'fk_team',
                                function ($model) {
                                    return $model['teame']['nama_team'] . ' [Ketua: ' . $model['penggunae']['nama'] .  ']';
                                }
                            ),
                            'theme' => Select2::THEME_KRAJEE,
                            'options' => [
                                'multiple' => true,
                                'placeholder' => 'Pilih Tim Kerja ...',
                                'value' => $model->isNewRecord ? '' : $tujuanDisposisiTeamLain, // Set the pre-selected values
                            ],
                        ])->label('Disposisi Lainnya'); ?>
                    <?php else: ?>
                        <?php $team = \app\models\Teamleader::find()
                            ->joinWith('projecte')
                            ->where([
                                'leader_status' => 1,
                                'nama_teamleader' => Yii::$app->user->identity->username,
                                'project.tahun' => date("Y")
                            ])
                            ->one()
                        ?>
                        <?= $form->field($model, 'tujuan_disposisi_team')->hiddenInput(['value' => $team->fk_team])->label(false) ?>
                        <?php
                        $projectMembers = \app\models\Projectmember::find()
                            ->joinWith(['projecte', 'penggunae'])
                            ->where([
                                'fk_team' => $team->fk_team,
                                'tahun' => date("Y"),
                            ])
                            ->andWhere(['NOT', ['pegawai' => Yii::$app->user->identity->username]])
                            ->andWhere(['NOT', ['member_status' => 0]])
                            ->orderBy(['pegawai' => SORT_ASC])
                            ->all();

                        // Map the data to Select2 format
                        $data = \yii\helpers\ArrayHelper::map($projectMembers, 'pegawai', function ($model) {
                            return $model->penggunae->nama;
                        });

                        // Add the current user's username if not already in the array
                        $currentUsername = Yii::$app->user->identity->username;
                        $data = array_merge(
                            [$currentUsername => 'Dikerjakan di Ketua Tim [' . Yii::$app->user->identity->nama . ']'],
                            $data
                        );
                        ?>
                        <?=
                        $form->field($model, 'tujuan_disposisi_pegawai')->widget(Select2::class, [
                            'data' => $data,
                            'theme' => Select2::THEME_KRAJEE,
                            'options' => [
                                'multiple' => false,
                                'placeholder' => 'Pilih Pegawai di Tim Anda ...',
                                'value' => $model->isNewRecord ? '' : $model->tujuan_disposisi_pegawai,
                            ],
                        ])->label('Disposisi dalam Tim'); ?>

                    <?php endif; ?>
                    <?= $form->field($model, 'instruksi')->textarea(['rows' => 6, 'value' => $model->isNewRecord ? '' : $model->instruksi]) ?>

                    <div class="form-group text-end mb-3">
                        <?= Html::submitButton('<i class="fas fa-save"></i> Simpan', ['class' => 'btn btn btn-outline-warning']) ?>
                    </div>
                </div>
                <div class="col-lg-6">
                    <?php if (file_exists(Yii::getAlias('@webroot/surat/masuk/' . $suratmasuk->id_suratmasuk . '.pdf'))) : ?>
                        <iframe id="pdf-iframe" src="<?= Yii::getAlias('@web') ?>/surat/masuk/<?php echo $suratmasuk->id_suratmasuk ?>.pdf" width="100%" height="800px"></iframe>
                    <?php else: ?>
                        <div id="pdf-container container" data-aos="fade-up">
                            <h5 class="text-center mt-2 mb-2"><em>Berkas PDF belum tersedia (belum diunggah oleh penginput surat atau terhapus).<br />Jika berkas sudah diupload namun belum tampil, mohon lakukan clear cache pada browser Anda, atau lihat melalui Moda Privasi (Incognito). Terima kasih.</em></h5>
                        </div>
                    <?php endif; ?>
                    <br />

                </div>
            </div>
            <div class="row">
                <div class="col-lg-6">

                </div>
                <div class="col-lg-6 mt-2">
                    <h5>Instruksi dari Pimpinan: </h5>
                    <?php if ($level == 1): ?>
                        <span class="fst-italic">Belum ada instruksi sebelumnya ...</span>
                    <?php else: ?>
                        <div class="alert alert-dark">
                            <strong><?= $disposisisatu->pemberie->nama ?></strong>
                            <br />
                            <?= $disposisisatu->instruksi ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            <?php ActiveForm::end(); ?>
        <?php else: ?>
            <p>Disposisi tidak ditemukan atau Anda tidak memiliki izin untuk mengeditnya.</p>
        <?php endif; ?>
    </div>
</div>