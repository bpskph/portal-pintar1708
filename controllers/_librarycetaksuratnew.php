<?php
use app\models\Pengguna;
$style =
    '
<style type="text/css">
    @font-face {
        font-family: "source_sans_proregular";
        src: local("Source Sans Pro"), url("fonts/sourcesans/sourcesanspro-regular-webfont.ttf") format("truetype");
        font-weight: normal;
        font-style: normal;
    }
    body {
        font-family: Arial, Helvetica, sans-serif !important;
        padding-left: 40px;
        padding-right: 40px;
    }
    p,
    table,
    ol {
        font-family: Arial, Helvetica, sans-serif !important;
        font-size: 15px;
    }
    p {
        line-height: 25px;
        margin-bottom: 0px;
    }
    hr {
        display: none;
    }
    table {
        border-collapse: collapse;
    }
    table.tanpaPadding>tbody>tr>td {
        padding: 1px !important;
    }
    table.garisBawah {
        border-bottom: 1px solid black;
    }
    table.garisBawahTr {
        border: 0.1px single black;
    }
    table.garisBawahTr>tbody>tr>td {
        border-top: 0.01px single black;
        border-bottom: 0.01px single black;
        border-left: 0px;
        border-right: 0px;
    }
    table.borderTipis>tbody>tr>td {
        border: 0.01px single black;
    }
    td,
    th {
        padding: 4px;
    }
    h5 {
        margin-bottom: 0px;
        margin-top: 0px;
    }
    tr.noBorderTop td {
        border-top: 0;
    }
    tr.noBorderBottom td {
        border-bottom: 0;
    }
    .footer {
        position: fixed;
        bottom: -60px;
        left: 0px;
        right: 0px;
        height: 60px;
        text-align: center;
        line-height: 15px;
    }
    @page {
        margin: 50px 25px;
    }
    tr.marginTop td {
        padding-top: 20px;
    }
    tr.noMarginTop td {
        padding-top: 0px;
    }
    tr.fontTebal {
        font-weight: bold;
    }
    h4.tulisanbps {
        font-family: "Tahoma", sans-serif !important;
        font-size: 18.7px;
        font-weight: bold;
    }
    h4, .tulisan {
        font-family: Arial, Helvetica, sans-serif !important;
    }
</style>
';
