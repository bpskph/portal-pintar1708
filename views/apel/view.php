<?php
use app\models\Pengguna;
use yii\helpers\Html;
use kartik\detail\DetailView;
/** @var yii\web\View $this */
/** @var app\models\Linkmat $model */
$this->title = 'Detail Apel # ' . $model->id_apel;
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
<div class="container" data-aos="fade-up">
    <h1><?= Html::encode($this->title) ?></h1>
    <div class="d-flex justify-content-between">
        <div class="p-2">
            <?php if (!Yii::$app->user->isGuest && $model->reporter === Yii::$app->user->identity->username) : ?>
                <?= Html::a('<i class="fas fa-edit"></i> Update', ['update', 'id' => $model->id_apel], ['class' => 'btn btn-sm btn-warning']) ?>
                <?= Html::a('<i class="fas fa-trash"></i> Hapus', ['delete', 'id' => $model->id_apel], [
                    'class' => 'btn btn-sm btn-danger',
                    'data' => [
                        'confirm' => 'Anda yakin akan menghapus apel ini dari sistem?',
                        'method' => 'post',
                    ],
                ]) ?>
            <?php endif; ?>
        </div>
        <div class="p-2">
        </div>
    </div>
    <?php
    if ($model->bendera != null) {
        // Step 1: Get the list of email addresses from the peserta attribute in the agenda table
        $emailList = explode(', ', $model->bendera);
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
    } else {
        $autofillString = '-';
    }
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
                'attribute' => 'id_apel',
                'label' => 'ID Apel/Upacara',
            ],
            [
                'attribute' => 'jenis_apel',
                'value' => $model->jenis_apel == 0 ? '<span title="Apel" class="badge bg-primary rounded-pill"><i class="far fa-flag"></i> Apel</span>' : ($model->jenis_apel == 1 ? '<span title="Upacara" class="badge bg-success rounded-pill"><i class="fas fa-flag"></i> Upacara</span>' : ''),
                'format' => 'html',
                'label' => 'Jenis Apel/Upacara',
            ],
            [
                'attribute' => 'tanggal_apel',
                'value' => \Yii::$app->formatter->asDatetime(strtotime($model->tanggal_apel), "d MMMM y"),
            ],
            [
                'attribute' => 'pembina_inspektur',
                'value' => $model->getPetugase($model->pembina_inspektur),
            ],
            [
                'attribute' => 'perwira',
                'value' => $model->getPetugase($model->perwira),
            ],
            [
                'attribute' => 'pemimpin_komandan',
                'value' => $model->getPetugase($model->pemimpin_komandan),
            ],
            [
                'attribute' => 'mc',
                'value' => $model->getPetugase($model->mc),
            ],
            [
                'attribute' => 'uud',
                'value' => $model->getPetugase($model->uud),
            ],
            [
                'attribute' => 'korpri',
                'value' => $model->getPetugase($model->korpri),
            ],
            [
                'attribute' => 'doa',
                'value' => $model->getPetugase($model->doa),
            ],
            [
                'attribute' => 'ajudan',
                'value' => $model->getPetugase($model->ajudan),
            ],
            [
                'attribute' => 'operator',
                'value' => $model->getPetugase($model->operator),
            ],
            [
                'attribute' => 'bendera',
                'value' => $autofillString,
                'format' => 'html'
            ],
            [
                'attribute' => 'tambahsatu_petugas',
                'value' => $model->getPetugase($model->tambahsatu_petugas),
                'label'=> $model->tambahsatu_text
            ],
            [
                'attribute' => 'tambahdua_petugas',
                'value' => $model->getPetugase($model->tambahdua_petugas),
                'label'=> $model->tambahdua_text
            ],
            [
                'attribute' => 'reporter',
                'value' => $model->reportere->nama,
            ],
            [
                'attribute' => 'timestamp',
                'value' => \Yii::$app->formatter->asDatetime(strtotime($model->timestamp), "d MMMM y 'pada' H:mm a"),
            ],
            [
                'attribute' => 'timestamp_apel_lastupdate',
                'value' => \Yii::$app->formatter->asDatetime(strtotime($model->timestamp_apel_lastupdate), "d MMMM y 'pada' H:mm a"),
            ],
        ],
    ]) ?>
</div>