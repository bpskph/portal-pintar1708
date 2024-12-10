<?php
$this->title = 'Update Pengguna: <br/>' . $model->nama;
?>
<div class="container-fluid" data-aos="fade-up">
    <div class="row">
        <div class="col-12">
            <?= $this->render('_form-update', [
                'model' => $model,
                'profil' => $profil,
                'modelusername' => $modelusername,
                'ada' => $ada,
                'namasat' => $namasat,
                'key' => $key,
                'bengkulu' => $bengkulu
            ]) ?>
        </div>
    </div>
</div>