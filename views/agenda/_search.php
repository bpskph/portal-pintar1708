<?php

use yii\helpers\Html;
use kartik\form\ActiveForm;
use yii\helpers\ArrayHelper;
use kartik\daterange\DateRangePicker;
use app\models\Project;
use app\models\Rooms;
?>
<div class="card <?= ((!Yii::$app->user->isGuest && Yii::$app->user->identity->theme == 0) ? '' : 'bg-dark') ?>">
    <div class="card-body" style="margin: 0 auto!important">
        <?php
        $roomOptions = ArrayHelper::map(Rooms::find()->select(['id_rooms', 'nama_ruangan'])->all(), 'id_rooms', 'nama_ruangan');
        $roomOptions['other'] = 'Lainnya';
        $projectOptions = ArrayHelper::map(Project::find()->select(['id_project', 'panggilan_project'])->all(), 'id_project', 'panggilan_project');
        $projectOptions['other'] = 'Lainnya';
        ?>
        <?php
        $form = ActiveForm::begin([
            'action' => ['index?owner=&year=&nopage=0'],
            'method' => 'get',
            'type' => ActiveForm::TYPE_INLINE,
            'fieldConfig' => ['options' => ['class' => 'form-group']]
        ]);
        ?>
        <?= $form->field($model, 'kegiatan', ['autoPlaceholder' => false,])->textInput(['placeholder' => 'Kegiatan ...']) ?>
        <?= DateRangePicker::widget([
            'model' => $model,
            'attribute' => 'waktu',
            'convertFormat' => true,
            'pluginOptions' => [
                'locale' => [
                    'format' => 'd M Y',
                ],
                'opens' => 'left',
            ],
            'options' => [
                'class' => 'form-control me-2 ms-2',
                'placeholder' => 'Waktu ...'
            ],
        ]);
        ?>
        <?=
        $form->field($model, 'metode')->dropDownList([
            0 => 'Online',
            1 => 'Offline',
            2 => 'Hybrid'
        ], ['prompt' => 'Jenis ...'])
        ?>
        <?=
        $form->field($model, 'progress')->dropDownList([
            0 => 'Rencana',
            1 => 'Selesai',
            2 => 'Tunda',
            3 => 'Batal'
        ], ['prompt' => 'Progress ...'])
        ?>
        <?=
        $form->field($model, 'tempat')->dropDownList(
            $roomOptions,
            ['prompt' => 'Ruangan ...']
        )
        ?>
        <?=
        $form->field($model, 'pelaksana')->dropDownList(
            $projectOptions,
            ['prompt' => 'Pelaksana ...']
        )
        ?>
        <?= $form->field($model, 'reporter', ['autoPlaceholder' => false,])->textInput(['placeholder' => 'Pengusul ...']) ?>
        <?= $form->field($model, 'pemimpin', ['autoPlaceholder' => false,])->textInput(['placeholder' => 'Pemimpin ...']) ?>
        <?=
        $form->field($model, 'fk_kategori')->dropDownList([
            1 => 'Rapat, Pertemuan, dan Diskusi',
            2 => 'Pelatihan, Workshop, dan Bimbingan Teknis',
            3 => 'Rakor, Ratek, dan Bimtek',
            4 => 'Pleno, Rekon, FGD',
            5 => 'Jurassik dan BeKuda',
            6 => 'Evaluasi dan Pemeriksaan',
            7 => 'Uji Kompetensi',
            8 => 'Seminar dan Knowledge Sharing',
            9 => 'Narasumber, Kompetisi, dan Penghargaan Eksternal',
            10 => 'Lainnya',
        ], ['prompt' => 'Kategori ...'])
        ?>
        <div class="form-group">
            <?= Html::submitButton('Search', ['class' => 'btn btn-warning me-2']) ?>
            <?= Html::a('Reset', ['index?owner=&year=' . date("Y") . '&nopage=0'], ['class' => 'btn btn btn-outline-warning', 'style' => 'text-decoration:none']) ?>
        </div>
        <?php ActiveForm::end(); ?>
    </div>
</div>