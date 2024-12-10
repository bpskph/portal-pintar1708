<?php

namespace app\models;

use Yii;

class Zooms extends \yii\db\ActiveRecord
{
    public $waktumulai;

    public static function tableName()
    {
        return 'zooms';
    }
    public function rules()
    {
        return [
            [['fk_agenda', 'jenis_zoom'], 'required'],
            [['fk_agenda'], 'unique', 'message' => 'Usulan zoom untuk agenda tersebut sudah ada pada database.'],
            [['fk_agenda', 'jenis_zoom', 'jenis_surat', 'deleted'], 'integer'],
            [['timestamp', 'timestamp_lastupdate', 'proposer', 'fk_surat'], 'safe'],
            [['proposer'], 'string', 'max' => 50],
            ['fk_agenda', 'validateRooms'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id_zooms' => 'Id Zooms',
            'fk_agenda' => 'Agenda Terkait',
            'jenis_zoom' => 'Jenis Zoom',
            'jenis_surat' => 'Jenis Surat',
            'fk_surat' => 'Nomor Surat',
            'proposer' => 'Pemohon',
            'deleted' => 'Deleted',
            'timestamp' => 'Timestamp',
            'timestamp_lastupdate' => 'Timestamp Last Update',
        ];
    }

    public function getAgendae()
    {
        return $this->hasOne(Agenda::className(), ['id_agenda' => 'fk_agenda']);
    }
    public function getZoomstypee()
    {
        return $this->hasOne(Zoomstype::className(), ['id_zoomstype' => 'jenis_zoom']);
    }
    public function getProposere()
    {
        return $this->hasOne(Pengguna::className(), ['username' => 'proposer']);
    }
    public function getSurate()
    {
        if ($this->jenis_surat == 0 && $this->fk_surat != '') {
            $fk_surat = str_replace('0-', '', $this->fk_surat);
            $surat = Suratrepo::findOne($fk_surat);
            $surat = $surat->nomor_suratrepo;
        } else {
            if ($this->fk_surat != '') {
                $fk_surat = str_replace('1-', '', $this->fk_surat);
                $surat = Suratrepoeks::findOne($fk_surat);
                $surat = $surat->nomor_suratrepoeks;
            } else {
                $surat = '-';
            }
        }
        return $surat;
    }
    public function validateRooms()
    {
        $data = Agenda::findOne($this->fk_agenda);

        $mulai = $data->waktumulai;
        $selesai = $data->waktuselesai;
        $zoom = $this->jenis_zoom;

        // Create the base query
        $query = Agenda::find()
            ->joinWith('zoomsnya')
            ->andWhere(['progress' => '0']) // Only check for pending agendas
            ->andWhere(['jenis_zoom' => $zoom]); // Check for the specific zoom account

        if (Yii::$app->controller->action->id == 'update') {
            $id = $this->id_zooms;
            $query->andWhere(['<>', 'id_zooms', $id]); // Exclude the current agenda from the check
        }

        // Check for overlapping times
        $query->andWhere([
            'or',
            ['between', 'waktumulai', $mulai, $selesai],
            ['between', 'waktuselesai', $mulai, $selesai],
            [
                'and',
                ['<=', 'waktumulai', $mulai],
                ['>=', 'waktuselesai', $selesai]
            ]
        ]);

        $model = $query->one();

        if ($model !== null) {
            $judul = $model->kegiatan;
            $formatter = Yii::$app->formatter;
            $formatter->locale = 'id-ID'; // Set the locale to Indonesian
            $timezone = new \DateTimeZone('Asia/Jakarta'); // Timezone for WIB

            // Format 'waktumulai_tunda'
            $waktumulai_tunda = new \DateTime($model->waktumulai_tunda, new \DateTimeZone('UTC'));
            $waktumulai_tunda->setTimeZone($timezone);
            $waktumulai_tundaFormatted = $formatter->asDatetime($waktumulai_tunda, 'd MMMM Y, H:mm');

            // Format 'waktuselesai_tunda'
            $waktuselesai_tunda = new \DateTime($model->waktuselesai_tunda, new \DateTimeZone('UTC'));
            $waktuselesai_tunda->setTimeZone($timezone);
            $waktuselesai_tundaFormatted = $formatter->asDatetime($waktuselesai_tunda, 'H:mm');

            // Determine the correct display format based on whether the dates are on the same day
            if ($waktumulai_tunda->format('Y-m-d') === $waktuselesai_tunda->format('Y-m-d')) {
                $watkutampilfinal = $waktumulai_tundaFormatted . ' - ' . $waktuselesai_tundaFormatted . ' WIB';
            } else {
                $waktuselesai_tundaFormatted = $formatter->asDatetime($waktuselesai_tunda, 'd MMMM Y, H:mm');
                $watkutampilfinal = $waktumulai_tundaFormatted . ' WIB <br/>s.d ' . $waktuselesai_tundaFormatted . ' WIB';
            }

            // Add error message for conflicting Zoom schedule
            $this->addError('tempat', 'Zoom dan jadwal tersebut sudah digunakan untuk Agenda "' . $judul . '" pada ' . $watkutampilfinal . '. Mohon sesuaikan data Agenda dan Zoom Anda.');
        }
    }
}
