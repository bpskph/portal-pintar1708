<?php

use app\models\Suratrepo;
use app\models\Suratrepoeks;
use yii\helpers\Html;
use yii\bootstrap5\ActiveForm;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use yii\web\View;

// Registering your custom JS and CSS files
$this->registerJsFile(Yii::$app->request->baseUrl . '/library/js/fi-zooms-form.js', ['position' => View::POS_END, 'depends' => [\yii\web\JqueryAsset::class]]);
?>
<div class="container" data-aos="fade-up">
    <div class="card alert <?= ((!Yii::$app->user->isGuest && Yii::$app->user->identity->theme == 0) ? 'bg-light' : 'bg-dark') ?>">
        <p>
            <a class="btn btn-outline-warning btn-sm" data-bs-toggle="collapse" href="#collapseExample" role="button" aria-expanded="false" aria-controls="collapseExample">
                Ketentuan Pengajuan Zoom (Click Me)
            </a>
        </p>
        <div class="collapse mb-2" id="collapseExample">
            <div class="card card-body <?= (!Yii::$app->user->isGuest && Yii::$app->user->identity->theme == 1) || Yii::$app->user->isGuest ? 'bg-dark text-light' : '' ?>">
                Dalam Sistem Portal Pintar, pegawai yang ingin mengajukan permohonan pemakaian Akun Zoom diharuskan:
                <ol>
                    <li>Menambahkan Agenda pada Portal Pintar dengan metode "Online". Jadwal pada agenda tersebut akan menjadi jadwal pemakaian zoom terkait.</li>
                    <li>Memastikan jadwal tidak bertabrakan dengan agenda lain yang membutuhkan fasilitas zoom (tersedia dua akun yang dapat digunakan bersamaan).</li>
                    <li>Menambahkan surat internal/eksternal sebagai rujukan kegiatan yang menggunakan zoom tersebut.</li>
                </ol>
            </div>
        </div>

        <?php $form = ActiveForm::begin([
            'layout' => 'horizontal',
            'fieldConfig' => [
                'horizontalCssClasses' => [
                    'label' => 'col-sm-2',
                    'wrapper' => 'col-sm-10',
                    'hint' => 'col-sm-offset-2 col-sm-10',
                ],
            ],
        ]); ?>
        <?= $form->errorSummary($model) ?>
        <?php
        if ($fk_agenda != '')
            echo $form->field($model, 'fk_agenda')->textInput(['value' => $fk_agenda, 'readonly' => true]);
        else
            echo $form->field($model, 'fk_agenda')->widget(Select2::class, [
                'data' => \yii\helpers\ArrayHelper::map(
                    \app\models\Agenda::find()->select('*')
                        ->joinWith('reportere')
                        ->where('progress = 0')
                        ->andWhere(['reporter' => Yii::$app->user->identity->username])
                        ->andWhere([
                            'or',
                            ['=', 'metode', 0],
                            ['=', 'metode', 2],
                        ])
                        ->all(),
                    'id_agenda',
                    function ($model) {
                        return $model['kegiatan'] . ' | diusulkan oleh : ' . $model['reportere']['nama'];
                    }
                ),
                'theme' => Select2::THEME_KRAJEE,
                'options' => [
                    'multiple' => false,
                    'placeholder' => 'Pilih Agenda (Jadwal Zoom) ...',
                ],
            ]);
        ?>

        <?=
        $form->field($model, 'jenis_zoom')->widget(Select2::class, [
            'data' => \yii\helpers\ArrayHelper::map(
                \app\models\Zoomstype::find()->all(),
                'id_zoomstype',
                function ($model) {
                    return $model['nama_zoomstype'] . ' | Kuota:  ' . $model['kuota'];
                }
            ),
            'theme' => Select2::THEME_KRAJEE,
            'options' => [
                'multiple' => false,
                'placeholder' => 'Pilih Jenis Zoom ...',
            ],
        ]); ?>

        <?php
        // Fetch data from Suratrepo
        $dataSuratrepo = ArrayHelper::map(
            Suratrepo::find()->select('*')
                ->where(['>=', 'tanggal_suratrepo', date("Y-01-01")])
                // ->andWhere(['owner' => Yii::$app->user->identity->username])
                ->orderBy('nomor_suratrepo')->asArray()->all(),
            function ($model) {
                return '0-' . $model['id_suratrepo'];
            },
            function ($model) {
                return '[INTERNAL] ' . $model['nomor_suratrepo'] . ' | ' . $model['perihal_suratrepo'];
            }
        );

        // Fetch data from Suratrepoeks
        $dataSuratrepoeks = ArrayHelper::map(
            Suratrepoeks::find()->select('*')
            ->joinWith('sharedtomembere')
            ->where(['>=', 'tanggal_suratrepoeks', date("Y-01-01")])
            ->andWhere(['owner' => Yii::$app->user->identity->username])
            ->orWhere(['approver' => Yii::$app->user->identity->username])
            ->orWhere(['approver' => Yii::$app->user->identity->username])
            ->orWhere(['projectmember.member_status' => 3, 'projectmember.pegawai' => Yii::$app->user->identity->username])
            ->orderBy('nomor_suratrepoeks')->asArray()->all(),
            function ($model) {
                return '1-' . $model['id_suratrepoeks'];
            },
            function ($model) {
                return '[EKSTERNAL] ' . $model['nomor_suratrepoeks'] . ' | ' . $model['perihal_suratrepoeks'];
            }
        );

        // Merge the two arrays
        $dataCombined = $dataSuratrepo + $dataSuratrepoeks;

        echo $form->field($model, 'fk_surat')->widget(Select2::classname(), [
            'name' => 'fk_surat',
            'data' => $dataCombined,
            'options' => ['placeholder' => 'Pilih Nomor Surat Terkait'],
            'pluginOptions' => [
                'allowClear' => true
            ],
        ]);

        ?>
        <div class="form-group text-end mb-3">
            <?= Html::submitButton('<i class="fas fa-save"></i> Simpan', ['class' => 'btn btn btn-outline-warning']) ?>
        </div>
        <?php ActiveForm::end(); ?>
    </div>
</div>