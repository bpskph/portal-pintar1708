<?php

use yii\helpers\Html;
use yii\web\View;
use kartik\detail\DetailView;

$this->title = 'Detail Surat # ' . $model->id_suratrepo;
$this->registerJsFile(Yii::$app->request->baseUrl . '/library/js/fi-copy-clipboard.js', ['position' => View::POS_END, 'depends' => [\yii\web\JqueryAsset::class]]);
?>
<link href="<?php echo Yii::$app->request->baseUrl; ?>/library/fi-page-invoice.css" rel="stylesheet">
<div class="container-fluid" data-aos="fade-up">
    <h1><?= Html::encode($this->title) ?></h1>
    <div class="d-flex justify-content-between">
        <div class="p-2">
            <h5>
                <?= Html::a('<i class="fas fa-scroll"></i> Surat Internal', ['index?owner=&year=' . date("Y")], ['class' => 'btn btn btn-outline-warning btn-sm']) ?>
            </h5>
        </div>
        <div class="p-2">
            <?php if (!Yii::$app->user->isGuest && (($model->owner === Yii::$app->user->identity->username && $model->fk_agenda == NULL) || ($model->owner === Yii::$app->user->identity->username && $model->fk_agenda != NULL && $model->agendae->progress != 3))) : ?>
                <?= Html::a('<i class="fas fa-edit"></i> Update', ['update', 'id' => $model->id_suratrepo], ['class' => 'btn btn-sm btn-warning']) ?>
                <?= Html::a('<i class="far fa-trash-alt"></i> Hapus', ['delete', 'id' => $model->id_suratrepo], [
                    'class' => 'btn btn-sm btn-danger',
                    'data' => [
                        'confirm' => 'Anda yakin akan menghapus surat ini?',
                        'method' => 'post',
                        'bs-toggle' => 'modal',
                        'bs-dismiss' => 'modal'
                    ],
                ]) ?>
            <?php endif; ?>
        </div>
    </div>
    <div class="row d-flex align-items-stretch">
        <div class="<?= (($model->is_undangan != null && $model->is_undangan == 1) ? 'col-lg-6' : 'col-lg-12') ?>">
            <div class="card <?= ((!Yii::$app->user->isGuest && Yii::$app->user->identity->theme == 0) ? '' : 'bg-dark') ?> h-100">
                <div class="card-body">
                    <?php if (isset($model->fk_agenda)) : ?>
                        <h3>
                            <span class="badge bg-primary">Detail Agenda</span>
                        </h3>
                        <?= $header; ?>
                        <hr class="bps" />
                    <?php endif; ?>
                    <h3>
                        <span class="badge bg-primary">Detail Surat</span>
                    </h3>
                    <?=
                    DetailView::widget([
                        'model' => $model,
                        'options' => ['class' => 'table ' . ((!Yii::$app->user->isGuest && Yii::$app->user->identity->theme == 0) ? '' : 'table-dark')],
                        'condensed' => true,
                        'striped' => false,
                        'bordered' => false,
                        'hover' => true,
                        'hAlign' => 'left',
                        'attributes' => [
                            'id_suratrepo',
                            'penerima_suratrepo',
                            [
                                'attribute' => 'tanggal_suratrepo',
                                'value' => \Yii::$app->formatter->asDatetime(strtotime($model->tanggal_suratrepo), "d MMMM y"),
                            ],
                            'perihal_suratrepo:ntext',
                            [
                                'attribute' => 'lampiran',
                                'value' => empty($model->lampiran) ? '-' : $model->lampiran,
                            ],
                            [
                                'attribute' => 'jenis',
                                'value' => $model->jenis == 0 ? '<span class="badge bg-primary rounded-pill"><i class="fas fa-scroll"></i> Surat Biasa</span>' : ($model->jenis == 1 ? '<span class="badge bg-success rounded-pill"><i class="fas fa-scroll"></i> Nota Dinas</span>' : ($model->jenis == 2 ? '<span  class="badge bg-secondary rounded-pill"><i class="fas fa-scroll"></i> Surat Keterangan</span>' : ($model->jenis == 3 ? '<span  class="badge bg-info rounded-pill"><i class="fas fa-scroll"></i> Berita Acara</span>' :
                                    '<span class="badge bg-warning rounded-pill"><i class="fas fa-scroll"></i> Surat Lainnya</span>'))),
                                'format' => 'html',
                                'label' => 'Jenis Surat',
                            ],
                            [
                                'attribute' => 'fk_suratsubkode',
                                'value' => $model->suratsubkodee->fk_suratkode . '-' . $model->suratsubkodee->rincian_suratsubkode,
                            ],
                            [
                                'attribute' => 'nomor_suratrepo',
                                'format' => 'raw',
                                'value' => $model->nomor_suratrepo . ' ' . Html::button('<i class="fas fa-copy"></i> Copy', [
                                    'class' => 'btn btn-primary btn-sm ms-2',
                                    'onclick' => 'copyToClipboard("' . $model->nomor_suratrepo . '")',
                                ]),
                            ],
                            [
                                'attribute' => 'pihak_pertama',
                                'value' => (empty($model->pihak_pertama)) ? '-' : $model->pihakpertamae->nama,
                            ],
                            [
                                'attribute' => 'pihak_kedua',
                                'value' => (empty($model->pihak_pertama)) ? '-' : $model->pihakkeduae->nama,
                            ],
                            [
                                'attribute' => 'ttd_by_jabatan',
                                'value' => (empty($model->ttd_by_jabatan)) ? '-' : $model->ttd_by_jabatan,
                            ],
                            [
                                'attribute' => 'ttd_by',
                                'value' => (empty($model->ttd_by)) ? '-' : $model->ttd_by,
                            ],
                            [
                                'attribute' => 'owner',
                                'value' => $model->ownere->nama,
                            ],
                            [
                                'attribute' => 'timestamp',
                                'value' => \Yii::$app->formatter->asDatetime(strtotime($model->timestamp), "d MMMM y 'pada' H:mm a"),
                            ],
                            [
                                'attribute' => 'timestamp_suratrepo_lastupdate',
                                'value' => \Yii::$app->formatter->asDatetime(strtotime($model->timestamp_suratrepo_lastupdate), "d MMMM y 'pada' H:mm a"),
                            ],
                        ],
                    ])
                    ?>
                    <br />
                    <?php if ($model->isi_suratrepo != NULL) { ?>
                        <hr class="bps" />
                        <div class="text-center">
                            <h3>
                                <span class="badge bg-primary">Isi Surat</span>
                            </h3>
                            <?= Html::a('<i class="fas fa-file-pdf"></i> Cetak PDF', ['cetaksurat', 'id' => $model->id_suratrepo], ['class' => 'btn btn-sm btn btn-outline-warning', 'target' => '_blank']) ?>
                            <button id="btn-export" onclick="exportHTMLWord('<?php echo $model->perihal_suratrepo ?>')" type="button" class="btn btn-sm btn-outline-success">
                                <i class="fas fa-file-word"></i> Export to Word
                            </button>
                        </div>
                        <br />
                        <div class="container" id="source-html">
                            <div data-size="A4" style="min-height: 1200px">
                                <?php //echo $model->isi_suratrepo;
                                echo $html; ?>
                            </div>
                        </div>
                    <?php } ?>
                    <?php if ($model->isi_lampiran != NULL) { ?>
                        <hr class="bps" />
                        <div class="text-center">
                            <h3>
                                <span class="badge bg-primary">Lampiran Surat</span>
                            </h3>
                            <?= Html::a('<i class="fas fa-file-pdf"></i> Cetak PDF', ['cetaklampiran', 'id' => $model->id_suratrepo], ['class' => 'btn btn-sm btn btn-outline-warning', 'target' => '_blank']) ?>
                            <button id="btn-export" onclick="exportHTMLWordLampiran('Lampiran - <?php echo $model->perihal_suratrepo ?>')" type="button" class="btn btn-sm btn-outline-success">
                                <i class="fas fa-file-word"></i> Export to Word
                            </button>
                        </div>
                        <br />
                        <div class="container" id="source-html-lampiran">
                            <div data-size="A4" style="min-height: 1200px">
                                <?php //echo $model->isi_suratrepoeks;
                                echo $model->isi_lampiran; ?>
                            </div>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>
        <?php if ($model->is_undangan != null && $model->is_undangan == 1): ?>
            <div class="col-lg-6 d-flex">
                <div class="card h-100 w-100 <?= ((!Yii::$app->user->isGuest && Yii::$app->user->identity->theme == 0) ? '' : 'bg-dark text-light') ?>">
                    <div class="card-body">
                        <p class="fst-italic bg-warning"></p>
                        <div class="alert alert-warning" role="alert">
                            <h4 class="alert-heading">Generated PDF</h4>
                            <p>Tampilan ini <strong>tidak tersimpan dalam sistem.</strong> Mohon upload PDF surat (yang sudah ditandatangani) melalui menu utama Surat Internal.</p>
                        </div>
                        <br />
                        <iframe src="data:application/pdf;base64,<?= $base64Pdf ?>" width="100%" height="800px">
                            This browser does not support PDFs. Please download the PDF to view it.
                        </iframe>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>

</div>
<script src="https://unpkg.com/html-docx-js/dist/html-docx.js"></script>
<script>
    function exportHTMLWord(rapat) {
        var sourceHTML = document.getElementById("source-html").innerHTML;
        var converted = htmlDocx.asBlob(sourceHTML, {
            margins: {
                top: -100,
                bottom: 1200,
                left: 1200,
                right: 1200
            },
            padding: {
                left: 40,
                right: 40
            }
        });
        var fileDownload = document.createElement("a");
        document.body.appendChild(fileDownload);
        fileDownload.href = URL.createObjectURL(converted);
        fileDownload.download = rapat + '.docx';
        fileDownload.click();
        document.body.removeChild(fileDownload);
    }

    function exportHTMLWordLampiran(rapat) {
        var sourceHTML = document.getElementById("source-html-lampiran").innerHTML;
        var converted = htmlDocx.asBlob(sourceHTML, {
            margins: {
                top: -100,
                bottom: 1200,
                left: 1200,
                right: 1200
            },
            padding: {
                left: 40,
                right: 40
            }
        });
        var fileDownload = document.createElement("a");
        document.body.appendChild(fileDownload);
        fileDownload.href = URL.createObjectURL(converted);
        fileDownload.download = rapat + '.docx';
        fileDownload.click();
        document.body.removeChild(fileDownload);
    }
</script>