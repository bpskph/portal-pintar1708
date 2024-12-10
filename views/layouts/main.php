<?php

use app\assets\AppAsset;
use app\assets\AppLightAsset;
use yii\bootstrap5\Html;

if (!Yii::$app->user->isGuest && Yii::$app->user->identity->theme == 0) //light theme
    AppLightAsset::register($this);
else
    AppAsset::register($this); //dark theme, default

$this->registerCsrfMetaTags();
$this->registerMetaTag(['charset' => Yii::$app->charset], 'charset');
$this->registerMetaTag(['name' => 'viewport', 'content' => 'width=device-width, initial-scale=1, shrink-to-fit=no']);
$this->registerMetaTag(['name' => 'description', 'content' => Yii::$app->params['meta_description']]);
$this->registerMetaTag(['name' => 'keywords', 'content' => Yii::$app->params['meta_keywords']]);
$this->registerLinkTag(['rel' => 'icon', 'type' => 'image/x-icon', 'href' => Yii::$app->request->baseUrl . '/images/favicon.png']);

$controllerId = Yii::$app->controller->id;
$script = <<< JS
    var controllerId = '$controllerId';
JS;
$this->registerJs($script, \yii\web\View::POS_HEAD);

?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>" class="h-100">

<head>
    <title><?= Html::encode(Yii::$app->name) ?></title>
    <?php $this->head() ?>
    <!-- =======================================================
  * Coded by: Safira Khansa, a.k.a. Nofriani
  * Started on: March 29th, 2023, Second Version: January 22nd, 2024
  ======================================================== -->
</head>
<!-- <body> -->

<body class="d-flex flex-column h-100 toggle-sidebar <?php echo Yii::$app->user->isGuest ? '' : Yii::$app->user->identity->themechoice; ?>">
    <?php $this->beginBody() ?>
    <!-- ======= Header ======= -->
    <header id="header" class="fixed-top d-flex align-items-center">
        <!-- <div class="container-fluid container-xl d-flex align-items-center justify-content-lg-between"> -->
        <div class="container-fluid container-xl d-flex align-items-center">
            <h1 class="logo me-auto me-lg-0"><a href="<?= Yii::$app->request->baseUrl; ?>/"><?= Yii::$app->name ?></a></h1>
            <nav id="navbar" class="navbar order-last order-lg-0">
                <ul>
                    <li><a class="nav-link scrollto <?= (Yii::$app->controller->id == 'site' && Yii::$app->controller->action->id == 'index') ? 'active' : '' ?>" href="<?php echo Yii::$app->request->baseUrl; ?>/site/index">Dashboard</a></li>
                    <?php if (!Yii::$app->user->isGuest) : ?>
                        <li class="dropdown"><a href="#" class="nav-link scrollto <?= (
                                                                                        Yii::$app->controller->id == 'agenda'
                                                                                        || Yii::$app->controller->id == 'zooms'
                                                                                        || Yii::$app->controller->id == 'beritarilis'
                                                                                        || Yii::$app->controller->id == 'apel'
                                                                                        || Yii::$app->controller->id == 'dl'
                                                                                        || Yii::$app->controller->id == 'sk'
                                                                                        || Yii::$app->controller->id == 'mobildinas'
                                                                                        || Yii::$app->controller->id == 'suratrepo'
                                                                                        || Yii::$app->controller->id == 'suratrepoeks'
                                                                                        || Yii::$app->controller->id == 'suratmasuk'
                                                                                        || Yii::$app->controller->id == 'laporan'
                                                                                        || Yii::$app->controller->id == 'agendapimpinan')
                                                                                        ? 'active' : '' ?>">Agenda & Surat<i class="bi bi-chevron-down"></i></a>
                            <ul>
                                <li><a href="<?php echo Yii::$app->request->baseUrl; ?>/agenda/index?owner=&year=<?php echo date("Y") ?>&nopage=0" class="<?= (Yii::$app->controller->id == 'agenda' && Yii::$app->controller->action->id != 'calendar') ? 'aktip' : '' ?>">Agenda Utama</a></li>
                                <li class="dropdown"><a href="#"><span class="<?= (
                                                                                    (Yii::$app->controller->id == 'agenda' && Yii::$app->controller->action->id == 'calendar')
                                                                                    || Yii::$app->controller->id == 'agendapimpinan')
                                                                                    ? 'aktip' : '' ?>">Agenda Lainnya</span> <i class="bi bi-chevron-right"></i></a>
                                    <ul>
                                        <?php if (Yii::$app->user->identity->username == 'sekbps17' || Yii::$app->user->identity->level == 0): ?>
                                            <li><a href="<?php echo Yii::$app->request->baseUrl; ?>/agendapimpinan/index" class="<?= (Yii::$app->controller->id == 'agendapimpinan') ? 'aktip' : '' ?>">Agenda Pimpinan</a></li>
                                        <?php endif; ?>
                                        <li><a href="<?php echo Yii::$app->request->baseUrl; ?>/agenda/calendar" class="<?= (Yii::$app->controller->id == 'agenda' && Yii::$app->controller->action->id == 'calendar') ? 'aktip' : '' ?>">Kalender Agenda</a></li>
                                    </ul>
                                </li>
                                <li class="dropdown"><a href="#"><span class="<?= (
                                                                                    Yii::$app->controller->id == 'suratrepo'
                                                                                    || Yii::$app->controller->id == 'suratrepoeks'
                                                                                    || Yii::$app->controller->id == 'suratmasuk')
                                                                                    ? 'aktip' : '' ?>">
                                            Surat-surat</span> <i class="bi bi-chevron-right"></i></a>
                                    <ul>
                                        <li><a href="<?php echo Yii::$app->request->baseUrl; ?>/suratrepo/index?owner=&year=<?php echo date("Y") ?>" class="<?= (Yii::$app->controller->id == 'suratrepo') ? 'aktip' : '' ?>">Surat Internal</a></li>
                                        <li><a href="<?php echo Yii::$app->request->baseUrl; ?>/suratrepoeks/index?owner=&year=<?php echo date("Y") ?>" class="<?= (Yii::$app->controller->id == 'suratrepoeks') ? 'aktip' : '' ?>">Surat Eksternal</a></li>
                                    </ul>
                                </li>
                            </ul>
                        </li>
                    <?php else : ?>
                        <li><a class="nav-link scrollto <?= (Yii::$app->controller->id == 'agenda' || Yii::$app->controller->id == 'zooms' || Yii::$app->controller->id == 'beritarilis' || Yii::$app->controller->id == 'apel' || Yii::$app->controller->id == 'suratrepo' || Yii::$app->controller->id == 'suratrepoeks' || Yii::$app->controller->id == 'laporan' || Yii::$app->controller->id == 'agendapimpinan')
                                                            ? 'active' : '' ?>" href="<?php echo Yii::$app->request->baseUrl; ?>/agenda/index?owner=&year=<?php echo date("Y") ?>&nopage=0">Agenda & Surat</a></li>
                    <?php endif; ?>
                    <li><a class="nav-link scrollto <?= (Yii::$app->controller->id == 'linkapp') ? 'active' : '' ?>" href="<?php echo Yii::$app->request->baseUrl; ?>/linkapp/index">Portal Aplikasi</a></li>
                    <li><a class="nav-link scrollto <?= (Yii::$app->controller->id == 'linkmat') ? 'active' : '' ?>" href="<?php echo Yii::$app->request->baseUrl; ?>/linkmat/index">Portal Sharing</a></li>
                    <li><a class="nav-link scrollto <?= (Yii::$app->controller->id == 'projectmember' || Yii::$app->controller->id == 'pengguna') ? 'active' : '' ?>" href="<?php echo Yii::$app->request->baseUrl;  ?>/projectmember/index?year=<?php echo date("Y") ?>">Tim Kerja</a></li>
                    <?php if (!Yii::$app->user->isGuest) : ?>
                        <?php $notifikasi = \app\models\Notification::find()->where(['user_id' => Yii::$app->user->identity->username])->andWhere('is_read = 0')->count() ?>
                        <li class="dropdown"><a href="#"><span><button type="button" class="btn btn-light position-relative"><?= Yii::$app->user->identity->username . ($notifikasi > 0 ? ' <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">' . $notifikasi . '</span>' : '') ?></button></span> <i class="bi bi-chevron-down"></i></a>
                            <ul>
                                <li>
                                    <a>
                                        <?php
                                        echo Html::beginForm(['/site/logout']) .  Html::submitButton(
                                            'Logout <i class="fas fa-sign-out-alt"></i>',
                                            [
                                                'class' => 'nav-link scrollto tombol-pengguna',
                                                'style' => 'border:0; background-color: transparent'
                                            ]
                                        ) . Html::endForm()  ?>
                                    </a>
                                </li>
                                <li><a href="<?= Yii::$app->request->baseUrl . '/notification/index' ?>" data-bs-toggle="modal" data-bs-target="#exampleModal" class="modal-link">Notifikasi <?= ($notifikasi > 0 ? ' <span class="position-absolute bottom-10 start-50 translate-middle badge rounded-pill bg-danger">' . $notifikasi . '</span>' : '') ?></a></li>
                                <li class="dropdown"><a href="#"><span class="<?= (
                                                                                    Yii::$app->controller->id == 'patches'
                                                                                    || Yii::$app->controller->id == 'popups'
                                                                                    || (Yii::$app->controller->id == 'site'
                                                                                        && Yii::$app->controller->action->id == 'dashboard')
                                                                                    || (Yii::$app->controller->id == 'site'
                                                                                        && Yii::$app->controller->action->id == 'evaluasi')
                                                                                    || (Yii::$app->controller->id == 'pengguna'
                                                                                        && Yii::$app->controller->action->id == 'view')
                                                                                )
                                                                                    ? 'aktip' : '' ?>">
                                            Fitur Lainnya</span> <i class="bi bi-chevron-right"></i></a>
                                    <ul>
                                        <li><a class="<?= (Yii::$app->controller->id == 'pengguna' && Yii::$app->controller->action->id == 'view') ? 'aktip' : '' ?>" href="<?= Yii::$app->request->baseUrl . '/pengguna/view?username=' . Yii::$app->user->identity->username ?>">Profil Saya</a></li>
                                        <li><a class="<?= (Yii::$app->controller->id == 'patches') ? 'aktip' : '' ?>" href="<?= Yii::$app->request->baseUrl . '/patches/index' ?>">Riwayat Pemutakhiran Aplikasi</a></li>
                                        <li><a class="<?= (Yii::$app->controller->id == 'site' && Yii::$app->controller->action->id == 'dashboard') ? 'aktip' : '' ?>" href="<?= Yii::$app->request->baseUrl . '/site/dashboard' ?>">Riwayat Akses Aplikasi</a></li>
                                        <li><a class="<?= (Yii::$app->controller->id == 'popups') ? 'aktip' : '' ?>" href="<?= Yii::$app->request->baseUrl . '/popups/index' ?>">Riwayat Pengumuman</a></li>
                                        <li><a class="<?= (Yii::$app->controller->id == 'site' && Yii::$app->controller->action->id == 'evaluasi') ? 'aktip' : '' ?>" href="<?= Yii::$app->request->baseUrl; ?>/site/evaluasi?year=<?php echo date("Y") ?>">Evaluasi Pemakaian Aplikasi</a></li>
                                        <li><a href="https://wa.me/6285664991937?text=Salam+Senyum,+Developer+Portal+Pintar%0ASaya+ingin+berdiskusi+terkait+Sistem+Portal+Pintar" target="_blank">Hubungi Developer</a></li>
                                    </ul>
                                </li>
                            </ul>
                        </li>
                    <?php else : ?>
                        <li><a class="nav-link scrollto tombol-pengguna" href="<?php echo Yii::$app->request->baseUrl; ?>/site/login">Login</a></li>
                    <?php endif; ?>
                </ul>
                <i class="bi bi-list mobile-nav-toggle"></i>
            </nav><!-- .navbar -->
            <?php
            if (!Yii::$app->user->isGuest && Yii::$app->user->identity->theme == 1) { //dark theme
            ?>
                <a href="<?= Yii::$app->request->baseUrl . '/site/theme?choice=0' ?>" class="book-a-table-btn scrollto d-none d-lg-flex"><i class="icon fa fa-sun"></i></a>
            <?php } elseif (!Yii::$app->user->isGuest && Yii::$app->user->identity->theme == 0) { //light theme
            ?>
                <a href="<?= Yii::$app->request->baseUrl . '/site/theme?choice=1' ?>" class="book-a-table-btn scrollto d-none d-lg-flex" style="background-color: #cda45e"><i class="icon fa fa-moon text-dark"></i></a>
            <?php } ?>
            <a href="https://www.canva.com/design/DAGQgVxVcSI/NPzAMvj5-fVqwimvxD6DkQ/view?utm_content=DAGQgVxVcSI&utm_campaign=designshare&utm_medium=link&utm_source=editor" class="book-a-table-btn scrollto d-none d-lg-flex text-warning d-flex align-items-center justify-content-center" title="Panduan Portal Pintar" target="_blank">
                <i class="fas fa-book-open"></i>
            </a>
        </div>
    </header><!-- End Header -->
    <!-- Navigation -->
    <main id="main">
        <?php if (Yii::$app->session->hasFlash('success')) : ?>
            <div class="toast-container p-3 top-0 end-0" data-aos="fade-left">
                <div class="toast show shadow-sm p-1 mb-2 rounded" role="alert" aria-live="assertive" aria-atomic="true" data-delay="2" id="myToast">
                    <div class="toast-header bg-success text-light">
                        <img src="<?php echo Yii::$app->request->baseUrl ?>/images/favicon.png" class="rounded me-2" width="20" height="20" alt="Portal Pintar Alert">
                        <strong class="me-auto"><?= Yii::$app->name ?></strong>
                        <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
                    </div>
                    <div class="toast-body bg-light text-dark">
                        <?= Yii::$app->session->getFlash('success') ?>
                    </div>
                </div>
            </div>
        <?php elseif (Yii::$app->session->hasFlash('warning')) : ?>
            <div class="toast-container p-3 top-0 end-0" data-aos="fade-left">
                <div class="toast show shadow-sm p-1 mb-2 rounded" role="alert" aria-live="assertive" aria-atomic="true" data-delay="2" id="myToast">
                    <div class="toast-header bg-warning text-dark">
                        <img src="<?php echo Yii::$app->request->baseUrl ?>/images/favicon.png" class="rounded me-2" width="20" height="20" alt="Portal Pintar Alert">
                        <strong class="me-auto"><?= Yii::$app->name ?></strong>
                        <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
                    </div>
                    <div class="toast-body bg-light text-dark">
                        <?= Yii::$app->session->getFlash('warning') ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>
        <?= $content ?>
        <div class="modal fade modal-xl modal-dark" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header <?= ((!Yii::$app->user->isGuest && Yii::$app->user->identity->theme == 0) ? '' : 'bg-dark') ?>">
                        <h5 class="modal-title" id="exampleModalLabel"></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body <?= ((!Yii::$app->user->isGuest && Yii::$app->user->identity->theme == 0) ? '' : 'bg-dark') ?>" id="modalContent">
                    </div>
                    <div class="modal-footer <?= ((!Yii::$app->user->isGuest && Yii::$app->user->identity->theme == 0) ? '' : 'bg-dark') ?>">
                        <button type="button" class="btn btn-warning" data-bs-dismiss="modal">Tutup</button>
                    </div>
                </div>
            </div>
        </div>
    </main>
    <footer id="footer">
        <div class="footer-top">
            <div class="container">
                <div class="row">
                    <div class="col-lg-7 col-md-6">
                        <div class="footer-info">
                            <h3>BPS KABUPATEN BENGKULU SELATAN</h3>
                            <p>
                                Jalan Affan Bachsin No.108A, Kabupaten Bengkulu Selatan,
                                Indonesia<br><br>
                                <strong>Phone:</strong> (+62 739) 21048<br>
                                <strong>Email:</strong> bps1701@bps.go.id<br>
                            </p>
                        </div>
                    </div>
                    <div class="col-lg-5 col-md-6 footer-links">
                        <h4>Tautan Lainnya</h4>
                        <ul>
                            <li><i class="bi bi-globe"></i> <i class="bx bx-chevron-right"></i> <a href="https://bengkulu.bps.go.id/">Website BPS Kabupaten Bengkulu Selatan</a></li>
                            <li><i class="bi bi-globe"></i> <i class="bx bx-chevron-right"></i><a href="https://webapps.bps.go.id/bengkulu/portalpintar/">Website Portal Pintar</a></li>
                            <li><i class="bi bi-instagram"></i> <i class="bx bx-chevron-right"></i><a href="https://webapps.bps.go.id/bengkulu/">List Aplikasi Aktif BPS Kabupaten Bengkulu Selatan</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <div class="container">
            <div class="copyright">
                &copy; Copyright <strong><span>Tim PTM BPS Provinsi Bengkulu</span></strong>. All Rights Reserved | <span class="text-secondary"> App Version:</span> <?= Yii::$app->params['appVersion']; ?>
            </div>
            <div class="credits" style="color: #aaa">
                Coded by <a href="https://khansasafira19.github.io/">nofriani@bps.go.id</a> | <a href="https://wa.me/6285664991937?text=Salam+Senyum,+Developer+Portal+Pintar%0ASaya+ingin+berdiskusi+terkait+Sistem+Portal+Pintar" target="_blank">Hubungi Developer</a>
            </div>
        </div>
    </footer><!-- End Footer -->
    <div id="preloader"></div>
    <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

    <?php $this->endBody() ?>
</body>

</html>
<?php $this->endPage() ?>