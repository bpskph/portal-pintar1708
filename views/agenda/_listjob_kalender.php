<?php if ($model->waktumulai <= date("Y-m-d") && $model->waktuselesai >= date("Y-m-d")) : ?>
    <div class="carikalender" id="proyek-<?php echo date("Y-m-d") ?>">
    <?php else : ?>
        <div class="carikalender" id="proyek-<?php echo date("Y-m-d") ?>" style="display:none">
        <?php endif; ?>
        <?php
        $formatter = Yii::$app->formatter;
        $formatter->locale = 'id-ID'; // set the locale to Indonesian
        $timezone = new \DateTimeZone('Asia/Jakarta'); // create a timezone object for WIB
        $waktumulai = new \DateTime($model->waktumulai, new \DateTimeZone('UTC')); // create a datetime object for waktumulai with UTC timezone
        $waktumulai->setTimeZone($timezone); // set the timezone to WIB
        $waktumulaiFormatted = $formatter->asDatetime($waktumulai, 'd MMMM Y, H:mm'); // format the waktumulai datetime value
        $waktuselesai = new \DateTime($model->waktuselesai, new \DateTimeZone('UTC')); // create a datetime object for waktuselesai with UTC timezone
        $waktuselesai->setTimeZone($timezone); // set the timezone to WIB
        $waktuselesaiFormatted = $formatter->asDatetime($waktuselesai, 'H:mm'); // format the waktuselesai time value only
        if ($waktumulai->format('Y-m-d') === $waktuselesai->format('Y-m-d')) {
            // if waktumulai and waktuselesai are on the same day, format the time range differently
            $waktumulaiFormatted = $formatter->asDatetime($waktumulai, 'd MMMM Y, H:mm'); // format the waktumulai datetime value with the year and time
            $waktuFormatted = $waktumulaiFormatted . ' - ' . $waktuselesaiFormatted . ' WIB'; // concatenate the formatted dates
        } else {
            // if waktumulai and waktuselesai are on different days, format the date range normally
            $waktuselesaiFormatted = $formatter->asDatetime($waktuselesai, 'd MMMM Y, H:mm'); // format the waktuselesai datetime value
            $waktuFormatted = $waktumulaiFormatted . ' WIB s.d ' . $waktuselesaiFormatted . ' WIB'; // concatenate the formatted dates
        }
        ?>
        <div class="row">
            <div class="col-2" style="vertical-align: middle!important;">
                <div class="bg-info alert" style="height: 90%!important; line-height: 90%; text-align: center; display: flex; justify-content: center; align-items: center;">
                    <h2><i class="far fa-calendar"></i></h2>
                </div>
            </div>

            <div class="col-10">
                <div class="callout callout-info">
                    <span class="info-box-number"><i class="fas fa-wave-square"></i> Project: <?php echo $model->projecte->nama_project ?></span>
                    <br />
                    <span class="info-box-number"><i class="fas fa-user"></i> Kegiatan: <?php echo $model->kegiatan ?></span>
                    <br />
                    <span class="info-box-text"><i class="fas fa-asterisk"></i> Dipimpin: <?php echo $model->pemimpine->nama ?></span>
                    <br />
                    <span class="info-box-text"><i class="fas fa-asterisk"></i> Diusulkan: <?php echo $model->reportere->nama ?></span>
                    <br />
                    <span class="info-box-text"><i class="far fa-clock"></i><?php echo ' Waktu: ' . $waktuFormatted ?></span>
                </div>
            </div>
        </div>
        <br />
        </div>
    </div>