<?php

namespace app\assets;

use yii\web\AssetBundle;

/**
 * Main application asset bundle.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class DashboardAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'https://fonts.googleapis.com/css?family=Poppins',
        'library/myresume/assets/vendor/aos/aos.css',
        'library/myresume/assets/vendor/bootstrap/css/bootstrap.min.css',
        'library/myresume/assets/vendor/bootstrap-icons/bootstrap-icons.css',
        'library/myresume/assets/vendor/boxicons/css/boxicons.min.css',
        'library/myresume/assets/vendor/glightbox/css/glightbox.min.css',
        'library/myresume/assets/vendor/swiper/swiper-bundle.min.css',
        'library/css/fi-dashboard-main.css',
        'library/css/fi-dashboard.css',
        'https://use.fontawesome.com/releases/v5.3.1/css/all.css',

    ];
    public $js = [
        'library/myresume/assets/vendor/purecounter/purecounter_vanilla.js',
        'library/myresume/assets/vendor/aos/aos.js',
        'library/myresume/assets/vendor/bootstrap/js/bootstrap.bundle.min.js',
        'library/myresume/assets/vendor/glightbox/js/glightbox.min.js',
        'library/myresume/assets/vendor/isotope-layout/isotope.pkgd.min.js',
        'library/myresume/assets/vendor/swiper/swiper-bundle.min.js',
        'library/myresume/assets/vendor/typed.js/typed.umd.js',
        'library/myresume/assets/vendor/waypoints/noframework.waypoints.js',
        'library/myresume/assets/vendor/php-email-form/validate.js',
        'library/myresume/assets/js/main.js',
    ];

    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap5\BootstrapAsset',
    ];
}
