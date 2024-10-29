<?php

use app\models\Pengguna;
use yii\helpers\Html;
use kartik\detail\DetailView;
use yii\bootstrap5\ActiveForm;
use yii\web\View;

$this->title = 'Detail Data SK # ' . $model->id_sk;
\yii\web\YiiAsset::register($this);
$this->registerJsFile(Yii::$app->request->baseUrl . '/library/js/fi-suratrepo-lihatscan.js', ['position' => View::POS_END, 'depends' => [\yii\web\JqueryAsset::class]]);
?>
<div class="container" data-aos="fade-up">
    <h1><?= Html::encode($this->title) ?></h1>
    <div class="d-flex justify-content-between">
        <div class="p-2">
            <?php if (!Yii::$app->user->isGuest && $model->reporter === Yii::$app->user->identity->username && $model->deleted == 0) : ?>
                <?php $form = ActiveForm::begin(['action' => ['delete', 'id_sk' => $model->id_sk], 'method' => 'post']); ?>
                <?= Html::a('<i class="fas fa-edit"></i> Update', ['update', 'id' => $model->id_sk], ['class' => 'btn btn-sm btn-warning']) ?>
                <?= Html::submitButton('Delete', ['class' => 'btn btn-outline-danger btn-sm', 'onclick' => "return confirm('Anda yakin akan menghapus data SK ini?');"]) ?>
                <?php ActiveForm::end(); ?>
            <?php endif; ?>
        </div>
        <div class="p-2">
            <?= Html::a('<i class="fas fa-car"></i> List SK', ['index'], ['class' => 'btn btn-outline-warning btn-sm']) ?>
        </div>
    </div>

    <?php
    if ($model->nama_dalam_sk != null) {
        // Step 1: Get the list of email addresses from the peserta attribute in the agenda table
        $emailList = explode(', ', $model->nama_dalam_sk);
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
        if (count($names) > 1) {
            $listItems = '';
            foreach ($names as $key => $name) {
                $listItems .= '<li>' .  ' ' . $name . '</li>';
            }
            $autofillString = '<ol>' . $listItems . '</ol>';
        } else
            $autofillString = $names[0];
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
                'attribute' => 'id_sk',
                'label' => 'ID Data SK',
            ],
            'nomor_sk',
            [
                'attribute' => 'tanggal_sk',
                'value' => \Yii::$app->formatter->asDatetime(strtotime($model->tanggal_sk), "d MMMM y"),
            ],
            'tentang_sk',
            [
                'attribute' => 'nama_dalam_sk',
                'value' => $autofillString,
                'format' => 'html'
            ],
            [
                'attribute' => 'reporter',
                'value' => $model->reportere->nama,
            ],
            [
                'attribute' => 'deleted',
                'value' => $model->deleted == 0 ? '<span title="Data Aktif" class="badge bg-primary rounded-pill"><i class="fas fa-check"></i> Data Aktif</span>' : ($model->deleted == 1 ? '<span title="Data Dihapus" class="badge bg-danger rounded-pill"><i class="fas fa-trash"></i> Data Dihapus oleh Pengguna</span>' : ''),
                'format' => 'html',
                'label' => 'Status Usulan',
            ],
            [
                'attribute' => 'timestamp',
                'value' => \Yii::$app->formatter->asDatetime(strtotime($model->timestamp), "d MMMM y 'pada' H:mm a"),
            ],
            [
                'attribute' => 'timestamp_lastupdate',
                'value' => \Yii::$app->formatter->asDatetime(strtotime($model->timestamp_lastupdate), "d MMMM y 'pada' H:mm a"),
            ],
        ],
    ]) ?>

    <iframe id="pdf-iframe" src="<?= Yii::getAlias('@web') ?>/sk/<?php echo $model->id_sk ?>.pdf" width="100%" height="700px"></iframe>

</div>