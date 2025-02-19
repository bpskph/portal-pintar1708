<?php
// Include Yii2's autoloader and bootstrap the application
require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../vendor/yiisoft/yii2/Yii.php';

// Load application configuration
$config = require __DIR__ . '/../config/web.php';

// Create and configure the application
(new yii\web\Application($config));

use app\controllers\AgendaController;
use app\models\Agenda;
use app\models\Pengguna;

// Fetch all unique "pemimpin" from Agenda
$pemimpins = Agenda::find()
    ->select('pemimpin')
    ->distinct()
    ->column();

foreach ($pemimpins as $pemimpin) {
    // Fetch total of agendas with no laporan
    $total1 = Agenda::find()
        ->leftJoin('laporan', 'agenda.id_agenda = laporan.id_laporan')
        ->where(['pemimpin' => $pemimpin])
        ->andWhere(['>', 'agenda.waktumulai', '2024-01-01'])
        ->andWhere(['agenda.progress' => 1])
        ->andWhere(['laporan.id_laporan' => null]) // No laporan exists
        ->count();

    // Fetch total of agendas with laporan not approved
    $total2 = Agenda::find()
        ->leftJoin('laporan', 'agenda.id_agenda = laporan.id_laporan')
        ->where(['pemimpin' => $pemimpin])
        ->andWhere(['>', 'agenda.waktumulai', '2024-01-01'])
        ->andWhere(['agenda.progress' => 1])
        ->andWhere(['laporan.approval' => 0]) // Approval not true
        ->count();

    if ($total1 > 0 || $total2 > 0) {
        // Fetch user details from Pengguna table
        $pengguna = Pengguna::find()
            ->where(['username' => $pemimpin])
            ->one();

        if ($pengguna) {
            $nomor_hp = $pengguna->nomor_hp;
            $nama_penerima = $pengguna->nama;

            // Prepare WhatsApp notification message
            $isi_notif = "*Portal Pintar - WhatsApp Notification Blast*\n" .
                "*Reminder Penyelesaian/Persetujuan Laporan Agenda*\n" .
                "Bapak/Ibu $nama_penerima, sejak 1 Januari 2024, terdapat $total1 agenda yang Anda pimpin belum memiliki laporan " .
                "dan $total2 laporan agenda yang belum Anda setujui. Mohon lakukan peninjauan di Sistem Portal Pintar, " .
                Yii::$app->params['namaSatkerKop'] . "portalpintar/agenda/index?owner=&year=" . date("Y") . "&nopage=0. \n\n" .
                "Untuk melihat daftar kegiatan yang Anda pimpin, lakukan pencarian _pemimpin_ dengan menggunakan username Anda pada halaman tersebut.\n\nTerima kasih.\n\n_#pesan ini dikirim oleh Portal Pintar dan tidak perlu dibalas_";

            // Send WhatsApp message using WA engine
            $response = AgendaController::wa_engine($nomor_hp, $isi_notif);

            // Log response
            if ($response) {
                echo "Notification sent to $nama_penerima ($nomor_hp): Success\n";
            } else {
                echo "Notification sent to $nama_penerima ($nomor_hp): Failed\n";
            }
        }
    }
}
