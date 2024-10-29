<?php

use app\assets\AppAsset;
use yii\bootstrap5\Html;

AppAsset::register($this);
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
    <title><?= Html::encode(Yii::$app->name) ?></title>
    <?php $this->head() ?>
    <?php include_once('_metatags.php') ?>
    <!-- =======================================================
  * Coded by: Safira Khansa, a.k.a. Nofriani
  * Started on: March 29th, 2023, Second Version: January 22nd, 2024
  ======================================================== -->
</head>

<body>
    <main>
        <div class="container">
            <?= $content; ?>
        </div>
    </main><!-- End #main -->
    <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>
    <?php include_once('_metatags2.php') ?>
</body>

</html>