<?php
use yii\helpers\Html;
/* @var $this yii\web\View */
/* @var $model app\models\Pengguna */
$this->title = 'Pendaftaran Pengguna PORTAL PINTAR';
?>
<div class="container-fluid" data-aos="fade-up">
    <div class="row">
        <div class="col-12">
            <?php if (!Yii::$app->user->isGuest) : ?>
                <?=
                $this->render('_form', [
                    'model' => $model,
                    'profil' => $profil,
                    'modelusername' => $modelusername,
                    'ada' => $ada,
                    'namasat' => $namasat,
                    'key' => $key,
                    'bengkulu' => $bengkulu
                ])
                ?>
            <?php else : ?>
                <?=
                $this->render('_form-guest', [
                    'model' => $model,
                    'profil' => $profil,
                    'modelusername' => $modelusername,
                    'ada' => $ada,
                    'namasat' => $namasat,
                    'key' => $key,
                    'bengkulu' => $bengkulu
                ])
                ?>
            <?php endif; ?>
        </div>
    </div>
</div>