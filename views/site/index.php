<?php

$this->title = 'Dashboard Agenda';

use app\models\Agenda;
use app\models\Apel;
use app\models\Beritarilis;
use app\models\Pengguna;
use yii\widgets\ListView;

$colClass = 'col-md-3';
function findWaktutampil($id_laporan)
{
    $dataagenda = Agenda::findOne(['id_agenda' => $id_laporan]);
    $waktutampil = '';
    if ($dataagenda->waktumulai_tunda != NULL && $dataagenda->waktuselesai_tunda) {
        $formatter = Yii::$app->formatter;
        $formatter->locale = 'id-ID';
        $timezone = new \DateTimeZone('Asia/Jakarta');
        $waktumulai_tunda = new \DateTime($dataagenda->waktumulai_tunda, new \DateTimeZone('UTC'));
        $waktumulai_tunda->setTimeZone($timezone);
        $waktumulai_tundaFormatted = $formatter->asDatetime($waktumulai_tunda, 'd MMMM Y, H:mm');
        $waktuselesai_tunda = new \DateTime($dataagenda->waktuselesai_tunda, new \DateTimeZone('UTC'));
        $waktuselesai_tunda->setTimeZone($timezone);
        $waktuselesai_tundaFormatted = $formatter->asDatetime($waktuselesai_tunda, 'H:mm');
        if ($waktumulai_tunda->format('Y-m-d') === $waktuselesai_tunda->format('Y-m-d')) {
            $waktumulai_tundaFormatted = $formatter->asDatetime($waktumulai_tunda, 'd MMMM Y, H:mm');
            $waktutampil =  $waktumulai_tundaFormatted . ' - ' . $waktuselesai_tundaFormatted . ' WIB';
        } else {
            $waktuselesai_tundaFormatted = $formatter->asDatetime($waktuselesai_tunda, 'd MMMM Y, H:mm');
            $waktutampil =  $waktumulai_tundaFormatted . ' WIB s.d ' . $waktuselesai_tundaFormatted . ' WIB';
        }
    } else {
        $formatter = Yii::$app->formatter;
        $formatter->locale = 'id-ID';
        $timezone = new \DateTimeZone('Asia/Jakarta');
        $waktumulai = new \DateTime($dataagenda->waktumulai, new \DateTimeZone('UTC'));
        $waktumulai->setTimeZone($timezone);
        $waktumulaiFormatted = $formatter->asDatetime($waktumulai, 'd MMMM Y, H:mm');
        $waktuselesai = new \DateTime($dataagenda->waktuselesai, new \DateTimeZone('UTC'));
        $waktuselesai->setTimeZone($timezone);
        $waktuselesaiFormatted = $formatter->asDatetime($waktuselesai, 'H:mm');
        if ($waktumulai->format('Y-m-d') === $waktuselesai->format('Y-m-d')) {
            $waktumulaiFormatted = $formatter->asDatetime($waktumulai, 'd MMMM Y, H:mm');
            $waktutampil =  $waktumulaiFormatted . ' - ' . $waktuselesaiFormatted . ' WIB';
        } else {
            $waktuselesaiFormatted = $formatter->asDatetime($waktuselesai, 'd MMMM Y, H:mm');
            $waktutampil =  $waktumulaiFormatted . ' WIB s.d ' . $waktuselesaiFormatted . ' WIB';
        }
    }
    return $waktutampil;
}
function findPeserta($dataagenda)
{
    $emailList = explode(', ', $dataagenda->peserta);
    $usernames = [];
    foreach ($emailList as $email) {
        $username = substr($email, 0, strpos($email, '@'));
        if (count($usernames) < 5) {
            $usernames[] = $username;
        } else {
            break;
        }
    }
    $names = Pengguna::find()
        ->select('nama')
        ->where(['in', 'username', $usernames])
        ->column();
    $listItems = '';
    foreach ($names as $key => $name) {
        $listItems .= '<li>' .  ' ' . $name . '</li>';
    }
    $autofillString = 'Peserta Kegiatan :<ol>' . $listItems . '</ol>';
    if (count($usernames) >= 5)
        return $autofillString . ' <br> ... dst. (Total : ' . count($emailList) . ' peserta)';
    else
        return $autofillString;
}
function findPesertaTambahan($dataagenda)
{
    $data = $dataagenda->peserta_lain;
    $small = substr($data, 0, 100);
    $removecomma = substr($small, 0, strrpos($small, ','));
    if ($data != null) {
        if (strlen($data) <= 100)
            $autofillString = 'Peserta Tambahan : <br/>' . $data;
        else
            $autofillString = 'Peserta Tambahan : <br/>' . $removecomma . ' ...';
    } else
        $autofillString = '';
    return $autofillString;
}
?>
<div class="row">
    <div class="col-lg-6">
        <h3 class="resume-title">AGENDA </h3>
        <?php if ($dataProvider->totalCount > 0) : ?>
            <?php foreach ($dataProvider->getModels() as $model) : ?>
                <div class="resume-item pb-0">
                    <h4> <?php echo '> ' . findWaktutampil($model->id_agenda) . ' <' ?>
                    </h4>
                    <div class="card <?= (!Yii::$app->user->isGuest && Yii::$app->user->identity->theme == 1) || Yii::$app->user->isGuest ? 'bg-dark text-light' : '' ?> ">
                        <!-- <div class="card-header"></div> -->
                        <div class="card-body">
                            <span style="font-size: 1.2rem; font-weight: bold">
                                <?php echo $model->kegiatan; ?>
                            </span>
                            <br />
                            Di <?php echo $model->tempate; ?>
                            <br />
                            <?php echo findPeserta($model) ?>
                            <br />
                            <?php echo findPesertaTambahan($model) ?>
                        </div>
                        <div class="card-footer text-right">
                            <em>Held By : </em> <?php echo $model->pelaksanalengkape; ?>
                        </div>
                    </div>
                </div>
                <br />
            <?php endforeach; ?>
        <?php else : ?>
            <div class="resume-item pb-0">
                <div class="card <?= (!Yii::$app->user->isGuest && Yii::$app->user->identity->theme == 1) || Yii::$app->user->isGuest ? 'bg-dark text-light' : '' ?> ">
                    <div class="card-body">
                        <em>Belum Ada Agenda ...</em>
                    </div>
                </div>
            </div>
            <br />
        <?php endif; ?>        
    </div>
    <div class="col-lg-6">
        <h3 class="resume-title">DATA PETUGAS APEL/UPACARA</h3>
        <div class="resume-item">
            <?php
            $apel = Apel::find()
                ->select('*')
                ->where(['>=', 'tanggal_apel', date("Y-m-d")])
                ->andWhere(['deleted' => 0])
                ->orderBy(['tanggal_apel' => SORT_ASC])
                ->one();
            ?>
            <?php if (isset($apel)) : ?>
                <?php
                \Yii::$app->formatter->locale = 'id-ID';
                $dayName = \Yii::$app->formatter->asDatetime(strtotime($apel->tanggal_apel), "EEEE");
                ?>
                <h4> <?php echo '> PETUGAS ' . ($apel->jenis_apel = 0 ? 'APEL' : 'UPACARA') . ' PADA ' . $dayName . ', ' . \Yii::$app->formatter->asDatetime(strtotime($apel->tanggal_apel), "d MMMM y") . ' <' ?>
                </h4>
                <div class="card <?= (!Yii::$app->user->isGuest && Yii::$app->user->identity->theme == 1) || Yii::$app->user->isGuest ? 'bg-dark text-light' : '' ?> ">
                    <!-- <div class="card-header"></div> -->
                    <div class="card-body">
                        <table class="table <?= (!Yii::$app->user->isGuest && Yii::$app->user->identity->theme == 1) || Yii::$app->user->isGuest ? 'table-dark' : '' ?> ">
                            <thead>
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">Tugas</th>
                                    <th scope="col">Petugas</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <th scope="row">1</th>
                                    <td>Pembina/ Inspektur</td>
                                    <td><?= $apel->getPetugase($apel->pembina_inspektur) ?></td>
                                </tr>
                                <tr>
                                    <th scope="row">2</th>
                                    <td>Pemimpin/ Komandan</td>
                                    <td><?= $apel->getPetugase($apel->pemimpin_komandan) ?></td>
                                </tr>
                                <tr>
                                    <th scope="row">3</th>
                                    <td>MC</td>
                                    <td><?= $apel->getPetugase($apel->mc) ?></td>
                                </tr>
                                <tr>
                                    <th scope="row">4</th>
                                    <td>Pembaca UUD</td>
                                    <td><?= $apel->getPetugase($apel->uud) ?></td>
                                </tr>
                                <tr>
                                    <th scope="row">5</th>
                                    <td>Panca Prasetya KORPRI</td>
                                    <td><?= $apel->getPetugase($apel->korpri) ?></td>
                                </tr>
                                <tr>
                                    <th scope="row">6</th>
                                    <td>Pembaca Doa</td>
                                    <td><?= $apel->getPetugase($apel->doa) ?></td>
                                </tr>
                                <tr>
                                    <th scope="row">7</th>
                                    <td>Ajudan</td>
                                    <td><?= $apel->getPetugase($apel->ajudan) ?></td>
                                </tr>
                                <tr>
                                    <th scope="row">8</th>
                                    <td>Operator Lagu</td>
                                    <td><?= $apel->getPetugase($apel->operator) ?></td>
                                </tr>
                                <?php
                                if ($apel->bendera != null) {
                                    $emailList = explode(', ', $apel->bendera);
                                    $usernames = [];
                                    foreach ($emailList as $email) {
                                        $username = substr($email, 0, strpos($email, '@'));
                                        $usernames[] = $username;
                                    }
                                    $names = Pengguna::find()
                                        ->select('nama')
                                        ->where(['in', 'username', $usernames])
                                        ->column();
                                    $listItems = '';
                                    foreach ($names as $key => $name) {
                                        $listItems .= '<li>' .  ' ' . $name . '</li>';
                                    }
                                    $autofillString = '<ol style="padding-left: 1rem;
                                        margin-bottom: 0rem;">' . $listItems . '</ol>';
                                } else {
                                    $autofillString = '-';
                                }
                                ?>
                                <?php if ($apel->jenis_apel == 1) : ?>
                                    <tr>
                                        <th scope="row">9</th>
                                        <td>Perwira</td>
                                        <td><?= ($apel->perwira != null ? $apel->getPetugase($apel->perwira) : '-') ?></td>
                                    </tr>
                                    <tr>
                                        <th scope="row">10</th>
                                        <td>Pembawa Bendera</td>
                                        <td><?= ($apel->bendera != null ? $autofillString : '-') ?></td>
                                    </tr>
                                <?php endif; ?>
                                <?php if ($apel->tambahsatu_text != null) : ?>
                                    <tr>
                                        <th scope="row">11</th>
                                        <td><?= ($apel->tambahsatu_text != null ? $apel->tambahsatu_text : '-') ?></td>
                                        <td><?= ($apel->tambahsatu_petugas != null ? $apel->getPetugase($apel->tambahsatu_petugas) : '-') ?></td>
                                    </tr>
                                <?php endif; ?>
                                <?php if ($apel->tambahdua_text != null) : ?>
                                    <tr>
                                        <th scope="row">12</th>
                                        <td><?= ($apel->tambahdua_text != null ? $apel->tambahdua_text : '-') ?></td>
                                        <td><?= ($apel->tambahdua_petugas != null ? $apel->getPetugase($apel->tambahdua_petugas) : '-') ?></td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php else : ?>
                <div class="card <?= (!Yii::$app->user->isGuest && Yii::$app->user->identity->theme == 1) || Yii::$app->user->isGuest ? 'bg-dark text-light' : '' ?> ">
                    <div class="card-body">
                        <h4><em>Belum ada data petugas apel ...</em></h4>
                    </div>
                </div>
            <?php endif; ?>
        </div>
        <h3 class="resume-title">JADWAL RILIS TERKINI</h3>
        <div class="resume-item">
            <?php
            $date = new DateTime("now", new DateTimeZone('Asia/Jakarta'));
            $beritarilis = Beritarilis::find()
                ->select('*')
                ->where(['>=', 'waktuselesai', $date->format('Y-m-d H:i:s')])
                ->andWhere(['deleted' => 0])
                ->orderBy(['waktuselesai' => SORT_ASC])
                ->one();
            ?>
            <?php if (isset($beritarilis)) : ?>
                <?php
                \Yii::$app->formatter->locale = 'id-ID';
                $dayName = \Yii::$app->formatter->asDatetime(strtotime($beritarilis->waktuselesai), "EEEE");
                ?>
                <h4> <?php echo '> JADWAL RILIS TERKINI <' ?>
                </h4>
                <?php
                $formatter = Yii::$app->formatter;
                $formatter->locale = 'id-ID';
                $timezone = new \DateTimeZone('Asia/Jakarta');
                $waktumulai = new \DateTime($beritarilis->waktumulai, new \DateTimeZone('UTC'));
                $waktumulai->setTimeZone($timezone);
                $waktumulaiFormatted = $formatter->asDatetime($waktumulai, 'd MMMM Y, H:mm');
                $waktuselesai = new \DateTime($beritarilis->waktuselesai, new \DateTimeZone('UTC'));
                $waktuselesai->setTimeZone($timezone);
                $waktuselesaiFormatted = $formatter->asDatetime($waktuselesai, 'H:mm');
                if ($waktumulai->format('Y-m-d') === $waktuselesai->format('Y-m-d')) {
                    $waktumulaiFormatted = $formatter->asDatetime($waktumulai, 'd MMMM Y, H:mm');
                    $waktutampil =  $waktumulaiFormatted . ' - ' . $waktuselesaiFormatted . ' WIB';
                } else {
                    $waktuselesaiFormatted = $formatter->asDatetime($waktuselesai, 'd MMMM Y, H:mm');
                    $waktutampil =  $waktumulaiFormatted . ' WIB s.d ' . $waktuselesaiFormatted . ' WIB';
                }
                ?>
                <div class="card <?= (!Yii::$app->user->isGuest && Yii::$app->user->identity->theme == 1) || Yii::$app->user->isGuest ? 'bg-dark text-light' : '' ?> ">
                    <!-- <div class="card-header"></div> -->
                    <div class="card-body">
                        <span style="font-size: 1.2rem; font-weight: bold">
                            <?php echo '> ' . $waktutampil . ' <' ?>
                        </span>
                        <br />
                        <div class="bd-callout bd-callout-info"><?php echo $beritarilis->materi_rilis; ?></div>
                        <i class="bi bi-pin-map-fill" style="font-size: 22px"> </i><?php echo $beritarilis->tempate ?>
                        <?php
                        if ($beritarilis->narasumber != null) {
                            $emailList = explode(', ', $beritarilis->narasumber);
                            $usernames = [];
                            foreach ($emailList as $email) {
                                $username = substr($email, 0, strpos($email, '@'));
                                $usernames[] = $username;
                            }
                            $names = Pengguna::find()
                                ->select('nama')
                                ->where(['in', 'username', $usernames])
                                ->column();
                            $listItems = '';
                            if (count($names) > 1) {
                                foreach ($names as $key => $name) {
                                    $listItems .= '<li>' .  ' ' . $name . '</li>';
                                }
                                $autofillString = 'Narasumber : <br><ol>' . $listItems . '</ol>';
                            } else {
                                $autofillString = 'Narasumber : ' . $names[0];
                            }
                        } else {
                            $autofillString = '-';
                        }
                        ?>
                        <br />
                        <i class="bi bi-person-badge-fill" style="font-size: 22px"> </i><?php echo $autofillString ?>
                    </div>
                </div>
            <?php else : ?>
                <div class="card <?= (!Yii::$app->user->isGuest && Yii::$app->user->identity->theme == 1) || Yii::$app->user->isGuest ? 'bg-dark text-light' : '' ?> ">
                    <div class="card-body">
                        <h4><em>Belum ada jadwal rilis ...</em></h4>
                    </div>
                </div>
            <?php endif; ?>
        </div>
        <h3 class="resume-title">PEGAWAI YANG DL HARI INI</h3>
        <div class="resume-item">
            <div class="p-2 card <?= (!Yii::$app->user->isGuest && Yii::$app->user->identity->theme == 1) || Yii::$app->user->isGuest ? 'bg-dark text-light' : '' ?> ">
                <?php if ($dataProviderDl->totalCount > 0) : ?>
                    <?php
                    echo ListView::widget([
                        'dataProvider' => $dataProviderDl, // Replace $dataProvider with your actual data provider
                        'itemView' => function ($model, $key, $index, $widget) {
                            $formatter = Yii::$app->formatter;
                            $formatter->locale = 'id-ID'; // set the locale to Indonesian
                            $timezone = new \DateTimeZone('Asia/Jakarta'); // create a timezone object for WIB
                            $waktumulai = new \DateTime($model->tanggal_mulai, new \DateTimeZone('UTC')); // create a datetime object for waktumulai with UTC timezone
                            $waktumulai->setTimeZone($timezone); // set the timezone to WIB
                            $waktumulaiFormatted = $formatter->asDatetime($waktumulai, 'd MMMM Y'); // format the waktumulai datetime value
                            $waktuselesai = new \DateTime($model->tanggal_selesai, new \DateTimeZone('UTC')); // create a datetime object for waktuselesai with UTC timezone
                            $waktuselesai->setTimeZone($timezone); // set the timezone to WIB
                            $waktuselesaiFormatted = $formatter->asDatetime($waktuselesai, 'd MMMM Y'); // format the waktuselesai time value only
                            if ($waktumulai->format('Y-m') === $waktuselesai->format('Y-m')) {
                                // if waktumulai and waktuselesai are on the same month, format the time range differently
                                $waktumulaiFormatted = $formatter->asDatetime($waktumulai, 'd'); // format the waktumulai datetime value with the year and time
                                $waktuDisplay =  $waktumulaiFormatted . ' - ' . $waktuselesaiFormatted; // concatenate the formatted dates
                            } else {
                                // if waktumulai and waktuselesai are on different days, format the date range normally
                                $waktuselesaiFormatted = $formatter->asDatetime($waktuselesai, 'd MMMM Y'); // format the waktuselesai datetime value
                                $waktuDisplay =   $waktumulaiFormatted . ' s.d ' . $waktuselesaiFormatted; // concatenate the formatted dates
                            }

                            // Step 1: Get the list of email addresses from the peserta attribute in the agenda table
                            $emailList = explode(', ', $model->pegawai);
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
                            if (count($names) <= 1) {
                                $autofillString = $names[0];
                            } else {
                                foreach ($names as $key => $name) {
                                    $listItems .= '<li>' .  ' ' . $name . '</li>';
                                }
                                $autofillString = '<ol style="padding-left: 1rem">' . $listItems . '</ol>';
                            }

                            return '
                        <div class="item">
                            <h6 class="fw-bold"> <i class="fas fa-map-marked-alt me-2"></i> ' . $model->tujuane->nama_tujuan . '</h6>
                            <table class="table table-borderless table-sm table-hover ' . ((!Yii::$app->user->isGuest && Yii::$app->user->identity->theme == 1) || Yii::$app->user->isGuest ? 'table-dark' : '') . '">
                                <tbody>
                                    <tr>                    
                                        <td style="width: 50%"><i class="fas fa-calendar-alt"></i> Jangka Waktu DL</td>
                                        <td>:</td>
                                        <td>' . $waktuDisplay . '</td>
                                    </tr>
                                    <tr>                    
                                        <td style="width: 50%"><i class="fab fa-black-tie"></i> Pegawai:</td>
                                        <td>:</td>
                                        <td>' . $autofillString . '</td>
                                    </tr>                                                            
                                </tbody>
                            </table>
                        </div>
                        <hr/>';
                        },
                        'options' => ['class' => 'list-view'], // Customize the list view options as needed
                        'itemOptions' => ['class' => 'list-group-item'], // Customize the item options as needed
                        'summary' => '', // Remove the summary if not needed
                    ]);
                    ?>
                <?php else : ?>
                    <?php
                    $weekend = (date('N', time()) >= 6); // Use time() instead of strtotime(date("NOW"))
                    if ($weekend)
                        $teks = 'Hari Ini Weekend ...';
                    else
                        $teks = 'Semua Pegawai Hadir di Kantor Hari Ini ...';

                    echo $teks; // Output the result
                    ?>
                    <div class="card-body">
                        <h4><em><?= $teks ?></em></h4>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        <h3 class="resume-title">KETERSEDIAAN KENDARAAN DINAS</h3>
        <div class="resume-item">
            <div class="p-2 card <?= (!Yii::$app->user->isGuest && Yii::$app->user->identity->theme == 1) || Yii::$app->user->isGuest ? 'bg-dark text-light' : '' ?> ">
                <!-- <h4>Peminjaman 2 Minggu ke Depan</h4> -->
                <?php if ($dataProviderMobil->totalCount > 0) : ?>
                    <?php
                    echo ListView::widget([
                        'dataProvider' => $dataProviderMobil, // Replace $dataProvider with your actual data provider
                        'itemView' => function ($model, $key, $index, $widget) {
                            $formatter = Yii::$app->formatter;
                            $formatter->locale = 'id-ID'; // set the locale to Indonesian
                            $timezone = new \DateTimeZone('Asia/Jakarta'); // create a timezone object for WIB
                            $waktumulai = new \DateTime($model->mulai, new \DateTimeZone('UTC')); // create a datetime object for waktumulai with UTC timezone
                            $waktumulai->setTimeZone($timezone); // set the timezone to WIB
                            $waktumulaiFormatted = $formatter->asDatetime($waktumulai, 'd MMMM Y, H:mm'); // format the waktumulai datetime value
                            $waktuselesai = new \DateTime($model->selesai, new \DateTimeZone('UTC')); // create a datetime object for waktuselesai with UTC timezone
                            $waktuselesai->setTimeZone($timezone); // set the timezone to WIB
                            $waktuselesaiFormatted = $formatter->asDatetime($waktuselesai, 'H:mm'); // format the waktuselesai time value only
                            if ($waktumulai->format('Y-m-d') === $waktuselesai->format('Y-m-d')) {
                                // if waktumulai and waktuselesai are on the same day, format the time range differently
                                $waktumulaiFormatted = $formatter->asDatetime($waktumulai, 'd MMMM Y, H:mm'); // format the waktumulai datetime value with the year and time
                                $waktuDisplay = $waktumulaiFormatted . ' - ' . $waktuselesaiFormatted . ' WIB'; // concatenate the formatted dates
                            } else {
                                // if waktumulai and waktuselesai are on different days, format the date range normally
                                $waktuselesaiFormatted = $formatter->asDatetime($waktuselesai, 'd MMMM Y, H:mm'); // format the waktuselesai datetime value
                                $waktuDisplay = $waktumulaiFormatted . ' WIB s.d ' . $waktuselesaiFormatted . ' WIB'; // concatenate the formatted dates
                            }
                            return  '
                            <div class="item">
                                <h6 class="fw-bold"> <i class="fas fa-stopwatch me-2"></i> ' . $waktuDisplay . '</h6>
                                <table class="table table-borderless table-sm table-hover ' . ((!Yii::$app->user->isGuest && Yii::$app->user->identity->theme == 1) || Yii::$app->user->isGuest ? 'table-dark' : '') . '">
                                    <tbody>
                                        <tr>                    
                                            <td style="width: 50%"><i class="fab fa-black-tie"></i> Peminjam/Penanggung Jawab</td>
                                            <td>:</td>
                                            <td>' . $model->borrowere->nama . '</td>
                                        </tr>
                                        <tr>
                                            <td style="width: 50%"><i class="fas fa-shield-alt"></i> Persetujuan</td>
                                            <td>:</td>
                                            <td>' . ($model->approval == 1 ?
                                '<span title="Disetujui" class="badge bg-primary rounded-pill"><i class="fas fa-check"></i> Sudah Disetujui</span>' : ($model->approval == 3 ?
                                    '<span title="Persetujuan Usulan Dibatalkan" class="badge bg-danger rounded-pill"><i class="fas fa-trash"></i> Persetujuan Dibatalkan</span>' : ($model->approval == 0 ?
                                        '<span title="Menunggu Konfirmasi" class="badge bg-secondary rounded-pill"><i class="fas fa-question"></i> Menunggu Konfirmasi</span>' : '<span title="Usulan Ditolak" class="badge bg-danger rounded-pill"><i class="fas fa-times"></i> Usulan Ditolak</span>'))) . '
                                            </td>
                                        </tr>                        
                                    </tbody>
                                </table>
                            </div>
                            <hr/>';
                        },
                        'options' => ['class' => 'list-view'], // Customize the list view options as needed
                        'itemOptions' => ['class' => 'list-group-item'], // Customize the item options as needed
                        'summary' => '', // Remove the summary if not needed
                    ]);
                    ?>
                <?php else : ?>
                    <div class="card-body">
                        <h4><em>Belum Ada Rencana Peminjaman 2 Minggu ke Depan ...</em></h4>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    setTimeout(function() {
        location.reload();
    }, 3600000);
</script>