<?php

/**
 * @link https://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license https://www.yiiframework.com/license/
 */

namespace app\assets;

use yii\web\AssetBundle;

/**
 * Main application asset bundle.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class AppAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Playfair+Display:ital,wght@0,400;0,500;0,600;0,700;1,400;1,500;1,600;1,700|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i',
        'library/restaurantly/assets/vendor/animate.css/animate.min.css',
        'library/restaurantly/assets/vendor/aos/aos.css',
        'https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css',
        'https://cdn.jsdelivr.net/npm/bootstrap-switch@3.4.0/dist/css/bootstrap3/bootstrap-switch.min.css',
        'https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css',
        'https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css',
        'library/restaurantly/assets/vendor/bootstrap-icons/bootstrap-icons.css',
        'library/restaurantly/assets/vendor/boxicons/css/boxicons.min.css',
        'library/restaurantly/assets/vendor/glightbox/css/glightbox.min.css',
        'library/restaurantly/assets/vendor/swiper/swiper-bundle.min.css',
        'library/restaurantly/assets/css/style.css',
        'library/css/fi-theme.css',
        'https://fonts.googleapis.com/css?family=Poppins',
        'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css',
        'https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css',
    ];
    public $js = [
        'https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js',
        'library/restaurantly/assets/vendor/aos/aos.js',
        'https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js',
        'https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js',
        'https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js',
        'library/niceadmin/assets/vendor/apexcharts/apexcharts.min.js',
        'library/restaurantly/assets/vendor/glightbox/js/glightbox.min.js',
        'library/restaurantly/assets/vendor/swiper/swiper-bundle.min.js',
        'library/restaurantly/assets/js/main.js',
        'https://cdn.jsdelivr.net/npm/sweetalert2@10/dist/sweetalert2.min.js',
        'https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js',
        'https://cdn.jsdelivr.net/npm/bootstrap-switch@3.4.0/dist/js/bootstrap-switch.min.js',
        'library/js/fi-toast.js'
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap5\BootstrapAsset',
        'kartik\form\ActiveFormAsset',
        'yii\bootstrap5\BootstrapPluginAsset',
    ];
}
