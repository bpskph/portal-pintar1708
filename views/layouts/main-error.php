<?php

use app\assets\ErrorAsset;
use yii\bootstrap5\Html;

ErrorAsset::register($this);
$this->registerCsrfMetaTags();
$this->registerMetaTag(['charset' => Yii::$app->charset], 'charset');
$this->registerMetaTag(['name' => 'viewport', 'content' => 'width=device-width, initial-scale=1, shrink-to-fit=no']);
$this->registerMetaTag(['name' => 'description', 'content' => Yii::$app->params['meta_description']]);
$this->registerMetaTag(['name' => 'keywords', 'content' => Yii::$app->params['meta_keywords']]);
$this->registerLinkTag(['rel' => 'icon', 'type' => 'image/x-icon', 'href' => Yii::$app->request->baseUrl . '/images/favicon.png']);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>" class="h-100">

<head>
    <title><?= Html::encode(Yii::$app->name) ?> - Error 404</title>
    <?php $this->head() ?>
</head>

<body>
    <?php $this->beginBody() ?>
    <main>
        <div class="container">
            <section class="section error-404 min-vh-100 d-flex flex-column align-items-center justify-content-center">
                <br />
                <br />
                <h1>안녕하세요!</h1>
                <br />
                <br />
                <h2>Mau Kemana? :)</h2>
                <a class="btn" href="<?php echo Yii::$app->request->baseUrl; ?>/">Kembali ke Beranda</a>
                <img src="<?php echo Yii::$app->request->baseUrl; ?>/library/niceadmin/assets/img/not-found.svg" class="img-fluid py-5" alt="Page Not Found">
                <div class="credits">
                    <!-- All the links in the footer should remain intact. -->
                    <!-- You can delete the links only if you purchased the pro version. -->
                    <!-- Licensing information: https://bootstrapmade.com/license/ -->
                    <!-- Purchase the pro version with working PHP/AJAX contact form: https://bootstrapmade.com/nice-admin-bootstrap-admin-html-template/ -->
                    Coded by <a href="https://khansasafira19.github.io/">Fii ^^</a>
                </div>
            </section>

        </div>
    </main><!-- End #main -->

    <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>
    <?php $this->endBody() ?>
</body>

</html>
<?php $this->endPage() ?>