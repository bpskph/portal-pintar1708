<?php

namespace app\assets;

use yii\web\AssetBundle;

class ErrorAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        // 'https://fonts.googleapis.com/css?family=Poppins',
        // 'library/not-found-page/dist/style.css',
        // 'library/css/fi-error.css',
        'https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Nunito:300,300i,400,400i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i',
        'library/niceadmin/assets/vendor/bootstrap/css/bootstrap.min.css',
        'library/niceadmin/assets/vendor/bootstrap-icons/bootstrap-icons.css',
        'library/niceadmin/assets/vendor/boxicons/css/boxicons.min.css',
        'library/niceadmin/assets/vendor/quill/quill.snow.css',
        'library/niceadmin/assets/vendor/quill/quill.bubble.css',
        'library/niceadmin/assets/vendor/remixicon/remixicon.css',
        'library/niceadmin/assets/vendor/simple-datatables/style.css',
        'library/niceadmin/assets/css/style.css',
    ];
    public $js = [
        'library/niceadmin/assets/vendor/apexcharts/apexcharts.min.js',
        'library/niceadmin/assets/vendor/bootstrap/js/bootstrap.bundle.min.js',
        'library/niceadmin/assets/vendor/chart.js/chart.umd.js',
        'library/niceadmin/assets/vendor/echarts/echarts.min.js',
        'library/niceadmin/assets/vendor/quill/quill.min.js',
        'library/niceadmin/assets/vendor/simple-datatables/simple-datatables.js',
        'library/niceadmin/assets/vendor/tinymce/tinymce.min.js',
        'library/niceadmin/assets/vendor/php-email-form/validate.js',
        'library/niceadmin/assets/js/main.js',
    ];

    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap5\BootstrapAsset',
    ];
}
