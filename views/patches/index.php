<?php
use yii\helpers\Html;
use kartik\grid\SerialColumn;
use kartik\grid\GridView;
use yii\bootstrap5\Modal;
use kartik\grid\ActionColumn;

$this->title = 'Daftar Patches Portal Pintar';
?>
<style>
    .kv-table-header {
        background: transparent !important;
    }
</style>
<div class="container-fluid" data-aos="fade-up">
    <h1 class="text-center"><?= Html::encode($this->title) ?></h1>
    <h5 class="text-center">Sejak 23 Mei 2023</h5>
    <h6 class="text-center"><i>by nofriani@bps.go.id</i></h6>
    <hr class="bps" />
    <?php if (!Yii::$app->user->isGuest && Yii::$app->user->identity->username == 'nofriani') : ?>
        <p class="text-right">
            <?= Html::a('<i class="fas fa-folder-plus"></i> Tambah Data Baru', ['create'], ['class' => 'btn btn btn-outline-warning btn-sm']) ?>
        </p>
    <?php endif; ?>
    <div class="card <?= ((!Yii::$app->user->isGuest && Yii::$app->user->identity->theme == 0) ? '' : 'bg-dark') ?>">
        <div class="card-body table-responsive p-0">
            <?php
            $layout = '
                        <div class="card-header ' . (!Yii::$app->user->isGuest ? Yii::$app->user->identity->themechoice : '') . '">
                            <div class="d-flex justify-content-between" style="margin-bottom: -0.8rem;">
                                <div class="p-2">
                                {toolbar}
                                </div>
                                <div class="p-2" style="margin-top:0.5rem;">
                                {summary}
                                </div>
                                <div class="p-2">
                                {pager}
                                </div>
                            </div>                            
                        </div>  
                        {items}
                    ';
            ?>
            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'tableOptions' => ['class' => 'table table-condensed ' . ((!Yii::$app->user->isGuest && Yii::$app->user->identity->theme == 0) ? '' : 'table-dark')],
                'columns' => [
                    [
                        'class' => SerialColumn::class,
                    ],
                    [
                        'attribute' => 'timestamp',
                        'value' => function ($model) {
                            return \Yii::$app->formatter->asDatetime(strtotime($model->timestamp), "d MMMM y 'pada' H:mm a");
                        },
                    ],
                    'title',
                    [
                        'attribute' => 'description',
                        'format' => 'html',
                    ],
                    [
                        'class' => ActionColumn::class,
                        'header' => 'Aksi',
                        'template' => '{wa_blast}',
                        'visibleButtons' => [
                            'wa_blast' => function ($model, $key, $index) {
                                return (!Yii::$app->user->isGuest
                                    && Yii::$app->user->identity->username === 'nofriani'
                                    && $model['is_notification'] === 1 
                                ) ? true : false;
                            },                            
                        ],
                        'visible'=>function ($model, $key, $index) {
                            return (!Yii::$app->user->isGuest
                                && Yii::$app->user->identity->username === 'nofriani'
                            ) ? true : false;
                        },
                        'buttons'  => [                            
                            'wa_blast' => function ($url, $model, $key) {
                                return Html::a('<i class="fab fa-whatsapp"></i> ', $url, [
                                    'title' => 'Kirim notifikasi ke WhatsApp kantor',
                                    'data-pjax' => 0,
                                    'data-confirm' => 'Anda yakin ingin mengirimkan WhatsApp Blast untuk notifikasi ini? <br/><strong>' . $model['title'] . '</strong>'
                                ]);
                            },                            
                        ],
                    ],
                ],
                'layout' => $layout,
                'bordered' => false,
                'striped' => false,
                'condensed' => false,
                'hover' => true,
                'headerRowOptions' => ['class' => 'kartik-sheet-style'],
                'filterRowOptions' => ['class' => 'kartik-sheet-style'],
                'export' =>false,                
                'pjax' => false,
                'pjaxSettings' => [
                    'neverTimeout' => true,
                    // 'enablePushState' => false,
                    'options' => ['id' => 'some_pjax_id'],
                ],
                'pager' => [
                    'firstPageLabel' => '<i class="fas fa-angle-double-left"></i>',
                    'lastPageLabel' => '<i class="fas fa-angle-double-right"></i>',
                    'prevPageLabel' => '<i class="fas fa-angle-left"></i>',   // Set the label for the "previous" page button
                    'nextPageLabel' => '<i class="fas fa-angle-right"></i>',
                    'maxButtonCount' => 10,
                ],
                'toggleDataOptions' => ['minCount' => 10],
                'floatOverflowContainer' => true,
                'floatHeader' => true,
                'floatHeaderOptions' => [
                    'scrollingTop' => '0',
                    'position' => 'absolute',
                    'top' => 50
                ],
            ]); ?>
        </div>
    </div>
</div>
<script>
    const button = document.getElementById('w2-button');
    const dropdown = document.getElementById('w3');
    button.addEventListener('click', () => {
        dropdown.classList.toggle('show');
    });
    document.addEventListener('click', (event) => {
        if (!event.target.matches('#w2-button, #w3')) {
            dropdown.classList.remove('show');
        }
    });
</script>
<?php
Modal::begin([
    'title' => '',
    'id' => 'modal',
    'size' => 'modal-lg'
]);
echo '<div id="modalContent"></div>';
Modal::end();
?>
<script>
    $(function() {
        // changed id to class
        $('.modalButton').click(function() {
            $.get($(this).attr('href'), function(data) {
                $('#modal').modal('show').find('#modalContent').html(data)
            });
            return false;
        });
    });
</script>
<script>
    const spans = document.querySelectorAll('span.bg-white'); // select all spans with the class 'bg-white'
    spans.forEach(span => { // loop through each selected span element
        if (span.innerHTML === '') { // check if the innerHTML property is empty
            span.style.display = 'none'; // hide the span element
        }
    });
</script>