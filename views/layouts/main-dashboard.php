<?php

use yii\helpers\Html;
use app\assets\DashboardAsset;
use app\assets\DashboardLightAsset;

if (!Yii::$app->user->isGuest && Yii::$app->user->identity->theme == 0) //light theme
    DashboardLightAsset::register($this);
else
    DashboardAsset::register($this); //dark theme, default

$this->registerCsrfMetaTags();
$this->registerMetaTag(['charset' => Yii::$app->charset], 'charset');
$this->registerMetaTag(['name' => 'viewport', 'content' => 'width=device-width, initial-scale=1, shrink-to-fit=no']);
$this->registerMetaTag(['name' => 'description', 'content' => Yii::$app->params['meta_description']]);
$this->registerMetaTag(['name' => 'keywords', 'content' => Yii::$app->params['meta_keywords']]);
$this->registerLinkTag(['rel' => 'icon', 'type' => 'image/x-icon', 'href' => Yii::$app->request->baseUrl . '/images/favicon.png']);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <title><?= Html::encode(Yii::$app->name) ?> - Dashboard</title>
    <?php $this->head() ?>
</head>

<body>
    <?php $this->beginBody() ?>
    <!-- ======= Mobile nav toggle button ======= -->
    <!-- <button type="button" class="mobile-nav-toggle d-xl-none"><i class="bi bi-list mobile-nav-toggle"></i></button> -->
    <i class="bi bi-list mobile-nav-toggle d-lg-none"></i>
    <!-- ======= Header ======= -->
    <header id="header" class="d-flex flex-column justify-content-center">
        <nav id="navbar" class="navbar nav-menu">
            <ul>
                <li><a href="<?php echo Yii::$app->request->baseUrl; ?>/site/index" class="nav-link scrollto active"><i class="bx bx-home"></i> <span>Dashboard</span></a></li>
                <li><a href="<?php echo Yii::$app->request->baseUrl; ?>/agenda/index?owner=&year=<?php echo date("Y") ?>&nopage=0" class="nav-link scrollto"><i class="bx bx-paperclip"></i> <span>Agenda dan Surat-surat</span></a></li>
                <?php if (!Yii::$app->user->isGuest) { ?>
                    <li><a href="<?php echo Yii::$app->request->baseUrl; ?>/agenda/calendar" class="nav-link scrollto"><i class="bx bx-calendar-event"></i> <span>Kalender Agenda</span></a></li>
                <?php } ?>
                <li><a href="<?php echo Yii::$app->request->baseUrl; ?>/linkapp/index" class="nav-link scrollto"><i class="bx bx-globe"></i> <span>Portal Aplikasi</span></a></li>
                <li><a href="<?php echo Yii::$app->request->baseUrl; ?>/linkmat/index" class="nav-link scrollto"><i class="bx bx-book"></i> <span>Portal Sharing</span></a></li>
                <li><a href="<?php echo Yii::$app->request->baseUrl; ?>/projectmember/index?year=<?php echo date("Y") ?>" class="nav-link scrollto"><i class="bx bx-user-pin"></i> <span>Tim Kerja</span></a></li>
                <?php if (Yii::$app->user->isGuest) { ?>
                    <li><a href="<?php echo Yii::$app->request->baseUrl; ?>/site/login" class="nav-link scrollto"><i class="bx bx-right-arrow"></i> <span>Login</span></a></li>
                <?php } ?>
            </ul>
        </nav><!-- .nav-menu -->
    </header><!-- End Header -->
    <!-- ======= Hero Section ======= -->
    <section id="hero" class="d-flex flex-column justify-content-center">
        <div class="container" data-aos="zoom-in" data-aos-delay="100">
            <h1>PORTAL PINTAR - TODAY INFO</h1>
            <p>P A S T I | <span class="typed" data-typed-items="PROFESIONAL, DATA BERKUALITAS, MELAYANI DENGAN HATI"></p>
        </div>
    </section><!-- End Hero -->
    <main id="main">
        <!-- ======= Resume Section ======= -->
        <section id="resume" class="resume">
            <div class="container ring" data-aos="zoom-in">
                <i class="circle" style="--clr:#7f7f7f;"></i>
                <i class="circle" style="--clr:#999999;"></i>
                <i class="circle" style="--clr:#b2b2b2;"></i>
                <?= $content ?>
            </div>
        </section>
        <!-- End Resume Section -->
    </main><!-- End #main -->
    <!-- ======= Footer ======= -->
    <footer id="footer">
        <div class="container">
            <h5 style="font-style: normal" ;>Portal Pusat Informasi Terkini Dalam Layar</h5>
            <div style="font-style: normal" class="credits">
                &copy; Copyright <strong><span>Tim PTM BPS Provinsi Bengkulu</span></strong>. All Rights Reserved | <span class="text-secondary"> App Version:</span> <?= Yii::$app->params['appVersion']; ?> | Coded by <a href="https://khansasafira19.github.io/" target="_blank">nofriani@bps.go.id</a>
            </div>
        </div>
    </footer><!-- End Footer -->
    <div id="preloader"></div>
    <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>
    <?php $this->endBody() ?>
</body>

</html>
<?php $this->endPage() ?>