<?php

$this->title = 'Evaluasi Kinerja dalam Portal Pintar Tahun ' . $year;
?>
<style>
    .dashboard .activity .activity-item .activite-label {
        min-width: 14px;
    }

    .gelap {
        --bs-list-group-bg: rgba(var(--bs-dark-rgb), var(--bs-bg-opacity)) !important;
        --bs-list-group-color: var(--bs-body-bg) !important;
    }
</style>
<section class="section dashboard" data-aos="fade-up">
    <div class="row">
        <!-- Left side columns -->
        <div class="col-lg-6">
            <div class="row">
                <h1>Tahun Berjalan (<?php echo ($year == 2023) ? 'Sejak 19 Mei 2023' : $year ?>) </h1>
                <!-- SNAPSHOT -->
                <div class="card mb-3 <?= ((!Yii::$app->user->isGuest && Yii::$app->user->identity->theme == 0) ? 'bg-light' : 'bg-dark') ?>">
                    <div class="card-body <?= ((!Yii::$app->user->isGuest && Yii::$app->user->identity->theme == 0) ? '' : 'bg-dark text-light') ?>">
                        <h5 class="card-title">Snapshot Input Data</h5>
                        <ol class="list-group list-group-numbered <?= ((!Yii::$app->user->isGuest && Yii::$app->user->identity->theme == 0) ? '' : 'gelap') ?>">
                            <li class="list-group-item d-flex justify-content-between align-items-start">
                                <div class="ms-2 me-auto">
                                    <div class="fw-bold">Jumlah Agenda Selesai/Direncanakan</div>
                                </div>
                                <span class="badge bg-primary rounded-pill"><?php echo $jumlahagenda ?> Kegiatan</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-start">
                                <div class="ms-2 me-auto">
                                    <div class="fw-bold">Jumlah Agenda Batal/Ditunda</div>
                                </div>
                                <span class="badge bg-primary rounded-pill"><?php echo $jumlahagendabatal ?> Kegiatan</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-start">
                                <div class="ms-2 me-auto">
                                    <div class="fw-bold">Jumlah Surat Internal</div>
                                    <?php echo ($year == 2023) ? '(Sejak 24 Mei 2023)' : $year ?>
                                </div>
                                <span class="badge bg-primary rounded-pill"><?php echo number_format($jumlahsuratinternal, 0, '', '.') ?> Nomor</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-start">
                                <div class="ms-2 me-auto">
                                    <div class="fw-bold">Jumlah Surat Eksternal</div>
                                    <?php echo ($year == 2023) ? '(Sejak 19 Mei 2023)' : $year ?>
                                </div>
                                <span class="badge bg-primary rounded-pill"><?php echo number_format($jumlahsurateksternal, 0, '', '.') ?> Nomor</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-start">
                                <div class="ms-2 me-auto">
                                    <div class="fw-bold">Jumlah Link Aplikasi yang Diinput</div>
                                </div>
                                <span class="badge bg-primary rounded-pill"><?php echo number_format($jumlahaplikasi, 0, '', '.') ?> Links</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-start">
                                <div class="ms-2 me-auto">
                                    <div class="fw-bold">Jumlah Link Sharing yang Diinput</div>
                                </div>
                                <span class="badge bg-primary rounded-pill"><?php echo number_format($jumlahmateri, 0, '', '.') ?> Links</span>
                            </li>
                        </ol>
                    </div>
                </div>

                <!-- TOP CONTRIBUTORS -->

                <div class="card mb-3 <?= ((!Yii::$app->user->isGuest && Yii::$app->user->identity->theme == 0) ? 'bg-light' : 'bg-dark') ?>">
                    <div class="card-body <?= ((!Yii::$app->user->isGuest && Yii::$app->user->identity->theme == 0) ? '' : 'bg-dark text-light') ?>">
                        <h5 class="card-title">Top Contributors</h5>
                        <em class="mb-1">Berdasarkan atribut "operator" (yang menginput)</em>
                        <ol class="list-group list-group-numbered <?= ((!Yii::$app->user->isGuest && Yii::$app->user->identity->theme == 0) ? '' : 'gelap') ?>">
                            <li class="list-group-item d-flex justify-content-between align-items-start">
                                <div class="ms-2 me-auto">
                                    Agenda
                                    <div class="fw-bold"><?php echo count($topcontributoragenda) > 0 ? $topcontributoragenda[0]['reportere']['nama'] : '-' ?></div>
                                </div>
                                <span class="badge bg-primary rounded-pill"><?php echo count($topcontributoragenda) > 0 ? $topcontributoragenda[0]['jumlahinput'] : '-' ?> Kegiatan</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-start">
                                <div class="ms-2 me-auto">
                                    Surat Internal <?php echo ($year == 2023) ? '(Dari 24 Mei)' : '' ?>
                                    <div class="fw-bold"><?php echo count($topcontributorsuratinternal) > 0 ?  $topcontributorsuratinternal[0]['ownere']['nama'] : '-' ?></div>
                                </div>
                                <span class="badge bg-primary rounded-pill"><?php echo count($topcontributorsuratinternal) > 0 ? $topcontributorsuratinternal[0]['jumlahinput'] : '-' ?> Nomor</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-start">
                                <div class="ms-2 me-auto">
                                    Link Aplikasi
                                    <div class="fw-bold"><?php echo count($topcontributoraplikasi) > 0 ? $topcontributoraplikasi[0]['ownere']['nama'] : '-'  ?></div>
                                </div>
                                <span class="badge bg-primary rounded-pill"><?php echo count($topcontributoraplikasi) > 0 ? $topcontributoraplikasi[0]['jumlahinput'] : '-'  ?> Links</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-start">
                                <div class="ms-2 me-auto">
                                    Link Materi
                                    <div class="fw-bold"><?php echo count($topcontributormateri) > 0 ? $topcontributormateri[0]['ownere']['nama'] : '-'  ?></div>
                                </div>
                                <span class="badge bg-primary rounded-pill"><?php echo count($topcontributormateri) > 0 ? $topcontributormateri[0]['jumlahinput'] : '-'  ?> Links</span>
                            </li>
                        </ol>
                    </div>
                </div>

                <!-- AGENDA -->
                <div class="card mb-3 <?= ((!Yii::$app->user->isGuest && Yii::$app->user->identity->theme == 0) ? 'bg-light' : 'bg-dark') ?>">
                    <div class="card-body <?= ((!Yii::$app->user->isGuest && Yii::$app->user->identity->theme == 0) ? '' : 'bg-dark text-light') ?>">
                        <h5 class="card-title">AGENDA</h5>
                        <ol class="list-group list-group-numbered <?= ((!Yii::$app->user->isGuest && Yii::$app->user->identity->theme == 0) ? '' : 'gelap') ?>">
                            <li class="list-group-item d-flex justify-content-between align-items-start">
                                <div class="ms-2 me-auto">
                                    <div class="fw-bold">Jumlah Agenda Selesai/Direncanakan</div>
                                </div>
                                <span class="badge bg-primary rounded-pill"><?php echo $jumlahagenda ?> Kegiatan</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-start">
                                <div class="ms-2 me-auto">
                                    <div class="fw-bold">Jumlah Agenda Batal/Ditunda</div>
                                </div>
                                <span class="badge bg-primary rounded-pill"><?php echo $jumlahagendabatal ?> Kegiatan</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-start">
                                <div class="ms-2 me-auto">
                                    <div class="fw-bold">Project (Tim) dengan Agenda Terbanyak (input)</div>
                                </div>
                                <ol>
                                    <li>Project <span class='fw-bold'><?php echo count($agendatimtersering) > 0 ? $agendatimtersering[0]['projecte']['panggilan_project'] : '-' ?> : </span> <?php echo count($agendatimtersering) > 0 ? number_format($agendatimtersering[0]['jumlahinput'], 0, '', '.') : '-' ?> Kegiatan </li>
                                    <li>Project <span class='fw-bold'><?php echo  count($agendatimtersering) > 0 ? $agendatimtersering[1]['projecte']['panggilan_project'] : '-' ?> : </span> <?php echo count($agendatimtersering) > 0 ? number_format($agendatimtersering[1]['jumlahinput'], 0, '', '.') : '-' ?> Kegiatan </li>
                                    <li>Project <span class='fw-bold'><?php echo count($agendatimtersering) > 0 ? $agendatimtersering[2]['projecte']['panggilan_project'] : '-' ?> : </span> <?php echo count($agendatimtersering) > 0 ? number_format($agendatimtersering[2]['jumlahinput'], 0, '', '.') : '-' ?> Kegiatan </li>
                                </ol>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-start">
                                <div class="ms-2 me-auto">
                                    <div class="fw-bold">Pegawai dengan Agenda Terbanyak (Peserta dalam Agenda)</div>
                                </div>
                                <ol>
                                    <?php foreach ($agendapesertatersering as $email => $count) {
                                        echo "<li><span class='fw-bold'
                                                                        > $email</span> ($count kali)</li>  " . PHP_EOL;
                                    } ?>
                                </ol>
                            </li>
                        </ol>
                    </div>
                </div>
                <!-- SURAT INTERNAL -->
                <div class="card mb-3 <?= ((!Yii::$app->user->isGuest && Yii::$app->user->identity->theme == 0) ? 'bg-light' : 'bg-dark') ?>">
                    <div class="card-body <?= ((!Yii::$app->user->isGuest && Yii::$app->user->identity->theme == 0) ? '' : 'bg-dark text-light') ?>">
                        <h5 class="card-title">SURAT INTERNAL</h5>
                        <ol class="list-group list-group-numbered <?= ((!Yii::$app->user->isGuest && Yii::$app->user->identity->theme == 0) ? '' : 'gelap') ?>">
                            <li class="list-group-item d-flex justify-content-between align-items-start">
                                <div class="ms-2 me-auto">
                                    <div class="fw-bold">Jumlah Surat</div>
                                </div>
                                <span class="badge bg-primary rounded-pill"><?php echo number_format($jumlahsuratinternal, 0, '', '.') ?> Nomor</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-start">
                                <div class="ms-2 me-auto">
                                    <div class="fw-bold">Top Contributors</div>
                                </div>
                                <ol>
                                    <li><span class='fw-bold'><?php echo count($topcontributorsuratinternal) > 0 ? $topcontributorsuratinternal[0]['ownere']['nama'] : '-' ?> : </span> <?php echo count($topcontributorsuratinternal) > 0 ? number_format($topcontributorsuratinternal[0]['jumlahinput'], 0, '', '.') : '-' ?> Nomor </li>
                                    <li><span class='fw-bold'><?php echo count($topcontributorsuratinternal) > 0 ? $topcontributorsuratinternal[1]['ownere']['nama'] : '-' ?> : </span> <?php echo count($topcontributorsuratinternal) > 0 ? number_format($topcontributorsuratinternal[1]['jumlahinput'], 0, '', '.') : '-' ?> Nomor </li>
                                    <li><span class='fw-bold'><?php echo count($topcontributorsuratinternal) > 0 ? $topcontributorsuratinternal[2]['ownere']['nama'] : '-' ?> : </span> <?php echo count($topcontributorsuratinternal) > 0 ? number_format($topcontributorsuratinternal[2]['jumlahinput'], 0, '', '.') : '-' ?> Nomor </li>
                                </ol>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-start">
                                <div class="ms-2 me-auto">
                                    <div class="fw-bold">Cakupan Surat Terbanyak</div>
                                </div>
                                <ol>
                                    <li><span class='fw-bold'><?php echo count($suratinternalcakupan) > 0 ? $suratinternalcakupan[0]['suratkodee']['rincian_suratkode'] : '-' ?> : </span> <?php echo count($suratinternalcakupan) > 0 ? number_format($suratinternalcakupan[0]['jumlahinput'], 0, '', '.')  : '-' ?> Kali </li>
                                    <li><span class='fw-bold'><?php echo count($suratinternalcakupan) > 0 ? $suratinternalcakupan[1]['suratkodee']['rincian_suratkode']  : '-' ?> : </span> <?php echo count($suratinternalcakupan) > 0 ? number_format($suratinternalcakupan[1]['jumlahinput'], 0, '', '.')  : '-' ?> Kali </li>
                                    <li><span class='fw-bold'><?php echo count($suratinternalcakupan) > 0 ? $suratinternalcakupan[2]['suratkodee']['rincian_suratkode']  : '-' ?> : </span> <?php echo count($suratinternalcakupan) > 0 ? number_format($suratinternalcakupan[2]['jumlahinput'], 0, '', '.')  : '-' ?> Kali </li>
                                </ol>
                            </li>
                        </ol>
                    </div>
                </div>
                <!-- SURAT EKSTERNAL -->
                <div class="card mb-3 <?= ((!Yii::$app->user->isGuest && Yii::$app->user->identity->theme == 0) ? 'bg-light' : 'bg-dark') ?>">
                    <div class="card-body <?= ((!Yii::$app->user->isGuest && Yii::$app->user->identity->theme == 0) ? '' : 'bg-dark text-light') ?>">
                        <h5 class="card-title">SURAT EKSTERNAL</h5>
                        <ol class="list-group list-group-numbered <?= ((!Yii::$app->user->isGuest && Yii::$app->user->identity->theme == 0) ? '' : 'gelap') ?>">
                            <li class="list-group-item d-flex justify-content-between align-items-start">
                                <div class="ms-2 me-auto">
                                    <div class="fw-bold">Jumlah Surat</div>
                                </div>
                                <span class="badge bg-primary rounded-pill"><?php echo number_format($jumlahsurateksternal, 0, '', '.') ?> Nomor</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-start">
                                <div class="ms-2 me-auto">
                                    <div class="fw-bold">Top Contributors</div>
                                </div>
                                <ol>
                                    <li><span class='fw-bold'><?php echo count($topcontributorsurateksternal) > 0 ? $topcontributorsurateksternal[0]['ownere']['nama'] : '-' ?> : </span> <?php echo count($topcontributorsurateksternal) > 0 ? number_format($topcontributorsurateksternal[0]['jumlahinput'], 0, '', '.') : '-' ?> Nomor </li>
                                    <li><span class='fw-bold'><?php echo count($topcontributorsurateksternal) > 0 ? $topcontributorsurateksternal[1]['ownere']['nama'] : '-' ?> : </span> <?php echo count($topcontributorsurateksternal) > 0 ? number_format($topcontributorsurateksternal[1]['jumlahinput'], 0, '', '.') : '-' ?> Nomor </li>
                                    <li><span class='fw-bold'><?php echo count($topcontributorsurateksternal) > 0 ? $topcontributorsurateksternal[2]['ownere']['nama'] : '-' ?> : </span> <?php echo count($topcontributorsurateksternal) > 0 ? number_format($topcontributorsurateksternal[2]['jumlahinput'], 0, '', '.') : '-' ?> Nomor </li>
                                </ol>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-start">
                                <div class="ms-2 me-auto">
                                    <div class="fw-bold">Cakupan Surat Terbanyak</div>
                                </div>
                                <ol>
                                    <li><span class='fw-bold'><?php echo count($surateksternalcakupan) > 0 ? $surateksternalcakupan[0]['suratkodee']['rincian_suratkode'] : '-' ?> : </span> <?php echo count($surateksternalcakupan) > 0 ? number_format($surateksternalcakupan[0]['jumlahinput'], 0, '', '.')  : '-' ?> Kali </li>
                                    <li><span class='fw-bold'><?php echo count($surateksternalcakupan) > 0 ? $surateksternalcakupan[1]['suratkodee']['rincian_suratkode']  : '-' ?> : </span> <?php echo count($surateksternalcakupan) > 0 ? number_format($surateksternalcakupan[1]['jumlahinput'], 0, '', '.')  : '-' ?> Kali </li>
                                    <li><span class='fw-bold'><?php echo count($surateksternalcakupan) > 0 ? $surateksternalcakupan[2]['suratkodee']['rincian_suratkode']  : '-' ?> : </span> <?php echo count($surateksternalcakupan) > 0 ? number_format($surateksternalcakupan[2]['jumlahinput'], 0, '', '.')  : '-' ?> Kali </li>
                                </ol>
                            </li>
                        </ol>
                    </div>
                </div>
                <!-- AGENDA -->
                <div class="card mb-3 <?= ((!Yii::$app->user->isGuest && Yii::$app->user->identity->theme == 0) ? 'bg-light' : 'bg-dark text-light') ?>">
                    <div class="card-body <?= ((!Yii::$app->user->isGuest && Yii::$app->user->identity->theme == 0) ? '' : 'bg-dark text-light') ?>">
                        <h2 class="text-center">JUMLAH AGENDA PER BULAN</h2>
                        <?php
                        $data = implode(", ", $graphagenda);
                        $datalabel = implode('", "', $graphagendalabel);
                        ?>
                        <!-- Line Chart -->
                        <div id="reportsChart"></div>
                        <script>
                            document.addEventListener("DOMContentLoaded", () => {
                                new ApexCharts(document.querySelector("#reportsChart"), {
                                    series: [{
                                        name: 'Kegiatan/Agenda',
                                        data: <?php echo '[' . $data . ']' ?>,
                                    }],
                                    chart: {
                                        height: 350,
                                        type: 'area',
                                        toolbar: {
                                            show: false
                                        },
                                    },
                                    theme: {
                                        mode: '<?php echo ((!Yii::$app->user->isGuest && Yii::$app->user->identity->theme == 0) ? 'light' : 'dark') ?>',
                                    },
                                    markers: {
                                        size: 4
                                    },
                                    colors: ['#4154f1', '#2eca6a', '#ff771d'],
                                    fill: {
                                        type: "gradient",
                                        gradient: {
                                            shadeIntensity: 1,
                                            opacityFrom: 0.3,
                                            opacityTo: 0.4,
                                            stops: [0, 90, 100]
                                        }
                                    },
                                    dataLabels: {
                                        enabled: false
                                    },
                                    stroke: {
                                        curve: 'smooth',
                                        width: 2
                                    },
                                    xaxis: {
                                        type: 'string',
                                        categories: <?php echo '["' . $datalabel . '"]' ?>
                                    },
                                    tooltip: {
                                        x: {
                                            format: 'dd/MM/yy HH:mm'
                                        },
                                    }
                                }).render();
                            });
                        </script>
                        <!-- End Line Chart -->
                    </div>
                </div>

                <!-- SURAT INTERNAL -->
                <div class="card mb-3 <?= ((!Yii::$app->user->isGuest && Yii::$app->user->identity->theme == 0) ? 'bg-light' : 'bg-dark') ?>">
                    <div class="card-body <?= ((!Yii::$app->user->isGuest && Yii::$app->user->identity->theme == 0) ? '' : 'bg-dark text-light') ?>">
                        <h2 class="text-center">JUMLAH SURAT INTERNAL PER BULAN</h2>
                        <?php
                        $datasuratinternal = implode(", ", $graphsuratinternal);
                        $datasuratinternallabel = implode('", "', $graphsuratinternallabel);
                        // echo $datasuratinternallabel;
                        ?>
                        <!-- Line Chart -->
                        <div id="suratinternal"></div>
                        <script>
                            document.addEventListener("DOMContentLoaded", () => {
                                new ApexCharts(document.querySelector("#suratinternal"), {
                                    series: [{
                                        name: 'Surat Internal',
                                        data: <?php echo '[' . $datasuratinternal . ']' ?>,
                                    }, ],
                                    chart: {
                                        height: 350,
                                        type: 'area',
                                        toolbar: {
                                            show: false
                                        },
                                    },
                                    theme: {
                                        mode: '<?php echo ((!Yii::$app->user->isGuest && Yii::$app->user->identity->theme == 0) ? 'light' : 'dark') ?>',
                                    },
                                    markers: {
                                        size: 4
                                    },
                                    colors: ['#4154f1'],
                                    fill: {
                                        type: "gradient",
                                        gradient: {
                                            shadeIntensity: 1,
                                            opacityFrom: 0.3,
                                            opacityTo: 0.4,
                                            stops: [0, 90, 100]
                                        }
                                    },
                                    dataLabels: {
                                        enabled: false
                                    },
                                    stroke: {
                                        curve: 'smooth',
                                        width: 2
                                    },
                                    xaxis: {
                                        type: 'string',
                                        categories: <?php echo '["' . $datasuratinternallabel . '"]' ?>
                                    },
                                    tooltip: {
                                        x: {
                                            format: 'dd/MM/yy HH:mm'
                                        },
                                    }
                                }).render();
                            });
                        </script>
                        <!-- End Line Chart -->
                    </div>
                </div>

                <!-- SURAT EKSTERNAL -->
                <div class="card mb-3 <?= ((!Yii::$app->user->isGuest && Yii::$app->user->identity->theme == 0) ? 'bg-light' : 'bg-dark') ?>">
                    <div class="card-body <?= ((!Yii::$app->user->isGuest && Yii::$app->user->identity->theme == 0) ? '' : 'bg-dark text-light') ?>">
                        <h2 class="text-center">JUMLAH SURAT EKSTERNAL PER BULAN</h2>
                        <?php
                        $datasurateksternal = implode(", ", $graphsurateksternal);
                        $datasurateksternallabel = implode('", "', $graphsurateksternallabel);
                        // echo $datasurateksternallabel;
                        ?>
                        <!-- Line Chart -->
                        <div id="surateksternal"></div>
                        <script>
                            document.addEventListener("DOMContentLoaded", () => {
                                new ApexCharts(document.querySelector("#surateksternal"), {
                                    series: [{
                                        name: 'Surat Eksternal',
                                        data: <?php echo '[' . $datasurateksternal . ']' ?>,
                                    }],
                                    chart: {
                                        height: 350,
                                        type: 'area',
                                        toolbar: {
                                            show: false
                                        },
                                    },
                                    theme: {
                                        mode: '<?php echo ((!Yii::$app->user->isGuest && Yii::$app->user->identity->theme == 0) ? 'light' : 'dark') ?>',
                                    },
                                    markers: {
                                        size: 4
                                    },
                                    colors: ['#2eca6a'],
                                    fill: {
                                        type: "gradient",
                                        gradient: {
                                            shadeIntensity: 1,
                                            opacityFrom: 0.3,
                                            opacityTo: 0.4,
                                            stops: [0, 90, 100]
                                        }
                                    },
                                    dataLabels: {
                                        enabled: false
                                    },
                                    stroke: {
                                        curve: 'smooth',
                                        width: 2
                                    },
                                    xaxis: {
                                        type: 'string',
                                        categories: <?php echo '["' . $datasurateksternallabel . '"]' ?>
                                    },
                                    tooltip: {
                                        x: {
                                            format: 'dd/MM/yy HH:mm'
                                        },
                                    }
                                }).render();
                            });
                        </script>
                        <!-- End Line Chart -->
                    </div>
                </div>
            </div>
        </div><!-- End Left side columns -->
        <!-- Right side columns -->
        <div class="col-lg-6">
            <div class="row">
                <h1>Tahun Lalu (<?php echo ($year == 2023) ? 'Mei - Desember 2023' : ($year - 1) ?>) </h1>
                <!-- SNAPSHOT -->
                <div class="card mb-3 <?= ((!Yii::$app->user->isGuest && Yii::$app->user->identity->theme == 0) ? 'bg-light' : 'bg-dark') ?>">
                    <div class="card-body <?= ((!Yii::$app->user->isGuest && Yii::$app->user->identity->theme == 0) ? '' : 'bg-dark text-light') ?>">
                        <h5 class="card-title">Snapshot Input Data</h5>
                        <ol class="list-group list-group-numbered <?= ((!Yii::$app->user->isGuest && Yii::$app->user->identity->theme == 0) ? '' : 'gelap') ?>">
                            <li class="list-group-item d-flex justify-content-between align-items-start">
                                <div class="ms-2 me-auto">
                                    <div class="fw-bold">Jumlah Agenda Selesai/Direncanakan</div>
                                </div>
                                <span class="badge bg-primary rounded-pill"><?php echo $jumlahagendabefore ?> Kegiatan</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-start">
                                <div class="ms-2 me-auto">
                                    <div class="fw-bold">Jumlah Agenda Batal/Ditunda</div>
                                </div>
                                <span class="badge bg-primary rounded-pill"><?php echo $jumlahagendabatalbefore ?> Kegiatan</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-start">
                                <div class="ms-2 me-auto">
                                    <div class="fw-bold">Jumlah Surat Internal</div>
                                    <?php echo ($year == 2023) ? '(Sejak 24 Mei 2023)' : $year ?>
                                </div>
                                <span class="badge bg-primary rounded-pill"><?php echo number_format($jumlahsuratinternalbefore, 0, '', '.') ?> Nomor</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-start">
                                <div class="ms-2 me-auto">
                                    <div class="fw-bold">Jumlah Surat Eksternal</div>
                                    <?php echo ($year == 2023) ? '(Sejak 19 Mei 2023)' : $year ?>
                                </div>
                                <span class="badge bg-primary rounded-pill"><?php echo number_format($jumlahsurateksternalbefore, 0, '', '.') ?> Nomor</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-start">
                                <div class="ms-2 me-auto">
                                    <div class="fw-bold">Jumlah Link Aplikasi yang Diinput</div>
                                </div>
                                <span class="badge bg-primary rounded-pill"><?php echo number_format($jumlahaplikasibefore, 0, '', '.') ?> Links</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-start">
                                <div class="ms-2 me-auto">
                                    <div class="fw-bold">Jumlah Link Sharing yang Diinput</div>
                                </div>
                                <span class="badge bg-primary rounded-pill"><?php echo number_format($jumlahmateribefore, 0, '', '.') ?> Links</span>
                            </li>
                        </ol>
                    </div>
                </div>

                <!-- TOP CONTRIBUTORS -->

                <div class="card mb-3 <?= ((!Yii::$app->user->isGuest && Yii::$app->user->identity->theme == 0) ? 'bg-light' : 'bg-dark') ?>">
                    <div class="card-body <?= ((!Yii::$app->user->isGuest && Yii::$app->user->identity->theme == 0) ? '' : 'bg-dark text-light') ?>">
                        <h5 class="card-title">Top Contributors</h5>
                        <em class="mb-1">Berdasarkan atribut "operator" (yang menginput)</em>
                        <ol class="list-group list-group-numbered <?= ((!Yii::$app->user->isGuest && Yii::$app->user->identity->theme == 0) ? '' : 'gelap') ?>">
                            <li class="list-group-item d-flex justify-content-between align-items-start">
                                <div class="ms-2 me-auto">
                                    Agenda
                                    <div class="fw-bold"><?php echo count($topcontributoragendabefore) > 0 ? $topcontributoragendabefore[0]['reportere']['nama'] : '-' ?></div>
                                </div>
                                <span class="badge bg-primary rounded-pill"><?php echo count($topcontributoragendabefore) > 0 ? $topcontributoragendabefore[0]['jumlahinput'] : '-' ?> Kegiatan</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-start">
                                <div class="ms-2 me-auto">
                                    Surat Internal <?php echo ($year == 2023) ? '(Dari 24 Mei)' : '' ?>
                                    <div class="fw-bold"><?php echo count($topcontributorsuratinternalbefore) > 0 ?  $topcontributorsuratinternalbefore[0]['ownere']['nama'] : '-' ?></div>
                                </div>
                                <span class="badge bg-primary rounded-pill"><?php echo count($topcontributorsuratinternalbefore) > 0 ? $topcontributorsuratinternalbefore[0]['jumlahinput'] : '-' ?> Nomor</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-start">
                                <div class="ms-2 me-auto">
                                    Link Aplikasi
                                    <div class="fw-bold"><?php echo count($topcontributoraplikasibefore) > 0 ? $topcontributoraplikasibefore[0]['ownere']['nama'] : '-'  ?></div>
                                </div>
                                <span class="badge bg-primary rounded-pill"><?php echo count($topcontributoraplikasibefore) > 0 ? $topcontributoraplikasibefore[0]['jumlahinput'] : '-'  ?> Links</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-start">
                                <div class="ms-2 me-auto">
                                    Link Materi
                                    <div class="fw-bold"><?php echo count($topcontributormateribefore) > 0 ? $topcontributormateribefore[0]['ownere']['nama'] : '-'  ?></div>
                                </div>
                                <span class="badge bg-primary rounded-pill"><?php echo count($topcontributormateribefore) > 0 ? $topcontributormateribefore[0]['jumlahinput'] : '-'  ?> Links</span>
                            </li>
                        </ol>
                    </div>
                </div>

                <!-- AGENDA -->
                <div class="card mb-3 <?= ((!Yii::$app->user->isGuest && Yii::$app->user->identity->theme == 0) ? 'bg-light' : 'bg-dark') ?>">
                    <div class="card-body <?= ((!Yii::$app->user->isGuest && Yii::$app->user->identity->theme == 0) ? '' : 'bg-dark text-light') ?>">
                        <h5 class="card-title">AGENDA</h5>
                        <ol class="list-group list-group-numbered <?= ((!Yii::$app->user->isGuest && Yii::$app->user->identity->theme == 0) ? '' : 'gelap') ?>">
                            <li class="list-group-item d-flex justify-content-between align-items-start">
                                <div class="ms-2 me-auto">
                                    <div class="fw-bold">Jumlah Agenda Selesai/Direncanakan</div>
                                </div>
                                <span class="badge bg-primary rounded-pill"><?php echo $jumlahagendabefore ?> Kegiatan</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-start">
                                <div class="ms-2 me-auto">
                                    <div class="fw-bold">Jumlah Agenda Batal/Ditunda</div>
                                </div>
                                <span class="badge bg-primary rounded-pill"><?php echo $jumlahagendabatalbefore ?> Kegiatan</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-start">
                                <div class="ms-2 me-auto">
                                    <div class="fw-bold">Project (Tim) dengan Agenda Terbanyak (input)</div>
                                </div>
                                <ol>
                                    <li>Project <span class='fw-bold'><?php echo count($agendatimterseringbefore) > 0 ? $agendatimterseringbefore[0]['projecte']['panggilan_project'] : '-' ?> : </span> <?php echo count($agendatimterseringbefore) > 0 ? number_format($agendatimterseringbefore[0]['jumlahinput'], 0, '', '.') : '-' ?> Kegiatan </li>
                                    <li>Project <span class='fw-bold'><?php echo  count($agendatimterseringbefore) > 0 ? $agendatimterseringbefore[1]['projecte']['panggilan_project'] : '-' ?> : </span> <?php echo count($agendatimterseringbefore) > 0 ? number_format($agendatimterseringbefore[1]['jumlahinput'], 0, '', '.') : '-' ?> Kegiatan </li>
                                    <li>Project <span class='fw-bold'><?php echo count($agendatimterseringbefore) > 0 ? $agendatimterseringbefore[2]['projecte']['panggilan_project'] : '-' ?> : </span> <?php echo count($agendatimterseringbefore) > 0 ? number_format($agendatimterseringbefore[2]['jumlahinput'], 0, '', '.') : '-' ?> Kegiatan </li>
                                </ol>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-start">
                                <div class="ms-2 me-auto">
                                    <div class="fw-bold">Pegawai dengan Agenda Terbanyak (Peserta dalam Agenda)</div>
                                </div>
                                <ol>
                                    <?php foreach ($agendapesertaterseringbefore as $email => $count) {
                                        echo "<li><span class='fw-bold'
                                                                        > $email</span> ($count kali)</li>  " . PHP_EOL;
                                    } ?>
                                </ol>
                            </li>
                        </ol>
                    </div>
                </div>
                <!-- SURAT INTERNAL -->
                <div class="card mb-3 <?= ((!Yii::$app->user->isGuest && Yii::$app->user->identity->theme == 0) ? 'bg-light' : 'bg-dark') ?>">
                    <div class="card-body <?= ((!Yii::$app->user->isGuest && Yii::$app->user->identity->theme == 0) ? '' : 'bg-dark text-light') ?>">
                        <h5 class="card-title">SURAT INTERNAL</h5>
                        <ol class="list-group list-group-numbered <?= ((!Yii::$app->user->isGuest && Yii::$app->user->identity->theme == 0) ? '' : 'gelap') ?>">
                            <li class="list-group-item d-flex justify-content-between align-items-start">
                                <div class="ms-2 me-auto">
                                    <div class="fw-bold">Jumlah Surat</div>
                                </div>
                                <span class="badge bg-primary rounded-pill"><?php echo number_format($jumlahsuratinternalbefore, 0, '', '.') ?> Nomor</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-start">
                                <div class="ms-2 me-auto">
                                    <div class="fw-bold">Top Contributors</div>
                                </div>
                                <ol>
                                    <li><span class='fw-bold'><?php echo count($topcontributorsuratinternalbefore) > 0 ? $topcontributorsuratinternalbefore[0]['ownere']['nama'] : '-' ?> : </span> <?php echo count($topcontributorsuratinternalbefore) > 0 ? number_format($topcontributorsuratinternalbefore[0]['jumlahinput'], 0, '', '.') : '-' ?> Nomor </li>
                                    <li><span class='fw-bold'><?php echo count($topcontributorsuratinternalbefore) > 0 ? $topcontributorsuratinternalbefore[1]['ownere']['nama'] : '-' ?> : </span> <?php echo count($topcontributorsuratinternalbefore) > 0 ? number_format($topcontributorsuratinternalbefore[1]['jumlahinput'], 0, '', '.') : '-' ?> Nomor </li>
                                    <li><span class='fw-bold'><?php echo count($topcontributorsuratinternalbefore) > 0 ? $topcontributorsuratinternalbefore[2]['ownere']['nama'] : '-' ?> : </span> <?php echo count($topcontributorsuratinternalbefore) > 0 ? number_format($topcontributorsuratinternalbefore[2]['jumlahinput'], 0, '', '.') : '-' ?> Nomor </li>
                                </ol>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-start">
                                <div class="ms-2 me-auto">
                                    <div class="fw-bold">Cakupan Surat Terbanyak</div>
                                </div>
                                <ol>
                                    <li><span class='fw-bold'><?php echo count($suratinternalcakupanbefore) > 0 ? $suratinternalcakupanbefore[0]['suratkodee']['rincian_suratkode'] : '-' ?> : </span> <?php echo count($suratinternalcakupanbefore) > 0 ? number_format($suratinternalcakupanbefore[0]['jumlahinput'], 0, '', '.')  : '-' ?> Kali </li>
                                    <li><span class='fw-bold'><?php echo count($suratinternalcakupanbefore) > 0 ? $suratinternalcakupanbefore[1]['suratkodee']['rincian_suratkode']  : '-' ?> : </span> <?php echo count($suratinternalcakupanbefore) > 0 ? number_format($suratinternalcakupanbefore[1]['jumlahinput'], 0, '', '.')  : '-' ?> Kali </li>
                                    <li><span class='fw-bold'><?php echo count($suratinternalcakupanbefore) > 0 ? $suratinternalcakupanbefore[2]['suratkodee']['rincian_suratkode']  : '-' ?> : </span> <?php echo count($suratinternalcakupanbefore) > 0 ? number_format($suratinternalcakupanbefore[2]['jumlahinput'], 0, '', '.')  : '-' ?> Kali </li>
                                </ol>
                            </li>
                        </ol>
                    </div>
                </div>
                <!-- SURAT EKSTERNAL -->
                <div class="card mb-3 <?= ((!Yii::$app->user->isGuest && Yii::$app->user->identity->theme == 0) ? 'bg-light' : 'bg-dark') ?>">
                    <div class="card-body <?= ((!Yii::$app->user->isGuest && Yii::$app->user->identity->theme == 0) ? '' : 'bg-dark text-light') ?>">
                        <h5 class="card-title">SURAT EKSTERNAL</h5>
                        <ol class="list-group list-group-numbered <?= ((!Yii::$app->user->isGuest && Yii::$app->user->identity->theme == 0) ? '' : 'gelap') ?>">
                            <li class="list-group-item d-flex justify-content-between align-items-start">
                                <div class="ms-2 me-auto">
                                    <div class="fw-bold">Jumlah Surat</div>
                                </div>
                                <span class="badge bg-primary rounded-pill"><?php echo number_format($jumlahsurateksternalbefore, 0, '', '.') ?> Nomor</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-start">
                                <div class="ms-2 me-auto">
                                    <div class="fw-bold">Top Contributors</div>
                                </div>
                                <ol>
                                    <li><span class='fw-bold'><?php echo count($topcontributorsurateksternalbefore) > 0 ? $topcontributorsurateksternalbefore[0]['ownere']['nama'] : '-' ?> : </span> <?php echo count($topcontributorsurateksternalbefore) > 0 ? number_format($topcontributorsurateksternalbefore[0]['jumlahinput'], 0, '', '.') : '-' ?> Nomor </li>
                                    <li><span class='fw-bold'><?php echo count($topcontributorsurateksternalbefore) > 0 ? $topcontributorsurateksternalbefore[1]['ownere']['nama'] : '-' ?> : </span> <?php echo count($topcontributorsurateksternalbefore) > 0 ? number_format($topcontributorsurateksternalbefore[1]['jumlahinput'], 0, '', '.') : '-' ?> Nomor </li>
                                    <li><span class='fw-bold'><?php echo count($topcontributorsurateksternalbefore) > 0 ? $topcontributorsurateksternalbefore[2]['ownere']['nama'] : '-' ?> : </span> <?php echo count($topcontributorsurateksternalbefore) > 0 ? number_format($topcontributorsurateksternalbefore[2]['jumlahinput'], 0, '', '.') : '-' ?> Nomor </li>
                                </ol>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-start">
                                <div class="ms-2 me-auto">
                                    <div class="fw-bold">Cakupan Surat Terbanyak</div>
                                </div>
                                <ol>
                                    <li><span class='fw-bold'><?php echo count($surateksternalcakupanbefore) > 0 ? $surateksternalcakupanbefore[0]['suratkodee']['rincian_suratkode'] : '-' ?> : </span> <?php echo count($surateksternalcakupanbefore) > 0 ? number_format($surateksternalcakupanbefore[0]['jumlahinput'], 0, '', '.')  : '-' ?> Kali </li>
                                    <li><span class='fw-bold'><?php echo count($surateksternalcakupanbefore) > 0 ? $surateksternalcakupanbefore[1]['suratkodee']['rincian_suratkode']  : '-' ?> : </span> <?php echo count($surateksternalcakupanbefore) > 0 ? number_format($surateksternalcakupanbefore[1]['jumlahinput'], 0, '', '.')  : '-' ?> Kali </li>
                                    <li><span class='fw-bold'><?php echo count($surateksternalcakupanbefore) > 0 ? $surateksternalcakupanbefore[2]['suratkodee']['rincian_suratkode']  : '-' ?> : </span> <?php echo count($surateksternalcakupanbefore) > 0 ? number_format($surateksternalcakupanbefore[2]['jumlahinput'], 0, '', '.')  : '-' ?> Kali </li>
                                </ol>
                            </li>
                        </ol>
                    </div>
                </div>
                <!-- AGENDA -->
                <div class="card mb-3 <?= ((!Yii::$app->user->isGuest && Yii::$app->user->identity->theme == 0) ? 'bg-light' : 'bg-dark text-light') ?>">
                    <div class="card-body <?= ((!Yii::$app->user->isGuest && Yii::$app->user->identity->theme == 0) ? '' : 'bg-dark text-light') ?>">
                        <h2 class="text-center">JUMLAH AGENDA PER BULAN</h2>
                        <?php
                        $databefore = implode(", ", $graphagendabefore);
                        $datalabelbefore = implode('", "', $graphagendalabelbefore);
                        ?>
                        <!-- Line Chart -->
                        <div id="reportsChartbefore"></div>
                        <script>
                            document.addEventListener("DOMContentLoaded", () => {
                                new ApexCharts(document.querySelector("#reportsChartbefore"), {
                                    series: [{
                                        name: 'Kegiatan/Agenda',
                                        data: <?php echo '[' . $databefore . ']' ?>,
                                    }],
                                    chart: {
                                        height: 350,
                                        type: 'area',
                                        toolbar: {
                                            show: false
                                        },
                                    },
                                    theme: {
                                        mode: '<?php echo ((!Yii::$app->user->isGuest && Yii::$app->user->identity->theme == 0) ? 'light' : 'dark') ?>',
                                    },
                                    markers: {
                                        size: 4
                                    },
                                    colors: ['#4154f1', '#2eca6a', '#ff771d'],
                                    fill: {
                                        type: "gradient",
                                        gradient: {
                                            shadeIntensity: 1,
                                            opacityFrom: 0.3,
                                            opacityTo: 0.4,
                                            stops: [0, 90, 100]
                                        }
                                    },
                                    dataLabels: {
                                        enabled: false
                                    },
                                    stroke: {
                                        curve: 'smooth',
                                        width: 2
                                    },
                                    xaxis: {
                                        type: 'string',
                                        categories: <?php echo '["' . $datalabelbefore . '"]' ?>
                                    },
                                    tooltip: {
                                        x: {
                                            format: 'dd/MM/yy HH:mm'
                                        },
                                    }
                                }).render();
                            });
                        </script>
                        <!-- End Line Chart -->
                    </div>
                </div>

                <!-- SURAT INTERNAL -->
                <div class="card mb-3 <?= ((!Yii::$app->user->isGuest && Yii::$app->user->identity->theme == 0) ? 'bg-light' : 'bg-dark') ?>">
                    <div class="card-body <?= ((!Yii::$app->user->isGuest && Yii::$app->user->identity->theme == 0) ? '' : 'bg-dark text-light') ?>">
                        <h2 class="text-center">JUMLAH SURAT INTERNAL PER BULAN</h2>
                        <?php
                        $datasuratinternalbefore = implode(", ", $graphsuratinternalbefore);
                        $datasuratinternallabelbefore = implode('", "', $graphsuratinternallabelbefore);
                        // echo $datasuratinternallabel;
                        ?>
                        <!-- Line Chart -->
                        <div id="suratinternalbefore"></div>
                        <script>
                            document.addEventListener("DOMContentLoaded", () => {
                                new ApexCharts(document.querySelector("#suratinternalbefore"), {
                                    series: [{
                                        name: 'Surat Internal',
                                        data: <?php echo '[' . $datasuratinternalbefore . ']' ?>,
                                    }, ],
                                    chart: {
                                        height: 350,
                                        type: 'area',
                                        toolbar: {
                                            show: false
                                        },
                                    },
                                    theme: {
                                        mode: '<?php echo ((!Yii::$app->user->isGuest && Yii::$app->user->identity->theme == 0) ? 'light' : 'dark') ?>',
                                    },
                                    markers: {
                                        size: 4
                                    },
                                    colors: ['#4154f1'],
                                    fill: {
                                        type: "gradient",
                                        gradient: {
                                            shadeIntensity: 1,
                                            opacityFrom: 0.3,
                                            opacityTo: 0.4,
                                            stops: [0, 90, 100]
                                        }
                                    },
                                    dataLabels: {
                                        enabled: false
                                    },
                                    stroke: {
                                        curve: 'smooth',
                                        width: 2
                                    },
                                    xaxis: {
                                        type: 'string',
                                        categories: <?php echo '["' . $datasuratinternallabelbefore . '"]' ?>
                                    },
                                    tooltip: {
                                        x: {
                                            format: 'dd/MM/yy HH:mm'
                                        },
                                    }
                                }).render();
                            });
                        </script>
                        <!-- End Line Chart -->
                    </div>
                </div>

                <!-- SURAT EKSTERNAL -->
                <div class="card mb-3 <?= ((!Yii::$app->user->isGuest && Yii::$app->user->identity->theme == 0) ? 'bg-light' : 'bg-dark') ?>">
                    <div class="card-body <?= ((!Yii::$app->user->isGuest && Yii::$app->user->identity->theme == 0) ? '' : 'bg-dark text-light') ?>">
                        <h2 class="text-center">JUMLAH SURAT EKSTERNAL PER BULAN</h2>
                        <?php
                        $datasurateksternalbefore = implode(", ", $graphsurateksternalbefore);
                        $datasurateksternallabelbefore = implode('", "', $graphsurateksternallabelbefore);
                        // echo $datasurateksternallabel;
                        ?>
                        <!-- Line Chart -->
                        <div id="surateksternalbefore"></div>
                        <script>
                            document.addEventListener("DOMContentLoaded", () => {
                                new ApexCharts(document.querySelector("#surateksternalbefore"), {
                                    series: [{
                                        name: 'Surat Eksternal',
                                        data: <?php echo '[' . $datasurateksternalbefore . ']' ?>,
                                    }],
                                    chart: {
                                        height: 350,
                                        type: 'area',
                                        toolbar: {
                                            show: false
                                        },
                                    },
                                    theme: {
                                        mode: '<?php echo ((!Yii::$app->user->isGuest && Yii::$app->user->identity->theme == 0) ? 'light' : 'dark') ?>',
                                    },
                                    markers: {
                                        size: 4
                                    },
                                    colors: ['#2eca6a'],
                                    fill: {
                                        type: "gradient",
                                        gradient: {
                                            shadeIntensity: 1,
                                            opacityFrom: 0.3,
                                            opacityTo: 0.4,
                                            stops: [0, 90, 100]
                                        }
                                    },
                                    dataLabels: {
                                        enabled: false
                                    },
                                    stroke: {
                                        curve: 'smooth',
                                        width: 2
                                    },
                                    xaxis: {
                                        type: 'string',
                                        categories: <?php echo '["' . $datasurateksternallabelbefore . '"]' ?>
                                    },
                                    tooltip: {
                                        x: {
                                            format: 'dd/MM/yy HH:mm'
                                        },
                                    }
                                }).render();
                            });
                        </script>
                        <!-- End Line Chart -->
                    </div>
                </div>
            </div>
        </div><!-- End Right side columns -->
    </div>
    </div>