<?php
use app\models\Pengguna;
use app\models\Project;
use yii\helpers\Html;
// use yii\widgets\ActiveForm;
use yii\bootstrap5\ActiveForm;
use yii\helpers\ArrayHelper;
use kartik\select2\Select2;
use yii\web\View;

$this->registerJsFile(Yii::$app->request->baseUrl . '/library/js/fi-projectmember-form.js', ['position' => View::POS_END, 'depends' => [\yii\web\JqueryAsset::class]]); 
?>
<div class="card transparan alert">
    <div class="card-body table-responsive p-0">
        <?php $form = ActiveForm::begin([
            'layout' => 'horizontal',
            'fieldConfig' => [
                'errorOptions' => [
                    'encode' => false,
                    'class' => 'help-block'
                ],
            ],
        ]); ?>
        <?= $form->field($model, 'fk_project')->widget(Select2::classname(), [
            'data' => ArrayHelper::map(
                Project::find()->select('*, team.panggilan_team as namateam')->joinWith('teame')->where(['tahun'=>date("Y")])->andWhere(['aktif' => 1])->asArray()->all(),
                'id_project',
                function ($model) {
                    return $model['id_project'] . '. ' . $model['nama_project'] . ' [' . $model['panggilan_project'] .  ' | ' . $model['namateam'] . '] ';
                }
            ),
            'options' => ['placeholder' => 'Pilih Project'],
            'pluginOptions' => [
                'allowClear' => true
            ],
        ]);
        ?>
        <?php // $form->field($model, 'pegawai')->textInput(['maxlength' => true]) 
        ?>
        <?= $form->field($model, 'pegawai')->widget(Select2::classname(), [
            'data' => ArrayHelper::map(
                Pengguna::find()->select('*')->asArray()->all(),
                'username',
                function ($model) {
                    return $model['nama'] . ' [' . $model['username'] .  '@bps.go.id]';
                }
            ),
            'options' => ['placeholder' => 'Pilih Anggota'],
            'pluginOptions' => [
                'allowClear' => true
            ],
        ]);
        ?>
        <?php // $form->field($model, 'member_status')->textInput() 
        ?>
        <?= $form->field($model, 'member_status')->widget(Select2::classname(), [
            'data' => [1 => "Anggota", 2 => "Ketua", 3 => "Operator"],
            'options' => ['placeholder' => 'Status Anggota'],
            'pluginOptions' => [
                'allowClear' => true
            ],
        ]);
        ?>
        <div class="form-group">
            <?= Html::submitButton('Simpan', ['class' => 'btn btn btn-outline-warning']) ?>
        </div>
        <br/>
        <?php ActiveForm::end(); ?>        
    </div>
</div>
